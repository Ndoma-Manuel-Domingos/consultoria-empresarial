@extends('layouts.app')

@section('title', 'Triagem '.$appointment->code)

@section('page_header')
<div class="d-flex align-items-center justify-content-between">

    <div>
        <h1 class="page-title">
            Triagem
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


<form method="POST" action="{{ route('tenant.reception.triage.store', $appointment) }}" id="triage-form" data-no-ajax>
    @csrf
    <div class="row">
        <div class="col-lg-8">

            {{-- ===================================================== --}}
            {{-- CONTEXTO DA EMPRESA --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Contexto empresarial
                        </h3>

                        <div class="card-subtitle-modern">
                            Conheça rapidamente a realidade do cliente.
                        </div>
                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Área de atividade
                            </label>

                            <input type="text" name="business_area" class="form-control" value="{{ old('business_area', $appointment->triage->business_area ?? '') }}" placeholder="Ex.: Comércio, Construção, Consultoria...">

                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Dimensão da empresa
                            </label>
                            <select name="company_size" class="form-control">
                                <option value="">
                                    Selecionar...
                                </option>
                                @foreach([
                                    'micro' => 'Micro empresa',
                                    'small' => 'Pequena empresa',
                                    'medium' => 'Média empresa',
                                    'large' => 'Grande empresa'
                                ] as $value => $label)
                                <option value="{{ $value }}" {{ old('company_size', $appointment->triage->company_size ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Tipo de necessidade
                            </label>
                            <select name="need_type" class="form-control">
                                <option value="">Selecionar...</option>
                                <option value="diagnosis" {{ old('need_type', $appointment->triage->need_type ?? '') === 'diagnosis' ? 'selected' : '' }}>Diagnóstico empresarial</option>
                                <option value="accounting" {{ old('need_type', $appointment->triage->need_type ?? '') === 'accounting' ? 'selected' : '' }}>Contabilidade</option>
                                <option value="tax" {{ old('need_type', $appointment->triage->need_type ?? '') === 'tax' ? 'selected' : '' }}>Fiscalidade</option>
                                <option value="hr" {{ old('need_type', $appointment->triage->need_type ?? '') === 'hr' ? 'selected' : '' }}>Recursos Humanos</option>
                                <option value="management" {{ old('need_type', $appointment->triage->need_type ?? '') === 'management' ? 'selected' : '' }}>Gestão</option>
                                <option value="finance" {{ old('need_type', $appointment->triage->need_type ?? '') === 'finance' ? 'selected' : '' }}>Finanças</option>
                                <option value="legal" {{ old('need_type', $appointment->triage->need_type ?? '') === 'legal' ? 'selected' : '' }}>Jurídico</option>
                                <option value="other" {{ old('need_type', $appointment->triage->need_type ?? '') === 'other' ? 'selected' : '' }}>Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Urgência
                            </label>
                            <select name="urgency" class="form-control">
                                <option value="low" {{ old('urgency', $appointment->triage->urgency ?? '') === 'low' ? 'selected' : '' }}>Baixa</option>
                                <option value="normal" {{ old('urgency', $appointment->triage->urgency ?? 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ old('urgency', $appointment->triage->urgency ?? '') === 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ old('urgency', $appointment->triage->urgency ?? '') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================================================== --}}
            {{-- SITUAÇÃO --}}
            {{-- ===================================================== --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Situação da empresa
                        </h3>
                        <div class="card-subtitle-modern">
                            Registe o contexto apresentado pelo cliente.
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        Situação atual
                    </label>
                    <textarea name="business_situation" rows="5" class="form-control" placeholder="Descreva a situação atual da empresa...">{{ old('business_situation', $appointment->triage->business_situation ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        Problema apresentado
                    </label>
                    <textarea name="presented_problem" rows="5" class="form-control" placeholder="Qual é o principal problema apresentado pelo cliente?">{{ old('presented_problem', $appointment->triage->presented_problem ?? '') }}</textarea>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">
                        Solução pretendida
                    </label>
                    <textarea name="requested_solution" rows="5" class="form-control" placeholder="O que o cliente espera obter?">{{ old('requested_solution', $appointment->triage->requested_solution ?? '') }}</textarea>
                </div>
            </div>
            {{-- ===================================================== --}}
            {{-- DOCUMENTOS --}}
            {{-- ===================================================== --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Documentação
                        </h3>
                        <div class="card-subtitle-modern">
                            Documentos apresentados e documentos em falta.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Documentos apresentados
                            </label>
                            <textarea name="documents_presented" rows="6" class="form-control" placeholder="Ex.: NIF, certidão comercial, balancete...">{{ old('documents_presented', $appointment->triage->documents_presented ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                Documentos pendentes
                            </label>
                            <textarea name="documents_pending" rows="6" class="form-control" placeholder="Documentos que o cliente deverá entregar posteriormente...">{{ old('documents_pending', $appointment->triage->documents_pending ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">
                        Notas internas
                    </label>
                    <textarea name="notes" rows="4" class="form-control" placeholder="Observações importantes para a equipa de consultoria...">{{ old('notes', $appointment->triage->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="col-lg-4">
            @include('tenant.reception.partials.client-summary', [
                'client' => $appointment->client
            ])
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Atendimento
                        </h3>
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">
                        Código
                    </small>
                    <div style="font-weight:700;">
                        {{ $appointment->code }}
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">
                        Motivo
                    </small>
                    <div>
                        {{ $appointment->reason ?: 'Não informado' }}
                    </div>
                </div>
                <div>
                    <small class="text-muted">
                        Prioridade
                    </small>
                    <div>
                        {{ ucfirst($appointment->priority) }}
                    </div>
                </div>
            </div>
            <div class="dashboard-card chart-card">
                <div class="card-header-modern">
                    <div>
                        <h3 class="card-title-modern">
                            Finalizar triagem
                        </h3>
                        <div class="card-subtitle-modern">
                            Depois de guardar, poderá selecionar os serviços.
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" data-loading-text="A guardar...">
                    <i class="fas fa-save mr-1"></i>
                    Guardar triagem
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
