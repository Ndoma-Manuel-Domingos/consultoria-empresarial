@extends('layouts.app')

@section('title', 'Recepção')

@section('page_header')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Recepção</h1>
        <div class="page-subtitle">
            Gira os atendimentos, triagens, serviços e pagamentos da sua organização.
        </div>
    </div>

    <div>
        <a href="{{ route('tenant.reception.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>
            Novo atendimento
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- ALERTAS --}}
@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle mr-1"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-1"></i>
    {{ $errors->first() }}
</div>
@endif


{{-- ESTATÍSTICAS --}}
<div class="row">

    <div class="col-lg-3 col-md-6">
        <div class="dashboard-card chart-card mb-4">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width:46px;height:46px;border-radius:12px;background:#eff6ff;
                            display:flex;align-items:center;justify-content:center;color:#2563eb;">
                    <i class="fas fa-clock"></i>
                </div>

                <div>
                    <div style="font-size:11px;color:#64748b;">
                        Aguardando
                    </div>
                    <div style="font-size:22px;font-weight:700;color:#0f172a;">
                        {{ $stats['waiting'] ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-3 col-md-6">
        <div class="dashboard-card chart-card mb-4">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width:46px;height:46px;border-radius:12px;background:#fff7ed;
                            display:flex;align-items:center;justify-content:center;color:#ea580c;">
                    <i class="fas fa-stethoscope"></i>
                </div>

                <div>
                    <div style="font-size:11px;color:#64748b;">
                        Em triagem
                    </div>
                    <div style="font-size:22px;font-weight:700;color:#0f172a;">
                        {{ $stats['triage'] ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-3 col-md-6">
        <div class="dashboard-card chart-card mb-4">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width:46px;height:46px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#16a34a;">
                    <i class="fas fa-user-clock"></i>
                </div>

                <div>
                    <div style="font-size:11px;color:#64748b;">
                        Em atendimento
                    </div>
                    <div style="font-size:22px;font-weight:700;color:#0f172a;">
                        {{ $stats['in_progress'] ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-3 col-md-6">
        <div class="dashboard-card chart-card mb-4">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width:46px;height:46px;border-radius:12px;background:#f5f3ff;display:flex;align-items:center;justify-content:center;color:#7c3aed;">
                    <i class="fas fa-check-circle"></i>
                </div>

                <div>
                    <div style="font-size:11px;color:#64748b;">
                        Concluídos
                    </div>
                    <div style="font-size:22px;font-weight:700;color:#0f172a;">
                        {{ $stats['completed'] ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


{{-- FILTROS --}}
<div class="dashboard-card chart-card mb-4">

    <div class="card-header-modern">
        <div>
            <h3 class="card-title-modern">
                <i class="fas fa-filter mr-1"></i>
                Filtros
            </h3>

            <div class="card-subtitle-modern">
                Pesquise e filtre os atendimentos da recepção.
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('tenant.reception.index') }}">

        <div class="row">

            <div class="col-lg-4">
                <div class="form-group">
                    <label class="form-label">
                        Pesquisa
                    </label>

                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Código, cliente ou NIF...">
                </div>
            </div>


            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label">
                        Estado
                    </label>

                    <select name="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Aguardando</option>
                        <option value="triage" {{ request('status') === 'triage' ? 'selected' : '' }}>Em triagem</option>
                        <option value="awaiting_payment" {{ request('status') === 'awaiting_payment' ? 'selected' : '' }}>Aguardando pagamento</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pago</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em atendimento</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluído</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label class="form-label">
                        Prioridade
                    </label>
                    <select name="priority" class="form-control">
                        <option value="">Todas</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>
            </div>


            <div class="col-lg-2">
                <div class="form-group">
                    <label class="form-label">
                        Data
                    </label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
            </div>

            <div class="col-lg-1 d-flex align-items-end">
                <div class="form-group w-100">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- LISTA --}}
<div class="dashboard-card chart-card">
    <div class="card-header-modern">
        <div>
            <h3 class="card-title-modern">
                Atendimentos
            </h3>
            <div class="card-subtitle-modern">
                Fila de atendimento da recepção.
            </div>
        </div>
        <div>
            <span class="badge badge-light">
                {{ $appointments->total() ?? 0 }} registos
            </span>
        </div>
    </div>
    <div class="table-responsive">
        @include('tenant.reception.partials.table', [
        'appointments' => $appointments
        ])
    </div>
    @if(method_exists($appointments, 'links'))
    <div class="mt-3">
        {{ $appointments->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
