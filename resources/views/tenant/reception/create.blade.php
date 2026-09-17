@extends('layouts.app')

@section('title', 'Novo atendimento')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Novo atendimento
        </h1>
        <div class="page-subtitle">
            Registe a chegada do cliente e inicie o processo de recepção.
        </div>
    </div>
    <div>
        <a href="{{ route('tenant.reception.index') }}"
           class="btn btn-secondary btn-sm">
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
<form method="POST" action="{{ route('tenant.reception.store') }}" id="reception-create-form" data-no-ajax>
    @csrf
    <div class="row">
        {{-- ========================================================= --}}
        {{-- DADOS DO ATENDIMENTO --}}
        {{-- ========================================================= --}}
        <div class="col-lg-8">
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            <i class="fas fa-user-clock mr-1"></i>
                            Entrada na recepção
                        </h3>
                        <div class="card-subtitle-modern">
                            Identifique o cliente e registe o motivo da visita.
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- CLIENTE --}}
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label">
                                Cliente / Empresa
                                <span class="text-danger">*</span>
                            </label>
                            <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
                                <option value="">
                                    Selecionar cliente...
                                </option>
                                @foreach($clients ?? [] as $client)
                                    <option value="{{ $client->id }}" data-type="{{ $client->type }}" data-nif="{{ $client->nif }}" data-phone="{{ $client->phone }}" data-email="{{ $client->email }}" data-city="{{ $client->city }}" {{ old('client_id') == $client->id ? 'selected' : '' }}> 
                                        {{ $client->name }}
                                        @if($client->nif)
                                            — NIF: {{ $client->nif }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    {{-- NOVO CLIENTE --}}
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group w-100">
                            <a href="{{ route('tenant.clients.create') }}" class="btn btn-outline-primary btn-block" target="_blank">
                                <i class="fas fa-user-plus mr-1"></i>
                                Novo cliente
                            </a>
                        </div>
                    </div>

                    {{-- DATA --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                Data
                            </label>
                            <input type="date" name="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror" value="{{ old('appointment_date', now()->format('Y-m-d')) }}" required>
                            @error('appointment_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- HORA --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                Hora
                            </label>
                            <input type="time" name="appointment_time" class="form-control" value="{{ old('appointment_time', now()->format('H:i')) }}">
                        </div>
                    </div>

                    {{-- PRIORIDADE --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                Prioridade
                            </label>
                            <select name="priority" class="form-control">
                                <option value="low"{{ old('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                                <option value="normal"{{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high"{{ old('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent"{{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>
                    </div>

                    {{-- TIPO DE SERVIÇO --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Área / tipo de serviço
                            </label>
                            <input type="text" name="service_type" class="form-control" value="{{ old('service_type') }}" placeholder="Ex.: Contabilidade, Fiscalidade, RH...">
                        </div>
                    </div>

                    {{-- MOTIVO --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Motivo da visita
                            </label>
                            <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" placeholder="Descreva brevemente o motivo">
                        </div>
                    </div>
                    {{-- NOTAS --}}
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label class="form-label">
                                Observações da recepção
                            </label>
                            <textarea name="reception_notes" rows="4" class="form-control" placeholder="Informações importantes fornecidas pelo cliente...">{{ old('reception_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            {{-- RESUMO CLIENTE --}}
            <div id="client-preview"></div>
        </div>
        {{-- ========================================================= --}}
        {{-- SIDEBAR --}}
        {{-- ========================================================= --}}
        <div class="col-lg-4">
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Fluxo do atendimento
                        </h3>
                        <div class="card-subtitle-modern">
                            O que acontece depois da recepção.
                        </div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            1. Recepção
                        </div>
                        <div class="activity-time">
                            Identificação do cliente
                        </div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            2. Triagem
                        </div>
                        <div class="activity-time">
                            Levantamento da necessidade
                        </div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            3. Serviços
                        </div>
                        <div class="activity-time">
                            Seleção dos serviços solicitados
                        </div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            4. Pagamento
                        </div>
                        <div class="activity-time">
                            Registo do pagamento
                        </div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div>
                        <div class="activity-title">
                            5. Ficha de triagem
                        </div>
                        <div class="activity-time">
                            Encaminhamento para consultoria
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard-card chart-card">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            <i class="fas fa-info-circle mr-1"></i>
                            Informação
                        </h3>
                        <div class="card-subtitle-modern">
                            Atenção
                        </div>
                    </div>
                </div>
                <p class="text-muted mb-0" style="font-size:12px;">
                    Confirme os dados da empresa antes de iniciar a triagem.
                    A ficha criada nesta etapa será utilizada pela equipa de consultoria.
                </p>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <a href="{{ route('tenant.reception.index') }}"
           class="btn btn-secondary mr-2">
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary" data-loading-text="A registar...">
            <i class="fas fa-check mr-1"></i>
            Registar atendimento
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(function () {
    $('#client_id').on('change', function () {

        const option = $(this).find('option:selected');

        if (!option.val()) {
            $('#client-preview').html('');
            return;
        }

        const html = `
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Resumo do cliente
                        </h3>
                        <div class="card-subtitle-modern">
                            Confirme os dados antes de continuar.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <strong>Cliente</strong>
                        <div class="text-muted mt-1">
                            ${option.text().trim()}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <strong>NIF</strong>
                        <div class="text-muted mt-1">
                            ${option.data('nif') || '-'}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <strong>Telefone</strong>
                        <div class="text-muted mt-1">
                            ${option.data('phone') || '-'}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <strong>Email</strong>
                        <div class="text-muted mt-1">
                            ${option.data('email') || '-'}
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#client-preview').html(html);
    });
    $('#client_id').trigger('change');
});
</script>
@endpush