@extends('layouts.app')

@section('title', 'Nova factura')

@section('page_header')

<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Nova factura
        </h1>
        <div class="page-subtitle">
            Emita uma factura através do sistema.
        </div>
    </div>
    <div>
        <a href="{{ route('tenant.invoices.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>
            Voltar
        </a>
    </div>
</div>
@endsection
@section('content')
@if($errors->any())
<div class="alert alert-danger mb-4">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ $errors->first() }}
</div>
@endif
<form id="sale-form" method="POST" action="{{ route('tenant.invoices.store') }}" data-no-ajax>
    @csrf
    <div class="row">
        {{-- ========= ESQUERDA ========== --}}
        <div class="col-lg-8">
            {{-- DOCUMENTO --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Documento
                        </h3>
                        <div class="card-subtitle-modern">
                            Configure o tipo de documento.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">
                            Tipo
                        </label>
                        <select name="document_type" class="form-control">
                            <option value="invoice">Factura</option>
                            <option value="receipt">Recibo</option>
                            <option value="proforma">Factura Proforma</option>
                            <option value="quotation">Orçamento</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            Série
                        </label>
                        <input type="text" name="series" class="form-control" value="FT" maxlength="30">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            Moeda
                        </label>
                        <input type="text" class="form-control" value="AOA — Kz" disabled>
                    </div>
                </div>
            </div>
            {{-- CLIENTE --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Cliente
                        </h3>
                        <div class="card-subtitle-modern">
                            Selecione um cliente ou consumidor final.
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">
                        Cliente
                    </label>
                    <select name="client_id" class="form-control">
                        <option value="">
                            Consumidor final
                        </option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" @selected($client->nif === '999999999')>
                            {{ $client->name }} @if($client->nif) — {{ $client->nif }} @endif
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- PRODUTOS --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Itens da factura
                        </h3>
                        <div class="card-subtitle-modern">
                            Utilize produtos cadastrados ou adicione um item livre.
                        </div>
                    </div>
                </div>
                {{-- SEARCH --}}
                <div class="form-group">
                    <label class="form-label">
                        Adicionar produto
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-search input-icon"></i>
                        <input type="text" id="product-search" class="auth-input" placeholder="Nome, código ou código de barras..." autocomplete="off" style="padding-left:40px;">
                    </div>
                </div>
                <div id="product-results" style="
                    display:none;
                    border:1px solid #e2e8f0;
                    border-radius:10px;
                    background:#fff;
                    margin-bottom:20px;
                    overflow:hidden;
                "></div>


                {{-- ACTIONS --}}

                <div class="mb-3">
                    <button type="button" id="add-free-item" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Adicionar item livre
                    </button>
                </div>
                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>
                                    Produto / Descrição
                                </th>

                                <th width="120">
                                    Preço
                                </th>

                                <th width="130">
                                    Qtd.
                                </th>

                                <th width="120">
                                    IVA
                                </th>

                                <th width="140">
                                    Total
                                </th>

                                <th width="50">
                                </th>

                            </tr>

                        </thead>


                        <tbody id="cart-body">
                            <tr id="empty-cart">
                                <td colspan="6" class="text-center" style="padding:50px;color:#94a3b8;">
                                    <i class="fas fa-shopping-cart" style="
                                    font-size:30px;
                                    margin-bottom:12px;
                                    display:block;
                                "></i>
                                    Adicione produtos à factura.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- OBSERVAÇÕES --}}
            <div class="dashboard-card chart-card">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Observações
                        </h3>
                    </div>
                </div>
                <textarea name="notes" class="form-control" rows="4" placeholder="Observações da factura...">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- ========= DIREITA ========== --}}
        <div class="col-lg-4">
            {{-- RESUMO --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Resumo
                        </h3>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>
                        Subtotal
                    </span>
                    <strong id="summary-subtotal">
                        0,00 Kz
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>
                        Desconto
                    </span>
                    <strong id="summary-discount">
                        0,00 Kz
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>
                        IVA
                    </span>
                    <strong id="summary-tax">
                        0,00 Kz
                    </strong>
                </div>
                <div class="modern-divider"></div>
                <div class="d-flex justify-content-between">
                    <strong style="font-size:15px;">
                        Total
                    </strong>
                    <strong id="summary-total" style="
                        color:#2563eb;
                        font-size:20px;
                    ">
                        0,00 Kz
                    </strong>
                </div>
            </div>
            {{-- DESCONTO --}}
            <div class="dashboard-card chart-card mb-4">
                <label class="form-label">
                    Desconto global
                </label>
                <div class="input-wrapper">
                    <span style="
                        position:absolute;
                        left:14px;
                        top:50%;
                        transform:translateY(-50%);
                        color:#94a3b8;
                        z-index:2;
                    ">
                        Kz
                    </span>
                    <input type="number" min="0" step="0.01" id="global-discount" name="discount" value="0" class="auth-input" style="padding-left:40px;">
                </div>
            </div>
            {{-- PAGAMENTO --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Pagamento
                        </h3>
                        <div class="card-subtitle-modern">
                            Pode utilizar pagamento misto.
                        </div>
                    </div>
                </div>
                {{-- CASH --}}
                <div class="form-group">
                    <label class="form-label">
                        Numerário
                    </label>
                    <div class="input-wrapper">
                        <span style="
                            position:absolute;
                            left:14px;
                            top:50%;
                            transform:translateY(-50%);
                            color:#94a3b8;
                            z-index:2;
                        ">
                            Kz
                        </span>
                        <input type="number" step="0.01" min="0" id="payment-cash" class="auth-input payment-input" data-method="cash" value="0" style="padding-left:40px;">
                    </div>
                </div>
                {{-- MULTICAIXA --}}
                <div class="form-group">
                    <label class="form-label">
                        Multicaixa
                    </label>
                    <div class="input-wrapper">
                        <span style="
                            position:absolute;
                            left:14px;
                            top:50%;
                            transform:translateY(-50%);
                            color:#94a3b8;
                            z-index:2;
                        ">
                            Kz
                        </span>
                        <input type="number" step="0.01" min="0" id="payment-multicaixa" class="auth-input payment-input" data-method="multicaixa" value="0" style="padding-left:40px;">
                    </div>
                </div>
                <div id="multicaixa-reference-wrapper" class="form-group" style="display:none;">
                    <label class="form-label">
                        Referência Multicaixa
                    </label>
                    <input type="text" id="multicaixa-reference" class="auth-input" placeholder="Referência da operação">
                </div>
                {{-- TRANSFERÊNCIA --}}
                <div class="form-group">
                    <label class="form-label">
                        Transferência
                    </label>
                    <div class="input-wrapper">
                        <span style="
                            position:absolute;
                            left:14px;
                            top:50%;
                            transform:translateY(-50%);
                            color:#94a3b8;
                            z-index:2;
                        ">
                            Kz
                        </span>

                        <input type="number" step="0.01" min="0" id="payment-transfer" class="auth-input payment-input" data-method="transfer" value="0" style="padding-left:40px;">

                    </div>

                </div>


                {{-- TPA --}}

                <div class="form-group">

                    <label class="form-label">
                        TPA
                    </label>

                    <div class="input-wrapper">

                        <span style="
                            position:absolute;
                            left:14px;
                            top:50%;
                            transform:translateY(-50%);
                            color:#94a3b8;
                            z-index:2;
                        ">
                            Kz
                        </span>

                        <input type="number" step="0.01" min="0" id="payment-tpa" class="auth-input payment-input" data-method="tpa" value="0" style="padding-left:40px;">

                    </div>

                </div>


                {{-- CRÉDITO --}}

                <div class="form-group">

                    <label class="form-label">
                        Crédito
                    </label>

                    <div class="input-wrapper">

                        <span style="
                            position:absolute;
                            left:14px;
                            top:50%;
                            transform:translateY(-50%);
                            color:#94a3b8;
                            z-index:2;
                        ">
                            Kz
                        </span>

                        <input type="number" step="0.01" min="0" id="payment-credit" class="auth-input payment-input" data-method="credit" value="0" style="padding-left:40px;">

                    </div>

                </div>


                <div class="modern-divider"></div>


                <div class="d-flex justify-content-between mb-2">

                    <span>
                        Total pago
                    </span>

                    <strong id="paid-total">
                        0,00 Kz
                    </strong>

                </div>


                <div class="d-flex justify-content-between mb-2">

                    <span>
                        Troco
                    </span>

                    <strong id="change-total" style="color:#059669;">
                        0,00 Kz
                    </strong>

                </div>


                <div class="d-flex justify-content-between">

                    <span>
                        Em falta
                    </span>

                    <strong id="balance-total" style="color:#dc2626;">
                        0,00 Kz
                    </strong>

                </div>

            </div>


            {{-- SUBMIT --}}

            <button type="submit" id="submit-sale" class="btn btn-primary btn-lg w-100">

                <i class="fas fa-check-circle mr-1"></i>

                Emitir factura

            </button>

        </div>

    </div>

</form>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput =  document.getElementById('product-search');
        const results =  document.getElementById('product-results');
        const cartBody =  document.getElementById('cart-body');
        const emptyCart =  document.getElementById('empty-cart');
        const discountInput =  document.getElementById('global-discount');
        const form =  document.getElementById('sale-form');
        let cart = [];
        let searchTimeout = null;
        let submitting = false;

        /*
         * ========= * MONEY
         * ========= */

        function money(value) {
            return new Intl.NumberFormat(
                'pt-AO', {
                    minimumFractionDigits: 2
                    , maximumFractionDigits: 2
                }
            ).format(value) + ' Kz';
        }
        /*
         * ========= * ESCAPE
         * ========= */

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        }


        /*
         * ========= * SEARCH
         * =========*/

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const value = this.value.trim();
            if (!value) {
                results.style.display = 'none';
                results.innerHTML = '';
                return;
            }
            searchTimeout = setTimeout(() => searchProducts(value) , 250);
        });

        async function searchProducts(search) {
            try {
                const response =
                    await fetch(
                        `{{ route('tenant.invoices.products') }}?search=${encodeURIComponent(search)}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                const products = await response.json();
                renderResults(products);
            } catch (error) {
                console.error(error);
            }
        }
        /*
         * ========= * RESULTS
         * ========= */

        function renderResults(products) {

            results.innerHTML = '';

            if (!products.length) {

                results.innerHTML = `

                <div style="
                    padding:15px;
                    color:#94a3b8;
                    font-size:12px;
                ">

                    Nenhum produto encontrado.

                </div>

            `;

                results.style.display =
                    'block';

                return;
            }


            products.forEach(product => {

                const item =
                    document.createElement('div');

                item.style.cssText = `

                padding:12px 15px;

                border-bottom:
                    1px solid #f1f5f9;

                cursor:pointer;

                display:flex;

                justify-content:
                    space-between;

                align-items:center;

            `;


                const stockText =
                    product.manage_stock

                    ?
                    `Stock: ${product.stock} ${product.unit}`

                    :
                    'Stock não controlado';


                item.innerHTML = `

                <div>

                    <div style="
                        font-size:12px;
                        font-weight:700;
                        color:#1e293b;
                    ">

                        ${escapeHtml(product.name)}

                    </div>


                    <div style="
                        margin-top:3px;
                        font-size:10px;
                        color:#94a3b8;
                    ">

                        Código:
                        ${escapeHtml(product.code)}

                        ·

                        ${stockText}

                    </div>

                </div>


                <div style="
                    color:#2563eb;
                    font-size:12px;
                    font-weight:700;
                ">

                    ${money(product.price)}

                </div>

            `;


                item.addEventListener(
                    'click'
                    , function() {

                        addProduct(product);

                        searchInput.value =
                            '';

                        results.style.display =
                            'none';

                    }
                );
                results.appendChild(item);
            });
            results.style.display = 'block';
        }

        /*
         * ========= * ADD PRODUCT
         * ========= */

        function addProduct(product) {

            const existing =
                cart.find(
                    item =>
                    item.product_id ===
                    product.id
                );


            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({
                    product_id: product.id,
                    name: product.name,
                    code: product.code,
                    unit: product.unit,
                    quantity: 1,
                    unit_price: Number(product.price),
                    tax_rate: Number(product.tax_rate),
                    tax_type: product.tax_type,
                    discount: 0,
                    manage_stock: product.manage_stock,
                    stock: Number(product.stock || 0),
                    allow_negative_stock: product.allow_negative_stock
                });
            }
            renderCart();
            calculate();
        }
        /*
         * ========= * ITEM LIVRE
         * ========= */

        document.getElementById('add-free-item').addEventListener('click', function() {
            cart.push({
                product_id: null,
                name: 'Item livre',
                code: '',
                unit: 'UN',
                quantity: 1,
                unit_price: 0,
                tax_rate: 0,
                tax_type: 'zero',
                discount: 0,
                manage_stock: false,
                stock: 0,
                allow_negative_stock: true,
                free: true
            });
            renderCart();
            calculate();
        });

        /*
         * ========= * CART
         * ========= */

        function renderCart() {
            cartBody.innerHTML = '';
            if (!cart.length) {
                cartBody.appendChild(emptyCart);
                return;
            }
            cart.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>
                    <input
                        type="text"
                        class="form-control item-name"
                        data-index="${index}"
                        value="${escapeHtml(item.name)}"
                        ${item.product_id ? 'readonly' : ''}
                    >

                    ${
                        item.code ? ` <div style="
                                margin-top:3px;
                                font-size:10px;
                                color:#94a3b8;
                            ">${escapeHtml(item.code)}
                            </div>` : ''
                    }

                </td>
                <td>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        value="${item.unit_price}"
                        class="form-control item-price"
                        data-index="${index}"
                    >

                </td>
                <td>
                    <div
                        class="d-flex align-items-center"
                        style="gap:5px;"
                    >

                        <button
                            type="button"
                            class="btn btn-secondary btn-sm qty-minus"
                            data-index="${index}"
                        >
                            -
                        </button>


                        <input
                            type="number"
                            step="0.001"
                            min="0.001"
                            value="${item.quantity}"
                            class="form-control item-qty"
                            data-index="${index}"
                            style="text-align:center;"
                        >


                        <button
                            type="button"
                            class="btn btn-secondary btn-sm qty-plus"
                            data-index="${index}"
                        >
                            +
                        </button>

                    </div>

                </td>
                <td>

                    ${
                        item.tax_type === 'standard'
                            ? `${item.tax_rate}%`
                            : 'Isento / 0%'
                    }

                </td>
                <td>

                    <strong>

                        ${money(
                            item.quantity *
                            item.unit_price
                            -
                            item.discount
                        )}

                    </strong>

                </td>
                <td>
                    <button
                        type="button"
                        class="btn btn-outline-danger btn-sm remove-item"
                        data-index="${index}"
                    >

                        <i class="fas fa-trash"></i>

                    </button>

                </td>
            `;
                cartBody.appendChild(tr);
            });
            /*
             * NAME
             */
            document.querySelectorAll('.item-name').forEach(input => {
                input.addEventListener('input', function() {
                    const index = Number(this.dataset.index);
                    cart[index].name = this.value;
                });
            });
            /*
             * PRICE
             */
            document.querySelectorAll('.item-price').forEach(input => {
                input.addEventListener('input', function() {
                    const index = Number(this.dataset.index);
                    cart[index].unit_price = Number(this.value) || 0;
                    calculate();
                });
            });
            /*
             * QUANTITY
             */
            document.querySelectorAll('.item-qty').forEach(input => {
                input.addEventListener('input', function() {
                    const index =  Number(this.dataset.index);
                    cart[index].quantity = Number(this.value) || 0;
                    calculate();
                });
            });

            /*
             * PLUS
             */
            document.querySelectorAll('.qty-plus').forEach(button => {
                button.addEventListener('click', function() {
                    const index = Number(this.dataset.index);
                    cart[index].quantity += 1;
                    renderCart();
                    calculate();
                });
            });

            /*
             * MINUS
             */
            document.querySelectorAll('.qty-minus').forEach(button => {
                button.addEventListener('click', function() {
                    const index = Number(this.dataset.index);
                    cart[index].quantity -= 1;
                    if (cart[index].quantity <= 0) {
                        cart.splice(index, 1);
                    }
                    renderCart();
                    calculate();
                });
            });
            /*
             * REMOVE
             */
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const index =  Number(this.dataset.index);
                    cart.splice(index, 1);
                    renderCart();
                    calculate();
                });
            });
        }

        /*
         * ========= * CALCULATE
         * ========= */

        function calculate() {

            let subtotal = 0;

            let tax = 0;


            cart.forEach(item => {

                const gross =
                    item.quantity *
                    item.unit_price;

                subtotal += gross;


                if (
                    item.tax_type ===
                    'standard' &&
                    item.tax_rate > 0
                ) {

                    tax +=
                        gross -
                        (
                            gross /
                            (
                                1 +
                                item.tax_rate / 100
                            )
                        );

                }

            });


            let discount =
                Number(
                    discountInput.value
                ) || 0;


            if (
                discount > subtotal
            ) {

                discount =
                    subtotal;

            }


            const total =
                Math.max(
                    0
                    , subtotal - discount
                );


            document
                .getElementById(
                    'summary-subtotal'
                )
                .textContent =
                money(subtotal);


            document
                .getElementById(
                    'summary-discount'
                )
                .textContent =
                money(discount);


            document
                .getElementById(
                    'summary-tax'
                )
                .textContent =
                money(tax);


            document
                .getElementById(
                    'summary-total'
                )
                .textContent =
                money(total);


            calculatePayments(total);

        }


        discountInput.addEventListener(
            'input'
            , calculate
        );


        /*
         * ========= * PAYMENTS
         * ========= */

        document
            .querySelectorAll('.payment-input')
            .forEach(input => {

                input.addEventListener(
                    'input'
                    , calculate
                );

            });


        function calculatePayments(total) {
            let paid = 0;
            document.querySelectorAll('.payment-input').forEach(input => {
                paid +=  Number(input.value) ||  0;
            });

            const change = Math.max(0, paid - total);
            const balance = Math.max(0, total - paid);
            document.getElementById('paid-total').textContent =money(paid);
            document.getElementById('change-total').textContent =money(change);
            document.getElementById('balance-total').textContent =money(balance);
            const multicaixa = document.getElementById('payment-multicaixa');
            const wrapper = document.getElementById('multicaixa-reference-wrapper');

            wrapper.style.display = Number(multicaixa.value) > 0 ? 'block' : 'none';
        }


        /*
         * ========= * SUBMIT
         * ========= */

        form.addEventListener('submit' , function(event) {
            event.preventDefault();
            if (submitting) {
                return;
            }

            if (!cart.length) {
                alert('Adicione pelo menos um item à factura.');
                return;
            }

            const subtotal = cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            const discount = Number(discountInput.value) || 0;
            const total = Math.max(0, subtotal - discount );

            let paid = 0;

            document.querySelectorAll('.payment-input').forEach(input => {
                paid += Number(input.value) || 0;
            });

            /*
                * Crédito pode deixar saldo.
                */

            const credit = Number(document.getElementById('payment-credit').value) || 0;

            if (paid < total && credit <= 0) {
                alert('O valor pago é inferior ao total da factura.');
                return;
            }

            /*
                * Remover campos antigos.
                */

            form.querySelectorAll('.dynamic-sale-input').forEach(element => element.remove());
            /*
                * ITEMS
                */

            cart.forEach((item, index) => {
                addHidden(`items[${index}][product_id]`, item.product_id || '');
                addHidden(`items[${index}][name]`, item.name);
                addHidden(`items[${index}][quantity]`, item.quantity);
                addHidden(`items[${index}][unit_price]`, item.unit_price);
                addHidden(`items[${index}][discount]`, item.discount || 0);
                addHidden(`items[${index}][tax_rate]`, item.tax_rate || 0);
                addHidden(`items[${index}][tax_type]`, item.tax_type || 'zero');
            });

            /*
                * PAYMENTS
                */

            let paymentIndex = 0;

            document.querySelectorAll('.payment-input').forEach(input => {
                const amount = Number(input.value) || 0;

                if (amount <= 0) {
                    return;
                }
                addHidden(`payments[${paymentIndex}][method]`, input.dataset.method);
                addHidden(`payments[${paymentIndex}][amount]` , amount);
                if (input.dataset.method === 'multicaixa') {
                    addHidden(`payments[${paymentIndex}][reference]`, document.getElementById('multicaixa-reference').value);
                }
                paymentIndex++;
            });

            if (!paymentIndex) {
                alert('Informe pelo menos uma forma de pagamento.');
                return;
            }

            submitting = true;
            const button = document.getElementById('submit-sale');
            button.disabled = true;
            button.innerHTML = `
                <i class=" fas fa-spinner fa-spin"></i>
                Processando factura...
            `;
            form.submit();
        });

        /*
         * ========= * HIDDEN
         * ========= */

        function addHidden(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            input.className = 'dynamic-sale-input';
            form.appendChild(input);
        }
        /*
         * INITIAL
         */
        renderCart();
        calculate();
    });
</script>
@endpush
