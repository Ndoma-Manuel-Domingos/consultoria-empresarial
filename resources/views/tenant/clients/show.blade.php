@extends('layouts.app')

@section('title', 'Detalhes do cliente')

@section('page_header')

<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title"> Detalhes do cliente </h1>
        <div class="page-subtitle"> Consulte as informações e os dados comerciais deste cliente. </div>
    </div>
    <div class="d-flex align-items-center">
        <a href="{{ route('tenant.clients.index') }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>

        <a href="{{ route('tenant.clients.edit', $client) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i>
            Editar cliente
        </a>
    </div>

</div> @endsection
@section('content')

{{-- ========== ALERTS ========== --}}
@if(session('success'))

<div class="alert alert-success mb-4"> <i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span> </div> @endif
@if($errors->any())

<div class="alert alert-danger mb-4"> <i class="fas fa-exclamation-circle"></i> <span>{{ $errors->first() }}</span> </div> @endif <div class="row">
    {{-- ========================================================= --}}
    {{-- LEFT COLUMN                                               --}}
    {{-- ========================================================= --}}
    <div class="col-lg-8">

        {{-- ========== CABEÇALHO DO CLIENTE ========== --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="d-flex align-items-center justify-content-between">

                <div class="d-flex align-items-center">

                    <div class="avatar avatar-lg mr-3">
                        {{ strtoupper(substr($client->name ?? 'C', 0, 1)) }}
                    </div>

                    <div>
                        <h2 style="
                        margin:0;
                        color:#0f172a;
                        font-size:18px;
                        font-weight:700;
                    ">
                            {{ $client->name }}
                        </h2>

                        @if($client->type === 'company' && $client->commercial_name)
                        <div style="
                        margin-top:3px;
                        color:#64748b;
                        font-size:11px;
                    ">
                            {{ $client->commercial_name }}
                        </div>
                        @endif

                        <div class="mt-2">
                            @if($client->type === 'company')
                            <span class="status status-info">
                                <i class="fas fa-building"></i>
                                Empresa
                            </span>
                            @else
                            <span class="status status-neutral">
                                <i class="fas fa-user"></i>
                                Pessoa singular
                            </span>
                            @endif

                            @if($client->is_active)
                            <span class="status status-success ml-1">
                                <i class="fas fa-check-circle"></i>
                                Ativo
                            </span>
                            @else
                            <span class="status status-danger ml-1">
                                <i class="fas fa-times-circle"></i>
                                Inativo
                            </span>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- ========== IDENTIFICAÇÃO ========== --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Identificação
                    </h3>

                    <div class="card-subtitle-modern">
                        Dados de identificação do cliente.
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            Nome
                        </label>

                        <div style="font-size:13px;color:#1e293b;font-weight:600;">
                            {{ $client->name ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            Tipo
                        </label>

                        <div>
                            @if($client->type === 'company')
                            <span class="status status-info">
                                <i class="fas fa-building"></i>
                                Empresa
                            </span>
                            @else
                            <span class="status status-neutral">
                                <i class="fas fa-user"></i>
                                Pessoa singular
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($client->company_name)
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            Razão social
                        </label>

                        <div style="font-size:13px;color:#475569;">
                            {{ $client->company_name }}
                        </div>
                    </div>
                </div>
                @endif

                @if($client->commercial_name)
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            Nome comercial
                        </label>

                        <div style="font-size:13px;color:#475569;">
                            {{ $client->commercial_name }}
                        </div>
                    </div>
                </div>
                @endif

            </div>

        </div>

        {{-- ========== DADOS FISCAIS ========== --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Dados fiscais
                    </h3>

                    <div class="card-subtitle-modern">
                        Informações fiscais e tributárias do cliente.
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            NIF
                        </label>

                        <div style="font-size:13px;color:#1e293b;font-weight:600;">
                            {{ $client->nif ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Tipo de NIF
                        </label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->nif_type ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Regime fiscal
                        </label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->tax_regime ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-12">

                    <label class="custom-checkbox" style="cursor:default;">

                        <input type="checkbox" disabled {{ $client->vat_payer ? 'checked' : '' }}>

                        <span class="checkbox-box"></span>

                        <span class="checkbox-label">
                            <strong>
                                Sujeito a IVA
                            </strong>
                            <br>

                            <small class="text-muted">
                                {{ $client->vat_payer
                                ? 'Este cliente é considerado sujeito passivo de IVA.'
                                : 'Este cliente não está marcado como sujeito passivo de IVA.'
                            }}
                            </small>
                        </span>

                    </label>

                </div>

            </div>

        </div>

        {{-- ========== CONTACTOS ========== --}}
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

                <div class="col-md-6">
                    <div class="form-group">

                        <label class="form-label">
                            Email
                        </label>

                        @if($client->email)
                        <div class="d-flex align-items-center">
                            <div class="activity-icon mr-2">
                                <i class="fas fa-envelope"></i>
                            </div>

                            <a href="mailto:{{ $client->email }}" style="font-size:12px;color:#2563eb;">
                                {{ $client->email }}
                            </a>
                        </div>
                        @else
                        <div style="color:#94a3b8;font-size:12px;">
                            Não informado
                        </div>
                        @endif

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">

                        <label class="form-label">
                            Telefone
                        </label>

                        @if($client->phone)
                        <div class="d-flex align-items-center">
                            <div class="activity-icon mr-2">
                                <i class="fas fa-phone"></i>
                            </div>

                            <a href="tel:{{ $client->phone }}" style="font-size:12px;color:#2563eb;">
                                {{ $client->phone }}
                            </a>
                        </div>
                        @else
                        <div style="color:#94a3b8;font-size:12px;">
                            Não informado
                        </div>
                        @endif

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">

                        <label class="form-label">
                            Telefone secundário
                        </label>

                        @if($client->phone_secondary)
                        <div style="font-size:12px;color:#475569;">
                            {{ $client->phone_secondary }}
                        </div>
                        @else
                        <div style="color:#94a3b8;font-size:12px;">
                            Não informado
                        </div>
                        @endif

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">

                        <label class="form-label">
                            Website
                        </label>

                        @if($client->website)

                        <a href="{{ $client->website }}" target="_blank" rel="noopener noreferrer" style="font-size:12px;color:#2563eb;">
                            <i class="fas fa-globe mr-1"></i>
                            {{ $client->website }}
                        </a>

                        @else

                        <div style="color:#94a3b8;font-size:12px;">
                            Não informado
                        </div>

                        @endif

                    </div>
                </div>

            </div>

        </div>

        {{-- ========== ENDEREÇO ========== --}}
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

                <div class="col-md-12">
                    <div class="form-group">

                        <label class="form-label">
                            Endereço
                        </label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->address ?: '-' }}
                        </div>

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Cidade</label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->city ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Província</label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->province ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Município</label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->municipality ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Código postal</label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->postal_code ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">País</label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->country ?: '-' }}
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ========== PESSOA DE CONTACTO ========== --}}
        @if(
        $client->contact_person ||
        $client->contact_person_phone ||
        $client->contact_person_email
        )

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Pessoa de contacto
                    </h3>

                    <div class="card-subtitle-modern">
                        Pessoa responsável pelo contacto com o cliente.
                    </div>
                </div>
            </div>

            <div class="row">

                @if($client->contact_person)
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Nome
                        </label>

                        <div style="font-size:13px;color:#1e293b;font-weight:600;">
                            {{ $client->contact_person }}
                        </div>
                    </div>
                </div>
                @endif

                @if($client->contact_person_phone)
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Telefone
                        </label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->contact_person_phone }}
                        </div>
                    </div>
                </div>
                @endif

                @if($client->contact_person_email)
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Email
                        </label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $client->contact_person_email }}
                        </div>
                    </div>
                </div>
                @endif

            </div>

        </div>

        @endif

        {{-- ========== OBSERVAÇÕES ========== --}}
        @if($client->notes)

        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Observações
                    </h3>

                    <div class="card-subtitle-modern">
                        Notas internas relacionadas ao cliente.
                    </div>
                </div>
            </div>

            <div style="
            padding:14px;
            color:#475569;
            background:#f8fafc;
            border:1px solid #f1f5f9;
            border-radius:10px;
            font-size:12px;
            line-height:1.7;
            white-space:pre-line;
        ">
                {{ $client->notes }}
            </div>

        </div>

        @endif

    </div>

    {{-- ========================================================= --}}
    {{-- RIGHT COLUMN                                              --}}
    {{-- ========================================================= --}}
    <div class="col-lg-4">

        {{-- ========== ESTADO ========== --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Estado
                    </h3>

                    <div class="card-subtitle-modern">
                        Estado atual do cliente.
                    </div>
                </div>
            </div>

            <div class="text-center" style="
                padding:15px;
                background:{{ $client->is_active ? '#ecfdf5' : '#fef2f2' }};
                border-radius:12px;
            ">

                <div style="
                    width:44px;
                    height:44px;
                    margin:0 auto 10px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    border-radius:11px;
                    background:{{ $client->is_active ? '#d1fae5' : '#fee2e2' }};
                    color:{{ $client->is_active ? '#059669' : '#dc2626' }};
                ">
                    <i class="fas {{ $client->is_active ? 'fa-check' : 'fa-ban' }}"></i>
                </div>

                <div style="
                color:{{ $client->is_active ? '#047857' : '#b91c1c' }};
                font-size:13px;
                font-weight:700;
            ">
                    {{ $client->is_active ? 'Cliente ativo' : 'Cliente inativo' }}
                </div>

                <div style="
                margin-top:4px;
                color:#64748b;
                font-size:10px;
            ">
                    {{ $client->is_active
                    ? 'O cliente está disponível no sistema.'
                    : 'O cliente está atualmente desativado.'
                }}
                </div>

            </div>

        </div>

        {{-- ========== CONDIÇÕES COMERCIAIS ========== --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Condições comerciais
                    </h3>

                    <div class="card-subtitle-modern">
                        Condições de crédito e pagamento.
                    </div>
                </div>
            </div>

            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-wallet"></i>
                </div>

                <div>
                    <div class="activity-title">
                        Limite de crédito
                    </div>

                    <div class="activity-time">
                        {{ number_format($client->credit_limit ?? 0, 2, ',', '.') }}
                    </div>
                </div>

            </div>

            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>

                <div>
                    <div class="activity-title">
                        Prazo de pagamento
                    </div>

                    <div class="activity-time">
                        {{ $client->payment_terms ?? 0 }}
                        {{ ($client->payment_terms ?? 0) == 1 ? 'dia' : 'dias' }}
                    </div>
                </div>

            </div>

        </div>

        {{-- ========== AUDITORIA ========== --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Informação
                    </h3>

                    <div class="card-subtitle-modern">
                        Registo e atualização do cliente.
                    </div>
                </div>
            </div>

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

            @if($client->created_by)
            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-user-plus"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Criado por
                    </div>

                    <div class="activity-time">
                        {{ $client->created_by }}
                    </div>

                </div>

            </div>
            @endif

            @if($client->updated_by)
            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-user-edit"></i>
                </div>

                <div>

                    <div class="activity-title">
                        Atualizado por
                    </div>

                    <div class="activity-time">
                        {{ $client->updated_by }}
                    </div>

                </div>

            </div>
            @endif

        </div>

        {{-- ========== AÇÕES ========== --}}
        <div class="dashboard-card chart-card">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Ações
                    </h3>

                    <div class="card-subtitle-modern">
                        Operações disponíveis para este cliente.
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column">

                <a href="{{ route('tenant.clients.edit', $client) }}" class="btn btn-primary btn-sm mb-2">
                    <i class="fas fa-edit"></i>
                    Editar cliente
                </a>

                <a href="{{ route('tenant.clients.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i>
                    Voltar para clientes
                </a>

            </div>

        </div>

    </div>

</div>
@endsection
