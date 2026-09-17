@extends('layouts.app')

@section('title', 'Pagamento '.$appointment->code)

@section('page_header')

<div class="d-flex align-items-center justify-content-between">

    <div>

        <h1 class="page-title">
            Pagamento
        </h1>

        <div class="page-subtitle">
            Atendimento {{ $appointment->code }} —
            {{ $appointment->client->name ?? 'Cliente' }}
        </div>

    </div>

    <div>

        <a href="{{ route('tenant.reception.show', $appointment) }}" class="btn btn-secondary btn-sm">

            <i class="fas fa-arrow-left mr-1"></i>
            Voltar

        </a>

    </div>

</div>

@endsection


@section('content')

@if($errors->any())

<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ $errors->first() }}
</div>
@endif
<form method="POST" action="{{ route('tenant.reception.payment.store', $appointment) }}" id="payment-form" data-no-ajax>
    @csrf
    <div class="row">
        {{-- SERVIÇOS --}}
        <div class="col-lg-8">
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Serviços solicitados
                        </h3>
                        <div class="card-subtitle-modern">
                            Selecione os serviços/produtos a cobrar.
                        </div>
                    </div>
                    <button type="button" id="add-service" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Adicionar serviço
                    </button>
                </div>
                <div id="services-container">
                    @php
                        $paymentServices = old('services');
                        if (!$paymentServices) {
                            $paymentServices = isset($appointment->services) ? $appointment->services->map(function ($item) {
                                return [
                                    'product_id' => $item->product_id,
                                    'name' => $item->name,
                                    'quantity' => $item->quantity,
                                    'unit_price' => $item->unit_price,
                                    'discount' => $item->discount,
                                    'total' => $item->total,
                                    'notes' => $item->notes,
                                ];
                            })->toArray() : [];
                        }
                    @endphp

                    @forelse($paymentServices as $index => $item)
                    <div class="service-row border rounded p-3 mb-3">
                        <div class="row">
                            <div class="col-md-5">
                                <label class="form-label">
                                    Serviço / Produto
                                </label>
                                <select name="services[{{ $index }}][product_id]" class="form-control product-select">
                                    <option value="">
                                        Selecionar...
                                    </option>
                                    @foreach($products ?? [] as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->sale_price }}" data-tax="{{ $product->tax_rate }}" {{ ($item['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                        — {{ number_format($product->sale_price, 2, ',', '.') }} Kz
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">
                                    Quantidade
                                </label>
                                <input type="number" name="services[{{ $index }}][quantity]" class="form-control service-quantity" value="{{ $item['quantity'] ?? 1 }}" min="0.01" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">
                                    Preço
                                </label>
                                <input type="number" name="services[{{ $index }}][unit_price]" class="form-control service-price" value="{{ $item['unit_price'] ?? 0 }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">
                                    Desconto
                                </label>
                                <input type="number" name="services[{{ $index }}][discount]" class="form-control service-discount" value="{{ $item['discount'] ?? 0 }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger remove-service">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="form-label">
                                    Observações
                                </label>
                                <input type="text" name="services[{{ $index }}][notes]" class="form-control" value="{{ $item['notes'] ?? '' }}" placeholder="Observação do serviço">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-services text-center py-5">
                        <div style="width:50px;height:50px;border-radius:13px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <div style="font-weight:700;">
                            Nenhum serviço adicionado
                        </div>
                        <div class="text-muted mt-1">
                            Adicione os serviços solicitados pelo cliente.
                        </div>
                    </div>
                    @endforelse
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

                <textarea name="notes" rows="4" class="form-control" placeholder="Observações relacionadas ao pagamento...">{{ old('notes', $payment->notes ?? '') }}</textarea>

            </div>
        </div>
        {{-- PAGAMENTO --}}
        <div class="col-lg-4">

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Resumo
                        </h3>

                        <div class="card-subtitle-modern">
                            Valores do atendimento.
                        </div>
                    </div>

                </div>


                <div class="d-flex justify-content-between mb-3">

                    <span class="text-muted">
                        Subtotal
                    </span>

                    <strong id="subtotal-display">
                        0,00 Kz
                    </strong>

                </div>


                <div class="d-flex justify-content-between mb-3">

                    <span class="text-muted">
                        Desconto
                    </span>

                    <strong id="discount-display">
                        0,00 Kz
                    </strong>

                </div>


                <hr>


                <div class="d-flex justify-content-between">

                    <span style="font-weight:700;">
                        Total
                    </span>

                    <strong id="total-display" style="font-size:20px;color:#2563eb;">
                        0,00 Kz
                    </strong>

                </div>

            </div>


            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Recebimento
                        </h3>

                        <div class="card-subtitle-modern">
                            Registe como o cliente efetuou o pagamento.
                        </div>
                    </div>

                </div>


                <div class="form-group">

                    <label class="form-label">
                        Valor recebido
                        <span class="text-danger">*</span>
                    </label>

                    <input type="number" name="amount_paid" id="amount_paid" class="form-control" value="{{ old('amount_paid', $payment->amount_paid ?? 0) }}" min="0" step="0.01" required>

                </div>


                <div class="form-group">

                    <label class="form-label">
                        Método de pagamento
                    </label>

                    <select name="payment_method" id="payment_method" class="form-control">

                        <option value="">Selecionar...</option>
                        <option value="cash" {{ old('payment_method', $payment->payment_method ?? '') === 'cash' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="transfer" {{ old('payment_method', $payment->payment_method ?? '') === 'transfer' ? 'selected' : '' }}>Transferência bancária</option>
                        <option value="multicaixa" {{ old('payment_method', $payment->payment_method ?? '') === 'multicaixa' ? 'selected' : '' }}>Multicaixa</option>
                        <option value="tpa" {{ old('payment_method', $payment->payment_method ?? '') === 'tpa' ? 'selected' : '' }}>TPA</option>
                        <option value="reference" {{ old('payment_method', $payment->payment_method ?? '') === 'reference' ? 'selected' : '' }}>Referência</option>
                        <option value="other" {{ old('payment_method', $payment->payment_method ?? '') === 'other' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>


                <div class="form-group">

                    <label class="form-label">
                        Referência
                    </label>

                    <input type="text" name="reference" class="form-control" value="{{ old('reference', $payment->reference ?? '') }}" placeholder="Nº de referência / comprovativo">

                </div>


                <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        Valor devido
                    </span>

                    <strong id="due-display">
                        0,00 Kz
                    </strong>

                </div>


                <div class="d-flex justify-content-between">

                    <span class="text-muted">
                        Troco
                    </span>

                    <strong id="change-display">
                        0,00 Kz
                    </strong>

                </div>

            </div>


            <button type="submit" class="btn btn-success btn-lg btn-block" data-loading-text="A processar pagamento...">

                <i class="fas fa-check-circle mr-1"></i>
                Confirmar pagamento

            </button>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    $(function() {
        let paymentServices = @json($paymentServices ?? []);

        let serviceIndex = paymentServices.length;

        function money(value) {
            return new Intl.NumberFormat('pt-AO', {
                minimumFractionDigits: 2
                , maximumFractionDigits: 2
            }).format(value) + ' Kz';
        }

        function calculateTotals() {
            let subtotal = 0;
            let discount = 0;
            $('.service-row').each(function() {
                const quantity = parseFloat($(this).find('.service-quantity').val()) || 0;
                const price = parseFloat($(this).find('.service-price').val()) || 0;
                const rowDiscount = parseFloat($(this).find('.service-discount').val()) || 0;
                subtotal += quantity * price;
                discount += rowDiscount;
            });
            const total = Math.max(subtotal - discount, 0);
            const paid = parseFloat($('#amount_paid').val()) || 0;
            const due = Math.max(total - paid, 0);
            const change = Math.max(paid - total, 0);

            $('#subtotal-display').text(money(subtotal));
            $('#discount-display').text(money(discount));
            $('#total-display').text(money(total));
            $('#due-display').text(money(due));
            $('#change-display').text(money(change));
        }

        $('#add-service').on('click', function() {
            const row = `
            <div class="service-row border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">
                            Serviço / Produto
                        </label>
                        <select name="services[${serviceIndex}][product_id]" class="form-control product-select">
                            <option value="">
                                Selecionar...
                            </option>
                            @foreach($products ?? [] as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">
                                    {{ $product->name }}
                                    — {{ number_format($product->sale_price, 2, ',', '.') }} Kz
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">
                            Quantidade
                        </label>
                        <input type="number" name="services[${serviceIndex}][quantity]" class="form-control service-quantity" value="1" min="0.01" step="0.01">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">
                            Preço
                        </label>
                        <input type="number" name="services[${serviceIndex}][unit_price]" class="form-control service-price" value="0" min="0" step="0.01">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">
                            Desconto
                        </label>
                        <input type="number" name="services[${serviceIndex}][discount]" class="form-control service-discount" value="0" min="0" step="0.01">
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger remove-service">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

            $('#services-container').find('.empty-services').remove();
            $('#services-container').append(row);
            serviceIndex++;
            calculateTotals();
        });

        $(document).on('click', '.remove-service', function() {
            $(this).closest('.service-row').remove();
            calculateTotals();
        });

        $(document).on('change', '.product-select', function() {
            const option = $(this).find('option:selected');
            const price = option.data('price') || 0;
            $(this).closest('.service-row').find('.service-price').val(price);
            calculateTotals();
        });

        $(document).on('input' , '.service-quantity, .service-price, .service-discount, #amount_paid' , calculateTotals);

        calculateTotals();
    });
</script>
@endpush
