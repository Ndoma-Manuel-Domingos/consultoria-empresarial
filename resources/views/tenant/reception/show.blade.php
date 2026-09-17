@extends('layouts.app')

@section('title', 'Atendimento '.$appointment->code)

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <div class="d-flex align-items-center">

            <h1 class="page-title mb-0">
                Atendimento {{ $appointment->code }}
            </h1>

            @php
            $statusLabels = [
                'waiting' => 'Aguardando',
                'triage' => 'Em triagem',
                'awaiting_payment' => 'Aguardando pagamento',
                'paid' => 'Pago',
                'in_progress' => 'Em atendimento',
                'completed' => 'Concluído',
                'cancelled' => 'Cancelado',
            ];

            $statusClasses = [
                'waiting' => 'status-warning',
                'triage' => 'status-warning',
                'awaiting_payment' => 'status-danger',
                'paid' => 'status-success',
                'in_progress' => 'status-info',
                'completed' => 'status-success',
                'cancelled' => 'status-danger',
            ];
            @endphp

            <span class="status {{ $statusClasses[$appointment->status] ?? 'status-info' }} ml-3">
                {{ $statusLabels[$appointment->status] ?? ucfirst($appointment->status) }}
            </span>

        </div>

        <div class="page-subtitle">
            Atendimento registado em
            {{ optional($appointment->appointment_date)->format('d/m/Y') }}
            @if($appointment->appointment_time)
            às {{ substr($appointment->appointment_time, 0, 5) }}
            @endif
        </div>
    </div>
    <div class="d-flex">
        <a href="{{ route('tenant.reception.index') }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left mr-1"></i>
            Voltar
        </a>
        @if(in_array($appointment->status, ['waiting', 'triage']))
        <a href="{{ route('tenant.reception.triage', $appointment) }}" class="btn btn-primary btn-sm mr-2">
            <i class="fas fa-stethoscope mr-1"></i>
            Triagem
        </a>
        @endif
        @if($appointment->status === 'awaiting_payment')
        <a href="{{ route('tenant.reception.payment', $appointment) }}" class="btn btn-success btn-sm mr-2">
            <i class="fas fa-credit-card mr-1"></i>
            Pagamento
        </a>
        @endif
        <a href="{{ route('tenant.reception.ficha', $appointment) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-file-medical mr-1"></i>
            Ficha
        </a>
    </div>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle mr-1"></i>
    {{ session('success') }}
</div>
@endif

<div class="row">
    {{-- COLUNA PRINCIPAL --}}
    <div class="col-lg-8">
        @include('tenant.reception.partials.client-summary', [
            'client' => $appointment->client
        ])

        @include('tenant.reception.partials.services', [
            'appointment' => $appointment,
            'services' => $appointment->services ?? collect()
        ])

        @include('tenant.reception.partials.payment-summary', [
            'appointment' => $appointment,
            'payment' => $payment ?? $appointment->payment ?? null
        ])

        @include('tenant.reception.partials.timeline', [
            'appointment' => $appointment
        ]) 
    </div>
    {{-- SIDEBAR --}}
    <div class="col-lg-4">
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Atendimento
                    </h3>
                    <div class="card-subtitle-modern">
                        Informações principais
                    </div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-hashtag"></i>
                </div>
                <div>
                    <div class="activity-title">
                        Código
                    </div>
                    <div class="activity-time">
                        {{ $appointment->code }}
                    </div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div>
                    <div class="activity-title">
                        Data
                    </div>
                    <div class="activity-time">
                        {{ optional($appointment->appointment_date)->format('d/m/Y') }}
                    </div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <div>
                    <div class="activity-title">
                        Prioridade
                    </div>
                    <div class="activity-time">
                        {{ ucfirst($appointment->priority) }}
                    </div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div>
                    <div class="activity-title">
                        Serviço
                    </div>
                    <div class="activity-time">
                        {{ $appointment->service_type ?: 'Não definido' }}
                    </div>
                </div>
            </div>

            @if($appointment->assignedUser)
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <div class="activity-title">
                        Responsável
                    </div>
                    <div class="activity-time">
                        {{ $appointment->assignedUser->name }}
                    </div>
                </div>
            </div>
            @endif
        </div>
        {{-- AÇÕES --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Ações
                    </h3>
                    <div class="card-subtitle-modern">
                        Próximas etapas
                    </div>
                </div>
            </div>
            @if(in_array($appointment->status, ['waiting', 'triage']))
            <a href="{{ route('tenant.reception.triage', $appointment) }}" class="btn btn-primary btn-block mb-2">
                <i class="fas fa-stethoscope mr-1"></i>
                Realizar triagem
            </a>
            @endif
            @if($appointment->status === 'awaiting_payment')
            <a href="{{ route('tenant.reception.payment', $appointment) }}" class="btn btn-success btn-block mb-2">
                <i class="fas fa-credit-card mr-1"></i>
                Processar pagamento
            </a>
            @endif
            @if(in_array($appointment->status, ['paid', 'in_progress', 'completed']))
            <a href="{{ route('tenant.reception.ficha', $appointment) }}" class="btn btn-outline-primary btn-block mb-2">
                <i class="fas fa-file-medical mr-1"></i>
                Ver ficha de triagem
            </a>
            @endif
        </div>
        @if($appointment->reason)
        <div class="dashboard-card chart-card">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Motivo da visita
                    </h3>
                </div>
            </div>
            <p class="text-muted mb-0">
                {{ $appointment->reason }}
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
