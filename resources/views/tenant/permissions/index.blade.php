@extends('layouts.app')

@section('title', 'Permissões')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Permissões
        </h1>
        <div class="page-subtitle">
            Gerencie as permissões disponíveis na sua organização.
        </div>
    </div>
    <div>
        <a href="{{ route('tenant.permissions.create') }}" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-plus mr-1"></i>
            Nova permissão
        </a>
    </div>
</div>
@endsection

@section('content')

@if (session('success'))
    <div class="alert alert-success mb-3">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger mb-3">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
@endif

<div class="dashboard-card chart-card">
    {{-- HEADER --}}
    <div class="card-header-modern">
        <div>
            <h3 class="card-title-modern">
                Lista de permissões
            </h3>

            <div class="card-subtitle-modern">
                Permissões utilizadas para controlar o acesso às funcionalidades.
            </div>
        </div>
        <div>
            <span class="status status-info">
                {{ $permissions->total() }} permissões
            </span>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="modern-table">

            <thead>
                <tr>
                    <th>
                        Permissão
                    </th>

                    <th>
                        Guard
                    </th>

                    <th>
                        Criada em
                    </th>

                    <th class="text-right">
                        Ações
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($permissions as $permission)
                    <tr>
                        <td>
                            <div style="font-weight:600;color:var(--text);">{{ $permission->name }}</div>
                            <div style="margin-top:3px;color:var(--text-light);font-size:10px;">ID #{{ $permission->id }}</div>
                        </td>
                        <td>
                            <span class="status status-neutral">{{ $permission->guard_name }}</span>
                        </td>
                        <td>{{ $permission->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="text-right">
                            <div class="d-inline-flex align-items-center" style="gap:5px;">
                                <a href="{{ route('tenant.permissions.edit', $permission) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('tenant.permissions.destroy', $permission) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Tem certeza que deseja eliminar esta permissão?');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="text-center py-5">
                                <div class="stat-icon blue mx-auto mb-3">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div style="font-size:13px;font-weight:600;color:var(--text);">
                                    Nenhuma permissão encontrada
                                </div>
                                <div style="margin-top:4px;color:var(--text-muted);font-size:11px;">
                                    Ainda não existem permissões cadastradas.
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if ($permissions->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $permissions->links() }}
        </div>
    @endif
</div>
@endsection
