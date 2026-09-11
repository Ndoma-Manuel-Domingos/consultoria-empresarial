@extends('layouts.app')

@section('title', 'Clientes')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title"> Clientes </h1>
        <div class="page-subtitle"> Gerencie os clientes e as informações de contacto da sua organização. </div>
    </div>
    <div>
        <a href="{{ route('tenant.clients.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-user-plus"></i>
            Novo cliente
        </a>
    </div>
</div> 
@endsection

@section('content')

{{-- ========== ALERTS =============== --}}
@if (session('success'))
<div class="alert alert-success mb-3"> <i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span> </div> 
@endif
@if ($errors->any())
<div class="alert alert-danger mb-3"> <i class="fas fa-exclamation-circle"></i> <span>{{ $errors->first() }}</span> </div> 
@endif

{{-- ========== FILTERS =============== --}}
<div class="dashboard-card mb-4">
    <div class="p-3">
        <form id="clients-filter-form" method="GET" action="{{ route('tenant.clients.index') }}">
            <div class="row align-items-end">

                {{-- SEARCH --}}
                <div class="col-lg-5 col-md-6">
                    <div class="form-group mb-0">
                        <label class="form-label">
                            Pesquisar
                        </label>

                        <div class="input-wrapper">
                            <i class="fas fa-search input-icon"></i>

                            <input type="text" name="search" id="clients-search" class="auth-input" value="{{ request('search') }}" placeholder="Nome, NIF, telefone ou email..." autocomplete="off">
                        </div>
                    </div>
                </div>

                {{-- TYPE --}}
                <div class="col-lg-2 col-md-3">
                    <div class="form-group mb-0">
                        <label class="form-label">Tipo</label>
                        <select name="type" id="clients-type" class="form-select">
                            <option value="">Todos</option>
                            <option value="individual" {{ request('type') === 'individual' ? 'selected' : '' }}>Pessoa singular</option>
                            <option value="company" {{ request('type') === 'company' ? 'selected' : '' }}>Empresa</option>
                        </select>
                    </div>
                </div>
                {{-- STATUS --}}
                <div class="col-lg-2 col-md-3">
                    <div class="form-group mb-0">
                        <label class="form-label">Estado</label>
                        <select name="status" id="clients-status" class="form-select">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="col-lg-3 col-md-12 mt-3 mt-lg-0">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill" id="clients-filter-button">
                            <i class="fas fa-filter"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('tenant.clients.index') }}" class="btn btn-secondary btn-sm ml-2" title="Limpar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ========== CLIENTS CARD =========== --}}

<div class="dashboard-card">
    <div class="chart-card">
        {{-- HEADER --}}
        <div class="card-header-modern">
            <div>
                <h3 class="card-title-modern">
                    Lista de clientes
                </h3>
                <div class="card-subtitle-modern">
                    Clientes associados à organização atual.
                </div>
            </div>
            <div>
                <span class="status status-neutral">
                    {{ $clients->total() }}
                    {{ $clients->total() == 1 ? 'cliente' : 'clientes' }}
                </span>
            </div>
        </div>
        {{-- TABLE --}}
        <div id="clients-table-container" class="table-responsive">
            @include('tenant.clients.partials.table')
        </div>
        {{-- PAGINATION --}}
        @if ($clients->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $clients->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ========== DELETE MODAL =========== --}}

<div class="modal fade" id="deleteClientModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            {{-- HEADER --}}
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div class="stat-icon red mr-3">
                        <i class="fas fa-trash"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">
                            Eliminar cliente
                        </h5>
                        <div style="margin-top:3px;color:#64748b;font-size:11px;">
                            Esta ação requer confirmação.
                        </div>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- BODY --}}
            <div class="modal-body">
                <div style="padding:14px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;">
                    <div style="color:#991b1b;font-size:12px;font-weight:600;">
                        Tem certeza que deseja eliminar este cliente?
                    </div>
                    <div id="delete-client-name" style="margin-top:5px;color:#64748b;font-size:11px;"></div>
                </div>
                <div style="margin-top:12px;color:#94a3b8;font-size:10px;">
                    Esta ação não poderá ser desfeita.
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Cancelar
                </button>
                <form id="delete-client-form" method="POST" action="" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash mr-1"></i>
                        Sim, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).on('click', '[data-delete-client]', function() {
        const clientId = $(this).data('delete-client');
        const clientName = $(this).data('client-name');
        $('#delete-client-name').text(clientName);
        $('#delete-client-form').attr('action', "{{ url('clients') }}/" + clientId);
        $('#deleteClientModal').modal('show');
    });
</script>
@endpush
