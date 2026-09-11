@extends('layouts.app')

@section('title', 'Movimentos de stock')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">

    <div>
        <h1 class="page-title">
            Movimentos de stock
        </h1>

        <div class="page-subtitle">
            Controle as entradas, saídas e ajustes de stock dos produtos.
        </div>
    </div>

    <div>
        <a href="{{ route('tenant.stock-movements.create') }}" class="btn btn-primary btn-sm">

            <i class="fas fa-plus"></i>

            Novo movimento
        </a>
    </div>

</div>
@endsection


@section('content')

@if(session('success'))

<div class="alert alert-success mb-4">

    <i class="fas fa-check-circle"></i>

    <span>
        {{ session('success') }}
    </span>

</div>

@endif


@if($errors->any())

<div class="alert alert-danger mb-4">

    <i class="fas fa-exclamation-circle"></i>

    <span>
        {{ $errors->first() }}
    </span>

</div>

@endif


{{-- FILTROS --}}

<div class="dashboard-card mb-4">

    <div class="p-3">

        <form method="GET" action="{{ route('tenant.stock-movements.index') }}">

            <div class="row align-items-end">

                {{-- SEARCH --}}

                <div class="col-lg-4 col-md-6">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Pesquisar
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-search input-icon"></i>

                            <input type="text" name="search" class="auth-input" value="{{ request('search') }}" placeholder="Produto, código ou referência...">

                        </div>

                    </div>

                </div>


                {{-- PRODUTO --}}

                <div class="col-lg-3 col-md-6">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Produto
                        </label>

                        <select name="product_id" class="form-select">

                            <option value="">
                                Todos os produtos
                            </option>

                            @foreach($products as $product)

                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                                @if($product->code)
                                — {{ $product->code }}
                                @endif
                            </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- TIPO --}}

                <div class="col-lg-2 col-md-6">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Tipo
                        </label>

                        <select name="type" class="form-select">

                            <option value="">
                                Todos
                            </option>

                            <option value="entry" {{ request('type') === 'entry' ? 'selected' : '' }}>
                                Entradas
                            </option>

                            <option value="exit" {{ request('type') === 'exit' ? 'selected' : '' }}>
                                Saídas
                            </option>

                            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>
                                Ajustes
                            </option>

                            <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>
                                Transferências
                            </option>

                        </select>

                    </div>

                </div>


                {{-- DATA --}}

                <div class="col-lg-3 col-md-6">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Período
                        </label>

                        <div class="d-flex">

                            <input type="date" name="date_from" class="auth-input" value="{{ request('date_from') }}">

                            <span class="mx-2 align-self-center">
                                —
                            </span>

                            <input type="date" name="date_to" class="auth-input" value="{{ request('date_to') }}">

                        </div>

                    </div>

                </div>


                {{-- ACTIONS --}}

                <div class="col-12 mt-3">

                    <div class="d-flex justify-content-end">

                        <button type="submit" class="btn btn-primary btn-sm">

                            <i class="fas fa-filter"></i>

                            Filtrar

                        </button>

                        <a href="{{ route('tenant.stock-movements.index') }}" class="btn btn-secondary btn-sm ml-2">

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
                    Histórico de movimentos
                </h3>

                <div class="card-subtitle-modern">
                    Registo de todas as operações realizadas no stock.
                </div>

            </div>

            <div>

                <span class="status status-neutral">

                    {{ $movements->total() }}

                    {{ $movements->total() == 1 ? 'movimento' : 'movimentos' }}

                </span>

            </div>

        </div>


        <div class="table-responsive">

            @include(
            'tenant.stock-movements.partials.table'
            )

        </div>

    </div>

</div>

@endsection
