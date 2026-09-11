@extends('layouts.app')

@section('title', isset($productLot) ? 'Editar lote' : 'Novo lote')

@section('page_header')

<div class="d-flex align-items-center justify-content-between">

    <div>

        <h1 class="page-title">
            {{ isset($productLot) ? 'Editar lote' : 'Novo lote' }}
        </h1>

        <div class="page-subtitle">
            {{ isset($productLot)
                ? 'Atualize as informações do lote.'
                : 'Registe um novo lote para um produto.' }}
        </div>

    </div>

    <div class="d-flex">

        <a href="{{ route('tenant.product-lots.index') }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>

        <button type="submit" form="product-lot-form" class="btn btn-primary btn-sm">
            <i class="fas fa-save"></i>

            {{ isset($productLot)
                ? 'Guardar alterações'
                : 'Criar lote' }}
        </button>

    </div>

</div>

@endsection

@section('content')

<form id="product-lot-form" method="POST" action="{{ isset($productLot)
        ? route('tenant.product-lots.update', $productLot)
        : route('tenant.product-lots.store') }}">

    @csrf

    @if(isset($productLot))
    @method('PUT')
    @endif

    @if($errors->any())

    <div class="alert alert-danger mb-4">

        <i class="fas fa-exclamation-circle"></i>

        <span>
            {{ $errors->first() }}
        </span>

    </div>

    @endif

    <div class="row">

        {{-- LEFT --}}
        <div class="col-lg-8">

            {{-- IDENTIFICAÇÃO --}}
            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>

                        <h3 class="card-title-modern">
                            Identificação do lote
                        </h3>

                        <div class="card-subtitle-modern">
                            Produto e número de identificação do lote.
                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-7">

                        <div class="form-group">

                            <label class="form-label required">
                                Produto
                            </label>

                            <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>

                                <option value="">
                                    Selecionar produto
                                </option>

                                @foreach($products as $product)

                                <option value="{{ $product->id }}" {{ old(
                                            'product_id',
                                            $productLot->product_id ?? ''
                                        ) == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                    — {{ $product->code }}
                                </option>

                                @endforeach

                            </select>

                            @error('product_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="form-label required">
                                Número do lote
                            </label>
                            <input type="text" name="lot_number" class="auth-input @error('lot_number') is-invalid @enderror" value="{{ old('lot_number', $productLot->lot_number ?? '') }}" placeholder="Ex.: LOTE-2026-001" required>
                            @error('lot_number')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
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

                        <div class="card-subtitle-modern">
                            Registe a data de fabrico e validade.
                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Data de fabrico
                            </label>

                            <input type="date" name="manufactured_at" class="auth-input" value="{{ old(
                                    'manufactured_at',
                                    isset($productLot->manufactured_at)
                                        ? $productLot->manufactured_at->format('Y-m-d')
                                        : ''
                                ) }}">

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Data de validade
                            </label>

                            <input type="date" name="expires_at" class="auth-input" value="{{ old(
                                    'expires_at',
                                    isset($productLot->expires_at)
                                        ? $productLot->expires_at->format('Y-m-d')
                                        : ''
                                ) }}">

                        </div>

                    </div>

                </div>

            </div>

            {{-- STOCK --}}
            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>

                        <h3 class="card-title-modern">
                            Stock e custo
                        </h3>

                        <div class="card-subtitle-modern">
                            Quantidade e custo associado ao lote.
                        </div>

                    </div>

                </div>

                <div class="row">

                    @if(isset($productLot))

                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Quantidade atual
                            </label>

                            <input type="number" step="0.001" min="0" name="current_quantity" class="auth-input" value="{{ old(
                                        'current_quantity',
                                        $productLot->current_quantity
                                    ) }}" required>

                            <small class="text-muted">
                                Disponível:
                                {{ number_format(
                                        $productLot->available_quantity,
                                        3,
                                        ',',
                                        '.'
                                    ) }}
                            </small>

                        </div>

                    </div>

                    @else

                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label required">
                                Quantidade inicial
                            </label>

                            <input type="number" step="0.001" min="0" name="initial_quantity" class="auth-input @error('initial_quantity') is-invalid @enderror" value="{{ old('initial_quantity', 0) }}" required>

                            @error('initial_quantity')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    @endif

                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Preço de custo
                            </label>

                            <div class="input-wrapper">

                                <span style="
                                    position:absolute;
                                    left:14px;
                                    top:50%;
                                    transform:translateY(-50%);
                                    color:#94a3b8;
                                    font-size:11px;
                                    z-index:2;
                                ">
                                    Kz
                                </span>

                                <input type="number" step="0.01" min="0" name="cost_price" class="auth-input" value="{{ old(
                                        'cost_price',
                                        $productLot->cost_price ?? ''
                                    ) }}" placeholder="0,00" style="padding-left:40px;">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="col-lg-4">

            {{-- ESTADO --}}
            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>

                        <h3 class="card-title-modern">
                            Estado
                        </h3>

                        <div class="card-subtitle-modern">
                            Controle a disponibilidade do lote.
                        </div>

                    </div>

                </div>

                <label class="custom-checkbox">

                    <input type="checkbox" name="is_active" value="1" {{ old(
                            'is_active',
                            $productLot->is_active ?? true
                        ) ? 'checked' : '' }}>

                    <span class="checkbox-box"></span>

                    <span class="checkbox-label">

                        <strong>
                            Lote ativo
                        </strong>

                        <br>

                        <small class="text-muted">
                            O lote poderá ser utilizado nas operações de stock.
                        </small>

                    </span>

                </label>

            </div>

            {{-- INFORMAÇÃO --}}
            @if(isset($productLot))

            <div class="dashboard-card chart-card">

                <div class="card-header-modern">

                    <div>

                        <h3 class="card-title-modern">
                            Informação
                        </h3>

                        <div class="card-subtitle-modern">
                            Dados de auditoria do lote.
                        </div>

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
                            Atualizado em
                        </div>

                        <div class="activity-time">
                            {{ $productLot->updated_at?->format('d/m/Y H:i') ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>

            @endif

        </div>

    </div>

</form>

@endsection
