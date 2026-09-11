@extends('layouts.app')

@section('title', 'Lote ' . $productLot->lot_number)

@section('page_header')

<div class="d-flex align-items-center justify-content-between">

    <div>

        <h1 class="page-title">
            Lote {{ $productLot->lot_number }}
        </h1>

        <div class="page-subtitle">
            Detalhes e situação atual do lote.
        </div>

    </div>

    <div class="d-flex">

        <a href="{{ route('tenant.product-lots.index') }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>

        <a href="{{ route('tenant.product-lots.edit', $productLot) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i>
            Editar
        </a>

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

    {{-- PRINCIPAL --}}
    <div class="col-lg-8">

        {{-- PRODUTO --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Produto
                    </h3>

                    <div class="card-subtitle-modern">
                        Produto associado ao lote.
                    </div>

                </div>

            </div>

            <div class="d-flex align-items-center">

                <div class="stat-icon blue mr-3">
                    <i class="fas fa-box"></i>
                </div>

                <div>

                    <div style="
                        font-size:15px;
                        font-weight:700;
                        color:#1e293b;
                    ">
                        {{ $productLot->product->name }}
                    </div>

                    <div style="
                        margin-top:3px;
                        color:#94a3b8;
                        font-size:11px;
                    ">
                        Código:
                        {{ $productLot->product->code }}
                    </div>

                </div>

            </div>

        </div>

        {{-- STOCK --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Situação do stock
                    </h3>

                    <div class="card-subtitle-modern">
                        Quantidades registadas para este lote.
                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-md-4">

                    <div class="stat-card">

                        <div class="stat-icon blue">
                            <i class="fas fa-boxes"></i>
                        </div>

                        <div class="stat-title">
                            Quantidade inicial
                        </div>

                        <div class="stat-value" style="font-size:20px;">
                            {{ number_format(
                                $productLot->initial_quantity,
                                3,
                                ',',
                                '.'
                            ) }}
                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="stat-card">

                        <div class="stat-icon green">
                            <i class="fas fa-cubes"></i>
                        </div>

                        <div class="stat-title">
                            Stock atual
                        </div>

                        <div class="stat-value" style="font-size:20px;">
                            {{ number_format(
                                $productLot->current_quantity,
                                3,
                                ',',
                                '.'
                            ) }}
                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="stat-card">

                        <div class="stat-icon orange">
                            <i class="fas fa-lock"></i>
                        </div>

                        <div class="stat-title">
                            Disponível
                        </div>

                        <div class="stat-value" style="font-size:20px;">
                            {{ number_format(
                                $productLot->available_quantity,
                                3,
                                ',',
                                '.'
                            ) }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- DATAS --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Datas do lote
                    </h3>

                </div>

            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="activity-item">

                        <div class="activity-icon">
                            <i class="fas fa-industry"></i>
                        </div>

                        <div>

                            <div class="activity-title">
                                Data de fabrico
                            </div>

                            <div class="activity-time">
                                {{ $productLot->manufactured_at?->format('d/m/Y') ?? 'Não informada' }}
                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="activity-item">

                        <div class="activity-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>

                        <div>

                            <div class="activity-title">
                                Data de validade
                            </div>

                            <div class="activity-time">

                                @if($productLot->expires_at)

                                {{ $productLot->expires_at->format('d/m/Y') }}

                                @else

                                Não informada

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- SIDEBAR --}}
    <div class="col-lg-4">

        {{-- ESTADO --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Estado do lote
                    </h3>

                </div>

            </div>

            @if(!$productLot->is_active)

            <span class="status status-neutral">
                Inativo
            </span>

            @elseif($productLot->is_expired)

            <span class="status status-danger">
                <i class="fas fa-exclamation-circle"></i>
                Expirado
            </span>

            @elseif($productLot->days_to_expire !== null &&
            $productLot->days_to_expire <= 30) <span class="status status-warning">
                <i class="fas fa-clock"></i>
                Expira em {{ $productLot->days_to_expire }} dias
                </span>

                @else

                <span class="status status-success">
                    <i class="fas fa-check-circle"></i>
                    Ativo
                </span>

                @endif

        </div>

        {{-- CUSTO --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Custo
                    </h3>

                    <div class="card-subtitle-modern">
                        Preço de custo deste lote.
                    </div>

                </div>

            </div>

            <div style="
                font-size:22px;
                font-weight:700;
                color:#1e293b;
            ">

                @if($productLot->cost_price !== null)

                {{ number_format(
                        $productLot->cost_price,
                        2,
                        ',',
                        '.'
                    ) }}

                <span style="
                        font-size:12px;
                        color:#64748b;
                    ">
                    Kz
                </span>

                @else

                —

                @endif

            </div>

        </div>

        {{-- AUDITORIA --}}
        <div class="dashboard-card chart-card">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Informação
                    </h3>

                </div>

            </div>

            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Criado em
                    </div>

                    <div class="activity-time">
                        {{ $productLot->created_at?->format('d/m/Y H:i') ?? '-' }}
                    </div>

                </div>

            </div>

            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-clock"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Última atualização
                    </div>

                    <div class="activity-time">
                        {{ $productLot->updated_at?->format('d/m/Y H:i') ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
