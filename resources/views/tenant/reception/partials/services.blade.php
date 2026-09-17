@php
    $services = $services ?? collect();

    $subtotal = $services->sum(function ($service) {
        return (float) $service->quantity * (float) $service->unit_price;
    });

    $discount = $services->sum('discount');

    $total = $services->sum(function ($service) {
        return (float) $service->total;
    });

    if ($total <= 0 && $subtotal> 0) {
        $total = max($subtotal - $discount, 0);
    }
@endphp

<div class="dashboard-card chart-card mb-4">
    <div class="card-header-modern">
        <div>
            <h3 class="card-title-modern">
                Serviços solicitados
            </h3>
            <div class="card-subtitle-modern">
                Serviços associados ao atendimento.
            </div>
        </div>

        {{-- @if(!$readonly ?? true) --}}
        <a href="{{ route('tenant.reception.payment', $appointment) }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-plus mr-1"></i>
            Gerir serviços
        </a>
        {{-- @endif --}}
    </div>
    @if($services->count())
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th class="text-center">Qtd.</th>
                    <th class="text-right">Preço</th>
                    <th class="text-right">Desconto</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>
                        <div style="font-weight:600;">
                            {{ $service->name }}
                        </div>
                        @if($service->notes)
                        <small class="text-muted">
                            {{ $service->notes }}
                        </small>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ number_format($service->quantity, 2, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($service->unit_price,2,',','.') }} Kz
                    </td>
                    <td class="text-right">
                        {{ number_format($service->discount,2,',','.') }} Kz
                    </td>
                    <td class="text-right">
                        <strong>
                            {{ number_format($service->total,2,',','.') }} Kz
                        </strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">
                        <strong>
                            Subtotal
                        </strong>
                    </td>
                    <td class="text-right">
                        <strong>
                            {{ number_format($subtotal, 2, ',', '.') }}
                            Kz
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">
                        <span class="text-muted">
                            Desconto
                        </span>
                    </td>
                    <td class="text-right">
                        <span class="text-muted">
                            {{ number_format($discount, 2, ',', '.') }}
                            Kz
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">
                        <strong>
                            Total
                        </strong>
                    </td>
                    <td class="text-right">
                        <strong style="
                            font-size:16px;
                            color:#2563eb;
                        ">
                            {{ number_format($total, 2, ',', '.') }}
                            Kz
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="text-center py-5">
        <div style="width:50px;height:50px;border-radius:12px;background:#f8fafc;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:#64748b;">
            <i class="fas fa-concierge-bell"></i>
        </div>
        <div style="font-weight:700;">
            Nenhum serviço registado
        </div>
        <div class="text-muted mt-1" style="font-size:12px;">
            Ainda não foram associados serviços a este atendimento.
        </div>
    </div>
    @endif
</div>
