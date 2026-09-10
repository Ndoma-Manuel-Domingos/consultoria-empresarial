<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $permissions = Permission::query()
            ->orderBy('name')
            ->paginate(15);

        return view('tenant.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.permissions.form', [
            'permission' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [ 'required', 'string', 'max:255', Rule::unique('permissions', 'name')->where('guard_name', 'web')]
        ], [
            'name.required' => 'O nome da permissão é obrigatório.', 
            'name.string' => 'O nome da permissão deve ser um texto válido.', 
            'name.max' => 'O nome da permissão não pode ter mais de 255 caracteres.', 
            'name.unique' => 'Já existe uma permissão com este nome.', 
        ]); 
        Permission::create([
            'name' => $validated['name'], 
            'guard_name' => 'web', 
        ]); 
        return redirect()->route('tenant.permissions.index')->with('success', 'Permissão criada com sucesso.'); 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('tenant.permissions.form', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission) 
    { 
        $validated = $request->validate([ 
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->where('guard_name', 'web')->ignore($permission->id)]
        ], [ 
            'name.required' => 'O nome da permissão é obrigatório.', 
            'name.string' => 'O nome da permissão deve ser um texto válido.', 
            'name.max' => 'O nome da permissão não pode ter mais de 255 caracteres.', 
            'name.unique' => 'Já existe outra permissão com este nome.', 
        ]); 
        $permission->update(['name' => $validated['name']]); 
        return redirect()->route('tenant.permissions.index')->with('success', 'Permissão atualizada com sucesso.'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission) 
    { 
        $permission->delete(); 
        return redirect()->route('tenant.permissions.index')->with('success', 'Permissão eliminada com sucesso.'); 
    } 
}
