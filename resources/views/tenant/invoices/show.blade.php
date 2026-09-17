@extends('layouts.app')
@section('title', $sale->number)
@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            {{ $sale->number }}
        </h1>
        <div class="page-subtitle">
            Detalhes do documento.
        </div>
    </div>
    <div class="d-flex" style="gap:8px;">
        <a href="{{ route('tenant.invoices.invoice', $sale->id) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-print mr-1"></i>
            Imprimir factura
        </a>
        <a href="{{ route('tenant.invoices.index') }}" class="btn btn-secondary">
            Voltar
        </a>
    </div>
</div>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle mr-1"></i>
    {{ session('success') }}
</div>
@endif

<div class="row">
    {{-- ESQUERDA --}}
    <div class="col-lg-8">
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        {{ $sale->number }}
                    </h3>
                    <div class="card-subtitle-modern">
                        {{ strtoupper($sale->document_type) }}
                        ·
                        {{ $sale->sale_date->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div>
                    @if($sale->status === 'completed')
                    <span class="badge badge-success">
                        Emitida
                    </span>
                    @elseif($sale->status === 'cancelled')
                    <span class="badge badge-danger">
                        Anulada
                    </span>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qtd.</th>
                            <th>Preço</th>
                            <th>IVA</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                        <tr>
                            <td>
                                <strong>
                                    {{ $item->product?->name ?? 'Item livre' }}
                                </strong>
                            </td>
                            <td>
                                {{ number_format($item->quantity, 3,',', '.') }}
                            </td>
                            <td>
                                {{ number_format($item->unit_price,2,',','.') }}
                                Kz
                            </td>
                            <td>
                                {{ number_format($item->tax_rate,2,',','.') }}%
                            </td>
                            <td class="text-right">
                                <strong>
                                    {{ number_format($item->total,2,',','.') }}
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
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Método</th>
                            <th>Referência</th>
                            <th class="text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->payments as $payment)
                        <tr>
                            <td>{{ ucfirst($payment->method) }}</td>


                            <td>

                                {{ $payment->reference ?: '—' }}

                            </td>


                            <td class="text-right">

                                <strong>

                                    {{ number_format(
                                        $payment->amount,
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

    </div>


    {{-- DIREITA --}}

    <div class="col-lg-4">


        {{-- CLIENTE --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Cliente
                    </h3>

                </div>

            </div>


            <strong>

                {{ $sale->client?->name
                    ?? 'Consumidor final' }}

            </strong>


            @if($sale->client?->nif)

            <div class="text-muted mt-1">

                NIF:
                {{ $sale->client->nif }}

            </div>

            @endif

        </div>


        {{-- RESUMO --}}

        <div class="dashboard-card chart-card mb-4">

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
                        font-size:20px;
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


            <div class="d-flex justify-content-between mt-3">

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
                    Saldo
                </span>

                <strong style="color:#dc2626;">

                    {{ number_format(
                        $sale->balance_due,
                        2,
                        ',',
                        '.'
                    ) }}

                    Kz

                </strong>

            </div>

        </div>


        {{-- ANULAR --}}

        @if($sale->status === 'completed')

        <div class="dashboard-card chart-card">

            <form method="POST" action="{{ route(
                        'tenant.invoices.cancel',
                        $sale
                    ) }}" onsubmit="
                        return confirm(
                            'Tem certeza que deseja anular este documento? O stock será reposto.'
                        );
                    ">

                @csrf

                <button type="submit" class="btn btn-outline-danger w-100">

                    <i class="fas fa-ban mr-1"></i>

                    Anular documento

                </button>

            </form>

        </div>

        @endif

    </div>

</div>

@endsection
