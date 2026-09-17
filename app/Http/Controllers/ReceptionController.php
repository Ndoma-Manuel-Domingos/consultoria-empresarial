<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveReceptionPaymentRequest;
use App\Http\Requests\SaveReceptionServicesRequest;
use App\Http\Requests\SaveReceptionTriageRequest;
use App\Http\Requests\StoreReceptionAppointmentRequest;
use App\Models\Client;
use App\Models\Product;
use App\Models\ReceptionAppointment;
use App\Services\ReceptionServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceptionController extends Controller
{
    public function __construct(protected ReceptionServices $receptionService) {}

    public function index(Request $request)
    {
        $tenantId = session('tenant_id');

        $query = ReceptionAppointment::query()
            ->with([ 'client', 'assignedUser', 'triage', 'payment', ])
            ->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($client) use ($search) {
                        $client->where('name', 'like', "%{$search}%")->orWhere('company_name', 'like', "%{$search}%")->orWhere('nif', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date')) {
            $query->whereDate(
                'appointment_date',
                $request->date
            );
        }

        $appointments = $query->latest('appointment_date')->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'waiting' => ReceptionAppointment::where('tenant_id', $tenantId)->where('status', 'waiting')->count(),
            'triage' => ReceptionAppointment::where('tenant_id', $tenantId)->where('status', 'triage')->count(),
            'in_progress' => ReceptionAppointment::where('tenant_id', $tenantId)->where('status', 'in_progress')->count(),
            'completed' => ReceptionAppointment::where('tenant_id', $tenantId)->where('status', 'completed')->count(),
        ];

        return view('tenant.reception.index', compact('appointments','stats'));
    }

    public function create()
    {
        $tenantId = session('tenant_id');
        $clients = Client::query()->where('tenant_id', $tenantId)->where('is_active', true)->orderBy('name')->get();

        return view('tenant.reception.create', compact('clients'));
    }

    public function store(StoreReceptionAppointmentRequest $request) 
    {
        $tenantId = session('tenant_id');

        $appointment = $this->receptionService->createAppointment(
            $tenantId,
            $request->validated()
        );

        return redirect()
            ->route('tenant.reception.show', $appointment)
            ->with('success', 'Atendimento criado com sucesso.');
    }

    public function show(ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $appointment->load([
            'client',
            'assignedUser',
            'triage',
            'services.product',
            'payment.receivedBy',
            'referredBy',
        ]);

        return view('tenant.reception.show', compact('appointment'));
    }

    public function triage(ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $appointment->load([
            'client',
            'triage',
        ]);

        return view('tenant.reception.triage', compact('appointment'));
    }

    public function saveTriage( SaveReceptionTriageRequest $request, ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $this->receptionService->saveTriage(
            $appointment,
            $request->validated()
        );

        return redirect()->route('tenant.reception.show', $appointment)->with('success','Triagem registada com sucesso.');
    }

    public function saveServices( SaveReceptionServicesRequest $request, ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $this->receptionService->saveServices(
            $appointment,
            $request->validated('services')
        );

        return redirect()
            ->route('tenant.reception.payment', $appointment)
            ->with('success', 'Serviços registados. Pode efetuar o pagamento.');
    }

    public function payment(ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $appointment->load([
            'client',
            'services.product',
            'payment',
        ]);

        $tenantId = session('tenant_id');

        $products = Product::query()
            ->where('tenant_id', session('tenant_id'))
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->orderBy('name')
        ->get();

        return view('tenant.reception.payment', compact('appointment', 'products'));
    }

    public function savePayment( SaveReceptionPaymentRequest $request, ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $this->receptionService->registerPayment(
            $appointment,
            $request->validated(),
            Auth::user()->id
        );

        return redirect()
            ->route('tenant.reception.ficha', $appointment)
            ->with('success', 'Pagamento registado com sucesso.');
    }

    public function ficha(ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $appointment->load([
            'client',
            'triage',
            'services.product',
            'payment',
            'assignedUser',
            'referredBy',
        ]);

        return view('tenant.reception.ficha', compact('appointment'));
    }

    public function refer(Request $request, ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $this->receptionService->referToConsultation(
            $appointment,
            Auth::user()->id,
            $request->input('referral_notes')
        );

        return redirect()
            ->route('tenant.reception.show', $appointment)
            ->with('success','Cliente encaminhado para o consultório.');
    }

    public function start(ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $this->receptionService->start($appointment);

        return back()->with('success', 'Atendimento iniciado.');
    }

    public function cancel(Request $request, ReceptionAppointment $appointment) 
    {
        $this->authorizeTenant($appointment);

        $this->receptionService->cancel(
            $appointment,
            $request->input('reason')
        );

        return back()->with('success', 'Atendimento cancelado.');
    }

    protected function authorizeTenant(ReceptionAppointment $appointment): void 
    {
        $tenantId = session('tenant_id');

        abort_unless( (int) $appointment->tenant_id === (int) $tenantId, 404);
    }
}