@extends('layouts.app')

@section('title', isset($client) ? 'Editar cliente' : 'Novo cliente')
@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title"> {{ isset($client) ? 'Editar cliente' : 'Novo cliente' }} </h1>
        <div class="page-subtitle">
            {{ isset($client) ? 'Atualize os dados e informações do cliente.' : 'Adicione um novo cliente à organização.' }}
        </div>
    </div>
    <div class="d-flex align-items-center">
        <a href="{{ route('tenant.clients.index') }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        <button type="submit" form="client-form" class="btn btn-primary btn-sm" data-loading-text="{{ isset($client) ? 'A guardar...' : 'A criar...' }}">
            <i class="fas fa-save"></i>
            {{ isset($client) ? 'Guardar alterações' : 'Criar cliente' }}
        </button>
    </div>
</div> 
@endsection

@section('content')

<form id="client-form" method="POST" action="{{ isset($client) ? route('tenant.clients.update', $client) : route('tenant.clients.store') }}" data-ajax="true">
    @csrf
    @if(isset($client))
    @method('PUT')
    @endif

    {{-- ========== ALERTS ============== --}}
    @if(session('success'))
    <div class="alert alert-success mb-4">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            {{ $errors->first() }}
        </div>
    </div>
    @endif

    <div class="row">
        {{-- ========================================================= --}}
        {{-- LEFT --}}
        {{-- ========================================================= --}}
        <div class="col-lg-8">
            {{-- ===================================================== --}}
            {{-- IDENTIFICAÇÃO --}}
            {{-- ===================================================== --}}
            <div class="dashboard-card chart-card mb-4">
                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Identificação
                        </h3>

                        <div class="card-subtitle-modern">
                            Informações básicas do cliente.
                        </div>
                    </div>

                </div>

                <div class="row">

                    {{-- TIPO --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label required">
                                Tipo de cliente
                            </label>

                            <select name="type" id="client-type" class="form-select @error('type') is-invalid @enderror" required>

                                <option value="individual" {{ old('type', $client->type ?? 'individual') === 'individual' ? 'selected' : '' }}>
                                    Pessoa singular
                                </option>

                                <option value="company" {{ old('type', $client->type ?? '') === 'company' ? 'selected' : '' }}>
                                    Empresa
                                </option>

                            </select>

                            @error('type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- NOME --}}
                    <div class="col-md-8">

                        <div class="form-group">

                            <label class="form-label required">
                                Nome
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-user input-icon"></i>

                                <input type="text" name="name" class="auth-input @error('name') is-invalid @enderror" value="{{ old('name', $client->name ?? '') }}" placeholder="Nome completo ou nome legal da empresa" required>

                            </div>

                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- COMPANY NAME --}}
                    <div class="col-md-6 client-company-field">

                        <div class="form-group">

                            <label class="form-label">
                                Razão social
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-building input-icon"></i>

                                <input type="text" name="company_name" class="auth-input @error('company_name') is-invalid @enderror" value="{{ old('company_name', $client->company_name ?? '') }}" placeholder="Razão social">

                            </div>

                            @error('company_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- COMMERCIAL NAME --}}
                    <div class="col-md-6 client-company-field">

                        <div class="form-group">

                            <label class="form-label">
                                Nome comercial
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-store input-icon"></i>

                                <input type="text" name="commercial_name" class="auth-input @error('commercial_name') is-invalid @enderror" value="{{ old('commercial_name', $client->commercial_name ?? '') }}" placeholder="Nome comercial">

                            </div>

                            @error('commercial_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- DADOS FISCAIS --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Dados fiscais
                        </h3>

                        <div class="card-subtitle-modern">
                            Informação fiscal e tributária do cliente.
                        </div>
                    </div>

                </div>

                <div class="row">

                    {{-- NIF --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                NIF
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-id-card input-icon"></i>

                                <input type="text" name="nif" class="auth-input @error('nif') is-invalid @enderror" value="{{ old('nif', $client->nif ?? '') }}" placeholder="Número de identificação fiscal">

                            </div>

                            @error('nif')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- NIF TYPE --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                Tipo de NIF
                            </label>

                            <select name="nif_type" class="form-select @error('nif_type') is-invalid @enderror">

                                <option value="">
                                    Selecionar
                                </option>

                                <option value="individual" {{ old('nif_type', $client->nif_type ?? '') === 'individual' ? 'selected' : '' }}>
                                    Pessoa singular
                                </option>

                                <option value="company" {{ old('nif_type', $client->nif_type ?? '') === 'company' ? 'selected' : '' }}>
                                    Pessoa coletiva
                                </option>

                            </select>

                            @error('nif_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- TAX REGIME --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                Regime fiscal
                            </label>

                            <select name="tax_regime" class="form-select @error('tax_regime') is-invalid @enderror">

                                <option value="">
                                    Selecionar regime
                                </option>

                                <option value="general" {{ old('tax_regime', $client->tax_regime ?? '') === 'general' ? 'selected' : '' }}>
                                    Regime geral
                                </option>

                                <option value="simplified" {{ old('tax_regime', $client->tax_regime ?? '') === 'simplified' ? 'selected' : '' }}>
                                    Regime simplificado
                                </option>

                                <option value="exclusion" {{ old('tax_regime', $client->tax_regime ?? '') === 'exclusion' ? 'selected' : '' }}>
                                    Regime de exclusão
                                </option>

                            </select>

                            @error('tax_regime')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- VAT --}}
                    <div class="col-12">

                        <label class="custom-checkbox">

                            <input type="checkbox" name="vat_payer" value="1" {{ old('vat_payer', $client->vat_payer ?? false) ? 'checked' : '' }}>

                            <span class="checkbox-box"></span>

                            <span class="checkbox-label">

                                <strong>
                                    Sujeito passivo de IVA
                                </strong>

                                <br>

                                <small class="text-muted">
                                    Indica se o cliente é contribuinte sujeito a IVA.
                                </small>

                            </span>

                        </label>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- CONTACTOS --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Contactos
                        </h3>

                        <div class="card-subtitle-modern">
                            Informações de contacto do cliente.
                        </div>
                    </div>

                </div>

                <div class="row">

                    {{-- EMAIL --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Email
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-envelope input-icon"></i>

                                <input type="email" name="email" class="auth-input @error('email') is-invalid @enderror" value="{{ old('email', $client->email ?? '') }}" placeholder="cliente@email.com">

                            </div>

                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- PHONE --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Telefone
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-phone input-icon"></i>

                                <input type="text" name="phone" class="auth-input @error('phone') is-invalid @enderror" value="{{ old('phone', $client->phone ?? '') }}" placeholder="+244 900 000 000">

                            </div>

                            @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- SECONDARY PHONE --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Telefone secundário
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-phone-alt input-icon"></i>

                                <input type="text" name="phone_secondary" class="auth-input @error('phone_secondary') is-invalid @enderror" value="{{ old('phone_secondary', $client->phone_secondary ?? '') }}" placeholder="+244 900 000 000">

                            </div>

                            @error('phone_secondary')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- WEBSITE --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Website
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-globe input-icon"></i>

                                <input type="url" name="website" class="auth-input @error('website') is-invalid @enderror" value="{{ old('website', $client->website ?? '') }}" placeholder="https://www.exemplo.com">

                            </div>

                            @error('website')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- ENDEREÇO --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Endereço
                        </h3>

                        <div class="card-subtitle-modern">
                            Localização e endereço do cliente.
                        </div>
                    </div>

                </div>

                <div class="row">

                    {{-- ADDRESS --}}
                    <div class="col-12">

                        <div class="form-group">

                            <label class="form-label">
                                Endereço
                            </label>

                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Rua, número, edifício, bairro...">{{ old('address', $client->address ?? '') }}</textarea>

                            @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- CITY --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                Cidade
                            </label>

                            <input type="text" name="city" class="auth-input @error('city') is-invalid @enderror" value="{{ old('city', $client->city ?? '') }}" placeholder="Ex.: Luanda">

                            @error('city')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- PROVINCE --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                Província
                            </label>

                            <input type="text" name="province" class="auth-input @error('province') is-invalid @enderror" value="{{ old('province', $client->province ?? '') }}" placeholder="Ex.: Luanda">

                            @error('province')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- MUNICIPALITY --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                Município
                            </label>

                            <input type="text" name="municipality" class="auth-input @error('municipality') is-invalid @enderror" value="{{ old('municipality', $client->municipality ?? '') }}" placeholder="Ex.: Talatona">

                            @error('municipality')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- POSTAL --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                Código postal
                            </label>

                            <input type="text" name="postal_code" class="auth-input @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $client->postal_code ?? '') }}" placeholder="Código postal">

                            @error('postal_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- COUNTRY --}}
                    <div class="col-md-8">

                        <div class="form-group">

                            <label class="form-label">
                                País
                            </label>

                            <input type="text" name="country" class="auth-input @error('country') is-invalid @enderror" value="{{ old('country', $client->country ?? 'Angola') }}" placeholder="País">

                            @error('country')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- PESSOA DE CONTACTO --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Pessoa de contacto
                        </h3>

                        <div class="card-subtitle-modern">
                            Contacto responsável ou pessoa de referência.
                        </div>
                    </div>

                </div>

                <div class="row">

                    {{-- CONTACT PERSON --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                Nome
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-user-tie input-icon"></i>

                                <input type="text" name="contact_person" class="auth-input @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $client->contact_person ?? '') }}" placeholder="Nome da pessoa">

                            </div>

                            @error('contact_person')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- CONTACT PHONE --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                Telefone
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-phone input-icon"></i>

                                <input type="text" name="contact_person_phone" class="auth-input @error('contact_person_phone') is-invalid @enderror" value="{{ old('contact_person_phone', $client->contact_person_phone ?? '') }}" placeholder="+244 900 000 000">

                            </div>

                            @error('contact_person_phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- CONTACT EMAIL --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="form-label">
                                Email
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-envelope input-icon"></i>

                                <input type="email" name="contact_person_email" class="auth-input @error('contact_person_email') is-invalid @enderror" value="{{ old('contact_person_email', $client->contact_person_email ?? '') }}" placeholder="contacto@email.com">

                            </div>

                            @error('contact_person_email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- CONDIÇÕES COMERCIAIS --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Condições comerciais
                        </h3>

                        <div class="card-subtitle-modern">
                            Defina as condições comerciais aplicáveis ao cliente.
                        </div>
                    </div>

                </div>

                <div class="row">

                    {{-- CREDIT LIMIT --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Limite de crédito
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-money-bill-wave input-icon"></i>

                                <input type="number" name="credit_limit" class="auth-input @error('credit_limit') is-invalid @enderror" value="{{ old('credit_limit', $client->credit_limit ?? '0') }}" min="0" step="0.01" placeholder="0,00">

                            </div>

                            @error('credit_limit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    {{-- PAYMENT TERMS --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label class="form-label">
                                Prazo de pagamento
                            </label>

                            <div class="input-wrapper">

                                <i class="fas fa-calendar-alt input-icon"></i>

                                <input type="number" name="payment_terms" class="auth-input @error('payment_terms') is-invalid @enderror" value="{{ old('payment_terms', $client->payment_terms ?? '0') }}" min="0" step="1" placeholder="Número de dias">

                            </div>

                            <small class="text-muted">
                                Prazo em dias.
                            </small>

                            @error('payment_terms')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- OBSERVAÇÕES --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Observações
                        </h3>

                        <div class="card-subtitle-modern">
                            Notas adicionais sobre o cliente.
                        </div>
                    </div>

                </div>

                <div class="form-group mb-0">

                    <label class="form-label">
                        Notas
                    </label>

                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="5" placeholder="Escreva aqui alguma observação relevante...">{{ old('notes', $client->notes ?? '') }}</textarea>

                    @error('notes')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- ESTADO --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Estado do cliente
                        </h3>

                        <div class="card-subtitle-modern">
                            Controle a disponibilidade do cliente na organização.
                        </div>
                    </div>

                </div>

                <label class="custom-checkbox">

                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active ?? true) ? 'checked' : '' }}>

                    <span class="checkbox-box"></span>

                    <span class="checkbox-label">

                        <strong>
                            Cliente ativo
                        </strong>

                        <br>

                        <small class="text-muted">
                            O cliente estará disponível para utilização no sistema.
                        </small>

                    </span>

                </label>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- RIGHT SIDEBAR --}}
        {{-- ========================================================= --}}

        <div class="col-lg-4">

            {{-- ===================================================== --}}
            {{-- RESUMO --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Cliente
                        </h3>

                        <div class="card-subtitle-modern">
                            Resumo da identificação.
                        </div>
                    </div>

                </div>

                <div class="text-center">

                    @php
                    $clientName = $client->name ?? 'Cliente';

                    $words = preg_split('/\s+/', trim($clientName));

                    if (count($words) >= 2) {
                    $initials = mb_substr($words[0], 0, 1)
                    . mb_substr($words[count($words) - 1], 0, 1);
                    } else {
                    $initials = mb_substr($clientName, 0, 2);
                    }

                    $initials = strtoupper($initials);
                    @endphp

                    <div class="avatar avatar-lg mx-auto mb-3" style="width:80px;height:80px;font-size:22px;">

                        @if(($client->type ?? 'individual') === 'company')

                        <i class="fas fa-building"></i>

                        @else

                        {{ $initials }}

                        @endif

                    </div>

                    <div style="
                    color:#0f172a;
                    font-size:14px;
                    font-weight:700;
                ">

                        {{ $client->name ?? 'Novo cliente' }}

                    </div>

                    <div class="mt-1" style="
                        color:#64748b;
                        font-size:10px;
                    ">

                        {{ ($client->type ?? 'individual') === 'company'
                        ? 'Empresa'
                        : 'Pessoa singular' }}

                    </div>

                    @if(isset($client))

                    <div class="mt-3">

                        @if($client->is_active)

                        <span class="status status-success">
                            <i class="fas fa-check-circle"></i>
                            Ativo
                        </span>

                        @else

                        <span class="status status-danger">
                            <i class="fas fa-times-circle"></i>
                            Inativo
                        </span>

                        @endif

                    </div>

                    @endif

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- ORGANIZAÇÃO --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Organização
                        </h3>

                        <div class="card-subtitle-modern">
                            Organização à qual o cliente está associado.
                        </div>
                    </div>

                </div>

                <div class="p-3" style="
                    background:#eff6ff;
                    border-radius:12px;
                ">

                    <div class="d-flex align-items-center">

                        <div class="tenant-avatar mr-3">

                            <i class="fas fa-building"></i>

                        </div>

                        <div>

                            <div style="
                            font-size:12px;
                            font-weight:700;
                            color:#2563eb;
                        ">

                                {{ $tenant->name ?? 'Organização atual' }}

                            </div>

                            <div style="
                            font-size:10px;
                            color:#64748b;
                        ">
                                Tenant atual
                            </div>

                        </div>

                    </div>

                </div>

                <div class="mt-3">

                    <div class="d-flex align-items-center" style="
                        font-size:11px;
                        color:#64748b;
                    ">

                        <i class="fas fa-shield-alt mr-2" style="color:#10b981;"></i>

                        Os dados deste cliente estão associados ao tenant atual.

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- CONTACTO RÁPIDO --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card mb-4">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Contacto
                        </h3>

                        <div class="card-subtitle-modern">
                            Principais informações de contacto.
                        </div>
                    </div>

                </div>

                @if(isset($client))

                <div class="activity-item">

                    <div class="activity-icon">
                        <i class="fas fa-phone"></i>
                    </div>

                    <div>

                        <div class="activity-title">
                            Telefone
                        </div>

                        <div class="activity-time">
                            {{ $client->phone ?: 'Não informado' }}
                        </div>

                    </div>

                </div>

                <div class="activity-item">

                    <div class="activity-icon">
                        <i class="fas fa-envelope"></i>
                    </div>

                    <div>

                        <div class="activity-title">
                            Email
                        </div>

                        <div class="activity-time">
                            {{ $client->email ?: 'Não informado' }}
                        </div>

                    </div>

                </div>

                <div class="activity-item">

                    <div class="activity-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>

                    <div>

                        <div class="activity-title">
                            Localização
                        </div>

                        <div class="activity-time">

                            {{ $client->city
                                ?: $client->municipality
                                ?: 'Não informado' }}

                        </div>

                    </div>

                </div>

                @else

                <div class="text-center py-3">

                    <div style="
                            width:42px;
                            height:42px;
                            margin:0 auto 10px;
                            border-radius:11px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            background:#eff6ff;
                            color:#2563eb;
                        ">
                        <i class="fas fa-user-plus"></i>
                    </div>

                    <div style="
                        color:#475569;
                        font-size:12px;
                        font-weight:600;
                    ">
                        Novo cliente
                    </div>

                    <div class="mt-1" style="
                            color:#94a3b8;
                            font-size:10px;
                        ">
                        Preencha os dados para criar o cliente.
                    </div>

                </div>

                @endif

            </div>

            {{-- ===================================================== --}}
            {{-- INFORMAÇÃO --}}
            {{-- ===================================================== --}}

            <div class="dashboard-card chart-card">

                <div class="card-header-modern">

                    <div>
                        <h3 class="card-title-modern">
                            Informação
                        </h3>

                        <div class="card-subtitle-modern">
                            Informação do registo.
                        </div>
                    </div>

                </div>

                @if(isset($client))

                <div class="activity-item">

                    <div class="activity-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>

                    <div>

                        <div class="activity-title">
                            Criado em
                        </div>

                        <div class="activity-time">
                            {{ $client->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </div>

                    </div>

                </div>

                <div class="activity-item">

                    <div class="activity-icon">
                        <i class="fas fa-clock"></i>
                    </div>

                    <div>

                        <div class="activity-title">
                            Última atualização
                        </div>

                        <div class="activity-time">
                            {{ $client->updated_at?->format('d/m/Y H:i') ?? '-' }}
                        </div>

                    </div>

                </div>

                @else

                <div class="text-center py-3">

                    <div style="
                            width:42px;
                            height:42px;
                            margin:0 auto 10px;
                            border-radius:11px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            background:#eff6ff;
                            color:#2563eb;
                        ">
                        <i class="fas fa-user-plus"></i>
                    </div>

                    <div style="
                        color:#475569;
                        font-size:12px;
                        font-weight:600;
                    ">
                        Novo cliente
                    </div>

                    <div class="mt-1" style="
                            color:#94a3b8;
                            font-size:10px;
                        ">
                        Preencha os dados para criar o cliente.
                    </div>

                </div>

                @endif

            </div>

        </div>

    </div>

</form>
@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('client-type');
        const companyFields = document.querySelectorAll('.client-company-field');

        function toggleCompanyFields() {
            const isCompany = typeSelect.value === 'company';
            companyFields.forEach(function(field) {
                field.style.display = isCompany ? '' : 'none';
            });
        }

        if (typeSelect) {
            toggleCompanyFields();
            typeSelect.addEventListener('change', toggleCompanyFields);
        }
    });
</script>

@endpush
