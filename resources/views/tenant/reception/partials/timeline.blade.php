@php

$status = $appointment->status;

$events = [
    [
        'key' => 'created',
        'title' => 'Atendimento registado',
        'description' => 'Cliente recebido pela recepção.',
        'date' => $appointment->created_at,
        'icon' => 'fa-user-plus',
        'active' => true,
    ],
    [
        'key' => 'triage',
        'title' => 'Triagem',
        'description' => 'Levantamento da situação e necessidade da empresa.',
        'date' => $appointment->triage?->created_at,
        'icon' => 'fa-stethoscope',
        'active' => in_array($status, [
            'triage',
            'awaiting_payment',
            'paid',
            'in_progress',
            'completed'
        ]),
    ],
    [
        'key' => 'payment',
        'title' => 'Pagamento',
        'description' => 'Registo financeiro dos serviços solicitados.',
        'date' => $appointment->payment?->paid_at,
        'icon' => 'fa-credit-card',
        'active' => in_array($status, [
            'paid',
            'in_progress',
            'completed'
        ]),
    ],
    [
        'key' => 'started',
        'title' => 'Encaminhado para consultoria',
        'description' => 'Cliente disponível para atendimento pela equipa.',
        'date' => $appointment->started_at,
        'icon' => 'fa-user-tie',
        'active' => in_array($status, [
            'in_progress',
            'completed'
        ]),
    ],
    [
        'key' => 'completed',
        'title' => 'Atendimento concluído',
        'description' => 'Processo de atendimento finalizado.',
        'date' => $appointment->completed_at,
        'icon' => 'fa-check',
        'active' => $status === 'completed',
    ],
];
@endphp

<div class="dashboard-card chart-card">
    <div class="card-header-modern">
        <div>
            <h3 class="card-title-modern">
                Histórico do atendimento
            </h3>
            <div class="card-subtitle-modern">
                Percurso do cliente na organização.
            </div>
        </div>
    </div>

    <div class="reception-timeline">
        @foreach($events as $event)
        <div class="reception-timeline-item {{ $event['active'] ? 'active' : '' }}">
            <div class="reception-timeline-icon">
                <i class="fas {{ $event['icon'] }}"></i>
            </div>
            <div class="reception-timeline-content">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="activity-title">
                            {{ $event['title'] }}
                        </div>
                        <div class="text-muted" style="font-size:11px;">
                            {{ $event['description'] }}
                        </div>
                    </div>

                    @if($event['date'])
                    <div class="text-muted" style="font-size:10px;white-space:nowrap;">
                        {{ optional($event['date'])->format('d/m/Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('styles')
<style>
    .reception-timeline {
        position: relative;
        padding: 5px 0;
    }

    .reception-timeline:before {
        content: '';
        position: absolute;
        left: 20px;
        top: 25px;
        bottom: 25px;
        width: 1px;
        background: #e2e8f0;
    }

    .reception-timeline-item {
        position: relative;
        display: flex;
        padding: 12px 0;
        opacity: .45;
    }

    .reception-timeline-item.active {
        opacity: 1;
    }

    .reception-timeline-icon {
        position: relative;
        z-index: 2;
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 11px;
        background: #f1f5f9;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
    }

    .reception-timeline-item.active .reception-timeline-icon {
        background: #eff6ff;
        color: #2563eb;
    }

    .reception-timeline-content {
        flex: 1;
        padding-top: 3px;
    }

</style>
@endpush
