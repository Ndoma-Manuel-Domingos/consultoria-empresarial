@extends('layouts.app')

@section('title', 'Detalhes do utilizador')
@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Detalhes do utilizador
        </h1>
        <div class="page-subtitle">
            Consulte as informações e permissões deste utilizador.
        </div>
    </div>
    <div class="d-flex align-items-center">
        <a href="{{ route('tenant.users.index') }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        <a href="{{ route('tenant.users.edit', $user) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i>
            Editar utilizador
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row">

    {{-- 
         MAIN CONTENT= --}}

    <div class="col-lg-8">


        {{-- ==
             USER HEADER= --}}

        <div class="dashboard-card mb-4">
            {{-- Cover --}}
            <div style="height:130px;background: linear-gradient(135deg,#2563eb,#1d4ed8,#0f172a);border-radius:14px 14px 0 0;"></div>
            <div class="px-4 pb-4">
                <div class="d-flex align-items-end" style="margin-top:-42px;">
                    {{-- Avatar --}}
                    <div style="width:90px;height:90px;flex:0 0 90px;border-radius:20px;background:white;border:5px solid white;box-shadow: 0 5px 20px rgba(15,23,42,.15);display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        @if(!empty($user->avatar))
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover">
                        @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#2563eb,#0ea5e9);color:white;font-size:28px;font-weight:700;">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    {{-- User information --}}
                    <div class="ml-3 mb-1">
                        <div class="d-flex align-items-center">
                            <h3 class="mb-0 mr-2" style="font-size:19px;font-weight:700;color:#0f172a;">
                                {{ $user->name }}
                            </h3>
                            @if($user->is_active ?? true)
                            <span class="status status-success">
                                <i class="fas fa-circle" style="font-size:6px;"></i>
                                Ativo
                            </span>
                            @else
                            <span class="status status-danger">
                                <i class="fas fa-circle" style="font-size:6px;"></i>
                                Inativo
                            </span>
                            @endif
                        </div>
                        <div class="mt-1" style="color:#64748b;font-size:11px;">
                            <i class="fas fa-envelope mr-1"></i>
                            {{ $user->email }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- == PERSONAL INFORMATION= --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Informações pessoais
                    </h3>
                    <div class="card-subtitle-modern">
                        Dados principais da conta do utilizador.
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- Nome --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            Nome completo
                        </label>
                        <div class="d-flex align-items-center" style="min-height:42px;padding:10px 13px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;color:#1e293b;font-size:13px;">
                            <i class="fas fa-user mr-2" style="color:#94a3b8;"></i>
                            {{ $user->name }}
                        </div>
                    </div>
                </div>
                {{-- Email --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            Email
                        </label>
                        <div class="d-flex align-items-center" style="min-height:42px;padding:10px 13px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;color:#1e293b;font-size:13px;">
                            <i class="fas fa-envelope mr-2" style="color:#94a3b8;"></i>
                            {{ $user->email }}
                        </div>
                    </div>
                </div>

                {{-- Telefone --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            Telefone
                        </label>
                        <div class="d-flex align-items-center" style="min-height:42px;padding:10px 13px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;color:#1e293b;font-size:13px;">
                            <i class="fas fa-phone mr-2" style="color:#94a3b8;"></i>
                            {{ $user->phone ?: 'Não informado' }}
                        </div>
                    </div>
                </div>
                {{-- Cargo --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            Cargo
                        </label>
                        <div class="d-flex align-items-center" style="min-height:42px;padding:10px 13px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;color:#1e293b;font-size:13px;">
                            <i class="fas fa-briefcase mr-2" style="color:#94a3b8;"></i>
                            {{ $user->job_title ?: 'Não informado' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- == ACCESS ROLES= --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Perfis de acesso
                    </h3>
                    <div class="card-subtitle-modern">
                        Perfis e funções atribuídos ao utilizador.
                    </div>
                </div>
                <div class="stat-icon blue">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            @if(isset($roles) && $roles->count())
            <div class="row">
                @foreach($roles as $role)
                <div class="col-md-6 mb-3">
                    <div style="display:flex;align-items:center;padding:13px;border:1px solid #e2e8f0;border-radius:10px;background:#fff;transition:.18s ease;" onmouseover="this.style.borderColor='#bfdbfe';this.style.background='#fafcff';" onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#fff';">
                        <div style="width:36px;height:36px;flex:0 0 36px;display:flex;align-items:center;justify-content:center;border-radius:9px;background:#eff6ff;color:#2563eb;font-size:12px;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="ml-3">
                            <div style="color:#1e293b;font-size:12px;font-weight:700;">
                                {{ $role->name }}
                            </div>
                            <div class="mt-1" style="color:#94a3b8;font-size:10px;">
                                Perfil de acesso
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i>
                <span>
                    Este utilizador ainda não possui nenhum perfil de acesso.
                </span>
            </div>
            @endif
        </div>
        {{--  ACCOUNT INFORMATION= --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Informação da conta
                    </h3>
                    <div class="card-subtitle-modern">
                        Informações técnicas e estado da conta.
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div>
                            <div class="activity-title">
                                Conta criada
                            </div>
                            <div class="activity-time">
                                {{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-sync-alt"></i>
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
                </div>
            </div>
        </div>
    </div>

    {{-- ====== SIDEBAR= --}}
    <div class="col-lg-4">
        {{-- == ORGANIZATION= --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Organização
                    </h3>
                    <div class="card-subtitle-modern">
                        Tenant atual do utilizador.
                    </div>
                </div>
            </div>
            <div class="p-3" style="background:#eff6ff;border-radius:12px;">
                <div class="d-flex align-items-center">
                    <div class="tenant-avatar mr-3">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <div style="
                                color:#2563eb;
                                font-size:12px;
                                font-weight:700;
                            ">
                            {{ $tenant->name ?? 'Organização atual' }}
                        </div>


                        <div class="mt-1" style="
                                color:#64748b;
                                font-size:10px;
                            ">
                            Organização
                        </div>

                    </div>

                </div>

            </div>


            <div class="mt-3">

                <div class="d-flex align-items-center" style="
                        color:#64748b;
                        font-size:10px;
                    ">

                    <i class="fas fa-shield-alt mr-2" style="color:#10b981;"></i>

                    Dados isolados por tenant.

                </div>

            </div>

        </div>



        {{-- ==
             ACCOUNT STATUS= --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Estado da conta
                    </h3>

                    <div class="card-subtitle-modern">
                        Estado atual do acesso.
                    </div>

                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon" style="
                        color:{{ ($user->is_active ?? true) ? '#059669' : '#dc2626' }};
                        background:{{ ($user->is_active ?? true) ? '#ecfdf5' : '#fef2f2' }};
                    ">

                    <i class="fas fa-user-check"></i>

                </div>


                <div>

                    <div class="activity-title">
                        Acesso ao sistema
                    </div>

                    <div class="activity-time">

                        {{ ($user->is_active ?? true)
                            ? 'O utilizador pode aceder ao sistema.'
                            : 'O acesso do utilizador está bloqueado.'
                        }}

                    </div>

                </div>


                <span class="ml-auto">

                    @if($user->is_active ?? true)

                    <span class="status status-success">
                        Ativo
                    </span>

                    @else

                    <span class="status status-danger">
                        Inativo
                    </span>

                    @endif

                </span>

            </div>

        </div>



        {{-- ==
             SECURITY= --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Segurança
                    </h3>

                    <div class="card-subtitle-modern">
                        Estado de segurança da conta.
                    </div>

                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-lock"></i>

                </div>

                <div>

                    <div class="activity-title">
                        Palavra-passe
                    </div>

                    <div class="activity-time">
                        Protegida e encriptada
                    </div>

                </div>

                <span class="ml-auto status status-success">
                    Seguro
                </span>

            </div>


            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-user-shield"></i>

                </div>

                <div>

                    <div class="activity-title">
                        Permissões
                    </div>

                    <div class="activity-time">
                        Controladas por perfis
                    </div>

                </div>

                <span class="ml-auto status status-success">
                    Ativo
                </span>

            </div>


            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-database"></i>

                </div>

                <div>

                    <div class="activity-title">
                        Isolamento
                    </div>

                    <div class="activity-time">
                        Dados separados por tenant
                    </div>

                </div>

                <span class="ml-auto status status-success">
                    Ativo
                </span>

            </div>

        </div>



        {{-- ==
             DANGER ZONE= --}}

        <div class="dashboard-card mb-4" style="border-color:#fecaca;">

            <div class="p-4">

                <div class="d-flex">

                    <div class="stat-icon red mr-3">

                        <i class="fas fa-exclamation-triangle"></i>

                    </div>


                    <div>

                        <h3 class="mb-1" style="
                                font-size:14px;
                                font-weight:700;
                                color:#991b1b;
                            ">
                            Zona de perigo
                        </h3>


                        <p class="mb-3" style="
                                color:#64748b;
                                font-size:11px;
                                line-height:1.5;
                            ">
                            A eliminação deste utilizador é uma ação
                            que pode afetar permanentemente os seus dados
                            e acessos.
                        </p>


                        <button type="button" class="btn btn-outline-danger btn-sm" data-delete-user data-user-name="{{ $user->name }}" data-action="{{ route('tenant.users.destroy', $user) }}">

                            <i class="fas fa-trash"></i>

                            Eliminar utilizador

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ==========
     DELETE CONFIRMATION= --}}

<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">


            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="fas fa-exclamation-triangle mr-2" style="color:#ef4444;"></i>

                    Eliminar utilizador

                </h5>


                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>

            </div>


            <div class="modal-body">

                <div class="text-center py-2">

                    <div style="
                            width:55px;
                            height:55px;
                            margin:0 auto 15px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            border-radius:50%;
                            background:#fef2f2;
                            color:#ef4444;
                            font-size:20px;
                        ">

                        <i class="fas fa-trash"></i>

                    </div>


                    <h4 style="
                            color:#1e293b;
                            font-size:15px;
                            font-weight:700;
                        ">
                        Tem a certeza?
                    </h4>


                    <p style="
                            color:#64748b;
                            font-size:12px;
                            line-height:1.6;
                        ">

                        Está prestes a eliminar o utilizador

                        <strong id="delete-user-name" style="color:#1e293b;">
                            {{ $user->name }}
                        </strong>.

                        Esta ação não poderá ser facilmente revertida.

                    </p>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Cancelar
                </button>


                <form id="delete-user-form" method="POST" style="display:inline;">

                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger btn-sm">

                        <i class="fas fa-trash mr-1"></i>

                        Sim, eliminar

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>



@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const deleteButton = document.querySelector('[data-delete-user]');

        const deleteForm = document.getElementById('delete-user-form');

        const deleteName = document.getElementById('delete-user-name');


        if (!deleteButton || !deleteForm) {
            return;
        }


        deleteButton.addEventListener('click', function() {

            const userName = this.dataset.userName;
            const action = this.dataset.action;


            if (action) {
                deleteForm.action = action;
            }


            if (userName && deleteName) {
                deleteName.textContent = userName;
            }


            $('#deleteUserModal').modal('show');

        });

    });

</script>

@endpush

@endsection
