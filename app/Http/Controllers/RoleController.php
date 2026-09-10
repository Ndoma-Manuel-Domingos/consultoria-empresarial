<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Tenant atual.
     */
    private function tenantId(): int
    {
        $tenantId = session('tenant_id');

        abort_unless($tenantId, 403, 'Nenhuma organização selecionada.');

        return (int) $tenantId;
    }

    /**
     * Display a listing of the resource.
     */
public function index(Request $request): View
    {
        $tenantId = $this->tenantId();

        $roles = Role::query()
            ->where('tenant_id', $tenantId)
            ->where('guard_name', 'web')
            ->withCount('permissions')
            ->withCount('users')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('tenant.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->tenantId();

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('tenant.roles.form', [
            'role' => null,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
    {
        $tenantId = $this->tenantId();

        $validated = $request->validate([
            'name' => ['required','string','max:100', Rule::unique('roles')
                ->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId)->where('guard_name', 'web');
                }),
            ],
            'permissions' => ['nullable','array',],
            'permissions.*' => ['integer','exists:permissions,id',],
        ], [
            'name.required' => 'O nome do perfil é obrigatório.',
            'name.unique' => 'Já existe um perfil com este nome nesta organização.',
        ]);

        DB::transaction(function () use ($validated, $tenantId) {
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
                'tenant_id' => $tenantId,
            ]);

            $permissionIds = $validated['permissions'] ?? [];

            $permissions = Permission::query()
                ->where('guard_name', 'web')
                ->whereIn('id', $permissionIds)
                ->get();

            $role->syncPermissions($permissions);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfil criado com sucesso.',
                'redirect' => route('tenant.roles.index'),
            ]);
        }

        return redirect()
            ->route('tenant.roles.index')
            ->with('success', 'Perfil criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $tenantId = $this->tenantId();

        abort_unless(
            (int) $role->tenant_id === $tenantId,
            404
        );

        $role->load('permissions');

        return view('tenant.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $tenantId = $this->tenantId();

        abort_unless(
            (int) $role->tenant_id === $tenantId,
            404
        );

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $role->load('permissions');

        return view('tenant.roles.form', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $tenantId = $this->tenantId();

        abort_unless(
            (int) $role->tenant_id === $tenantId,
            404
        );

        $validated = $request->validate([
            'name' => ['required','string','max:100', Rule::unique('roles')
                ->ignore($role->id)
                ->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId)->where('guard_name', 'web');
                }),
            ],
            'permissions' => ['nullable','array',],
            'permissions.*' => ['integer','exists:permissions,id',],
        ], [
            'name.required' => 'O nome do perfil é obrigatório.',
            'name.unique' => 'Já existe outro perfil com este nome nesta organização.',
        ]);

        DB::transaction(function () use ($validated, $role) {

            $role->update([
                'name' => $validated['name'],
            ]);

            $permissionIds = $validated['permissions'] ?? [];

            $permissions = Permission::query()
                ->where('guard_name', 'web')
                ->whereIn('id', $permissionIds)
                ->get();

            $role->syncPermissions($permissions);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso.',
                'redirect' => route('tenant.roles.index'),
            ]);
        }

        return redirect()
            ->route('tenant.roles.index')
            ->with('success', 'Perfil atualizado com sucesso.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Role $role)
    {
        $tenantId = $this->tenantId();

        abort_unless(
            (int) $role->tenant_id === $tenantId,
            404
        );

        /*
         * Não permitir eliminar perfil que esteja atribuído
         * a utilizadores.
         */
        if ($role->users()->exists()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este perfil não pode ser eliminado porque está atribuído a um ou mais utilizadores.',
                ], 422);
            }
            return back()->withErrors([
                'role' => 'Este perfil não pode ser eliminado porque está atribuído a utilizadores.',
            ]);
        }

        DB::transaction(function () use ($role) {
            $role->syncPermissions([]);
            $role->delete();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfil eliminado com sucesso.',
            ]);
        }

        return redirect()
            ->route('tenant.roles.index')
            ->with('success', 'Perfil eliminado com sucesso.');
    }
}
