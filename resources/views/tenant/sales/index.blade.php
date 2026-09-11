@extends('layouts.app')

@section('title', 'Vendas')

@section('page_header')

<div class="d-flex align-items-center justify-content-between">

    <div>

        <h1 class="page-title">
            Vendas
        </h1>

        <div class="page-subtitle">
            Consulte e acompanhe as vendas realizadas.
        </div>

    </div>

    <a href="{{ route('tenant.sales.create') }}" class="btn btn-primary btn-sm">

        <i class="fas fa-cash-register"></i>

        Nova venda

    </a>

</div>

@endsection


@section('content')


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


<div class="dashboard-card mb-4">

    <div class="p-3">

        <form method="GET">

            <div class="row align-items-end">

                <div class="col-md-5">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Pesquisar
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-search input-icon"></i>

                            <input type="text" name="search" value="{{ request('search') }}" class="auth-input" placeholder="Número da venda ou cliente...">

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="form-group mb-0">

                        <label class="form-label">
                            Estado
                        </label>

                        <select name="status" class="form-select">

                            <option value="">
                                Todos
                            </option>

                            <option value="completed" @selected(request('status')==='completed' )>
                                Concluídas
                            </option>

                            <option value="cancelled" @selected(request('status')==='cancelled' )>
                                Anuladas
                            </option>

                        </select>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="d-flex">

                        <button class="btn btn-primary btn-sm flex-fill">

                            <i class="fas fa-filter"></i>

                            Filtrar

                        </button>

                        <a href="{{ route('tenant.sales.index') }}" class="btn btn-secondary btn-sm ml-2">

                            <i class="fas fa-times"></i>

                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>


<div class="dashboard-card">

    <div class="chart-card">

        <div class="card-header-modern">

            <div>

                <h3 class="card-title-modern">
                    Histórico de vendas
                </h3>

                <div class="card-subtitle-modern">
                    Todas as vendas da organização.
                </div>

            </div>

            <span class="status status-neutral">

                {{ $sales->total() }}

                {{ $sales->total() === 1 ? 'venda' : 'vendas' }}

            </span>

        </div>


        <div class="table-responsive">

            <table class="modern-table">

                <thead>

                    <tr>

                        <th>
                            Documento
                        </th>

                        <th>
                            Data
                        </th>

                        <th>
                            Cliente
                        </th>

                        <th>
                            Pagamento
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Estado
                        </th>

                        <th></th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($sales as $sale)

                    <tr>

                        <td>

                            <strong>
                                {{ $sale->number }}
                            </strong>

                        </td>

                        <td>
                            {{ $sale->sale_date?->format('d/m/Y H:i') }}
                        </td>

                        <td>

                            {{ $sale->client?->name
                                ?? 'Consumidor final' }}

                        </td>

                        <td>

                            @if($sale->payment_method === 'mixed')

                            <span class="status status-info">
                                Misto
                            </span>

                            @elseif($sale->payment_method === 'cash')

                            <span class="status status-success">
                                Numerário
                            </span>

                            @elseif($sale->payment_method === 'multicaixa')

                            <span class="status status-info">
                                Multicaixa
                            </span>

                            @else

                            {{ ucfirst($sale->payment_method ?? '-') }}

                            @endif

                        </td>

                        <td>

                            <strong>
                                {{ number_format(
                                    $sale->total,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                                Kz
                            </strong>

                        </td>

                        <td>

                            @if($sale->status === 'completed')

                            <span class="status status-success">
                                Concluída
                            </span>

                            @else

                            <span class="status status-danger">
                                Anulada
                            </span>

                            @endif

                        </td>

                        <td>

                            <a href="{{ route(
                                    'tenant.sales.show',
                                    $sale
                                ) }}" class="btn btn-outline-primary btn-sm">

                                <i class="fas fa-eye"></i>

                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7" class="text-center" style="
                                padding:40px;
                                color:#94a3b8;
                            ">

                            Nenhuma venda encontrada.

                        </td>

                    </tr>

                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="mt-3">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
