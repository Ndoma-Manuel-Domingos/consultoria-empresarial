@extends('layouts.app')

@section('title', 'Facturação')

@section('page_header')

<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Facturação
        </h1>

        <div class="page-subtitle">
            Gestão de facturas, documentos e vendas.
        </div>
    </div>
    <div>
        <a href="{{ route('tenant.invoices.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Nova factura
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
@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ $errors->first() }}
</div>
@endif

<div class="dashboard-card chart-card">
    <div class="card-header-modern">
        <div>
            <h3 class="card-title-modern">
                Documentos emitidos
            </h3>
            <div class="card-subtitle-modern">
                Consulte e acompanhe as suas facturas.
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">
                    Pesquisa
                </label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Número, NIF ou cliente...">
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    Estado
                </label>
                <select name="status" class="form-control">
                    <option value="">Todos</option>
                    <option value="completed" @selected(request('status')==='completed' )>Concluídas</option>
                    <option value="cancelled" @selected(request('status')==='cancelled' )>Anuladas</option>
                    <option value="draft" @selected(request('status')==='draft' )>Rascunhos</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">
                    Tipo
                </label>
                <select name="document_type" class="form-control">
                    <option value="">Todos</option>
                    <option value="invoice" @selected(request('document_type')==='invoice' )>Factura</option>
                    <option value="receipt" @selected(request('document_type')==='receipt' )>Recibo</option>
                    <option value="proforma" @selected(request('document_type')==='proforma' )>Proforma</option>
                    <option value="quotation" @selected(request('document_type')==='quotation' )>Orçamento</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">
                    De
                </label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">
                    Até
                </label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-secondary">
                <i class="fas fa-search mr-1"></i>
                Filtrar
            </button>
            <a href="{{ route('tenant.invoices.index') }}" class="btn btn-light">
                Limpar
            </a>
        </div>
    </form>

    {{-- TABELA --}}
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Estado</th>
                    <th>Pagamento</th>
                    <th class="text-right">Total</th>
                    <th width="120"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td>
                        <strong>
                            {{ $sale->number }}
                        </strong>
                        <div class="text-muted small">
                            {{ strtoupper($sale->document_type) }}
                        </div>
                    </td>
                    <td>
                        {{ $sale->client?->name ?? 'Consumidor final' }}
                        @if($sale->client?->nif)
                        <div class="text-muted small">
                            NIF: {{ $sale->client->nif }}
                        </div>
                        @endif
                    </td>
                    <td>
                        {{ $sale->sale_date?->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        @if($sale->status === 'completed')
                        <span class="badge badge-success">
                            Emitida
                        </span>
                        @elseif($sale->status === 'cancelled')
                        <span class="badge badge-danger">
                            Anulada
                        </span>
                        @else
                        <span class="badge badge-secondary">
                            Rascunho
                        </span>
                        @endif
                    </td>
                    <td>
                        @if($sale->payment_method === 'cash')
                        Numerário
                        @elseif($sale->payment_method === 'multicaixa')
                        Multicaixa
                        @elseif($sale->payment_method === 'mixed')
                        Misto
                        @elseif($sale->payment_method === 'credit')
                        Crédito
                        @else
                        —
                        @endif
                    </td>
                    <td class="text-right">
                        <strong>
                            {{ number_format($sale->total, 2, ',', '.') }}
                            Kz
                        </strong>
                    </td>
                    <td class="text-right">
                        <a href="{{ route('tenant.invoices.show', $sale->id) }}" class="btn btn-sm btn-light" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('tenant.invoices.invoice', $sale->id) }}" target="_blank" class="btn btn-sm btn-light" title="Factura">
                            <i class="fas fa-file-invoice"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding:50px;color:#94a3b8;">
                        <i class="fas fa-file-invoice" style="font-size:35px;display:block;margin-bottom:12px;"></i>
                        Nenhum documento encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $sales->links() }}
    </div>
</div>
@endsection
