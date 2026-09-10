<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');

        abort_unless($tenantId, 403, 'Nenhuma organização selecionada.');

        $search = trim($request->get('search', ''));
        $roleId = $request->get('role');
        $status = $request->get('status');

        $users = User::query()
            ->whereHas('tenants', function ($query) use ($tenantId) {
                $query->where('tenants.id', $tenantId);
            })
            ->with([
                'roles' => function ($query) use ($tenantId) {
                    $query->where('roles.tenant_id', $tenantId);
                }
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleId, function ($query) use ($roleId, $tenantId) {
                $query->whereHas('roles', function ($q) use ($roleId, $tenantId) {
                    $q->where('roles.id', $roleId)
                        ->where('roles.tenant_id', $tenantId);
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest('users.created_at')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::query()
            ->where('tenant_id', $tenantId)
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('tenant.users.partials.table', compact('users'))->render(),
                'pagination' => $users->links()->render(),
            ]);
        }

        return view('tenant.users.index', compact(
            'users',
            'roles'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenantId = session('tenant_id');

        abort_unless($tenantId, 403, 'Nenhuma organização selecionada.');

        $roles = Role::query()
            ->where('tenant_id', $tenantId)
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

            return view('tenant.users.form', [
            'user' => null,
            'roles' => $roles,
            'mode' => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        abort_unless($tenantId, 403, 'Nenhuma organização selecionada.');

        $validated = $request->validate([
            'name' => ['required','string','max:255',],
            'email' => ['required','email','max:255','unique:users,email',],
            'phone' => ['nullable','string','max:30',],
            'job_title' => ['nullable','string','max:255',],
            'password' => ['required','string','min:8','confirmed',],
            // O campo enviado pelo formulário
            'roles' => [
                'required',
                'array',
                'min:1',
            ],

            // Cada elemento do array
            'roles.*' => [
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) use ($tenantId) {
                    $query->where('tenant_id', $tenantId)
                        ->where('guard_name', 'web');
                }),
            ],
            'status' => ['nullable',Rule::in(['active', 'inactive']),],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user = DB::transaction(function () use ($validated, $tenantId) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'job_title' => $validated['job_title'] ?? null,
                'password' => Hash::make($validated['password']),
                'status' => $validated['status'] ?? 'active',
            ]);
            /*
             * Associar utilizador ao tenant.
             *
             * Não usamos o campo "role" da tenant_user
             * para controlar permissões.
             */
            DB::table('tenant_user')->insert([
                'user_id' => $user->id,
                'tenant_id' => $tenantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            /*
             * Obter a Role garantindo novamente
             * que pertence ao tenant atual.
             */
            $role = Role::query()
                ->where('id', $validated['roles'])
                ->where('tenant_id', $tenantId)
                ->where('guard_name', 'web')
                ->firstOrFail();

            /*
             * Spatie.
             */
            $user->assignRole($role);

            return $user;
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilizador criado com sucesso.',
                'redirect' => route('tenant.users.index'),
                'user_id' => $user->id,
            ]);
        }

        return redirect()
            ->route('tenant.users.index')
            ->with('success', 'Utilizador criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $tenantId = session('tenant_id');
        $tenant = Tenant::findOrFail($tenantId);

        abort_unless($tenantId, 403, 'Nenhuma organização selecionada.');

        $this->ensureUserBelongsToTenant($user, $tenantId);

        $user->load(['roles' => function ($query) use ($tenantId) {
            $query->where('roles.tenant_id', $tenantId);
        }]);

        $roles = $user->roles()->where('roles.tenant_id', $tenantId)->get();

        return view('tenant.users.show', compact('user', 'roles', 'tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(User $user)
    {
        $tenantId = session('tenant_id');

        abort_unless($tenantId, 403, 'Nenhuma organização selecionada.');

        $this->ensureUserBelongsToTenant($user, $tenantId);

        $roles = Role::query()
            ->where('tenant_id', $tenantId)
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        /*
         * Obter a Role deste utilizador neste tenant.
         */
        $currentRole = $user->roles()
            ->where('roles.tenant_id', $tenantId)
            ->where('roles.guard_name', 'web')
            ->pluck("id")->toArray();

        return view('tenant.users.form', [
            'user' => $user,
            'roles' => $roles,
            'currentRole' => $currentRole,
            'mode' => 'edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $tenantId = session('tenant_id');

        abort_unless($tenantId, 403, 'Nenhuma organização selecionada.');

        $this->ensureUserBelongsToTenant($user, $tenantId);

        $validated = $request->validate([
            'name' => ['required','string','max:255',],
            'email' => ['required','email','max:255',Rule::unique('users', 'email')    ->ignore($user->id),],
            'phone' => ['nullable','string','max:30',],
            'job_title' => ['nullable','string','max:255',],
            'password' => ['nullable','string','min:8','confirmed',],
            // O campo enviado pelo formulário
            'roles' => [
                'required',
                'array',
                'min:1',
            ],

            // Cada elemento do array
            'roles.*' => [
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) use ($tenantId) {
                    $query->where('tenant_id', $tenantId)
                        ->where('guard_name', 'web');
                }),
            ],

            'status' => ['nullable', Rule::in(['active', 'inactive']),],
        ]);

        DB::transaction(function () use ($validated, $user, $tenantId) {

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'job_title' => $validated['job_title'] ?? null,
                'status' => $validated['status'] ?? 'active',
            ];

            /*
             * Só altera password se o campo tiver sido preenchido.
             */
            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            /*
             * Confirmar Role do tenant.
             */
            $role = Role::query()
                ->where('id', $validated['roles'])
                ->where('tenant_id', $tenantId)
                ->where('guard_name', 'web')
                ->firstOrFail();

            /*
             * Remover apenas as Roles deste tenant.
             *
             * Isto é importante caso o utilizador
             * pertença a vários tenants.
             */
            $tenantRoleIds = Role::query()
                ->where('tenant_id', $tenantId)
                ->pluck('id');

            if ($tenantRoleIds->isNotEmpty()) {
                $user->roles()
                    ->whereIn('roles.id', $tenantRoleIds)
                    ->detach();
            }

            /*
             * Atribuir a nova Role.
             */
            $user->assignRole($role);
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilizador atualizado com sucesso.',
                'redirect' => route('tenant.users.index'),
            ]);
        }

        return redirect()
            ->route('tenant.users.index')
            ->with('success', 'Utilizador atualizado com sucesso.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $tenantId = session('tenant_id');

        abort_unless($tenantId, 403, 'Nenhuma organização selecionada.');

        $this->ensureUserBelongsToTenant($user, $tenantId);

        /*
         * Impedir que o utilizador elimine a própria conta.
         */
        if (Auth::user()->id === $user->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não pode eliminar a sua própria conta.',
                ], 422);
            }

            return back()->withErrors([
                'user' => 'Não pode eliminar a sua própria conta.',
            ]);
        }

        DB::transaction(function () use ($user, $tenantId) {

            /*
             * Remover associação deste utilizador
             * apenas ao tenant atual.
             */
            DB::table('tenant_user')
                ->where('user_id', $user->id)
                ->where('tenant_id', $tenantId)
                ->delete();

            /*
             * Remover Roles pertencentes ao tenant atual.
             */
            $tenantRoleIds = Role::query()
                ->where('tenant_id', $tenantId)
                ->pluck('id');

            if ($tenantRoleIds->isNotEmpty()) {
                $user->roles()
                    ->whereIn('roles.id', $tenantRoleIds)
                    ->detach();
            }

            /*
             * Verificar se o utilizador ainda pertence
             * a algum tenant.
             */
            $remainingTenants = DB::table('tenant_user')
                ->where('user_id', $user->id)
                ->exists();

            /*
             * Só apagamos o User de verdade se ele
             * não pertencer a mais nenhuma organização.
             */
            if (!$remainingTenants) {
                $user->delete();
            }
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilizador removido com sucesso.',
            ]);
        }

        return redirect()
            ->route('tenant.users.index')
            ->with('success', 'Utilizador removido com sucesso.');
    }

        /**
     * Garantir que o utilizador pertence ao tenant atual.
     */
    private function ensureUserBelongsToTenant(User $user, int $tenantId): void 
    {
        $belongsToTenant = DB::table('tenant_user')
            ->where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->exists();

        abort_unless($belongsToTenant, 404, 'Utilizador não encontrado nesta organização.');
    }
}
