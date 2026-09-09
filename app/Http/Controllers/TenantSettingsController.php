<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TenantSettingsController extends Controller
{
    /**
     * Mostrar as configurações do tenant atual.
     */
    public function edit(Request $request): View
    {
        $tenant = app('currentTenant');

        return view('tenant.settings', [
            'tenant' => $tenant,
        ]);
    }

/**
     * Atualizar as configurações do tenant atual.
     */
    public function update(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');

        /*
         * Validação
         */
        $validated = $request->validate([
            'name' => ['required','string','max:255',],
            'description' => ['nullable','string','max:1000',],
            'email' => ['nullable','email','max:255',],
            'phone' => ['nullable','string','max:50',],
            'address' => ['nullable','string','max:255',],
            'city' => ['nullable','string','max:100',],
            'country' => ['nullable','string','max:100',],
            'postal_code' => ['nullable','string','max:30',],
            'logo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048',],
        ]);

        /*
         * Atualizar dados básicos
         */
        $tenant->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
        ]);


        /*
         * Upload do logo
         */
        if ($request->hasFile('logo')) {
            /*
             * Apagar logo antigo
             */
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            /*
             * Guardar novo logo
             *
             * Exemplo:
             * storage/app/public/tenants/1/logo.webp
             */
            $logoPath = $request->file('logo')->store(
                "tenants/{$tenant->id}",
                'public'
            );


            /*
             * Guardar caminho na BD
             */
            $tenant->update([
                'logo' => $logoPath,
            ]);
        }


        /*
         * Atualizar a instância do tenant no container.
         *
         * Isto garante que, durante esta request,
         * currentTenant representa o tenant atualizado.
         */
        app()->instance(
            'currentTenant',
            $tenant->fresh()
        );


        return redirect()
            ->route('tenant.settings.edit')
            ->with(
                'success',
                'As configurações da organização foram atualizadas com sucesso.'
            );
    }
}
