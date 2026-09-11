@extends('layouts.app')

@section('title', 'Lotes')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Lotes
        </h1>

        <div class="page-subtitle">
            Controle os lotes, validades e quantidades dos produtos.
        </div>
    </div>

    <div>
        <a href="{{ route('tenant.product-lots.create') }}"
           class="btn btn-primary btn-sm">

            <i class="fas fa-plus"></i>
            Novo lote
        </a>
    </div>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success mb-3">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger mb-3">
    <i class="fas fa-exclamation-circle"></i>
    <span>{{ $errors->first() }}</span>
</div>
@endif

{{-- FILTROS --}}
<div class="dashboard-card mb-4">
    <div class="p-3">

        <form method="GET"
              action="{{ route('tenant.product-lots.index') }}">

            <div class="row align-items-end">

                <div class="col-lg-5 col-md-6">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Pesquisar
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-search input-icon"></i>

                            <input
                                type="text"
                                name="search"
                                class="auth-input"
                                value="{{ request('search') }}"
                                placeholder="Lote, produto ou código..."
                            >

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-3">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Produto
                        </label>

                        <select name="product_id" class="form-select">

                            <option value="">
                                Todos os produtos
                            </option>

                            @foreach($products as $product)

                                <option
                                    value="{{ $product->id }}"
                                    {{ request('product_id') == $product->id ? 'selected' : '' }}
                                >
                                    {{ $product->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="col-lg-2 col-md-3">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Estado
                        </label>

                        <select name="status" class="form-select">

                            <option value="">
                                Todos
                            </option>

                            <option value="active"
                                {{ request('status') === 'active' ? 'selected' : '' }}>
                                Ativos
                            </option>

                            <option value="inactive"
                                {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                Inativos
                            </option>

                        </select>

                    </div>

                </div>

                <div class="col-lg-2">

                    <div class="d-flex">

                        <button
                            type="submit"
                            class="btn btn-primary btn-sm flex-fill"
                        >
                            <i class="fas fa-filter"></i>
                            Filtrar
                        </button>

                        <a
                            href="{{ route('tenant.product-lots.index') }}"
                            class="btn btn-secondary btn-sm ml-2"
                            title="Limpar filtros"
                        >
                            <i class="fas fa-times"></i>
                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>
</div>

{{-- LISTA --}}
<div class="dashboard-card">

    <div class="chart-card">

        <div class="card-header-modern">

            <div>
                <h3 class="card-title-modern">
                    Lista de lotes
                </h3>

                <div class="card-subtitle-modern">
                    Lotes registados na organização atual.
                </div>
            </div>

            <span class="status status-neutral">
                {{ $lots->total() }}
                {{ $lots->total() == 1 ? 'lote' : 'lotes' }}
            </span>

        </div>

        <div class="table-responsive">

            @include('tenant.product-lots.partials.table')

        </div>

    </div>

</div>

@endsection
