@extends('layouts.app')

@section('title', 'Configuração da organização')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Configuração da organização
        </h1>
        <div class="page-subtitle">
            Gerencie as informações e preferências da sua organização.
        </div>
    </div>
    <div>
        <button type="submit" form="tenant-settings-form" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-save mr-1"></i>
            Guardar alterações
        </button>
    </div>
</div>
@endsection

@section('content')
<form id="tenant-settings-form" method="POST" action="{{ route('tenant.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    {{-- ==
         ALERTS
    ======= --}}

    @if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle mr-1"></i>
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle mr-1"></i>
        {{ $errors->first() }}
    </div>
    @endif

    {{-- ======
         ORGANIZATION HEADER
    ======= --}}

    <div class="dashboard-card mb-4">
        <div style="height:130px;background: linear-gradient(135deg,#2563eb,#1d4ed8,#0f172a);border-radius:14px 14px 0 0;"></div>
        <div class="px-4 pb-4">
            <div class="d-flex align-items-end" style="margin-top:-38px;">
                <div style="width:80px;height:80px;border-radius:18px;background:white;border:5px solid white;box-shadow:0 5px 20px rgba(15,23,42,.15);display:flex;align-items:center;justify-content:center;overflow:hidden;">
                    @if (!empty($tenant->logo))
                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                    <span style="color:#2563eb;font-size:25px;font-weight:700;">
                        {{ strtoupper(substr($tenant->name ?? 'T', 0, 1)) }}
                    </span>
                    @endif
                </div>

                <div class="ml-3 mb-1">
                    <h4 class="mb-1" style="font-size:18px;font-weight:700;">
                        {{ $tenant->name }}
                    </h4>
                    <div style="color:#64748b;font-size:12px;">
                        <i class="fas fa-building mr-1"></i>
                        Organização
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- ==
             MAIN SETTINGS
        === --}}
        <div class="col-lg-8">
            {{-- General information --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Informações gerais
                        </h3>
                        <div class="card-subtitle-modern">
                            Informações principais da organização.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Nome da organização
                            </label>
                            <input type="text" name="name" class="auth-input" value="{{ old('name', $tenant->name) }}" placeholder="Nome da organização" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Identificador
                            </label>
                            <div class="input-wrapper">
                                <span style="position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#94a3b8;z-index:2;">
                                    #
                                </span>
                                <input type="text" class="auth-input" value="{{ $tenant->slug }}" disabled style="padding-left:35px;">
                            </div>
                            <small class="text-muted">
                                O identificador não pode ser alterado.
                            </small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">
                                Descrição
                            </label>
                            <textarea name="description" class="form-control" rows="4" style="border:1px solid #e2e8f0;border-radius:10px;font-size:13px;padding:12px 15px;resize:none;" placeholder="Descreva brevemente a organização...">{{ old('description', $tenant->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Contact --}}

            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Contactos
                        </h3>
                        <div class="card-subtitle-modern">
                            Informações de contacto da organização.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Email
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" class="auth-input" value="{{ old('email', $tenant->email) }}" placeholder="empresa@email.com">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Telefone
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="text" name="phone" class="auth-input" value="{{ old('phone', $tenant->phone) }}" placeholder="+244 900 000 000">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">
                                Endereço
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <input type="text" name="address" class="auth-input" value="{{ old('address', $tenant->address) }}" placeholder="Endereço da organização">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                Cidade
                            </label>
                            <input type="text" name="city" class="auth-input" value="{{ old('city', $tenant->city) }}" placeholder="Luanda">
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                País
                            </label>
                            <input type="text" name="country" class="auth-input" value="{{ old('country', $tenant->country) }}" placeholder="Angola">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                Código postal
                            </label>
                            <input type="text" name="postal_code" class="auth-input" value="{{ old('postal_code', $tenant->postal_code) }}" placeholder="0000-000">
                        </div>
                    </div>
                </div>
            </div>


            {{-- Danger zone --}}

            <div class="dashboard-card mb-4" style="border-color:#fecaca;">
                <div class="p-4">
                    <div class="d-flex">
                        <div class="stat-icon red mr-3">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h3 class="mb-1" style="font-size:15px;font-weight:700;color:#991b1b;">
                                Zona de perigo
                            </h3>
                            <p class="mb-3" style="color:#64748b;font-size:12px;">
                                Ações nesta área podem afetar
                                permanentemente a organização.
                            </p>
                            <button type="button" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash mr-1"></i>
                                Eliminar organização
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ==
             SIDEBAR SETTINGS
        === --}}

        <div class="col-lg-4">
            {{-- Logo --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Identidade visual
                        </h3>
                        <div class="card-subtitle-modern">
                            Personalize a aparência da organização.
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="mx-auto mb-3" style="width:100px;height:100px;border-radius:18px;border:2px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#f8fafc;">
                        @if (!empty($tenant->logo))
                        <img src="{{ asset('storage/' . $tenant->logo) }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                        <i class="fas fa-building" style="color:#94a3b8;font-size:28px;"></i>
                        @endif
                    </div>
                    <label for="logo" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-upload mr-1"></i>
                        Alterar logotipo
                    </label>
                    <input type="file" id="logo" name="logo" class="d-none" accept="image/png,image/jpeg,image/webp">
                    <div class="mt-2" style="color:#94a3b8;font-size:10px;">
                        PNG, JPG ou WEBP · Máx. 2 MB
                    </div>
                </div>
            </div>

            {{-- Plan --}}

            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Plano atual
                        </h3>
                        <div class="card-subtitle-modern">
                            Subscrição da organização.
                        </div>
                    </div>
                </div>

                <div class="p-3" style="background:#eff6ff;border-radius:12px;">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div style="color:#2563eb;font-size:12px;font-weight:700;">
                                {{ $tenant->plan->name ?? 'Professional' }}
                            </div>
                            <div class="mt-1" style="color:#64748b;font-size:11px;">
                                Plano atual
                            </div>
                        </div>
                        <i class="fas fa-crown" style="color:#2563eb;font-size:20px;"></i>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-sm btn-block mt-3">
                    Gerir subscrição
                </button>
            </div>


            {{-- Security --}}
            <div class="dashboard-card chart-card">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Segurança
                        </h3>
                        <div class="card-subtitle-modern">
                            Estado de segurança da organização.
                        </div>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            Organização protegida
                        </div>
                        <div class="activity-time">
                            Dados isolados por tenant
                        </div>
                    </div>
                    <span class="ml-auto status status-success">
                        Ativo
                    </span>
                </div>

                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            HTTPS
                        </div>
                        <div class="activity-time">
                            Conexão segura
                        </div>
                    </div>
                    <span class="ml-auto status status-success">
                        Ativo
                    </span>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
