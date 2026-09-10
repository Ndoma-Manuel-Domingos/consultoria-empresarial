@extends('layouts.app')

@section('title', 'Perfil - ' . $role->name)

@section('page_header')

<div class="d-flex align-items-center justify-content-between">

    <div>

        <h1 class="page-title">
            {{ $role->name }}
        </h1>

        <div class="page-subtitle">
            Detalhes e permissões associadas ao perfil.
        </div>

    </div>

    <div class="d-flex" style="gap:8px;">

        <a href="{{ route('tenant.roles.index') }}" class="btn btn-secondary btn-sm">

            <i class="fas fa-arrow-left mr-1"></i>

            Voltar

        </a>

        <a href="{{ route('tenant.roles.edit', $role) }}" class="btn btn-primary btn-sm">

            <i class="fas fa-edit mr-1"></i>

            Editar perfil

        </a>

    </div>

</div>

@endsection


@section('content')

<div class="row">

    {{-- PERFIL --}}

    <div class="col-lg-4">

        <div class="dashboard-card chart-card mb-4">
            <div class="text-center">

                <div style="width:80px;height:80px;margin:0 auto 15px;border-radius:20px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;display:flex;align-items:center;justify-content:center;font-size:28px;box-shadow:0 8px 20px rgba(37,99,235,.20);">
                    <i class="fas fa-user-shield"></i>
                </div>


                <h3 style="
                        margin:0;
                        color:#1e293b;
                        font-size:17px;
                        font-weight:700;
                    ">

                    {{ $role->name }}

                </h3>


                <div style="
                        margin-top:5px;
                        color:#94a3b8;
                        font-size:10px;
                    ">

                    Perfil de acesso

                </div>


                <div class="d-flex justify-content-center" style="
                        gap:7px;
                        margin-top:15px;
                    ">

                    <span class="status status-info">

                        {{ $role->permissions->count() }}

                        permissões

                    </span>

                    <span class="status status-neutral">

                        web

                    </span>

                </div>

            </div>


            <div class="modern-divider"></div>


            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-calendar"></i>

                </div>

                <div>

                    <div class="activity-title">
                        Criado em
                    </div>

                    <div class="activity-time">

                        {{ $role->created_at->format('d/m/Y H:i') }}

                    </div>

                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-sync"></i>

                </div>

                <div>

                    <div class="activity-title">
                        Atualizado em
                    </div>

                    <div class="activity-time">

                        {{ $role->updated_at->format('d/m/Y H:i') }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- PERMISSÕES --}}

    <div class="col-lg-8">

        <div class="dashboard-card chart-card">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">

                        Permissões

                    </h3>

                    <div class="card-subtitle-modern">

                        Funcionalidades disponíveis para este perfil.

                    </div>

                </div>

            </div>


            @forelse($role->permissions as $permission)

            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-key"></i>

                </div>

                <div>

                    <div class="activity-title">
                        {{ $permission->name }}
                    </div>

                    <div class="activity-time">

                        Guard: {{ $permission->guard_name }}

                    </div>

                </div>

                <span class="ml-auto status status-success">

                    Ativo

                </span>

            </div>

            @empty

            <div class="text-center" style="padding:40px;">

                <i class="fas fa-lock" style="
                            color:#cbd5e1;
                            font-size:28px;
                        "></i>

                <div style="margin-top:10px;color:#64748b;font-size:12px;">
                    Este perfil ainda não possui permissões.
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
