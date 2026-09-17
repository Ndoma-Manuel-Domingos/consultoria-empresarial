@php
$payment = $payment ?? null;

$paymentStatusLabels = [
    'pending' => 'Pendente',
    'partial' => 'Parcial',
    'paid' => 'Pago',
    'cancelled' => 'Cancelado',
];

$paymentMethodLabels = [
    'cash' => 'Dinheiro',
    'transfer' => 'Transferência',
    'multicaixa' => 'Multicaixa',
    'tpa' => 'TPA',
    'reference' => 'Referência',
    'other' => 'Outro',
];
@endphp

<div class="dashboard-card chart-card mb-4">
    <div class="card-header-modern">
        <div>
            <h3 class="card-title-modern">
                Pagamento
            </h3>
            <div class="card-subtitle-modern">
                Estado financeiro do atendimento.
            </div>
        </div>
        @if($payment && $payment->status === 'paid')
        <span class="status status-success">
            <i class="fas fa-check-circle mr-1"></i>
            Pago
        </span>
        @elseif($payment)
        <span class="status status-warning">
            {{ $paymentStatusLabels[$payment->status] ?? ucfirst($payment->status) }}
        </span>
        @endif
    </div>
    @if($payment)
    <div class="row">
        <div class="col-md-4">
            <small class="text-muted">
                Subtotal
            </small>
            <div style="font-size:16px;font-weight:700;">
                {{ number_format( $payment->subtotal, 2, ',', '.') }} Kz
            </div>
        </div>
        <div class="col-md-4">
            <small class="text-muted">
                Desconto
            </small>
            <div style="font-size:16px;font-weight:700;">
                {{ number_format($payment->discount,2,',','.') }}
                Kz
            </div>
        </div>
        <div class="col-md-4">
            <small class="text-muted">
                Total
            </small>
            <div style="font-size:18px;font-weight:700;color:#2563eb;">
                {{ number_format($payment->total,2,',','.') }}
                Kz
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <small class="text-muted">
                Pago
            </small>
            <div style="font-size:16px;font-weight:700;color:#16a34a;">
                {{ number_format($payment->amount_paid,2,',','.') }}
                Kz
            </div>
        </div>
        <div class="col-md-4">
            <small class="text-muted">
                Em dívida
            </small>
            <div style="font-size:16px;font-weight:700;color:#dc2626;">
                {{ number_format($payment->amount_due,2,',','.') }}
                Kz
            </div>
        </div>
        <div class="col-md-4">
            <small class="text-muted">
                Troco
            </small>
            <div style="font-size:16px;font-weight:700;">
                {{ number_format($payment->change_amount,2,',','.') }}
                Kz
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <small class="text-muted">
                Método
            </small>
            <div style="font-weight:600;">
                {{ $paymentMethodLabels[$payment->payment_method] ?? 'Não informado' }}
            </div>
        </div>
        <div class="col-md-6">
            <small class="text-muted">
                Referência
            </small>
            <div style="font-weight:600;">
                {{ $payment->reference ?: '—' }}
            </div>
        </div>
    </div>
    @if($payment->paid_at)
    <div class="mt-3">
        <small class="text-muted">
            Pago em
        </small>
        <div style="font-weight:600;">
            {{ optional($payment->paid_at)->format('d/m/Y H:i') }}
        </div>
    </div>
    @endif
    @if($payment->notes)
    <div class="mt-3">
        <small class="text-muted">
            Observações
        </small>
        <div class="text-muted" style="white-space:pre-line;">
            {{ $payment->notes }}
        </div>
    </div>
    @endif
    @else
    <div class="text-center py-4">
        <i class="fas fa-credit-card text-muted" style="font-size:28px;"></i>
        <div class="mt-2" style="font-weight:700;">
            Nenhum pagamento registado
        </div>
        <div class="text-muted" style="font-size:12px;">
            O pagamento ainda não foi processado.
        </div>
    </div>
    @endif
</div>
