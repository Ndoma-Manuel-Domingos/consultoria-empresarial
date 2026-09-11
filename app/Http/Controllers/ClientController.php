<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');

        abort_unless( $tenantId, 403, 'Nenhuma organização selecionada.');

        $query = Client::query()->where('tenant_id', $tenantId);

        /*
        |--------------------------------------------------------------------------
        | PESQUISA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('commercial_name', 'like', "%{$search}%")
                    ->orWhere('nif', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO POR TIPO
        |--------------------------------------------------------------------------
        */

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO POR ESTADO
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            }
            if ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ORDENAÇÃO
        |--------------------------------------------------------------------------
        */

        $clients = $query->latest('id')->paginate(15)->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('tenant.clients.partials.table', compact('clients'))->render(),
                'pagination' => $clients->links()->render(),
            ]);
        }

        return view(
            'tenant.clients.index',
            compact('clients')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tenantId = session('tenant_id');

        abort_unless($tenantId,403,'Nenhuma organização selecionada.');

        return view(
            'tenant.clients.form',
            [
                'client' => null,
                'mode' => 'create',
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        abort_unless( $tenantId, 403, 'Nenhuma organização selecionada.');

        $validated = $this->validateClient($request,$tenantId);

        try {
            $client = DB::transaction(function () use ($validated, $tenantId) {
                return Client::create([
                    /*
                    |--------------------------------------------------------------------------
                    | TENANT
                    |--------------------------------------------------------------------------
                    */
                    'tenant_id' => $tenantId,
                    /*
                    |--------------------------------------------------------------------------
                    | IDENTIFICAÇÃO
                    |--------------------------------------------------------------------------
                    */
                    'type' => $validated['type'],
                    'name' => $validated['name'],
                    'company_name' => $validated['company_name'] ?? null,
                    'commercial_name' => $validated['commercial_name'] ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | DADOS FISCAIS
                    |--------------------------------------------------------------------------
                    */
                    'nif' => $validated['nif'] ?? null,
                    'nif_type' => $validated['nif_type'] ?? null,
                    'tax_regime' => $validated['tax_regime'] ?? null,
                    'vat_payer' => $validated['vat_payer'] ?? false,

                    /*
                    |--------------------------------------------------------------------------
                    | CONTACTOS
                    |--------------------------------------------------------------------------
                    */
                    'email' => $validated['email'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'phone_secondary' => $validated['phone_secondary'] ?? null,
                    'website' => $validated['website'] ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | ENDEREÇO
                    |--------------------------------------------------------------------------
                    */
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'province' => $validated['province'] ?? null,
                    'municipality' => $validated['municipality'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'country' => $validated['country'] ?? 'Angola',

                    /*
                    |--------------------------------------------------------------------------
                    | CONTACTO PRINCIPAL
                    |--------------------------------------------------------------------------
                    */
                    'contact_person' => $validated['contact_person'] ?? null,
                    'contact_person_phone' => $validated['contact_person_phone'] ?? null,
                    'contact_person_email' => $validated['contact_person_email'] ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | CONDIÇÕES COMERCIAIS
                    |--------------------------------------------------------------------------
                    */
                    'credit_limit' => $validated['credit_limit'] ?? 0,
                    'payment_terms' => $validated['payment_terms'] ?? 0,

                    /*
                    |--------------------------------------------------------------------------
                    | OUTROS
                    |--------------------------------------------------------------------------
                    */

                    'notes' => $validated['notes'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,

                    /*
                    |--------------------------------------------------------------------------
                    | AUDITORIA
                    |--------------------------------------------------------------------------
                    */
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
            });

            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente criado com sucesso.',
                    'redirect' => route('tenant.clients.index'),
                    'client_id' => $client->id,
                ]);
            }

            return redirect()
                ->route('tenant.clients.index')
                ->with( 'success', 'Cliente criado com sucesso.');

        } catch (\Throwable $e) {
            report($e);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível criar o cliente.',
                ], 500);
            }
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Não foi possível criar o cliente.',
                ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, Client $client)
    {
        $tenantId = session('tenant_id');

        abort_unless( $tenantId, 403, 'Nenhuma organização selecionada.');

        /*
        |--------------------------------------------------------------------------
        | SEGURANÇA MULTI-TENANT
        |--------------------------------------------------------------------------
        */
        abort_unless( (int) $client->tenant_id === (int) $tenantId, 404);
        return view('tenant.clients.show',compact('client'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $tenantId = session('tenant_id');

        abort_unless( $tenantId, 403, 'Nenhuma organização selecionada.');

        /*
        |--------------------------------------------------------------------------
        | SEGURANÇA MULTI-TENANT
        |--------------------------------------------------------------------------
        */

        abort_unless((int) $client->tenant_id === (int) $tenantId,404);

        return view('tenant.clients.form', [
            'client' => $client,
            'mode' => 'edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $tenantId = session('tenant_id');

        abort_unless( $tenantId, 403, 'Nenhuma organização selecionada.');

        /*
        |--------------------------------------------------------------------------
        | SEGURANÇA MULTI-TENANT
        |--------------------------------------------------------------------------
        */

        abort_unless( (int) $client->tenant_id === (int) $tenantId, 404);

        $validated = $this->validateClient($request, $tenantId, $client->id);

        try {
            DB::transaction(function () use ($client,  $validated) {

                $client->update([

                    /*
                    |--------------------------------------------------------------------------
                    | IDENTIFICAÇÃO
                    |--------------------------------------------------------------------------
                    */
                    'type' => $validated['type'],
                    'name' => $validated['name'],
                    'company_name' => $validated['company_name'] ?? null,
                    'commercial_name' => $validated['commercial_name'] ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | DADOS FISCAIS
                    |--------------------------------------------------------------------------
                    */
                    'nif' => $validated['nif'] ?? null,
                    'nif_type' => $validated['nif_type'] ?? null,
                    'tax_regime' => $validated['tax_regime'] ?? null,
                    'vat_payer' => $validated['vat_payer'] ?? false,

                    /*
                    |--------------------------------------------------------------------------
                    | CONTACTOS
                    |--------------------------------------------------------------------------
                    */
                    'email' => $validated['email'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'phone_secondary' => $validated['phone_secondary'] ?? null,
                    'website' => $validated['website'] ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | ENDEREÇO
                    |--------------------------------------------------------------------------
                    */
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'province' => $validated['province'] ?? null,
                    'municipality' => $validated['municipality'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'country' => $validated['country'] ?? 'Angola',

                    /*
                    |--------------------------------------------------------------------------
                    | CONTACTO
                    |--------------------------------------------------------------------------
                    */
                    'contact_person' => $validated['contact_person'] ?? null,
                    'contact_person_phone' => $validated['contact_person_phone'] ?? null,
                    'contact_person_email' => $validated['contact_person_email'] ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | COMERCIAL
                    |--------------------------------------------------------------------------
                    */
                    'credit_limit' => $validated['credit_limit'] ?? 0,
                    'payment_terms' => $validated['payment_terms'] ?? 0,

                    /*
                    |--------------------------------------------------------------------------
                    | OUTROS
                    |--------------------------------------------------------------------------
                    */
                    'notes' => $validated['notes'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,

                    /*
                    |--------------------------------------------------------------------------
                    | AUDITORIA
                    |--------------------------------------------------------------------------
                    */
                    'updated_by' => Auth::user()->id,
                ]);
            });

            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente atualizado com sucesso.',
                    'redirect' => route('tenant.clients.index'),
                    'client_id' => $client->id,
                ]);
            }

            return redirect()->route('tenant.clients.index')->with('success', 'Cliente atualizado com sucesso.');

        } catch (\Throwable $e) {

            report($e);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível atualizar o cliente.',
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Não foi possível atualizar o cliente.',
                ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Client $client)
    {
        $tenantId = session('tenant_id');

        abort_unless($tenantId,403,'Nenhuma organização selecionada.');

        /*
        |--------------------------------------------------------------------------
        | SEGURANÇA MULTI-TENANT
        |--------------------------------------------------------------------------
        */

        abort_unless( (int) $client->tenant_id === (int) $tenantId, 404);

        try {

            DB::transaction(function () use ($client) {
                $client->delete();
            });

            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente eliminado com sucesso.',
                ]);
            }

            return redirect()
                ->route('tenant.clients.index')
                ->with('success','Cliente eliminado com sucesso.');

        } catch (\Throwable $e) {
            report($e);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível eliminar o cliente.',
                ], 500);
            }

            return back()
                ->withErrors([
                    'error' => 'Não foi possível eliminar o cliente.',
                ]);
        }
    }


    private function validateClient( Request $request, int $tenantId, ?int $clientId = null): array 
    {
        return $request->validate([
            /*
            |--------------------------------------------------------------------------
            | IDENTIFICAÇÃO
            |--------------------------------------------------------------------------
            */

            'type' => [ 'required', Rule::in(['individual', 'company',]),],
            'name' => ['required','string','max:255',],
            'company_name' => ['nullable','string','max:255',],
            'commercial_name' => ['nullable','string','max:255',],

            /*
            |--------------------------------------------------------------------------
            | NIF
            |--------------------------------------------------------------------------
            |
            | O NIF é único dentro do tenant.
            |
            */

            'nif' => ['nullable','string','max:30', Rule::unique('clients', 'nif')
                ->where(function ($query) use ($tenantId) {
                    $query->where( 'tenant_id', $tenantId);
                })
                ->ignore($clientId),
            ],
            'nif_type' => ['nullable','string','max:30',],

            /*
            |--------------------------------------------------------------------------
            | FISCAL
            |--------------------------------------------------------------------------
            */
            'tax_regime' => ['nullable','string','max:50',],
            'vat_payer' => ['nullable','boolean',],

            /*
            |--------------------------------------------------------------------------
            | CONTACTOS
            |--------------------------------------------------------------------------
            */
            'email' => ['nullable','email','max:255',],
            'phone' => ['nullable','string','max:30',],
            'phone_secondary' => ['nullable','string','max:30',],
            'website' => ['nullable','string','max:255',],

            /*
            |--------------------------------------------------------------------------
            | ENDEREÇO
            |--------------------------------------------------------------------------
            */
            'address' => ['nullable','string','max:1000',],
            'city' => ['nullable','string','max:100',],
            'province' => ['nullable','string','max:100',],
            'municipality' => ['nullable','string','max:100',],
            'postal_code' => ['nullable','string','max:20',],
            'country' => ['nullable','string','max:100',],

            /*
            |--------------------------------------------------------------------------
            | CONTACTO PRINCIPAL
            |--------------------------------------------------------------------------
            */
            'contact_person' => ['nullable','string','max:255',],
            'contact_person_phone' => ['nullable','string','max:30',],
            'contact_person_email' => ['nullable','email','max:255',],

            /*
            |--------------------------------------------------------------------------
            | CONDIÇÕES COMERCIAIS
            |--------------------------------------------------------------------------
            */
            'credit_limit' => ['nullable','numeric','min:0','max:999999999999.99',],
            'payment_terms' => ['nullable','integer','min:0','max:365',],

            /*
            |--------------------------------------------------------------------------
            | OUTROS
            |--------------------------------------------------------------------------
            */
            'notes' => ['nullable','string','max:5000',],
            'is_active' => ['nullable','boolean',],
        ]);
    }
}
