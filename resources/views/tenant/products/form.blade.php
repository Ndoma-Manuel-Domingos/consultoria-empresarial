@extends('layouts.app')

@section('title', isset($product) ? 'Editar produto' : 'Novo produto')

@section('page_header')

<div class="d-flex align-items-center justify-content-between"> <div> <h1 class="page-title"> {{ isset($product) ? 'Editar produto' : 'Novo produto' }} </h1>
    <div class="page-subtitle">
        {{ isset($product) ? 'Atualize as informações, preços, fiscalidade e configurações de stock do produto.' : 'Registe um novo produto no sistema.' }}
    </div>
</div>
<div class="d-flex align-items-center">
    <a href="{{ route('tenant.products.index') }}" class="btn btn-secondary btn-sm mr-2">
        <i class="fas fa-arrow-left"></i>
        Voltar
    </a>

    <button type="submit" form="product-form" class="btn btn-primary btn-sm" data-loading-text="{{ isset($product) ? 'A guardar...' : 'A criar...' }}">
        <i class="fas fa-save"></i>
        {{ isset($product) ? 'Guardar alterações' : 'Criar produto' }}
    </button>
</div>

</div> @endsection
@section('content')

<form id="product-form" method="POST" action="{{ isset($product) ? route('tenant.products.update', $product) : route('tenant.products.store') }}" data-ajax="true" > @csrf
@if(isset($product))
    @method('PUT')
@endif

{{-- =========================================================
    ALERTS
========================================================== --}}

@if(session('success'))
    <div class="alert alert-success mb-4">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle"></i>

        <div>
            {{ $errors->first() }}
        </div>
    </div>
@endif

<div class="row">

    {{-- =====================================================
        LEFT
    ====================================================== --}}

    <div class="col-lg-8">

        {{-- =================================================
            IDENTIFICAÇÃO DO PRODUTO
        ================================================== --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Identificação do produto
                    </h3>

                    <div class="card-subtitle-modern">
                        Dados principais utilizados para identificar o produto.
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- Código --}}
                <div class="col-md-4">
                    <div class="form-group">

                        <label class="form-label required">
                            Código
                        </label>

                        <div class="input-wrapper">
                            <i class="fas fa-hashtag input-icon"></i>

                            <input
                                type="text"
                                name="code"
                                class="auth-input @error('code') is-invalid @enderror"
                                value="{{ old('code', $product->code ?? '') }}"
                                placeholder="Ex.: PROD-001"
                                required
                            >
                        </div>

                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                {{-- Código de barras --}}
                <div class="col-md-4">
                    <div class="form-group">

                        <label class="form-label">
                            Código de barras
                        </label>

                        <div class="input-wrapper">
                            <i class="fas fa-barcode input-icon"></i>

                            <input
                                type="text"
                                name="barcode"
                                class="auth-input @error('barcode') is-invalid @enderror"
                                value="{{ old('barcode', $product->barcode ?? '') }}"
                                placeholder="EAN / GTIN"
                            >
                        </div>

                        @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                {{-- Unidade --}}
                <div class="col-md-4">
                    <div class="form-group">

                        <label class="form-label required">
                            Unidade
                        </label>

                        <select
                            name="unit"
                            class="form-select @error('unit') is-invalid @enderror"
                            required
                        >
                            <option value="">Selecionar</option>

                            @foreach([
                                'UN' => 'Unidade',
                                'CX' => 'Caixa',
                                'KG' => 'Quilograma',
                                'G'  => 'Grama',
                                'L'  => 'Litro',
                                'ML' => 'Mililitro',
                                'M'  => 'Metro',
                                'M2' => 'Metro quadrado',
                                'M3' => 'Metro cúbico',
                                'PAR' => 'Par',
                                'PCT' => 'Pacote',
                                'FR' => 'Frasco',
                                'AMP' => 'Ampola',
                                'COMP' => 'Comprimido',
                            ] as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    {{ old('unit', $product->unit ?? '') === $value ? 'selected' : '' }}
                                >
                                    {{ $label }} ({{ $value }})
                                </option>

                            @endforeach

                        </select>

                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                {{-- Nome --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label required">
                            Nome do produto
                        </label>
                        <input type="text" name="name" class="auth-input @error('name') is-invalid @enderror" value="{{ old('name', $product->name ?? '') }}" placeholder="Nome completo do produto" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Nome curto --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Nome curto
                        </label>
                        <input type="text" name="short_name" class="auth-input @error('short_name') is-invalid @enderror" value="{{ old('short_name', $product->short_name ?? '') }}" placeholder="Nome abreviado">
                        @error('short_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Tipo --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label required">
                            Tipo de produto
                        </label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">Selecionar tipo</option>
                            <option value="product"
                                {{ old('type', $product->type ?? '') === 'product' ? 'selected' : '' }}>
                                Produto
                            </option>
                            <option value="service"
                                {{ old('type', $product->type ?? '') === 'service' ? 'selected' : '' }}>
                                Serviço
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Descrição --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">
                            Descrição
                        </label>
                        <textarea name="description" class="@error('description') is-invalid @enderror" placeholder="Descrição do produto...">{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- =================================================
            CLASSIFICAÇÃO
        ================================================== --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Classificação
                    </h3>

                    <div class="card-subtitle-modern">
                        Organize o produto por categoria, subcategoria, marca e modelo.
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- Categoria --}}
                <div class="col-md-6">
                    <div class="form-group">

                        <label class="form-label">
                            Categoria
                        </label>

                        <div class="input-wrapper">
                            <i class="fas fa-folder input-icon"></i>

                            <input
                                type="text"
                                name="category"
                                class="auth-input @error('category') is-invalid @enderror"
                                value="{{ old('category', $product->category ?? '') }}"
                                placeholder="Ex.: Medicamentos"
                            >
                        </div>

                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                {{-- Subcategoria --}}
                <div class="col-md-6">
                    <div class="form-group">

                        <label class="form-label">
                            Subcategoria
                        </label>

                        <div class="input-wrapper">
                            <i class="fas fa-folder-open input-icon"></i>

                            <input
                                type="text"
                                name="subcategory"
                                class="auth-input @error('subcategory') is-invalid @enderror"
                                value="{{ old('subcategory', $product->subcategory ?? '') }}"
                                placeholder="Ex.: Analgésicos"
                            >
                        </div>

                        @error('subcategory')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                {{-- Marca --}}
                <div class="col-md-6">
                    <div class="form-group">

                        <label class="form-label">
                            Marca
                        </label>

                        <input
                            type="text"
                            name="brand"
                            class="auth-input @error('brand') is-invalid @enderror"
                            value="{{ old('brand', $product->brand ?? '') }}"
                            placeholder="Marca do produto"
                        >

                        @error('brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                {{-- Modelo --}}
                <div class="col-md-6">
                    <div class="form-group">

                        <label class="form-label">
                            Modelo / referência
                        </label>

                        <input
                            type="text"
                            name="model"
                            class="auth-input @error('model') is-invalid @enderror"
                            value="{{ old('model', $product->model ?? '') }}"
                            placeholder="Modelo ou referência"
                        >

                        @error('model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

            </div>

        </div>

        {{-- =================================================
            PREÇOS E FISCALIDADE
        ================================================== --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Preços e fiscalidade
                    </h3>

                    <div class="card-subtitle-modern">
                        Configure os valores comerciais e o tratamento fiscal do produto.
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Preço custo --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="cost_price">
                            Preço de custo
                        </label>
                        <div class="input-wrapper">
                            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:11px;z-index:2;">
                                Kz
                            </span>
                            <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" class="auth-input @error('cost_price') is-invalid @enderror" value="{{ old('cost_price', $product->cost_price ?? '') }}" placeholder="0,00" style="padding-left:40px;">
                        </div>
                        @error('cost_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Preço venda --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label required" for="sale_price">
                            Preço de venda
                        </label>
                        <div class="input-wrapper">
                            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:11px;z-index:2;">
                                Kz
                            </span>
                            <input type="number" step="0.01" id="sale_price" min="0" name="sale_price" class="auth-input @error('sale_price') is-invalid @enderror" value="{{ old('sale_price', $product->sale_price ?? '') }}" placeholder="0,00" style="padding-left:40px;">
                        </div>
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Margem --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="margin_percent">
                            Margem
                        </label>
                        <div class="input-wrapper">
                            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;z-index:2;">
                                %
                            </span>
                            <input type="number" step="0.01" min="0" id="margin_percent" name="margin_percent" class="auth-input @error('margin_percent') is-invalid @enderror" value="{{ old('margin_percent', $product->margin_percent ?? '') }}" placeholder="0,00" style="padding-left:35px;">
                        </div>
                        @error('margin_percent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Preço com imposto --}}
                <div class="col-md-4">
                    <div class="form-group">

                        <label class="form-label" for="sale_price_with_tax">
                            Preço de venda com imposto
                        </label>

                        <div class="input-wrapper">
                            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:11px;z-index:2;">
                                Kz
                            </span>

                            <input type="number" step="0.01" id="sale_price_with_tax" min="0" name="sale_price_with_tax" class="auth-input @error('sale_price_with_tax') is-invalid @enderror" value="{{ old('sale_price_with_tax', $product->sale_price_with_tax ?? '') }}" placeholder="0,00" style="padding-left:40px;">
                        </div>
                        @error('sale_price_with_tax')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                {{-- Tipo IVA --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label required">
                            Tratamento fiscal
                        </label>
                        <select name="tax_type" class="form-select @error('tax_type') is-invalid @enderror" required>
                            <option value="">Selecionar</option>
                            <option value="standard"
                                {{ old('tax_type', $product->tax_type ?? '') === 'standard' ? 'selected' : '' }}>IVA normal
                            </option>
                            <option value="exempt"
                                {{ old('tax_type', $product->tax_type ?? '') === 'exempt' ? 'selected' : '' }}>Isento de IVA
                            </option>
                            <option value="zero"
                                {{ old('tax_type', $product->tax_type ?? '') === 'zero' ? 'selected' : '' }}>IVA à taxa zero
                            </option>
                        </select>
                        @error('tax_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Taxa --}}
                <div class="col-md-4">
                    <div class="form-group">

                        <label class="form-label">
                            Taxa de IVA
                        </label>

                        <div class="input-wrapper">

                            <span
                                style="
                                    position:absolute;
                                    left:14px;
                                    top:50%;
                                    transform:translateY(-50%);
                                    color:#94a3b8;
                                    font-size:12px;
                                    z-index:2;
                                "
                            >
                                %
                            </span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="tax_rate"
                                class="auth-input @error('tax_rate') is-invalid @enderror"
                                value="{{ old('tax_rate', $product->tax_rate ?? '') }}"
                                placeholder="14,00"
                                style="padding-left:35px;"
                            >

                        </div>

                        @error('tax_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                {{-- Código de isenção --}}
                <div class="col-md-4">
                    <div class="form-group">

                        <label class="form-label">
                            Código de isenção
                        </label>

                        <input
                            type="text"
                            name="tax_exemption_code"
                            class="auth-input @error('tax_exemption_code') is-invalid @enderror"
                            value="{{ old('tax_exemption_code', $product->tax_exemption_code ?? '') }}"
                            placeholder="Código"
                        >

                        @error('tax_exemption_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                {{-- Motivo isenção --}}
                <div class="col-md-8">
                    <div class="form-group">

                        <label class="form-label">
                            Motivo da isenção
                        </label>

                        <input
                            type="text"
                            name="tax_exemption_reason"
                            class="auth-input @error('tax_exemption_reason') is-invalid @enderror"
                            value="{{ old('tax_exemption_reason', $product->tax_exemption_reason ?? '') }}"
                            placeholder="Motivo"
                        >

                        @error('tax_exemption_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

            </div>

        </div>

        {{-- =================================================
            GESTÃO DE STOCK
        ================================================== --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Gestão de stock
                    </h3>

                    <div class="card-subtitle-modern">
                        Defina como o produto será controlado no inventário.
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="custom-checkbox">

                        <input
                            type="checkbox"
                            name="manage_stock"
                            value="1"
                            {{ old('manage_stock', $product->manage_stock ?? true) ? 'checked' : '' }}
                        >

                        <span class="checkbox-box"></span>

                        <span class="checkbox-label">
                            <strong>Gerir stock</strong>
                            <br>
                            <small class="text-muted">
                                Controlar entradas, saídas e saldo disponível.
                            </small>
                        </span>

                    </label>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="custom-checkbox">

                        <input
                            type="checkbox"
                            name="allow_negative_stock"
                            value="1"
                            {{ old('allow_negative_stock', $product->allow_negative_stock ?? false) ? 'checked' : '' }}
                        >

                        <span class="checkbox-box"></span>

                        <span class="checkbox-label">
                            <strong>Permitir stock negativo</strong>
                            <br>
                            <small class="text-muted">
                                Permitir vendas mesmo sem stock disponível.
                            </small>
                        </span>

                    </label>

                </div>

                {{-- Stock mínimo --}}
                <div class="col-md-6">

                    <div class="form-group">
                        <label class="form-label">
                            Stock mínimo
                        </label>
                        <input type="number" step="0.001" min="0" name="minimum_stock" class="auth-input @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', $product->minimum_stock ?? '') }}" placeholder="0">
                        <div class="mt-1" style="font-size:10px;color:#94a3b8;">
                            Quantidade que indica necessidade de reposição.
                        </div>
                        @error('minimum_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Stock máximo --}}
                <div class="col-md-6">

                    <div class="form-group">

                        <label class="form-label">
                            Stock máximo
                        </label>

                        <input
                            type="number"
                            step="0.001"
                            min="0"
                            name="maximum_stock"
                            class="auth-input @error('maximum_stock') is-invalid @enderror"
                            value="{{ old('maximum_stock', $product->maximum_stock ?? '') }}"
                            placeholder="0"
                        >

                        <div class="mt-1" style="font-size:10px;color:#94a3b8;">
                            Limite máximo recomendado para reposição.
                        </div>

                        @error('maximum_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>

        {{-- =================================================
            LOTES E VALIDADE
        ================================================== --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>
                    <h3 class="card-title-modern">
                        Lotes e validade
                    </h3>

                    <div class="card-subtitle-modern">
                        Configure o controlo de lotes e produtos com prazo de validade.
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="custom-checkbox">

                        <input
                            type="checkbox"
                            name="manage_lots"
                            value="1"
                            {{ old('manage_lots', $product->manage_lots ?? false) ? 'checked' : '' }}
                        >

                        <span class="checkbox-box"></span>

                        <span class="checkbox-label">

                            <strong>Gerir lotes</strong>

                            <br>

                            <small class="text-muted">
                                Controlar o stock por número de lote.
                            </small>

                        </span>

                    </label>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="custom-checkbox">

                        <input
                            type="checkbox"
                            name="has_expiration"
                            value="1"
                            {{ old('has_expiration', $product->has_expiration ?? false) ? 'checked' : '' }}
                        >

                        <span class="checkbox-box"></span>

                        <span class="checkbox-label">

                            <strong>Possui validade</strong>

                            <br>

                            <small class="text-muted">
                                Controlar data de expiração do produto.
                            </small>

                        </span>

                    </label>

                </div>

                {{-- Alertas --}}
                <div class="col-md-6">

                    <div class="form-group">

                        <label class="form-label">
                            Alerta de validade
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-calendar-alt input-icon"></i>

                            <input
                                type="number"
                                min="0"
                                name="expiration_alert_days"
                                class="auth-input @error('expiration_alert_days') is-invalid @enderror"
                                value="{{ old('expiration_alert_days', $product->expiration_alert_days ?? 30) }}"
                                placeholder="30"
                            >

                        </div>

                        <div class="mt-1" style="font-size:10px;color:#94a3b8;">
                            Número de dias antes da validade para emitir alerta.
                        </div>

                        @error('expiration_alert_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>

        {{-- =================================================
            OBSERVAÇÕES
        ================================================== --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>
                    <h3 class="card-title-modern">
                        Observações
                    </h3>

                    <div class="card-subtitle-modern">
                        Informações adicionais sobre o produto.
                    </div>
                </div>

            </div>

            <div class="form-group mb-0">

                <textarea
                    name="notes"
                    class="@error('notes') is-invalid @enderror"
                    placeholder="Observações internas..."
                >{{ old('notes', $product->notes ?? '') }}</textarea>

                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

            </div>

        </div>

    </div>

    {{-- =====================================================
        RIGHT SIDEBAR
    ====================================================== --}}

    <div class="col-lg-4">

        {{-- =================================================
            FORNECEDOR
        ================================================== --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Fornecedor
                    </h3>

                    <div class="card-subtitle-modern">
                        Fornecedor principal deste produto.
                    </div>

                </div>

            </div>

            @if(isset($suppliers) && $suppliers->count())

                <div class="form-group mb-0">

                    <label class="form-label">
                        Fornecedor principal
                    </label>

                    <select
                        name="supplier_id"
                        class="form-select @error('supplier_id') is-invalid @enderror"
                    >

                        <option value="">
                            Sem fornecedor
                        </option>

                        @foreach($suppliers as $supplier)

                            <option
                                value="{{ $supplier->id }}"
                                {{ old('supplier_id', $product->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}
                            >
                                {{ $supplier->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                </div>

            @else

                <div class="alert alert-info mb-0">

                    <i class="fas fa-info-circle"></i>

                    <span>
                        Ainda não existem fornecedores cadastrados.
                    </span>

                </div>

            @endif

        </div>

        {{-- =================================================
            ESTADO
        ================================================== --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Estado do produto
                    </h3>

                    <div class="card-subtitle-modern">
                        Controle a disponibilidade do produto no sistema.
                    </div>

                </div>

            </div>

            <label class="custom-checkbox mb-3">

                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                >

                <span class="checkbox-box"></span>

                <span class="checkbox-label">

                    <strong>
                        Produto ativo
                    </strong>

                    <br>

                    <small class="text-muted">
                        O produto estará disponível no sistema.
                    </small>

                </span>

            </label>

            <label class="custom-checkbox mb-3">

                <input
                    type="checkbox"
                    name="is_sellable"
                    value="1"
                    {{ old('is_sellable', $product->is_sellable ?? true) ? 'checked' : '' }}
                >

                <span class="checkbox-box"></span>

                <span class="checkbox-label">

                    <strong>
                        Disponível para venda
                    </strong>

                    <br>

                    <small class="text-muted">
                        Permitir utilização do produto em vendas.
                    </small>

                </span>

            </label>

            <label class="custom-checkbox">

                <input
                    type="checkbox"
                    name="is_purchasable"
                    value="1"
                    {{ old('is_purchasable', $product->is_purchasable ?? true) ? 'checked' : '' }}
                >

                <span class="checkbox-box"></span>

                <span class="checkbox-label">

                    <strong>
                        Disponível para compra
                    </strong>

                    <br>

                    <small class="text-muted">
                        Permitir aquisição deste produto.
                    </small>

                </span>

            </label>

        </div>

        {{-- =================================================
            RESUMO
        ================================================== --}}

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Resumo
                    </h3>

                    <div class="card-subtitle-modern">
                        Configuração atual do produto.
                    </div>

                </div>

            </div>

            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-box"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Gestão de stock
                    </div>

                    <div class="activity-time">

                        {{ old('manage_stock', $product->manage_stock ?? true) ? 'Ativada' : 'Desativada' }}

                    </div>

                </div>

            </div>

            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-layer-group"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Gestão de lotes
                    </div>

                    <div class="activity-time">

                        {{ old('manage_lots', $product->manage_lots ?? false) ? 'Ativada' : 'Desativada' }}

                    </div>

                </div>

            </div>

            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Validade
                    </div>

                    <div class="activity-time">

                        {{ old('has_expiration', $product->has_expiration ?? false) ? 'Controlada' : 'Não controlada' }}

                    </div>

                </div>

            </div>

            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-receipt"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Tratamento fiscal
                    </div>

                    <div class="activity-time">

                        @php
                            $taxType = old('tax_type', $product->tax_type ?? '');
                        @endphp

                        @switch($taxType)
                            @case('standard')
                                IVA normal
                                @break

                            @case('exempt')
                                Isento
                                @break

                            @case('zero')
                                Taxa zero
                                @break

                            @default
                                Não definido
                        @endswitch

                    </div>

                </div>

            </div>

        </div>

        {{-- =================================================
            AUDITORIA
        ================================================== --}}

        @if(isset($product))

            <div class="dashboard-card chart-card">

                <div class="card-header-modern">

                    <div>

                        <h3 class="card-title-modern">
                            Informação
                        </h3>

                        <div class="card-subtitle-modern">
                            Registo e atualização do produto.
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
                            {{ $product->created_at?->format('d/m/Y H:i') ?? '-' }}
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
                            {{ $product->updated_at?->format('d/m/Y H:i') ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

</form>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================================
    // ELEMENTOS
    // ==========================================================

    const costPrice = document.getElementById('cost_price');
    const salePrice = document.getElementById('sale_price');
    const marginPercent = document.getElementById('margin_percent');

    const salePriceWithTax = document.getElementById('sale_price_with_tax');

    const taxType = document.getElementById('tax_type');
    const taxRate = document.getElementById('tax_rate');


    // ==========================================================
    // CONVERTER VALOR PARA NÚMERO
    // ==========================================================

    function toNumber(value) {

        if (value === null || value === undefined || value === '') {
            return 0;
        }

        // Aceita:
        // 10000
        // 10000.50
        // 10000,50

        value = String(value)
            .trim()
            .replace(',', '.');

        const number = parseFloat(value);

        return isNaN(number) ? 0 : number;
    }


    // ==========================================================
    // FORMATAR VALOR
    // ==========================================================

    function formatNumber(value, decimals = 2) {

        if (!isFinite(value)) {
            return '';
        }

        return Number(value).toFixed(decimals);
    }


    // ==========================================================
    // CALCULAR MARGEM
    // ==========================================================
    //
    // Margem = ((Venda - Custo) / Venda) * 100
    //
    // Exemplo:
    //
    // Custo = 10.000
    // Venda = 15.000
    //
    // Margem = ((15.000 - 10.000) / 15.000) * 100
    // Margem = 33,33%
    //
    // ==========================================================

    function calculateMargin() {

        const cost = toNumber(costPrice.value);
        const sale = toNumber(salePrice.value);

        if (cost <= 0 || sale <= 0) {
            marginPercent.value = '';
            return;
        }

        const margin = ((sale - cost) / sale) * 100;

        marginPercent.value = formatNumber(margin);
    }


    // ==========================================================
    // CALCULAR PREÇO DE VENDA ATRAVÉS DA MARGEM
    // ==========================================================
    //
    // Venda = Custo / (1 - Margem / 100)
    //
    // Exemplo:
    //
    // Custo = 10.000
    // Margem = 30%
    //
    // Venda = 10.000 / (1 - 0.30)
    // Venda = 14.285,71
    //
    // ==========================================================

    function calculateSalePrice() {

        const cost = toNumber(costPrice.value);
        const margin = toNumber(marginPercent.value);

        if (cost <= 0 || margin <= 0) {
            return;
        }

        // Margem não pode chegar a 100%
        if (margin >= 100) {
            return;
        }

        const sale = cost / (1 - (margin / 100));

        salePrice.value = formatNumber(sale);

        calculateSalePriceWithTax();
    }


    // ==========================================================
    // CALCULAR PREÇO COM IVA
    // ==========================================================

    function calculateSalePriceWithTax() {

        if (!salePriceWithTax) {
            return;
        }

        const sale = toNumber(salePrice.value);

        if (sale <= 0) {
            salePriceWithTax.value = '';
            return;
        }

        const type = taxType ? taxType.value : '';
        const rate = taxRate ? toNumber(taxRate.value) : 0;

        let total = sale;


        // ======================================================
        // IVA NORMAL
        // ======================================================

        if (type === 'standard') {

            total = sale + (sale * rate / 100);

        }


        // ======================================================
        // ISENTO
        // ======================================================

        else if (type === 'exempt') {

            total = sale;

        }


        // ======================================================
        // TAXA ZERO
        // ======================================================

        else if (type === 'zero') {

            total = sale;

        }


        salePriceWithTax.value = formatNumber(total);
    }


    // ==========================================================
    // ALTERAR TIPO DE IVA
    // ==========================================================

    function handleTaxTypeChange() {

        if (!taxType) {
            return;
        }

        const type = taxType.value;


        // ------------------------------------------------------
        // Isento ou taxa zero
        // ------------------------------------------------------

        if (type === 'exempt' || type === 'zero') {

            if (taxRate) {
                taxRate.value = '0.00';
                taxRate.setAttribute('readonly', 'readonly');
            }

        }


        // ------------------------------------------------------
        // IVA normal
        // ------------------------------------------------------

        else if (type === 'standard') {

            if (taxRate) {

                taxRate.removeAttribute('readonly');

                // Se estiver vazio, coloca 14%
                if (taxRate.value === '') {
                    taxRate.value = '14.00';
                }

            }

        }


        // ------------------------------------------------------
        // Nenhum selecionado
        // ------------------------------------------------------

        else {

            if (taxRate) {
                taxRate.removeAttribute('readonly');
            }

        }


        calculateSalePriceWithTax();
    }


    // ==========================================================
    // EVENTOS - PREÇO DE CUSTO
    // ==========================================================

    if (costPrice) {

        costPrice.addEventListener('input', function () {

            // Alterou custo
            // Recalcula margem

            calculateMargin();

            // Recalcula preço com IVA

            calculateSalePriceWithTax();

        });

    }


    // ==========================================================
    // EVENTOS - PREÇO DE VENDA
    // ==========================================================

    if (salePrice) {

        salePrice.addEventListener('input', function () {

            // Alterou preço de venda
            // Recalcula margem

            calculateMargin();

            // Recalcula preço com IVA

            calculateSalePriceWithTax();

        });

    }


    // ==========================================================
    // EVENTOS - MARGEM
    // ==========================================================

    if (marginPercent) {

        marginPercent.addEventListener('input', function () {

            // Alterou margem
            // Calcula automaticamente preço de venda

            calculateSalePrice();

        });

    }


    // ==========================================================
    // EVENTOS - TIPO IVA
    // ==========================================================

    if (taxType) {

        taxType.addEventListener('change', function () {

            handleTaxTypeChange();

        });

    }


    // ==========================================================
    // EVENTOS - TAXA IVA
    // ==========================================================

    if (taxRate) {

        taxRate.addEventListener('input', function () {

            calculateSalePriceWithTax();

        });

    }


    // ==========================================================
    // INICIALIZAÇÃO
    // ==========================================================

    handleTaxTypeChange();

    calculateMargin();

    calculateSalePriceWithTax();

});
</script>
@endsection
