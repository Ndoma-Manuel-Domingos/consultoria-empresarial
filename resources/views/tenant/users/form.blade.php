@extends('layouts.app')

@section('title', isset($user) ? 'Editar utilizador' : 'Novo utilizador')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            {{ isset($user) ? 'Editar utilizador' : 'Novo utilizador' }}
        </h1>
        <div class="page-subtitle">
            {{ isset($user) ? 'Atualize os dados e permissões do utilizador.' : 'Adicione um novo utilizador à organização.' }}
        </div>
    </div>
    <div class="d-flex align-items-center">
        <a href="{{ route('tenant.users.index') }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        <button type="submit" form="user-form" class="btn btn-primary btn-sm" data-loading-text="{{ isset($user) ? 'A guardar...' : 'A criar...' }}">
            <i class="fas fa-save"></i>
            {{ isset($user) ? 'Guardar alterações' : 'Criar utilizador' }}
        </button>
    </div>
</div>
@endsection
@section('content')
<form id="user-form" method="POST" 
    action="{{$user ? route('tenant.users.update', $user) : route('tenant.users.store')}}" 
    enctype="multipart/form-data" data-ajax="true">
    @csrf
    @if(isset($user))
    @method('PUT')
    @endif
    {{-- ========== ALERTS ============== --}}
    @if(session('success'))
    <div class="alert alert-success mb-4">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            {{ $errors->first() }}
        </div>
    </div>
    @endif

    <div class="row">
        {{-- ========== LEFT =========== --}}
        <div class="col-lg-8">
            {{-- ========== DADOS PESSOAIS ========== --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Dados do utilizador
                        </h3>
                        <div class="card-subtitle-modern">
                            Informações básicas do utilizador.
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Nome --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Nome</label>
                            <input type="text" name="name" class="auth-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name ?? '') }}" placeholder="Nome completo" required>
                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    {{-- Email --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Email</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" class="auth-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}" placeholder="utilizador@email.com" required>
                            </div>
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Telefone --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Telefone</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="text" name="phone" class="auth-input @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+244 900 000 000">
                            </div>
                            @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Cargo --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Cargo</label>
                            <div class="input-wrapper">
                                <i class="fas fa-briefcase input-icon"></i>
                                <input type="text" name="job_title" class="auth-input" value="{{ old('job_title', $user->job_title ?? '') }}" placeholder="Ex.: Gestor, Consultor...">
                            </div>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label {{ isset($user) ? '' : 'required' }}">Palavra-passe</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="password" class="auth-input @error('password') is-invalid @enderror" placeholder="{{ isset($user) ? 'Deixe vazio para manter' : 'Palavra-passe' }}" {{ isset($user) ? '' : 'required' }}>
                            </div>
                            @if(isset($user))
                            <small class="text-muted">
                                Preencha apenas se quiser alterar a palavra-passe.
                            </small>
                            @endif
                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Confirmar password --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label {{ isset($user) ? '' : 'required' }}">
                                Confirmar palavra-passe
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="password_confirmation" class="auth-input" placeholder="Confirmar palavra-passe" {{ isset($user) ? '' : 'required' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- = PERFIL / ROLES ============ --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Perfil de acesso
                        </h3>
                        <div class="card-subtitle-modern">
                            Defina as permissões deste utilizador dentro da organização atual.
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">
                        Perfis de acesso
                    </label>
                    @php
                    $selectedRoles = old('roles', isset($user) ? ($currentRole ?? []) : []);
                    @endphp
                    @if(isset($roles) && $roles->count())
                    <div class="row">
                        @foreach($roles as $role)
                        <div class="col-md-6 mb-3">
                            <label class="custom-checkbox">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, $selectedRoles) ? 'checked' : '' }}>
                                <span class="checkbox-box"></span>
                                <span class="checkbox-label">
                                    <strong>
                                        {{ $role->name }}
                                    </strong>
                                </span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i>
                        <span>
                            Ainda não existem perfis de acesso configurados para esta organização.
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ==========  ESTADO ============== --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Estado da conta
                        </h3>
                        <div class="card-subtitle-modern">
                            Controle o acesso do utilizador ao sistema.
                        </div>
                    </div>
                </div>

                <label class="custom-checkbox">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label">
                        <strong>
                            Utilizador ativo
                        </strong>
                        <br>
                        <small class="text-muted">
                            O utilizador poderá iniciar sessão e utilizar o sistema.
                        </small>
                    </span>
                </label>
            </div>
        </div>

        {{-- ====== RIGHT SIDEBAR ========= --}}
        <div class="col-lg-4">
            {{-- =========== AVATAR =========== --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Fotografia
                        </h3>
                        <div class="card-subtitle-modern">
                            Imagem do perfil do utilizador.
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <div id="avatar-preview" class="avatar avatar-lg mx-auto mb-3" style="width:100px;height:100px;font-size:28px;">
                        @if(isset($user) && !empty($user->avatar))
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                        @else
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                    <label for="avatar" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-camera"></i>
                        Alterar fotografia
                    </label>
                    <input type="file" id="avatar" name="avatar" class="d-none" accept="image/png,image/jpeg,image/webp">
                    <div class="mt-2" style="color:#94a3b8;font-size:10px;">
                        JPG, PNG ou WEBP · Máx. 2 MB
                    </div>
                </div>
            </div>

            {{-- ======= ORGANIZAÇÃO ============= --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Organização
                        </h3>
                        <div class="card-subtitle-modern">
                            Tenant ao qual o utilizador pertence.
                        </div>
                    </div>
                </div>
                <div class="p-3" style="background:#eff6ff;border-radius:12px;">
                    <div class="d-flex align-items-center">
                        <div class="tenant-avatar mr-3">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:700;color:#2563eb;">
                                {{ $tenant->name ?? 'Organização atual' }}
                            </div>
                            <div style="font-size:10px;color:#64748b;">
                                Tenant atual
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex align-items-center" style="font-size:11px;color:#64748b;">
                        <i class="fas fa-shield-alt mr-2" style="color:#10b981;"></i>
                        Os dados deste utilizador estão associados ao tenant atual.
                    </div>
                </div>
            </div>

            {{-- ========= INFORMAÇÃO ========== --}}
            <div class="dashboard-card chart-card">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Informação
                        </h3>
                        <div class="card-subtitle-modern">
                            Informações da conta.
                        </div>
                    </div>
                </div>
                @if(isset($user))
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            Criado em
                        </div>
                        <div class="activity-time">
                            {{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            Última atualização
                        </div>
                        <div class="activity-time">
                            {{ $user->updated_at?->format('d/m/Y H:i') ?? '-' }}
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-3">
                    <div style="width:42px;height:42px;margin:0 auto 10px;border-radius:11px;display:flex;align-items:center;justify-content:center;background:#eff6ff;color:#2563eb;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div style="color:#475569;font-size:12px;font-weight:600;">
                        Novo utilizador
                    </div>
                    <div class="mt-1" style="color:#94a3b8;font-size:10px;">
                        Preencha os dados para criar a conta.
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- =========== AVATAR PREVIEW =============== --}}

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatar-preview');
        if (!avatarInput || !avatarPreview) {
            return;
        }
        avatarInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }
            if (!file.type.startsWith('image/')) {
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.innerHTML = `
                <img src="${e.target.result}" alt="Pré-visualização" style="width:100%;height:100%;object-fit:cover;">
            `;
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush
@endsection
