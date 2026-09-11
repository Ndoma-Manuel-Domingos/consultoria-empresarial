@extends('layouts.app')

@section('title', 'Movimento de stock')

@section('page_header')

<div class="d-flex align-items-center justify-content-between">

    <div>

        <h1 class="page-title">
            Movimento de stock
        </h1>

        <div class="page-subtitle">
            Detalhes da operação realizada.
        </div>

    </div>

    <div>

        <a href="{{ route('tenant.stock-movements.index') }}" class="btn btn-secondary btn-sm">

            <i class="fas fa-arrow-left"></i>

            Voltar

        </a>

    </div>

</div>

@endsection


@section('content')

<div class="row">

    <div class="col-lg-8">

        {{-- MOVIMENTO --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Detalhes do movimento
                    </h3>

                    <div class="card-subtitle-modern">
                        Informação completa da operação.
                    </div>

                </div>


                <div>

                    @switch($stockMovement->type)

                    @case('entry')

                    <span class="status status-success">
                        <i class="fas fa-arrow-down"></i>
                        Entrada
                    </span>

                    @break

                    @case('exit')

                    <span class="status status-danger">
                        <i class="fas fa-arrow-up"></i>
                        Saída
                    </span>

                    @break

                    @case('adjustment')

                    <span class="status status-warning">
                        <i class="fas fa-sliders-h"></i>
                        Ajuste
                    </span>

                    @break

                    @case('transfer')

                    <span class="status status-info">
                        <i class="fas fa-exchange-alt"></i>
                        Transferência
                    </span>

                    @break

                    @endswitch

                </div>

            </div>


            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label class="form-label">
                            Produto
                        </label>

                        <div style="
                            font-size:13px;
                            font-weight:700;
                            color:#334155;
                        ">

                            {{ $stockMovement->product->name ?? '-' }}

                        </div>

                        @if($stockMovement->product?->code)

                        <div style="
                                margin-top:3px;
                                color:#94a3b8;
                                font-size:10px;
                            ">

                            Código:
                            {{ $stockMovement->product->code }}

                        </div>

                        @endif

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="form-group">

                        <label class="form-label">
                            Lote
                        </label>

                        @if($stockMovement->productLot)

                        <span class="status status-info">

                            {{ $stockMovement->productLot->lot_number }}

                        </span>

                        @else

                        <span style="color:#94a3b8;">
                            Sem lote
                        </span>

                        @endif

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="form-group">

                        <label class="form-label">
                            Quantidade
                        </label>

                        <div style="
                            font-size:18px;
                            font-weight:700;
                            color:#334155;
                        ">

                            {{ number_format(
                                $stockMovement->quantity,
                                3,
                                ',',
                                '.'
                            ) }}

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="form-group">

                        <label class="form-label">
                            Stock anterior
                        </label>

                        <div style="
                            font-size:16px;
                            font-weight:700;
                            color:#64748b;
                        ">

                            {{ number_format(
                                $stockMovement->stock_before,
                                3,
                                ',',
                                '.'
                            ) }}

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="form-group">

                        <label class="form-label">
                            Stock posterior
                        </label>

                        <div style="
                            font-size:16px;
                            font-weight:700;
                            color:#2563eb;
                        ">

                            {{ number_format(
                                $stockMovement->stock_after,
                                3,
                                ',',
                                '.'
                            ) }}

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- DOCUMENTO --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Documento e referência
                    </h3>

                </div>

            </div>


            <div class="row">

                <div class="col-md-4">

                    <label class="form-label">
                        Tipo de documento
                    </label>

                    <div style="color:#475569;font-size:12px;">

                        {{ $stockMovement->document_type ?: '—' }}

                    </div>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Nº documento
                    </label>

                    <div style="color:#475569;font-size:12px;">

                        {{ $stockMovement->document_number ?: '—' }}

                    </div>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Referência
                    </label>

                    <div style="color:#475569;font-size:12px;">

                        {{ $stockMovement->reference ?: '—' }}

                    </div>

                </div>


                <div class="col-12 mt-3">

                    <label class="form-label">
                        Motivo
                    </label>

                    <div style="color:#475569;font-size:12px;">

                        {{ $stockMovement->reason ?: '—' }}

                    </div>

                </div>


                @if($stockMovement->notes)

                <div class="col-12 mt-3">

                    <label class="form-label">
                        Observações
                    </label>

                    <div style="
                            padding:12px;
                            background:#f8fafc;
                            border:1px solid #e2e8f0;
                            border-radius:9px;
                            color:#475569;
                            font-size:12px;
                        ">

                        {{ $stockMovement->notes }}

                    </div>

                </div>

                @endif

            </div>

        </div>

    </div>


    {{-- RIGHT --}}

    <div class="col-lg-4">

        {{-- VALOR --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Valor do movimento
                    </h3>

                </div>

            </div>


            <div class="text-center py-3">

                <div class="stat-icon blue mx-auto mb-3" style="
                        width:60px;
                        height:60px;
                        font-size:22px;
                    ">

                    <i class="fas fa-coins"></i>

                </div>


                <div style="
                    font-size:22px;
                    font-weight:700;
                    color:#334155;
                ">

                    {{ number_format(
                        $stockMovement->total_cost,
                        2,
                        ',',
                        '.'
                    ) }}

                    Kz

                </div>


                <div style="
                    margin-top:4px;
                    color:#94a3b8;
                    font-size:10px;
                ">

                    Custo total

                </div>

            </div>


            <div class="modern-divider"></div>


            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-tag"></i>

                </div>

                <div>

                    <div class="activity-title">
                        Custo unitário
                    </div>

                    <div class="activity-time">

                        {{ number_format(
                            $stockMovement->unit_cost,
                            2,
                            ',',
                            '.'
                        ) }}

                        Kz

                    </div>

                </div>

            </div>

        </div>


        {{-- AUDITORIA --}}

        <div class="dashboard-card chart-card">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Auditoria
                    </h3>

                    <div class="card-subtitle-modern">
                        Registo da operação.
                    </div>

                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-calendar"></i>

                </div>

                <div>

                    <div class="activity-title">
                        Data do movimento
                    </div>

                    <div class="activity-time">

                        {{ $stockMovement->movement_date?->format('d/m/Y H:i') }}

                    </div>

                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-user"></i>

                </div>

                <div>

                    <div class="activity-title">
                        Registado por
                    </div>

                    <div class="activity-time">

                        {{ $stockMovement->creator->name ?? 'Sistema' }}

                    </div>

                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon">

                    <i class="fas fa-clock"></i>

                </div>

                <div>

                    <div class="activity-title">
                        Criado em
                    </div>

                    <div class="activity-time">

                        {{ $stockMovement->created_at?->format('d/m/Y H:i') }}

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
