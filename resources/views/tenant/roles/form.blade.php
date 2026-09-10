@extends('layouts.app')

@section('title', $role ? 'Editar perfil' : 'Novo perfil')
@section('page_header')

<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            {{ $role ? 'Editar perfil' : 'Novo perfil' }}
        </h1>
        <div class="page-subtitle">
            {{ $role
                ? 'Atualize as informações e permissões deste perfil.'
                : 'Crie um novo perfil de acesso para a organização.'
            }}
        </div>
    </div>
    <div>
        <a href="{{ route('tenant.roles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>
            Voltar
        </a>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{$role ? route('tenant.roles.update', $role) : route('tenant.roles.store')}}" id="role-form" class="ajax-form">
    @csrf
    @if($role)
    @method('PUT')
    @endif
    <div class="row">
        {{-- ============================= --}}
        {{-- INFORMAÇÕES --}}
        {{-- ============================= --}}
        <div class="col-lg-5">
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Informações do perfil
                        </h3>
                        <div class="card-subtitle-modern">
                            Defina as informações básicas do perfil.
                        </div>
                    </div>
                </div>
                {{-- NOME --}}
                <div class="form-group">
                    <label class="form-label required">
                        Nome do perfil
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-user-shield input-icon"></i>
                        <input type="text" name="name" class="auth-input @error('name') is-invalid @enderror" value="{{ old('name', $role->name ?? '') }}" placeholder="Ex.: Administrador" maxlength="100" required>
                    </div>
                    @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                {{-- GUARD --}}
                <div class="form-group">
                    <label class="form-label">
                        Guard
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="text" class="auth-input" value="web" disabled>
                    </div>
                    <small style="display:block;margin-top:6px;color:#94a3b8;font-size:10px;">
                        Este perfil será utilizado pela autenticação web.
                    </small>
                </div>

                {{-- INFO --}}
                <div style="padding:14px;border-radius:10px;background:#eff6ff;border:1px solid #dbeafe;">
                    <div style="display:flex;gap:10px;">
                        <i class="fas fa-info-circle" style="color:#2563eb;margin-top:2px;"></i>
                        <div>
                            <div style="color:#1e40af;font-size:11px;font-weight:700;">
                                Sobre os perfis
                            </div>

                            <div style="color:#64748b;font-size:10px;line-height:1.6;margin-top:3px;">
                                Um perfil é um conjunto de permissões.
                                Depois de criado, pode ser atribuído aos
                                utilizadores da organização.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- PERMISSÕES --}}
        {{-- ============================= --}}
        <div class="col-lg-7">
            <div class="dashboard-card chart-card">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Permissões do perfil
                        </h3>
                        <div class="card-subtitle-modern">
                            Selecione as funcionalidades que este perfil poderá utilizar.
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" id="toggle-all-permissions">
                            <i class="fas fa-check-double mr-1"></i>
                            Selecionar todas
                        </button>
                    </div>
                </div>

                {{-- PESQUISA DE PERMISSÕES --}}
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-search input-icon"></i>
                        <input type="text" id="permission-search" class="auth-input" placeholder="Pesquisar permissão...">
                    </div>
                </div>

                {{-- LISTA --}}
                <div id="permissions-list" style="max-height:480px;overflow-y:auto;padding-right:5px;">
                    @php
                    $selectedPermissions = old('permissions', $role ? $role->permissions->pluck('id')->toArray() : []);
                    @endphp

                    @forelse($permissions as $permission)
                    <label class="permission-option" data-permission-name="{{ strtolower($permission->name) }}">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="permission-checkbox" {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}>
                        <span class="permission-check">
                            <i class="fas fa-check"></i>
                        </span>
                        <span class="permission-info">
                            <span class="permission-name">
                                {{ $permission->name }}
                            </span>
                            <span class="permission-guard">
                                <i class="fas fa-shield-alt mr-1"></i>
                                {{ $permission->guard_name }}
                            </span>
                        </span>
                    </label>
                    @empty
                    <div class="text-center" style="padding:40px 20px;">
                        <i class="fas fa-key" style="color:#cbd5e1;font-size:30px;"></i>
                        <div style="margin-top:10px;color:#64748b;font-size:12px;font-weight:600;">
                            Nenhuma permissão encontrada.
                        </div>
                    </div>
                    @endforelse
                </div>

                {{-- FOOTER --}}
                <div style="margin-top:20px;padding-top:15px;border-top:1px solid #f1f5f9;">
                    <div class="d-flex justify-content-between">
                        <span style="color:#64748b;font-size:11px;">
                            Permissões selecionadas:
                        </span>
                        <strong id="permission-counter" style="color:#2563eb;font-size:11px;">
                            0
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- FOOTER --}}
    {{-- ============================= --}}

    <div class="dashboard-card mt-4" style="padding:16px 20px;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div style="color:#334155;font-size:12px;font-weight:600;">
                    {{ $role ? 'Guardar alterações' : 'Criar perfil' }}
                </div>
                <div style="color:#94a3b8;font-size:10px;margin-top:3px;">
                    As alterações serão aplicadas imediatamente.
                </div>
            </div>

            <div class="d-flex" style="gap:8px;">
                <a href="{{ route('tenant.roles.index') }}" class="btn btn-secondary btn-sm">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary btn-sm" id="role-submit-button">
                    <i class="fas fa-save mr-1"></i>
                    {{ $role ? 'Guardar alterações' : 'Criar perfil' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const counter = document.getElementById('permission-counter');
    const toggleButton = document.getElementById(
        'toggle-all-permissions'
    );
    const searchInput = document.getElementById(
        'permission-search'
    );
    /*
    |--------------------------------------------------------------------------
    | Contador
    |--------------------------------------------------------------------------
    */
    function updateCounter() {
        const selected = document.querySelectorAll(
            '.permission-checkbox:checked'
        ).length;
        if (counter) {
            counter.textContent = selected;
        }
        if (toggleButton) {
            const visible = document.querySelectorAll(
                '.permission-option:not(.hidden-permission) .permission-checkbox'
            ).length;
            toggleButton.innerHTML =
                selected >= visible && visible > 0
                    ? '<i class="fas fa-times mr-1"></i> Desmarcar todas'
                    : '<i class="fas fa-check-double mr-1"></i> Selecionar todas';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Selecionar / desmarcar todas
    |--------------------------------------------------------------------------
    */

    if (toggleButton) {
        toggleButton.addEventListener('click', function () {
            const visibleOptions = document.querySelectorAll(
                '.permission-option:not(.hidden-permission) .permission-checkbox'
            );
            const allChecked = [...visibleOptions].every(
                checkbox => checkbox.checked
            );
            visibleOptions.forEach(function (checkbox) {
                checkbox.checked = !allChecked;
            });
            updateCounter();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Alteração checkbox
    |--------------------------------------------------------------------------
    */
    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateCounter);
    });

    /*
    |--------------------------------------------------------------------------
    | Pesquisa
    |--------------------------------------------------------------------------
    */

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const value = this.value.toLowerCase().trim();

            document.querySelectorAll('.permission-option').forEach(function (option) {
                const name = option.dataset.permissionName || '';

                if (value === '' ||name.includes(value)) {
                    option.classList.remove(
                        'hidden-permission'
                    );
                } else {
                    option.classList.add(
                        'hidden-permission'
                    );
                }
            });
        });
    }
    updateCounter();
});

</script>

@endpush
