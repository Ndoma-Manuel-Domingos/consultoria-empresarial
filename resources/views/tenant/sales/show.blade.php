@extends('layouts.app')

@section('title', 'Venda ' . $sale->number)

@section('page_header')

<div class="d-flex align-items-center justify-content-between">

    <div>

        <h1 class="page-title">

            Venda {{ $sale->number }}

        </h1>

        <div class="page-subtitle">

            Detalhes da venda e rastreabilidade do stock.

        </div>

    </div>


    <div class="d-flex">

        <a href="{{ route('tenant.sales.index') }}" class="btn btn-secondary btn-sm mr-2">

            <i class="fas fa-arrow-left"></i>

            Voltar

        </a>


        @if($sale->status === 'completed')

        <form method="POST" action="{{ route(
                    'tenant.sales.cancel',
                    $sale
                ) }}" onsubmit="
                    return confirm(
                        'Tem certeza que deseja anular esta venda? O stock será reposto.'
                    );
                ">

            @csrf

            <button type="submit" class="btn btn-outline-danger btn-sm">

                <i class="fas fa-ban"></i>

                Anular venda

            </button>

        </form>

        @endif

    </div>

</div>

@endsection


@section('content')


@if(session('success'))

<div class="alert alert-success mb-4">

    <i class="fas fa-check-circle"></i>

    {{ session('success') }}

</div>

@endif


<div class="row">

    <div class="col-lg-8">

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Produtos vendidos
                    </h3>

                    <div class="card-subtitle-modern">
                        Itens e lotes utilizados nesta venda.
                    </div>

                </div>

            </div>


            <div class="table-responsive">

                <table class="modern-table">

                    <thead>

                        <tr>

                            <th>
                                Produto
                            </th>

                            <th>
                                Quantidade
                            </th>

                            <th>
                                Preço
                            </th>

                            <th>
                                IVA
                            </th>

                            <th>
                                Total
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($sale->items as $item)

                        <tr>

                            <td>

                                <strong>
                                    {{ $item->product->name }}
                                </strong>

                                <div style="
                                        margin-top:3px;
                                        color:#94a3b8;
                                        font-size:10px;
                                    ">

                                    {{ $item->product->code }}

                                </div>


                                @if($item->lots->count())

                                <div style="
                                            margin-top:8px;
                                            padding:8px;
                                            background:#f8fafc;
                                            border-radius:8px;
                                        ">

                                    <div style="
                                                font-size:9px;
                                                color:#64748b;
                                                font-weight:700;
                                                margin-bottom:5px;
                                                text-transform:uppercase;
                                            ">
                                        FIFO / Lotes
                                    </div>

                                    @foreach($item->lots as $itemLot)

                                    <div class="d-flex justify-content-between" style="
                                                    font-size:10px;
                                                    margin-bottom:3px;
                                                ">

                                        <span>

                                            Lote:
                                            <strong>
                                                {{ $itemLot->productLot->lot_number }}
                                            </strong>

                                        </span>

                                        <span>

                                            {{ number_format(
                                                        $itemLot->quantity,
                                                        3,
                                                        ',',
                                                        '.'
                                                    ) }}

                                        </span>

                                    </div>

                                    @endforeach

                                </div>

                                @endif

                            </td>


                            <td>

                                {{ number_format(
                                    $item->quantity,
                                    3,
                                    ',',
                                    '.'
                                ) }}

                                {{ $item->product->unit }}

                            </td>


                            <td>

                                {{ number_format(
                                    $item->unit_price,
                                    2,
                                    ',',
                                    '.'
                                ) }}

                                Kz

                            </td>


                            <td>

                                {{ number_format(
                                    $item->tax_rate,
                                    2,
                                    ',',
                                    '.'
                                ) }}%

                            </td>


                            <td>

                                <strong>

                                    {{ number_format(
                                        $item->total,
                                        2,
                                        ',',
                                        '.'
                                    ) }}

                                    Kz

                                </strong>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        {{-- PAGAMENTOS --}}

        <div class="dashboard-card chart-card">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Pagamentos
                    </h3>

                </div>

            </div>


            @foreach($sale->payments as $payment)

            <div class="activity-item">

                <div class="activity-icon">

                    @if($payment->method === 'cash')

                    <i class="fas fa-money-bill-wave"></i>

                    @elseif($payment->method === 'multicaixa')

                    <i class="fas fa-credit-card"></i>

                    @else

                    <i class="fas fa-wallet"></i>

                    @endif

                </div>


                <div style="flex:1;">

                    <div class="activity-title">

                        @switch($payment->method)

                        @case('cash')
                        Numerário
                        @break

                        @case('multicaixa')
                        Multicaixa
                        @break

                        @case('transfer')
                        Transferência
                        @break

                        @case('tpa')
                        TPA
                        @break

                        @case('credit')
                        Crédito
                        @break

                        @default
                        {{ ucfirst($payment->method) }}

                        @endswitch

                    </div>


                    @if($payment->reference)

                    <div class="activity-time">

                        Referência:
                        {{ $payment->reference }}

                    </div>

                    @endif

                </div>


                <strong>

                    {{ number_format(
                            $payment->amount,
                            2,
                            ',',
                            '.'
                        ) }}

                    Kz

                </strong>

            </div>

            @endforeach

        </div>

    </div>


    {{-- RIGHT --}}

    <div class="col-lg-4">


        {{-- STATUS --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <h3 class="card-title-modern">
                    Estado
                </h3>

            </div>


            @if($sale->status === 'completed')

            <span class="status status-success">
                <i class="fas fa-check-circle"></i>
                Venda concluída
            </span>

            @else

            <span class="status status-danger">
                <i class="fas fa-ban"></i>
                Venda anulada
            </span>

            @endif

        </div>


        {{-- RESUMO --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <h3 class="card-title-modern">
                    Resumo financeiro
                </h3>

            </div>


            <div class="d-flex justify-content-between mb-2">

                <span>
                    Subtotal
                </span>

                <strong>

                    {{ number_format(
                        $sale->subtotal,
                        2,
                        ',',
                        '.'
                    ) }}
                    Kz

                </strong>

            </div>


            <div class="d-flex justify-content-between mb-2">

                <span>
                    Desconto
                </span>

                <strong>

                    {{ number_format(
                        $sale->discount,
                        2,
                        ',',
                        '.'
                    ) }}
                    Kz

                </strong>

            </div>


            <div class="d-flex justify-content-between mb-3">

                <span>
                    IVA
                </span>

                <strong>

                    {{ number_format(
                        $sale->tax_amount,
                        2,
                        ',',
                        '.'
                    ) }}
                    Kz

                </strong>

            </div>


            <div class="modern-divider"></div>


            <div class="d-flex justify-content-between">

                <strong>
                    Total
                </strong>

                <strong style="
                        color:#2563eb;
                        font-size:18px;
                    ">

                    {{ number_format(
                        $sale->total,
                        2,
                        ',',
                        '.'
                    ) }}

                    Kz

                </strong>

            </div>

        </div>


        {{-- PAGAMENTO --}}

        <div class="dashboard-card chart-card">

            <div class="card-header-modern">

                <h3 class="card-title-modern">
                    Pagamento
                </h3>

            </div>


            <div class="d-flex justify-content-between mb-2">

                <span>
                    Pago
                </span>

                <strong>

                    {{ number_format(
                        $sale->paid_amount,
                        2,
                        ',',
                        '.'
                    ) }}

                    Kz

                </strong>

            </div>


            <div class="d-flex justify-content-between">

                <span>
                    Troco
                </span>

                <strong style="color:#059669;">

                    {{ number_format(
                        $sale->change_amount,
                        2,
                        ',',
                        '.'
                    ) }}

                    Kz

                </strong>

            </div>

        </div>


    </div>

</div>

@endsection
