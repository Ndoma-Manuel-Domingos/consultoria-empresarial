@extends('layouts.app')

@section('title', 'Produtos')

@section('page_header')

<div class="d-flex align-items-center justify-content-between">

    <div>
        <h1 class="page-title">
            Produtos
        </h1>

        <div class="page-subtitle">
            Gerencie produtos, serviços, preços e informações fiscais da organização.
        </div>
    </div>

    <div>
        <a href="{{ route('tenant.products.create') }}" class="btn btn-primary btn-sm">

            <i class="fas fa-plus"></i>
            Novo produto

        </a>
    </div>

</div>

@endsection


@section('content')

{{-- ALERTS --}}

@if(session('success'))

<div class="alert alert-success mb-3">

    <i class="fas fa-check-circle"></i>

    <span>
        {{ session('success') }}
    </span>

</div>

@endif


@if($errors->any())

<div class="alert alert-danger mb-3">

    <i class="fas fa-exclamation-circle"></i>

    <span>
        {{ $errors->first() }}
    </span>

</div>

@endif

{{-- FILTROS --}}
<div class="dashboard-card mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('tenant.products.index') }}">

            <div class="row align-items-end">

                {{-- SEARCH --}}

                <div class="col-lg-4 col-md-6">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Pesquisar
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-search input-icon"></i>

                            <input type="text" name="search" class="auth-input" value="{{ request('search') }}" placeholder="Nome, código ou código de barras...">

                        </div>

                    </div>

                </div>


                {{-- TYPE --}}

                <div class="col-lg-2 col-md-3">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Tipo
                        </label>

                        <select name="type" class="form-select">

                            <option value="">
                                Todos
                            </option>

                            <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>
                                Produtos
                            </option>

                            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>
                                Serviços
                            </option>

                        </select>

                    </div>

                </div>


                {{-- CATEGORY --}}

                <div class="col-lg-2 col-md-3">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Categoria
                        </label>

                        <select name="category" class="form-select">

                            <option value="">
                                Todas
                            </option>

                            @foreach($categories as $category)

                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>

                                {{ $category }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                </div>
                {{-- STATUS --}}
                <div class="col-lg-2 col-md-3">
                    <div class="form-group mb-0">
                        <label class="form-label">
                            Estado
                        </label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                </div>
                {{-- ACTIONS --}}
                <div class="col-lg-2 col-md-12 mt-3 mt-lg-0">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-filter"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('tenant.products.index') }}" class="btn btn-secondary btn-sm ml-2" title="Limpar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- PRODUTOS --}}

<div class="dashboard-card">
    <div class="chart-card">
        <div class="card-header-modern">
            <div>
                <h3 class="card-title-modern">
                    Lista de produtos
                </h3>
                <div class="card-subtitle-modern">
                    Produtos e serviços cadastrados na organização atual.
                </div>
            </div>
            <div>
                <span class="status status-neutral">
                    {{ $products->total() }}
                    {{ $products->total() == 1 ? 'item' : 'itens' }}
                </span>
            </div>
        </div>
        <div class="table-responsive">
            @include('tenant.products.partials.table')
        </div>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div class="stat-icon red mr-3">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">
                            Desativar produto
                        </h5>
                        <div style="margin-top:3px;color:#64748b;font-size:11px;">
                            Esta ação requer confirmação.
                        </div>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div style="padding:14px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;">
                    <div style="color:#991b1b;font-size:12px;font-weight:600;">
                        Tem certeza que deseja desativar este produto?
                    </div>
                    <div id="delete-product-name" style="margin-top:5px;color:#64748b;font-size:11px;">
                    </div>

                </div>

                <div class="mt-3" style="color:#94a3b8;font-size:10px;">
                    O produto não será eliminado fisicamente.
                    Poderá ser reativado posteriormente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Cancelar
                </button>
                <form id="delete-product-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-ban mr-1"></i>
                        Sim, desativar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')

<script>
    $(document).on('click', '[data-delete-product]', function() {

        const url = $(this).data('delete-product');
        const name = $(this).data('product-name');

        $('#delete-product-form').attr('action', url);
        $('#delete-product-name').text(name);

        $('#deleteProductModal').modal('show');

    });

</script>

@endpush
