@extends('layouts.app')

@section('title', 'Perfis de acesso')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Perfis de acesso
        </h1>
        <div class="page-subtitle">
            Crie e gerencie os perfis de acesso da sua organização.
        </div>
    </div>
    <div>
        <a href="{{ route('tenant.roles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>
            Novo perfil
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="ajax-page">
    {{-- ALERTA --}}
    @if(session('success'))
    <div class="alert alert-success mb-4">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
    @endif

    {{-- CARD --}}

    <div class="dashboard-card">
        <div class="chart-card">
            {{-- HEADER --}}
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Lista de perfis
                    </h3>
                    <div class="card-subtitle-modern">
                        Perfis utilizados para controlar o acesso às funcionalidades.
                    </div>
                </div>
                <div>
                    <span class="status status-info">
                        {{ $roles->total() }}
                        {{ $roles->total() == 1 ? 'perfil' : 'perfis' }}
                    </span>
                </div>
            </div>
            {{-- PESQUISA --}}
            <form method="GET" action="{{ route('tenant.roles.index') }}" class="mb-4 ajax-search-form" data-progress="true">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input type="text" name="search" class="auth-input" value="{{ request('search') }}" placeholder="Pesquisar perfil...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary btn-block">
                            <i class="fas fa-search mr-1"></i>
                            Pesquisar
                        </button>
                    </div>
                </div>
            </form>

            {{-- TABELA --}}
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>PERFIL</th>
                            <th>PERMISSÕES</th>
                            <th>UTILIZADORES</th>
                            <th>CRIADO EM</th>
                            <th class="text-right">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon blue mr-3">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div>
                                        <div style="color:#1e293b;font-size:12px;font-weight:700;">
                                            {{ $role->name }}
                                        </div>
                                        <div style="color:#94a3b8;font-size:10px;margin-top:3px;">
                                            ID #{{ $role->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status status-info">
                                    {{ $role->permissions_count }}
                                    {{ $role->permissions_count == 1
                                        ? 'permissão'
                                        : 'permissões'
                                    }}
                                </span>
                            </td>
                            <td>
                                <span class="status status-neutral">
                                    {{ $role->users_count }}
                                    {{ $role->users_count == 1
                                        ? 'utilizador'
                                        : 'utilizadores'
                                    }}
                                </span>
                            </td>
                            <td>
                                <span style="color:#64748b;font-size:11px;">
                                    {{ $role->created_at->format('d/m/Y H:i') }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end" style="gap:7px;">
                                    {{-- VER --}}
                                    <a href="{{ route('tenant.roles.show', $role) }}" class="btn btn-outline-secondary btn-sm" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- EDITAR --}}
                                    <a href="{{ route('tenant.roles.edit', $role) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- ELIMINAR --}}
                                    <form method="POST" action="{{ route('tenant.roles.destroy', $role) }}" class="ajax-delete-form" data-title="Eliminar perfil?" data-message="Tem a certeza que deseja eliminar o perfil {{ $role->name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding:50px 20px;">
                                <div style="width:60px;height:60px;margin:0 auto 15px;border-radius:16px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:22px;">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div style="font-size:14px;font-weight:700;color:#334155;">
                                    Nenhum perfil encontrado
                                </div>
                                <div style="margin-top:5px;font-size:11px;color:#94a3b8;">
                                    Crie o primeiro perfil da organização.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINAÇÃO --}}
            @if($roles->hasPages())
            <div class="mt-4">
                {{ $roles->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
