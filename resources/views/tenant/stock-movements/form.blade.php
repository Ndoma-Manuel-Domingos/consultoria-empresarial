@extends('layouts.app')
@section('title', 'Novo movimento de stock')
@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Novo movimento
        </h1>
        <div class="page-subtitle">
            Registe uma entrada, saída ou ajuste de stock.
        </div>
    </div>
    <div>
        <a href="{{ route('tenant.stock-movements.index') }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        <button type="submit" form="stock-movement-form" class="btn btn-primary btn-sm">
            <i class="fas fa-save"></i>
            Registar movimento
        </button>
    </div>
</div>

@endsection
@section('content')
<form id="stock-movement-form" method="POST" action="{{ route('tenant.stock-movements.store') }}">
    @csrf
    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            {{ $errors->first() }}
        </div>
    </div>
    @endif

    <div class="row">
        {{-- LEFT --}}
        <div class="col-lg-8">
            {{-- MOVIMENTO --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Dados do movimento
                        </h3>
                        <div class="card-subtitle-modern">
                            Defina o produto e a operação a realizar.
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- PRODUTO --}}
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label required">
                                Produto
                            </label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">
                                    Selecionar produto
                                </option>
                                @foreach($products as $product)
                                <option 
                                    value="{{ $product->id }}" 
                                    data-cost="{{ $product->cost_price }}" 
                                    data-unit="{{ $product->unit }}" 
                                    data-manage-stock="{{ $product->manage_stock ? 1 : 0 }}"
                                    data-manage-lots="{{ $product->manage_lots ? 1 : 0 }}"
                                    data-has-expiration="{{ $product->has_expiration ? 1 : 0 }}"
                                    
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                    @if($product->code)
                                    — {{ $product->code }}
                                    @endif
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

                    
                    {{-- TIPO --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label required">
                                Tipo
                            </label>
                            <select name="type" id="movement_type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Selecionar</option>
                                {{-- Entradas --}}
                                <option value="in" {{ old('type') === 'in' ? 'selected' : '' }}>Entrada</option>
                                <option value="purchase" {{ old('type') === 'purchase' ? 'selected' : '' }}>Compra</option>
                                {{-- <option value="adjustment_in" {{ old('type') === 'adjustment_in' ? 'selected' : '' }}>Ajuste de Entrada</option> --}}
                                <option value="return_in" {{ old('type') === 'return_in' ? 'selected' : '' }}>Devolução de Cliente</option>
                                <option value="transfer_in" {{ old('type') === 'transfer_in' ? 'selected' : '' }}>Transferência de Entrada</option>
                                {{-- Saídas --}}
                                <option value="out" {{ old('type') === 'out' ? 'selected' : '' }}>Saída</option>
                                <option value="sale" {{ old('type') === 'sale' ? 'selected' : '' }}>Venda</option>
                                {{-- <option value="adjustment_out" {{ old('type') === 'adjustment_out' ? 'selected' : '' }}>Ajuste de Saída</option> --}}
                                <option value="return_out" {{ old('type') === 'return_out' ? 'selected' : '' }}>Devolução ao Fornecedor</option>
                                <option value="transfer_out" {{ old('type') === 'transfer_out' ? 'selected' : '' }}>Transferência de Saída</option>

                                
                                <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>Ajuste</option>

                            </select>
                            @error('type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    {{-- LOTE --}}
                    <div class="col-md-4" id="product-lot-wrapper" style="display:none;">
                        <div class="form-group">
                            <label class="form-label required" for="product_lot_id">
                                Lote
                            </label>
                            <select name="product_lot_id" id="product_lot_id" class="form-select @error('product_lot_id') is-invalid @enderror">
                                <option value="">
                                    Selecionar lote
                                </option>
                            </select>
                            @error('product_lot_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div id="lot-help" class="mt-2" style="display:none;">
                                <div style="padding:9px 11px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:10px;color:#64748b;">
                                    <div class="d-flex justify-content-between">
                                        <span>Stock disponível</span>
                                        <strong id="lot-current-stock">0,000</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1" id="lot-expiration-row">
                                        <span>Validade</span>
                                        <strong id="lot-expiration">—</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- QUANTIDADE --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label required">
                                Quantidade
                            </label>
                            <input type="number" name="quantity" id="quantity" class="auth-input @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" step="0.001" min="0.001" placeholder="0,000" required>
                            @error('quantity')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    {{-- CUSTO --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                Custo unitário
                            </label>
                            <div class="input-wrapper">
                                <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:11px;z-index:2;">
                                    Kz
                                </span>
                                <input type="number" name="unit_cost" id="unit_cost" class="auth-input" value="{{ old('unit_cost') }}" step="0.01" min="0" placeholder="0,00" style="padding-left:40px;">
                            </div>
                        </div>
                    </div>
                    {{-- DATA --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label required">
                                Data do movimento
                            </label>
                            <input type="datetime-local" name="movement_date" class="auth-input" value="{{ old('movement_date', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    </div>
                    {{-- AJUSTE --}}
                    <div class="col-md-6" id="adjustment-type-wrapper" style="{{ old('type') === 'adjustment' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label class="form-label">
                                Tipo de ajuste
                            </label>
                            <select name="adjustment_type" class="form-select">
                                <option value="increase" {{ old('adjustment_type') === 'increase' ? 'selected' : '' }}>
                                    Aumentar stock
                                </option>
                                <option value="decrease" {{ old('adjustment_type') === 'decrease' ? 'selected' : '' }}>
                                    Reduzir stock
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            {{-- DOCUMENTAÇÃO --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Documento e referência
                        </h3>
                        <div class="card-subtitle-modern">
                            Associe o movimento a uma compra, venda ou documento.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                Tipo de documento
                            </label>
                            <select name="document_type" class="form-select">
                                <option value="">Selecionar</option>
                                <option value="purchase" {{ old('document_type') === 'purchase' ? 'selected' : '' }}>Compra</option>
                                <option value="sale" {{ old('document_type') === 'sale' ? 'selected' : '' }}>Venda</option>
                                <option value="inventory" {{ old('document_type') === 'inventory' ? 'selected' : '' }}>Inventário</option>
                                <option value="adjustment" {{ old('document_type') === 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                                <option value="other" {{ old('document_type') === 'other' ? 'selected' : '' }}>Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                Nº documento
                            </label>
                            <input type="text" name="document_number" class="auth-input" value="{{ old('document_number') }}" placeholder="Ex.: FT 2026/001">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                Referência
                            </label>
                            <input type="text" name="reference" class="auth-input" value="{{ old('reference') }}" placeholder="Referência interna">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label class="form-label">
                                Motivo
                            </label>
                            <input type="text" name="reason" class="auth-input" value="{{ old('reason') }}" placeholder="Motivo do movimento...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- NOTES --}}
            <div class="dashboard-card chart-card">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Observações
                        </h3>
                    </div>
                </div>
                <textarea name="notes" class="form-control" rows="4" placeholder="Observações adicionais...">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-lg-4">
            {{-- RESUMO --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>

                        <h3 class="card-title-modern">
                            Resumo
                        </h3>

                        <div class="card-subtitle-modern">
                            Informação da operação.
                        </div>

                    </div>

                </div>
                <div class="text-center py-3">
                    <div id="movement-icon" class="stat-icon blue mx-auto mb-3" style="width:60px;height:60px;font-size:22px;">
                        <i class="fas fa-box"></i>
                    </div>
                    <div id="movement-label" style="font-size:13px;font-weight:700;color:#334155;">
                        Novo movimento
                    </div>
                    <div id="movement-description" class="mt-1" style="color:#94a3b8;font-size:10px;">
                        Selecione o tipo de operação.
                    </div>
                </div>
                <div class="modern-divider"></div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            Produto
                        </div>
                        <div id="summary-product" class="activity-time">
                            —
                        </div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            Quantidade
                        </div>
                        <div id="summary-quantity" class="activity-time">
                            0,000
                        </div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            Valor total
                        </div>
                        <div id="summary-total" class="activity-time">
                            0,00 Kz
                        </div>
                    </div>
                </div>
            </div>
            {{-- AVISO --}}
            <div class="dashboard-card chart-card">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Atenção
                        </h3>
                    </div>
                </div>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i>
                    <span>
                        Os movimentos registados fazem parte do histórico
                        permanente do stock.
                    </span>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const product = document.getElementById('product_id');
    const type = document.getElementById('movement_type');
    const quantity = document.getElementById('quantity');
    const unitCost = document.getElementById('unit_cost');

    const lotWrapper = document.getElementById('product-lot-wrapper');
    const lotSelect = document.getElementById('product_lot_id');
    const lotHelp = document.getElementById('lot-help');

    const lotCurrentStock = document.getElementById('lot-current-stock');
    const lotExpiration = document.getElementById('lot-expiration');
    const lotExpirationRow = document.getElementById('lot-expiration-row');

    const adjustmentWrapper =
        document.getElementById('adjustment-type-wrapper');

    const movementIcon =
        document.getElementById('movement-icon');

    const movementLabel =
        document.getElementById('movement-label');

    const movementDescription =
        document.getElementById('movement-description');

    const summaryProduct =
        document.getElementById('summary-product');

    const summaryQuantity =
        document.getElementById('summary-quantity');

    const summaryTotal =
        document.getElementById('summary-total');


    /*
    |--------------------------------------------------------------------------
    | CONFIGURAÇÃO
    |--------------------------------------------------------------------------
    */

    const oldProductId = "{{ old('product_id', '') }}";
    const oldLotId = "{{ old('product_lot_id', '') }}";


    /*
    |--------------------------------------------------------------------------
    | FORMATAR NÚMEROS
    |--------------------------------------------------------------------------
    */

    function formatQuantity(value) {

        value = parseFloat(value || 0);

        return value.toLocaleString('pt-PT', {
            minimumFractionDigits: 3,
            maximumFractionDigits: 3
        });
    }


    function formatMoney(value) {

        value = parseFloat(value || 0);

        return value.toLocaleString('pt-PT', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }


    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR PRODUTO
    |--------------------------------------------------------------------------
    */

    function updateProduct() {

        const selected =
            product.options[product.selectedIndex];

        if (!selected || !selected.value) {

            summaryProduct.textContent = '—';

            lotWrapper.style.display = 'none';

            lotSelect.innerHTML =
                '<option value="">Selecionar lote</option>';

            lotHelp.style.display = 'none';

            updateTotal();

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | RESUMO
        |--------------------------------------------------------------------------
        */

        summaryProduct.textContent =
            selected.text.trim();


        /*
        |--------------------------------------------------------------------------
        | CUSTO
        |--------------------------------------------------------------------------
        */

        const cost =
            parseFloat(selected.dataset.cost || 0);

        if (!unitCost.value && cost > 0) {

            unitCost.value =
                cost.toFixed(2);
        }


        /*
        |--------------------------------------------------------------------------
        | STOCK / LOTES
        |--------------------------------------------------------------------------
        */

        const manageStock =
            selected.dataset.manageStock === '1';

        const manageLots =
            selected.dataset.manageLots === '1';


        /*
        |--------------------------------------------------------------------------
        | PRODUTO COM CONTROLE DE LOTES
        |--------------------------------------------------------------------------
        */

        if (manageStock && manageLots) {

            lotWrapper.style.display = '';

            loadProductLots(selected.value);

        } else {

            lotWrapper.style.display = 'none';

            lotSelect.innerHTML =
                '<option value="">Este produto não utiliza lotes</option>';

            lotHelp.style.display = 'none';
        }


        updateTotal();
    }


    /*
    |--------------------------------------------------------------------------
    | CARREGAR LOTES
    |--------------------------------------------------------------------------
    */

    function loadProductLots(productId) {

        if (!productId) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | LOADING
        |--------------------------------------------------------------------------
        */

        lotSelect.innerHTML =
            '<option value="">A carregar lotes...</option>';

        lotSelect.disabled = true;

        lotHelp.style.display = 'none';


        /*
        |--------------------------------------------------------------------------
        | URL
        |--------------------------------------------------------------------------
        |
        | Cria esta rota no Laravel:
        |
        | tenant.stock-movements.product-lots
        |
        */

        const url = "{{ route('tenant.stock-movements.product-lots', ':product') }}".replace(':product', productId);
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(
                    'Erro ao carregar os lotes.'
                );
            }
            return response.json();
        })
        .then(data => {
            lotSelect.innerHTML =
                '<option value="">Selecionar lote</option>';
            if (!data.lots || data.lots.length === 0) {
                lotSelect.innerHTML =
                    '<option value="">Nenhum lote disponível</option>';
                lotSelect.disabled = true;
                lotHelp.style.display = 'none';
                return;
            }
            /*
            |--------------------------------------------------------------------------
            | TIPO DE MOVIMENTO
            |--------------------------------------------------------------------------
            */
            const movementType =
                type.value;
            data.lots.forEach(function (lot) {
                /*
                |--------------------------------------------------------------------------
                | SAÍDA
                |--------------------------------------------------------------------------
                |
                | Na saída não devemos permitir lote sem stock.
                |
                */

                if (movementType === 'out' &&parseFloat(lot.current_quantity) <= 0) {
                    return;
                }
                /*
                |--------------------------------------------------------------------------
                | AJUSTE
                |--------------------------------------------------------------------------
                */

                const option = document.createElement('option');
                option.value = lot.id;

                /*
                |--------------------------------------------------------------------------
                | TEXTO
                |--------------------------------------------------------------------------
                */

                let text = 'Lote ' + lot.lot_number;

                text += ' — Stock: ' + formatQuantity(lot.current_quantity);

                /*
                |--------------------------------------------------------------------------
                | VALIDADE
                |--------------------------------------------------------------------------
                */

                if (lot.expires_at) {

                    text +=
                        ' — Val.: ' +
                        formatDate(lot.expires_at);
                }


                option.textContent =
                    text;


                /*
                |--------------------------------------------------------------------------
                | DATA
                |--------------------------------------------------------------------------
                */

                option.dataset.currentQuantity =
                    lot.current_quantity || 0;

                option.dataset.expiresAt =
                    lot.expires_at || '';

                option.dataset.costPrice =
                    lot.cost_price || '';


                /*
                |--------------------------------------------------------------------------
                | LOTES EXPIRADOS
                |--------------------------------------------------------------------------
                */

                if (lot.is_expired) {

                    option.disabled = true;

                    option.textContent +=
                        ' — EXPIRADO';
                }


                /*
                |--------------------------------------------------------------------------
                | LOTES PRÓXIMOS DA VALIDADE
                |--------------------------------------------------------------------------
                */

                if (lot.is_expiring_soon) {

                    option.textContent +=
                        ' — A expirar';
                }


                lotSelect.appendChild(option);

            });


            lotSelect.disabled = false;


            /*
            |--------------------------------------------------------------------------
            | RESTAURAR OLD
            |--------------------------------------------------------------------------
            */

            if (oldLotId) {

                const oldOption =
                    lotSelect.querySelector(
                        'option[value="' + oldLotId + '"]'
                    );

                if (oldOption) {

                    oldOption.selected = true;

                    updateLot();
                }
            }

        })

        .catch(error => {

            console.error(error);

            lotSelect.innerHTML =
                '<option value="">Erro ao carregar lotes</option>';

            lotSelect.disabled = true;

        });

    }


    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR LOTE
    |--------------------------------------------------------------------------
    */

    function updateLot() {

        const selected =
            lotSelect.options[lotSelect.selectedIndex];


        if (
            !selected ||
            !selected.value
        ) {

            lotHelp.style.display = 'none';

            lotCurrentStock.textContent =
                '0,000';

            lotExpiration.textContent =
                '—';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | STOCK
        |--------------------------------------------------------------------------
        */

        const currentStock =
            parseFloat(
                selected.dataset.currentQuantity || 0
            );


        lotCurrentStock.textContent =
            formatQuantity(currentStock);


        /*
        |--------------------------------------------------------------------------
        | VALIDADE
        |--------------------------------------------------------------------------
        */

        const expiresAt =
            selected.dataset.expiresAt;


        if (expiresAt) {

            lotExpiration.textContent =
                formatDate(expiresAt);

            lotExpirationRow.style.display =
                '';

        } else {

            lotExpiration.textContent =
                'Sem validade';

            lotExpirationRow.style.display =
                '';
        }


        /*
        |--------------------------------------------------------------------------
        | CUSTO DO LOTE
        |--------------------------------------------------------------------------
        */

        const lotCost =
            parseFloat(
                selected.dataset.costPrice || 0
            );


        /*
        |--------------------------------------------------------------------------
        | ENTRADA
        |--------------------------------------------------------------------------
        |
        | Se o lote tiver custo definido,
        | podemos preencher automaticamente.
        |
        */

        if (
            type.value === 'in' &&
            lotCost > 0
        ) {

            unitCost.value =
                lotCost.toFixed(2);

        }


        /*
        |--------------------------------------------------------------------------
        | MOSTRAR INFORMAÇÃO
        |--------------------------------------------------------------------------
        */

        lotHelp.style.display = '';


        /*
        |--------------------------------------------------------------------------
        | VALIDAR QUANTIDADE PARA SAÍDA
        |--------------------------------------------------------------------------
        */

        validateQuantity();

        updateTotal();
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATAR DATA
    |--------------------------------------------------------------------------
    */

    function formatDate(date) {

        if (!date) {
            return '—';
        }

        const parts =
            date.split('-');

        if (parts.length !== 3) {
            return date;
        }

        return (
            parts[2] +
            '/' +
            parts[1] +
            '/' +
            parts[0]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR QUANTIDADE
    |--------------------------------------------------------------------------
    */

    function validateQuantity() {

        const movementType =
            type.value;

        const qty =
            parseFloat(quantity.value || 0);


        /*
        |--------------------------------------------------------------------------
        | LIMPAR
        |--------------------------------------------------------------------------
        */

        quantity.classList.remove(
            'is-invalid'
        );


        const existingError =
            document.getElementById(
                'stock-quantity-warning'
            );

        if (existingError) {

            existingError.remove();
        }


        /*
        |--------------------------------------------------------------------------
        | SOMENTE SAÍDA
        |--------------------------------------------------------------------------
        */

        if (
            movementType !== 'out'
        ) {
            return true;
        }


        /*
        |--------------------------------------------------------------------------
        | LOTE
        |--------------------------------------------------------------------------
        */

        const selectedLot =
            lotSelect.options[
                lotSelect.selectedIndex
            ];


        if (
            !selectedLot ||
            !selectedLot.value
        ) {
            return true;
        }


        const available =
            parseFloat(
                selectedLot.dataset.currentQuantity || 0
            );


        /*
        |--------------------------------------------------------------------------
        | QUANTIDADE MAIOR QUE STOCK
        |--------------------------------------------------------------------------
        */

        if (
            qty > available
        ) {

            quantity.classList.add(
                'is-invalid'
            );


            const warning =
                document.createElement('div');

            warning.id =
                'stock-quantity-warning';

            warning.className =
                'invalid-feedback';

            warning.style.display =
                'block';

            warning.textContent =
                'A quantidade não pode ser superior ao stock disponível do lote (' +
                formatQuantity(available) +
                ').';


            quantity.parentNode.appendChild(
                warning
            );


            return false;
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

    function updateTotal() {

        const qty =
            parseFloat(
                quantity.value || 0
            );

        const cost =
            parseFloat(
                unitCost.value || 0
            );

        const total =
            qty * cost;


        /*
        |--------------------------------------------------------------------------
        | QUANTIDADE
        |--------------------------------------------------------------------------
        */

        summaryQuantity.textContent =
            formatQuantity(qty);


        /*
        |--------------------------------------------------------------------------
        | VALOR
        |--------------------------------------------------------------------------
        */

        summaryTotal.textContent =
            formatMoney(total) +
            ' Kz';


        validateQuantity();
    }


    /*
    |--------------------------------------------------------------------------
    | TIPO DO MOVIMENTO
    |--------------------------------------------------------------------------
    */

    function updateType() {

        const value =
            type.value;


        /*
        |--------------------------------------------------------------------------
        | AJUSTE
        |--------------------------------------------------------------------------
        */

        adjustmentWrapper.style.display =
            value === 'adjustment'
                ? ''
                : 'none';


        /*
        |--------------------------------------------------------------------------
        | ENTRADA
        |--------------------------------------------------------------------------
        */

        if (value === 'in') {

            movementIcon.className =
                'stat-icon green mx-auto mb-3';

            movementIcon.innerHTML =
                '<i class="fas fa-arrow-down"></i>';

            movementLabel.textContent =
                'Entrada de stock';

            movementDescription.textContent =
                'A quantidade será adicionada ao stock.';

        }


        /*
        |--------------------------------------------------------------------------
        | SAÍDA
        |--------------------------------------------------------------------------
        */

        else if (value === 'out') {

            movementIcon.className =
                'stat-icon red mx-auto mb-3';

            movementIcon.innerHTML =
                '<i class="fas fa-arrow-up"></i>';

            movementLabel.textContent =
                'Saída de stock';

            movementDescription.textContent =
                'A quantidade será retirada do stock usando FIFO.';

        }


        /*
        |--------------------------------------------------------------------------
        | AJUSTE
        |--------------------------------------------------------------------------
        */

        else if (value === 'adjustment') {
            movementIcon.className = 'stat-icon orange mx-auto mb-3';
            movementIcon.innerHTML = '<i class="fas fa-sliders-h"></i>';
            movementLabel.textContent = 'Ajuste de stock';
            movementDescription.textContent = 'Corrija diferenças encontradas no inventário.';

        }


        /*
        |--------------------------------------------------------------------------
        | TRANSFERÊNCIA
        |--------------------------------------------------------------------------
        */

        else if (value === 'transfer_in') {
            movementIcon.className = 'stat-icon blue mx-auto mb-3';
            movementIcon.innerHTML = '<i class="fas fa-exchange-alt"></i>';
            movementLabel.textContent = 'Transferência';
            movementDescription.textContent = 'Movimento entre localizações.';
        }

        /*
        |--------------------------------------------------------------------------
        | NENHUM
        |--------------------------------------------------------------------------
        */

        else {
            movementIcon.className = 'stat-icon blue mx-auto mb-3';
            movementIcon.innerHTML = '<i class="fas fa-box"></i>';
            movementLabel.textContent = 'Novo movimento';
            movementDescription.textContent = 'Selecione o tipo de operação.';
        }


        /*
        |--------------------------------------------------------------------------
        | RECARREGAR LOTES
        |--------------------------------------------------------------------------
        */

        const selected =
            product.options[
                product.selectedIndex
            ];


        if (
            selected &&
            selected.value &&
            selected.dataset.manageStock === '1' &&
            selected.dataset.manageLots === '1'
        ) {

            loadProductLots(
                selected.value
            );
        }


        updateTotal();
    }


    /*
    |--------------------------------------------------------------------------
    | EVENTOS
    |--------------------------------------------------------------------------
    */

    product.addEventListener(
        'change',
        updateProduct
    );


    type.addEventListener(
        'change',
        updateType
    );


    lotSelect.addEventListener(
        'change',
        updateLot
    );


    quantity.addEventListener(
        'input',
        updateTotal
    );


    unitCost.addEventListener(
        'input',
        updateTotal
    );


    /*
    |--------------------------------------------------------------------------
    | SUBMIT
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('stock-movement-form')
        .addEventListener('submit', function (event) {

            if (!validateQuantity()) {

                event.preventDefault();

                quantity.focus();

                return false;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDAÇÃO DO LOTE
            |--------------------------------------------------------------------------
            */

            const selected =
                product.options[
                    product.selectedIndex
                ];


            if (
                selected &&
                selected.value &&
                selected.dataset.manageStock === '1' &&
                selected.dataset.manageLots === '1'
            ) {

                const movementType =
                    type.value;


                /*
                | Para produtos controlados por lote,
                | exigimos lote na entrada/saída/ajuste.
                */

                if (
                    (
                        movementType === 'in' ||
                        movementType === 'out' ||
                        movementType === 'adjustment'
                    ) &&
                    !lotSelect.value
                ) {

                    event.preventDefault();

                    alert(
                        'Este produto possui controlo por lote. Selecione o lote antes de continuar.'
                    );

                    lotSelect.focus();

                    return false;
                }
            }

        });


    /*
    |--------------------------------------------------------------------------
    | INICIALIZAÇÃO
    |--------------------------------------------------------------------------
    */

    updateProduct();

    updateType();

    updateTotal();

});
</script>


@endsection
