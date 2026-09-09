@extends('layouts.app')

@section('title', 'Meu perfil')

@section('page_header')

<div>
    <h1 class="page-title">
        Meu perfil
    </h1>
    <div class="page-subtitle">
        Gerencie as suas informações pessoais e segurança da conta.
    </div>
</div>
@endsection

@section('content')
<div class="row">

    {{-- =====================================================
        PROFILE SIDEBAR
    ====================================================== --}}

    <div class="col-lg-4 mb-4">
        <div class="dashboard-card">
            <div style="height:100px;background:    linear-gradient(        135deg,        #2563eb,        #1d4ed8,        #0f172a    );border-radius:14px 14px 0 0;"></div>
            <div class="text-center px-4 pb-4">
                <div style="
                        width:86px;
                        height:86px;
                        margin:-43px auto 15px;

                        border-radius:50%;

                        border:5px solid white;

                        background:
                            linear-gradient(
                                135deg,
                                #2563eb,
                                #0ea5e9
                            );

                        display:flex;
                        align-items:center;
                        justify-content:center;

                        color:white;

                        font-size:27px;
                        font-weight:700;

                        box-shadow:
                            0 8px 25px rgba(15,23,42,.15);
                    ">

                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                </div>


                <h3 class="mb-1" style="
                        font-size:18px;
                        font-weight:700;
                    ">
                    {{ auth()->user()->name }}
                </h3>


                <div style="
                        color:#64748b;
                        font-size:12px;
                    ">
                    {{ auth()->user()->email }}
                </div>


                <div class="mt-3">
                    <span class="status status-success">
                        <i class="fas fa-circle mr-1" style="font-size:6px;"></i>
                        Conta ativa
                    </span>
                </div>


                <div class="mt-4 pt-4" style="border-top:1px solid #f1f5f9;">
                    <div class="row">
                        <div class="col-6">

                            <div style="
                                    font-size:20px;
                                    font-weight:700;
                                ">
                                {{ $stats['tenants'] ?? auth()->user()->tenants()->count() }}
                            </div>

                            <div style="
                                    color:#94a3b8;
                                    font-size:10px;
                                ">
                                Organizações
                            </div>

                        </div>


                        <div class="col-6">

                            <div style="
                                    font-size:20px;
                                    font-weight:700;
                                ">
                                {{ $stats['days'] ?? 0 }}
                            </div>

                            <div style="
                                    color:#94a3b8;
                                    font-size:10px;
                                ">
                                Dias de conta
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        {{-- Account security --}}
        <div class="dashboard-card chart-card mt-4">
            <div class="card-header-modern">
                <div>

                    <h3 class="card-title-modern">
                        Segurança da conta
                    </h3>

                    <div class="card-subtitle-modern">
                        Recomendações de segurança.
                    </div>

                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-envelope"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Email verificado
                    </div>

                    <div class="activity-time">
                        {{ auth()->user()->email }}
                    </div>

                </div>

                <span class="ml-auto status status-success">
                    Verificado
                </span>

            </div>


            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-key"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Palavra-passe
                    </div>

                    <div class="activity-time">
                        Última alteração recente
                    </div>

                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Autenticação em dois fatores
                    </div>

                    <div class="activity-time">
                        Proteja ainda mais a sua conta.
                    </div>

                </div>

                <span class="ml-auto status status-warning">
                    Desativado
                </span>

            </div>


            <button type="button" class="btn btn-outline-primary btn-sm btn-block mt-3">
                Configurar 2FA
            </button>

        </div>
    </div>


    {{-- =====================================================
         PROFILE FORM
    ====================================================== --}}

    <div class="col-lg-8">
        {{-- Personal information --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Informações pessoais
                    </h3>
                    <div class="card-subtitle-modern">
                        Atualize os seus dados pessoais.
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Nome completo
                            </label>
                            <div class="input-wrapper">

                                <i class="fas fa-user input-icon"></i>

                                <input type="text" name="name" class="auth-input" value="{{ old('name', auth()->user()->name) }}" required autocomplete="name">

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
                                <input type="text" name="phone" class="auth-input" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+244 900 000 000" autocomplete="tel">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">
                                Email
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" class="auth-input" value="{{ old('email', auth()->user()->email) }}" required autocomplete="email">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-save mr-1"></i>
                        Guardar alterações
                    </button>
                </div>
            </form>
        </div>

        {{-- Password --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Palavra-passe
                    </h3>
                    <div class="card-subtitle-modern">
                        Atualize regularmente a sua palavra-passe
                        para manter a conta segura.
                    </div>
                </div>
                <div class="stat-icon blue">
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">
                        Palavra-passe atual
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="current_password" class="auth-input" placeholder="Palavra-passe atual" required autocomplete="current-password">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Nova palavra-passe
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password" name="password" class="auth-input" placeholder="Nova palavra-passe" required autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Confirmar palavra-passe
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password" name="password_confirmation" class="auth-input" placeholder="Confirmar palavra-passe" required autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3 mb-3" style="
                        background:#f8fafc;
                        border-radius:10px;
                        color:#64748b;
                        font-size:11px;
                    ">

                    <i class="fas fa-info-circle mr-1"></i>

                    Recomendamos utilizar pelo menos 8 caracteres,
                    incluindo letras maiúsculas, minúsculas,
                    números e símbolos.

                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-key mr-1"></i>
                        Alterar palavra-passe
                    </button>
                </div>
            </form>
        </div>

        {{-- Sessions --}}
        <div class="dashboard-card chart-card">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Sessões e dispositivos
                    </h3>
                    <div class="card-subtitle-modern">
                        Gerencie os dispositivos que têm acesso à sua conta.
                    </div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon" style="
                        background:#ecfdf5;
                        color:#10b981;
                    ">
                    <i class="fas fa-laptop"></i>
                </div>

                <div>
                    <div class="activity-title">
                        Este dispositivo
                    </div>
                    <div class="activity-time">
                        Sessão atual · Agora
                    </div>
                </div>

                <span class="ml-auto status status-success">
                    Atual
                </span>
            </div>

            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div>
                    <div class="activity-title">
                        Dispositivo móvel
                    </div>
                    <div class="activity-time">
                        Última atividade há 2 dias
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger ml-auto">
                    Revogar
                </button>
            </div>

            <div class="mt-3">
                <button type="button" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt mr-1"></i>
                    Terminar todas as outras sessões
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
