@extends('layouts.app')

@section('title', 'Nova venda')
@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Nova venda
        </h1>
        <div class="page-subtitle">
            Registe uma venda através do terminal POS.
        </div>
    </div>
    <div>
        <a href="{{ route('tenant.sales.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
    </div>
</div>
@endsection

@section('content')
@if($errors->any())
<div class="alert alert-danger mb-4">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        {{ $errors->first() }}
    </div>
</div>
@endif

<form id="sale-form" method="POST" action="{{ route('tenant.sales.store') }}" data-no-ajax>
@csrf
<div class="row">
    {{-- === POS ==== --}}
    <div class="col-lg-8">
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Produtos
                    </h3>
                    <div class="card-subtitle-modern">
                        Pesquise por nome, código ou código de barras.
                    </div>
                </div>
            </div>
            {{-- SEARCH --}}
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-search input-icon"></i>
                    <input type="text" id="product-search" class="auth-input" placeholder="Pesquisar produto..." autocomplete="off" style="padding-left:40px;">
                </div>
            </div>
            {{-- RESULTS --}}
            <div id="product-results" style="display:none;border:1px solid #e2e8f0;border-radius:10px;background:#fff;margin-bottom:20px;overflow:hidden;"></div>

            {{-- CART --}}
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                    <tr>
                        <th>Produto</th>
                        <th width="110">Preço</th>
                        <th width="130">Quantidade</th>
                        <th width="130">Total</th>
                        <th width="50"></th>
                    </tr>
                    </thead>
                    <tbody id="cart-body">
                    <tr id="empty-cart">
                        <td colspan="5" class="text-center" style="padding:50px;color:#94a3b8;">
                            <i class="fas fa-shopping-cart" style="font-size:30px;margin-bottom:12px;display:block;"></i>
                            Adicione produtos à venda.
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
            <textarea name="notes" class="form-control" placeholder="Observações da venda...">{{ old('notes') }}</textarea>
        </div>
    </div>
    {{-- ====  PAGAMENTO ====== --}}
    <div class="col-lg-4">
        {{-- CLIENTE --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">Cliente</h3>
                </div>
            </div>
            <div class="form-group mb-0">
                <label class="form-label">Cliente</label>
                <select name="client_id" class="form-select">
                    <option value="">Cliente consumidor final</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" @selected($client->nif == '999999999')>
                            {{ $client->name }}
                            @if($client->nif) — {{ $client->nif }}@endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

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
                <strong id="summary-total" style="color:#2563eb;font-size:20px;">
                    0,00 Kz
                </strong>
            </div>
        </div>

        {{-- DESCONTO GLOBAL --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="form-group mb-0">
                <label class="form-label">
                    Desconto global
                </label>
                <div class="input-wrapper">
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;z-index:2;">
                        Kz
                    </span>
                    <input type="number" min="0" step="0.01" id="global-discount" name="discount" value="0" class="auth-input" style="padding-left:40px;">
                </div>
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
            {{-- NUMERÁRIO --}}
            <div class="form-group">
                <label class="form-label">
                    Numerário
                </label>
                <div class="input-wrapper">
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;z-index:2;">
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
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;z-index:2;">
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

            {{-- TOTAL PAGO --}}
            <div class="d-flex justify-content-between mb-2">
                <span>
                    Total pago
                </span>
                <strong id="paid-total">
                    0,00 Kz
                </strong>
            </div>

            {{-- TROCO --}}
            <div class="d-flex justify-content-between">
                <span>
                    Troco
                </span>
                <strong id="change-total" style="color:#059669;">
                    0,00 Kz
                </strong>
            </div>
        </div>
        {{-- SUBMIT --}}
        <button type="submit" id="submit-sale" class="btn btn-primary btn-lg w-100">
            <i class="fas fa-check-circle"></i>
            Finalizar venda
        </button>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
    console.log('VENDA: DOM carregado');
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('product-search');
    const results = document.getElementById('product-results');
    const cartBody = document.getElementById('cart-body');
    const emptyCart = document.getElementById('empty-cart');
    const discountInput = document.getElementById('global-discount');
    const form = document.getElementById('sale-form');
    let cart = [];
    let searchTimeout = null;

    /*
     * ======================================================
     * MONEY
     * ======================================================
     */

    function money(value) {
        return new Intl.NumberFormat('pt-AO',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        ).format(value) + ' Kz';
    }


    /*
     * ======================================================
     * SEARCH
     * ======================================================
     */

    searchInput.addEventListener( 'input', function () {
        clearTimeout(searchTimeout);
        const value = this.value.trim();
        if (!value) {
            results.style.display = 'none';
            results.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => searchProducts(value), 250);
    });


    async function searchProducts(search) {
        try {
            const response = await fetch(`{{ route('tenant.sales.products') }}?search=${encodeURIComponent(search)}`,
                {
                    headers: { 'Accept': 'application/json'}
                }
            );
            const products = await response.json();
            renderResults(products);
        } catch (error) {
            console.error(error);
        }
    }

    /*
     * ======================================================
     * RESULTS
     * ======================================================
     */
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
            results.style.display = 'block';
            return;
        }

        products.forEach(product => {
            const item = document.createElement('div');

            item.style.cssText = `
                padding:12px 15px;
                border-bottom:1px solid #f1f5f9;
                cursor:pointer;
                display:flex;
                justify-content:space-between;
                align-items:center;
            `;

            const stockText = product.manage_stock ? `Stock: ${product.stock} ${product.unit}` : 'Stock não controlado';

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
                        Código: ${escapeHtml(product.code)}
                        · ${stockText}
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


            item.addEventListener('click', function () {
                addProduct(product);
                searchInput.value = '';
                results.style.display = 'none';
            });
            results.appendChild(item);
        });
        results.style.display = 'block';
    }

    /*
     * ======================================================
     * ADD PRODUCT
     * ======================================================
     */

    function addProduct(product) {
        const existing = cart.find(item => item.product_id === product.id);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                product_id:  product.id,
                name:  product.name,
                code:  product.code,
                unit:  product.unit,
                quantity:  1,
                unit_price:  Number(product.price),
                tax_rate:  Number(product.tax_rate),
                tax_type:  product.tax_type,
                discount:  0,
                manage_lots:  product.manage_lots,
                stock:  Number(product.stock),
                allow_negative_stock:  product.allow_negative_stock
            });
        }
        renderCart();
        calculate();
    }

    /*
     * ======================================================
     * CART
     * ======================================================
     */

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
                        <div style="
                            font-weight:600;
                            color:#1e293b;
                        ">
                            ${escapeHtml(item.name)}
                        </div>

                        <div style="
                            margin-top:3px;
                            font-size:10px;
                            color:#94a3b8;
                        ">
                            ${escapeHtml(item.code)}
                        </div>
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

                        <strong>
                            ${money(
                                item.quantity *
                                item.unit_price
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
            }
        );
        /*
         * PRICE
         */
        document.querySelectorAll('.item-price').forEach(input => {
            input.addEventListener('input', function () {
                const index =  Number(this.dataset.index);
                cart[index].unit_price =  Number(this.value) || 0;
                calculate();
                renderCart();
            });
        });


        /*
         * QUANTITY
         */
        document.querySelectorAll('.item-qty').forEach(input => {
            input.addEventListener('input', function () {
                const index = Number(this.dataset.index);
                cart[index].quantity = Number(this.value) || 0;
                calculate();
                renderCart();

            });
        });


        /*
         * PLUS
         */
        document.querySelectorAll('.qty-plus').forEach(button => {
            button.addEventListener('click', function () {
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
            button.addEventListener('click', function () {
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
            button.addEventListener('click', function () {
                const index = Number(this.dataset.index);
                cart.splice(index, 1);
                renderCart();
                calculate();
            });
        });
    }

    function calculate() {
        let subtotal = 0;
        let tax = 0;

        cart.forEach(item => {
            const line = item.quantity * item.unit_price;
            subtotal += line;
            if ( item.tax_type === 'standard' && item.tax_rate > 0) {
                tax += line - (line / (1 + item.tax_rate / 100));
            }
        });

        let discount = Number(discountInput.value) || 0;

        if (discount > subtotal) {
            discount = subtotal;
        }

        const total =  subtotal - discount;
        document.getElementById('summary-subtotal').textContent = money(subtotal);
        document.getElementById('summary-discount').textContent = money(discount);
        document.getElementById('summary-tax').textContent = money(tax);
        document.getElementById('summary-total').textContent =  money(total);
        calculatePayments(total);
    }

    discountInput.addEventListener('input',calculate);

    /*
     * ======================================================
     * PAYMENTS
     * ======================================================
     */
    document.querySelectorAll('.payment-input').forEach(input => {
        input.addEventListener( 'input', function () {
            const multicaixa = document.getElementById('payment-multicaixa');
            const wrapper = document.getElementById('multicaixa-reference-wrapper');
            if (Number(multicaixa.value) > 0) {
                wrapper.style.display =  'block';
            } else {
                wrapper.style.display = 'none';
            }
            calculate();
        });
    });


    function calculatePayments(total) {
        const cash = Number(document.getElementById('payment-cash').value) || 0;
        const multicaixa = Number(document.getElementById('payment-multicaixa').value) || 0;
        const paid = cash + multicaixa;
        const change = Math.max(0, paid - total);
        document.getElementById('paid-total').textContent = money(paid);
        document.getElementById('change-total').textContent = money(change);
    }
    /*
     * ======================================================
     * SUBMIT
     * ======================================================
     */

    let submitting = false;
        console.log('VENDA: listener submit criado');
    form.addEventListener('submit', function (event) {

        console.log('VENDA: SUBMIT DISPARADO', new Date().toISOString());

        event.preventDefault();

        if (submitting) {
             console.log('VENDA: BLOQUEADO - já estava submetendo');
            return;
        }

        if (!cart.length) {
            alert('Adicione pelo menos um produto.');
            return;
        }
        

        const subtotal = cart.reduce((sum, item) => sum + (item.quantity * item.unit_price ),0);
        const discount = Number(discountInput.value) || 0;
        const total = Math.max(0,subtotal - discount);
        const cash =  Number(document.getElementById('payment-cash').value) || 0;
        const multicaixa =  Number(document.getElementById('payment-multicaixa').value) || 0;
        const paid = cash + multicaixa;
        if (paid < total) {
            alert( 'O valor pago é inferior ao total da venda.');
            return;
        }
        /*
            * Remove inputs antigos.
            */
        form.querySelectorAll('.dynamic-sale-input').forEach(element => element.remove());

        /*
            * ITEMS
            */
        cart.forEach( (item, index) => {
            addHidden(
                `items[${index}][product_id]`,
                item.product_id
            );
            addHidden(
                `items[${index}][quantity]`,
                item.quantity
            );
            addHidden(
                `items[${index}][unit_price]`,
                item.unit_price
            );
            addHidden(
                `items[${index}][discount]`,
                item.discount
            );
        });

        /*
            * PAYMENTS
            */
        let paymentIndex = 0;


        if (cash > 0) {
            addHidden(`payments[${paymentIndex}][method]`,'cash');
            addHidden(`payments[${paymentIndex}][amount]`,cash);
            paymentIndex++;
        }


        if (multicaixa > 0) {
            addHidden(`payments[${paymentIndex}][method]`,'multicaixa');
            addHidden(`payments[${paymentIndex}][amount]`,multicaixa);
            addHidden(`payments[${paymentIndex}][reference]`, document.getElementById('multicaixa-reference').value);

            paymentIndex++;
        }


        if (!paymentIndex) {
            alert('Informe pelo menos uma forma de pagamento.');
            return;
        }

        submitting = true;
        const submitButton = document.getElementById('submit-sale');
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <i class="fas fa-spinner fa-spin"></i>
            Processando venda...
        `;

        form.submit();
    });


    function addHidden(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        input.className = 'dynamic-sale-input';
        form.appendChild(input);
    }

    /*
     * ======================================================
     * SECURITY / HTML
     * ======================================================
     */

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }
    /*
     * INITIAL
     */
    renderCart();
    calculate();

});
</script>
@endpush
