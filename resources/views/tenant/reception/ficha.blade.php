@extends('layouts.app')

@section('title', 'Ficha de triagem '. $appointment->code)

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Ficha de triagem
        </h1>
        <div class="page-subtitle">
            Atendimento {{ $appointment->code }}
        </div>
    </div>
    <div class="d-flex">
        <a href="{{ route('tenant.reception.show', $appointment) }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left mr-1"></i>
            Voltar
        </a>
        <button type="button" onclick="window.print()" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-print mr-1"></i>
            Imprimir
        </button>
    </div>
</div>
@endsection

@section('content')
<div id="triage-sheet">
    {{-- CABEÇALHO --}}
    <div class="dashboard-card chart-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div style="width:58px;height:58px;border-radius:14px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:15px;">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div>
                        <h2 style="margin:0;font-size:20px;font-weight:700;color:#0f172a;">
                            Ficha de Triagem Empresarial
                        </h2>
                        <div class="text-muted mt-1">
                            Documento de encaminhamento para consultoria
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <div class="text-muted">
                    Código
                </div>
                <div style="font-size:20px;font-weight:700;color:#2563eb;">
                    {{ $appointment->code }}
                </div>
            </div>
        </div>
    </div>

    {{-- CLIENTE --}}
    @include('tenant.reception.partials.client-summary', [
        'client' => $appointment->client,
        'print' => true
    ])

    {{-- TRIAGEM --}}
    @if($appointment->triage)
    <div class="dashboard-card chart-card mb-4">
        <div class="card-header-modern">
            <div>
                <h3 class="card-title-modern">
                    Dados da triagem
                </h3>
                <div class="card-subtitle-modern">
                    Informações recolhidas pela recepção.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-4">
                    <small class="text-muted">
                        Área de atividade
                    </small>
                    <div style="font-weight:600;">
                        {{ $appointment->triage->business_area ?: 'Não informado' }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-4">
                    <small class="text-muted">
                        Dimensão
                    </small>
                    <div style="font-weight:600;">
                        {{ $appointment->triage->company_size ?: 'Não informado' }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-4">
                    <small class="text-muted">
                        Necessidade
                    </small>
                    <div style="font-weight:600;">
                        {{ $appointment->triage->need_type ?: 'Não informado' }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-4">
                    <small class="text-muted">
                        Urgência
                    </small>
                    <div style="font-weight:600;">
                        {{ $appointment->triage->urgency ?: 'Não informado' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <div class="form-label">
                        Situação atual
                    </div>
                    <div class="text-muted" style="white-space:pre-line;">
                        {{ $appointment->triage->business_situation ?: 'Não informado' }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <div class="form-label">
                        Problema apresentado
                    </div>
                    <div class="text-muted" style="white-space:pre-line;">
                        {{ $appointment->triage->presented_problem ?: 'Não informado' }}
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-4">
                    <div class="form-label">
                        Solução solicitada
                    </div>
                    <div class="text-muted" style="white-space:pre-line;">
                        {{ $appointment->triage->requested_solution ?: 'Não informado' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <div class="form-label">
                        Documentos apresentados
                    </div>
                    <div class="text-muted" style="white-space:pre-line;">
                        {{ $appointment->triage->documents_presented ?: 'Nenhum documento registado.' }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <div class="form-label">
                        Documentos pendentes
                    </div>
                    <div class="text-muted" style="white-space:pre-line;">
                        {{ $appointment->triage->documents_pending ?: 'Nenhum documento pendente registado.' }}
                    </div>
                </div>
            </div>
        </div>

        @if($appointment->triage->notes)
        <div>
            <div class="form-label">
                Notas internas
            </div>
            <div class="text-muted" style="white-space:pre-line;">
                {{ $appointment->triage->notes }}
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        A triagem ainda não foi preenchida.
    </div>
    @endif

    {{-- SERVIÇOS --}}
    @include('tenant.reception.partials.services', [
        'appointment' => $appointment,
        'services' => $appointment->services ?? collect(),
        'readonly' => true
    ])

    {{-- PAGAMENTO --}}
    @include('tenant.reception.partials.payment-summary', [
        'appointment' => $appointment,
        'payment' => $payment ?? $appointment->payment ?? null,
        'readonly' => true
    ])

    {{-- ENCAMINHAMENTO --}}
    <div class="dashboard-card chart-card mb-4">
        <div class="card-header-modern">
            <div>
                <h3 class="card-title-modern">
                    Encaminhamento
                </h3>
                <div class="card-subtitle-modern">
                    Informação para a equipa de consultoria.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <small class="text-muted">
                    Estado
                </small>
                <div class="mt-1">
                    @php
                        $label = [
                            'waiting' => 'Aguardando',
                            'triage' => 'Triagem',
                            'awaiting_payment' => 'Aguardando pagamento',
                            'paid' => 'Pago',
                            'in_progress' => 'Em atendimento',
                            'completed' => 'Concluído',
                            'cancelled' => 'Cancelado',
                        ][$appointment->status] ?? $appointment->status;
                    @endphp
                    <strong>
                        {{ $label }}
                    </strong>
                </div>
            </div>
            <div class="col-md-4">
                <small class="text-muted">
                    Data de entrada
                </small>
                <div class="mt-1">
                    <strong>
                        {{ optional($appointment->appointment_date)->format('d/m/Y') }}
                    </strong>
                </div>
            </div>
            <div class="col-md-4">
                <small class="text-muted">
                    Responsável
                </small>
                <div class="mt-1">
                    <strong>
                        {{ $appointment->assignedUser->name ?? 'Por atribuir' }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    {{-- RODAPÉ --}}
    <div class="text-center text-muted mt-4 mb-4" style="font-size:11px;">
        Ficha gerada pelo módulo de Recepção — {{ config('app.name') }}
        <br>
        Gerada em {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .main-sidebar,
        .main-header,
        .content-header,
        .no-print,
        footer {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
            background: #fff !important;
        }
        .dashboard-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        .btn {
            display: none !important;
        }
    }
</style>
@endpush
