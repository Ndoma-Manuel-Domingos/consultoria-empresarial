@extends('layouts.app')

@section('title', 'Detalhes do produto')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title"> Detalhes do produto </h1>
        <div class="page-subtitle">
            Consulte as informações comerciais, fiscais e de stock deste produto.
        </div>
    </div>

    <div class="d-flex align-items-center">
        <a href="{{ route('tenant.products.index') }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>

        <a href="{{ route('tenant.products.edit', $product) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i>
            Editar produto
        </a>
    </div>

</div> 
@endsection
@section('content')

{{-- ================= ALERTS ================= --}}
@if(session('success'))
<div class="alert alert-success mb-4"> <i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span> </div> 
@endif

@if($errors->any())
<div class="alert alert-danger mb-4"> <i class="fas fa-exclamation-circle"></i> <span>{{ $errors->first() }}</span> </div> 
@endif 


<div class="row">
    {{-- ===================== LEFT ========================== --}}
    <div class="col-lg-8">
        {{-- ================= IDENTIFICAÇÃO ================= --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Informações do produto
                    </h3>

                    <div class="card-subtitle-modern">
                        Dados principais de identificação e classificação.
                    </div>
                </div>
                <div>
                    @if($product->is_active)
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
            </div>

            <div class="row">

                {{-- Código --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Código
                        </label>

                        <div class="p-2" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;">
                            <span style="font-size:12px;font-weight:600;color:#334155;">
                                {{ $product->code ?: '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Código de barras --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Código de barras
                        </label>

                        <div class="p-2" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;">
                            <span style="font-size:12px;color:#475569;">
                                {{ $product->barcode ?: '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Tipo --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Tipo
                        </label>

                        <div class="p-2" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;">

                            @if($product->type === 'product')
                            <span class="status status-info">
                                <i class="fas fa-box"></i>
                                Produto
                            </span>
                            @elseif($product->type === 'service')
                            <span class="status status-neutral">
                                <i class="fas fa-concierge-bell"></i>
                                Serviço
                            </span>
                            @else
                            <span class="status status-neutral">
                                {{ ucfirst($product->type ?? '-') }}
                            </span>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- Nome --}}
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="form-label">
                            Nome
                        </label>

                        <div style="font-size:16px;font-weight:700;color:#0f172a;">
                            {{ $product->name }}
                        </div>

                        @if($product->short_name)
                        <div style="margin-top:3px;color:#94a3b8;font-size:11px;">
                            {{ $product->short_name }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Unidade --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Unidade
                        </label>

                        <div class="p-2" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;">
                            <strong style="font-size:12px;">
                                {{ $product->unit ?: '-' }}
                            </strong>
                        </div>
                    </div>
                </div>

                {{-- Categoria --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Categoria
                        </label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $product->category ?: 'Sem categoria' }}
                        </div>
                    </div>
                </div>

                {{-- Subcategoria --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            Subcategoria
                        </label>

                        <div style="font-size:12px;color:#475569;">
                            {{ $product->subcategory ?: '-' }}
                        </div>
                    </div>
                </div>

                {{-- Marca --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">
                            Marca
                        </label>
                        <div style="font-size:12px;color:#475569;">
                            {{ $product->brand ?: '-' }}
                        </div>
                    </div>
                </div>
                {{-- Modelo --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">
                            Modelo
                        </label>
                        <div style="font-size:12px;color:#475569;">
                            {{ $product->model ?: '-' }}
                        </div>
                    </div>
                </div>
                {{-- Descrição --}}
                <div class="col-12">
                    <div class="form-group mb-0">
                        <label class="form-label">
                            Descrição
                        </label>
                        <div style="padding:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;color:#475569;font-size:12px;line-height:1.6;">
                            {{ $product->description ?: 'Sem descrição.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= PREÇOS ================= --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Preços e margem
                    </h3>
                    <div class="card-subtitle-modern">
                        Informação comercial do produto.
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- Custo --}}
                <div class="col-md-4">
                    <div style="padding:15px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;">
                        <div style="color:#64748b;font-size:10px;font-weight:600;">
                            PREÇO DE CUSTO
                        </div>
                        <div style="margin-top:5px;color:#0f172a;font-size:20px;font-weight:700;">
                            {{ number_format((float)$product->cost_price, 2, ',', '.') }}
                            <small style="font-size:10px;color:#94a3b8;">Kz</small>
                        </div>
                    </div>
                </div>

                {{-- Venda --}}
                <div class="col-md-4">
                    <div style="padding:15px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;">
                        <div style="color:#2563eb;font-size:10px;font-weight:600;">
                            PREÇO DE VENDA
                        </div>
                        <div style="margin-top:5px;color:#1d4ed8;font-size:20px;font-weight:700;">
                            {{ number_format((float)$product->sale_price, 2, ',', '.') }}
                            <small style="font-size:10px;">Kz</small>
                        </div>
                    </div>
                </div>

                {{-- Margem --}}
                <div class="col-md-4">
                    <div style="padding:15px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;">
                        <div style="color:#059669;font-size:10px;font-weight:600;">
                            MARGEM
                        </div>
                        <div style="margin-top:5px;color:#047857;font-size:20px;font-weight:700;">
                            {{ number_format((float)$product->margin_percent, 2, ',', '.') }}
                            <small style="font-size:11px;">%</small>
                        </div>
                    </div>
                </div>

                {{-- Preço com IVA --}}
                <div class="col-md-4 mt-3">
                    <div class="form-group mb-0">
                        <label class="form-label">
                            Preço de venda com imposto
                        </label>
                        <div style="font-size:14px;font-weight:700;color:#334155;">
                            {{ number_format((float)$product->sale_price_with_tax, 2, ',', '.') }}
                            Kz
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= FISCALIDADE ================= --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Fiscalidade
                    </h3>

                    <div class="card-subtitle-modern">
                        Configuração fiscal aplicada ao produto.
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-4">
                    <label class="form-label">
                        Tratamento fiscal
                    </label>

                    @if($product->tax_type === 'standard')
                    <span class="status status-info">
                        IVA normal
                    </span>
                    @elseif($product->tax_type === 'exempt')
                    <span class="status status-warning">
                        Isento de IVA
                    </span>
                    @elseif($product->tax_type === 'zero')
                    <span class="status status-neutral">
                        IVA à taxa zero
                    </span>
                    @else
                    <span class="status status-neutral">
                        -
                    </span>
                    @endif
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Taxa de IVA
                    </label>

                    <div style="font-size:13px;font-weight:600;color:#334155;">
                        {{ number_format((float)$product->tax_rate, 2, ',', '.') }} %
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Código de isenção
                    </label>

                    <div style="font-size:12px;color:#475569;">
                        {{ $product->tax_exemption_code ?: '-' }}
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <label class="form-label">
                        Motivo da isenção
                    </label>

                    <div style="font-size:12px;color:#64748b;">
                        {{ $product->tax_exemption_reason ?: '-' }}
                    </div>
                </div>

            </div>

        </div>

        {{-- ================= STOCK ================= --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Gestão de stock
                    </h3>
                    <div class="card-subtitle-modern">
                        Configurações utilizadas no controlo de inventário.
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- Gestão stock --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Gestão de stock
                    </label>

                    @if($product->manage_stock)
                    <span class="status status-success">
                        <i class="fas fa-check"></i>
                        Ativa
                    </span>
                    @else
                    <span class="status status-neutral">
                        <i class="fas fa-minus"></i>
                        Desativada
                    </span>
                    @endif
                </div>

                {{-- Stock negativo --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Stock negativo
                    </label>

                    @if($product->allow_negative_stock)
                    <span class="status status-warning">
                        Permitido
                    </span>
                    @else
                    <span class="status status-success">
                        Bloqueado
                    </span>
                    @endif
                </div>

                {{-- Unidade --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Unidade de stock
                    </label>

                    <div style="font-size:12px;font-weight:600;color:#334155;">
                        {{ $product->unit ?: '-' }}
                    </div>
                </div>

                {{-- Mínimo --}}
                <div class="col-md-4 mt-3">
                    <label class="form-label">
                        Stock mínimo
                    </label>

                    <div style="font-size:14px;font-weight:700;color:#334155;">
                        {{ number_format((float)$product->minimum_stock, 3, ',', '.') }}
                    </div>
                </div>

                {{-- Máximo --}}
                <div class="col-md-4 mt-3">
                    <label class="form-label">
                        Stock máximo
                    </label>
                    <div style="font-size:14px;font-weight:700;color:#334155;">
                        {{ number_format((float)$product->maximum_stock, 3, ',', '.') }}
                    </div>
                </div>

                {{-- Actual --}}
                <div class="col-md-4 mt-3">
                    <label class="form-label">
                        Stock Actual
                    </label>
                    <div style="font-size:14px;font-weight:700;color:#334155;">
                        {{ number_format((float)$product->stock_quantity, 3, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

                {{-- ================= LOTES ================= --}}
        <div class="dashboard-card chart-card mb-4">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Lotes e validade
                    </h3>
                    <div class="card-subtitle-modern">
                        Configurações para controlo de lotes e produtos expirados.
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">
                        Gestão de lotes
                    </label>
                    @if($product->manage_lots)
                    <span class="status status-success">
                        <i class="fas fa-check"></i>
                        Ativa
                    </span>
                    @else
                    <span class="status status-neutral">
                        Desativada
                    </span>
                    @endif
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        Controlo de validade
                    </label>
                    @if($product->has_expiration)
                    <span class="status status-warning">
                        <i class="fas fa-calendar-alt"></i>
                        Ativo
                    </span>
                    @else
                    <span class="status status-neutral">
                        Não aplicável
                    </span>
                    @endif
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        Alerta de validade
                    </label>

                    <div style="font-size:13px;font-weight:600;color:#334155;">
                        {{ $product->expiration_alert_days ?? 0 }}
                        <span style="font-size:10px;color:#94a3b8;">
                            dias antes
                        </span>
                    </div>
                </div>

            </div>

        </div>

    </div>
    {{-- ===================== RIGHT ========================= --}}
    <div class="col-lg-4">
        {{-- ================= STATUS ================= --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Estado comercial
                    </h3>

                    <div class="card-subtitle-modern">
                        Disponibilidade do produto nas operações.
                    </div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>

                <div>
                    <div class="activity-title">
                        Venda
                    </div>

                    <div class="activity-time">
                        @if($product->is_sellable)
                        <span style="color:#059669;">
                            Permitida
                        </span>
                        @else
                        <span style="color:#dc2626;">
                            Bloqueada
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-truck"></i>
                </div>

                <div>
                    <div class="activity-title">
                        Compra
                    </div>

                    <div class="activity-time">
                        @if($product->is_purchasable)
                        <span style="color:#059669;">
                            Permitida
                        </span>
                        @else
                        <span style="color:#dc2626;">
                            Bloqueada
                        </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= FORNECEDOR ================= --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Fornecedor
                    </h3>

                    <div class="card-subtitle-modern">
                        Fornecedor principal associado.
                    </div>
                </div>
            </div>
            @if($product->supplier)
            <div class="d-flex align-items-center">

                <div class="tenant-avatar mr-3">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <div style="color:#334155;font-size:13px;font-weight:700;">
                        {{ $product->supplier->name }}
                    </div>
                    @if(isset($product->supplier->email))
                    <div style="margin-top:3px;color:#94a3b8;font-size:10px;">
                        {{ $product->supplier->email }}
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="text-center py-3">
                <div style="width:42px;height:42px;margin:0 auto 10px;display:flex;align-items:center;justify-content:center;border-radius:11px;background:#f8fafc;color:#94a3b8;">
                    <i class="fas fa-truck"></i>
                </div>
                <div style="color:#475569;font-size:12px;font-weight:600;">
                    Sem fornecedor
                </div>
                <div style="margin-top:3px;color:#94a3b8;font-size:10px;">
                    Nenhum fornecedor associado.
                </div>
            </div>
            @endif
        </div>

        {{-- ================= OBSERVAÇÕES ================= --}}
        <div class="dashboard-card chart-card mb-4">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Observações
                    </h3>

                    <div class="card-subtitle-modern">
                        Notas internas sobre o produto.
                    </div>
                </div>
            </div>

            <div style="padding:12px;background:#f8fafc;border-radius:9px;color:#64748b;font-size:11px;line-height:1.6;">
                {{ $product->notes ?: 'Nenhuma observação registada.' }}
            </div>

        </div>

        {{-- ================= AUDITORIA ================= --}}
        <div class="dashboard-card chart-card">

            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Informação
                    </h3>

                    <div class="card-subtitle-modern">
                        Dados de criação e atualização.
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
                        {{ $product->created_at?->format('d/m/Y H:i') ?? '-' }}
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
                        {{ $product->updated_at?->format('d/m/Y H:i') ?? '-' }}
                    </div>
                </div>

            </div>

            @if($product->createdBy)
            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-user"></i>
                </div>

                <div>
                    <div class="activity-title">
                        Criado por
                    </div>

                    <div class="activity-time">
                        {{ $product->createdBy->name }}
                    </div>
                </div>

            </div>
            @endif

            @if($product->updatedBy)
            <div class="activity-item">

                <div class="activity-icon">
                    <i class="fas fa-user-edit"></i>
                </div>

                <div>
                    <div class="activity-title">
                        Atualizado por
                    </div>

                    <div class="activity-time">
                        {{ $product->updatedBy->name }}
                    </div>
                </div>

            </div>
            @endif

        </div>

    </div>
</div>


@endsection