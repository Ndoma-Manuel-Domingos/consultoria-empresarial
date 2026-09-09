@extends('layouts.app')

@section('title', 'Dashboard')

@section('page_header')

<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">
            Dashboard
        </h1>
        <div class="page-subtitle">
            Visão geral da sua organização
        </div>
    </div>
    <div>
        <button class="btn btn-primary btn-sm">
            <i class="fas fa-download mr-1"></i>
            Exportar relatório
        </button>
    </div>
</div>

@endsection

@section('content')

{{--  STATS ========= --}}

<div class="row">
    {{-- Revenue --}}
    <div class="col-lg-3 col-md-6 mb-4">

        <div class="dashboard-card stat-card">

            <div class="stat-header">

                <div>

                    <div class="stat-title">
                        Receita total
                    </div>

                    <div class="stat-value">
                        {{ number_format($stats['revenue'] ?? 0, 2, ',', '.') }}
                    </div>

                </div>

                <div class="stat-icon blue">
                    <i class="fas fa-wallet"></i>
                </div>

            </div>

            <div class="stat-footer trend-up">

                <i class="fas fa-arrow-up"></i>

                12,5% este mês

            </div>

        </div>

    </div>

    {{-- Users --}}
    <div class="col-lg-3 col-md-6 mb-4">

        <div class="dashboard-card stat-card">

            <div class="stat-header">

                <div>

                    <div class="stat-title">
                        Utilizadores
                    </div>

                    <div class="stat-value">
                        {{ number_format($stats['users'] ?? 0) }}
                    </div>

                </div>

                <div class="stat-icon green">
                    <i class="fas fa-users"></i>
                </div>

            </div>

            <div class="stat-footer trend-up">

                <i class="fas fa-arrow-up"></i>

                8,2% este mês

            </div>

        </div>

    </div>

    {{-- Transactions --}}
    <div class="col-lg-3 col-md-6 mb-4">

        <div class="dashboard-card stat-card">

            <div class="stat-header">

                <div>

                    <div class="stat-title">
                        Operações
                    </div>

                    <div class="stat-value">
                        {{ number_format($stats['transactions'] ?? 0) }}
                    </div>

                </div>

                <div class="stat-icon orange">
                    <i class="fas fa-exchange-alt"></i>
                </div>

            </div>

            <div class="stat-footer trend-up">

                <i class="fas fa-arrow-up"></i>

                5,7% este mês

            </div>

        </div>

    </div>

    {{-- Pending --}}
    <div class="col-lg-3 col-md-6 mb-4">

        <div class="dashboard-card stat-card">

            <div class="stat-header">

                <div>

                    <div class="stat-title">
                        Pendentes
                    </div>

                    <div class="stat-value">
                        {{ number_format($stats['pending'] ?? 0) }}
                    </div>

                </div>

                <div class="stat-icon red">
                    <i class="fas fa-clock"></i>
                </div>

            </div>

            <div class="stat-footer trend-down">

                <i class="fas fa-arrow-down"></i>

                3,4% este mês

            </div>

        </div>

    </div>
</div>

{{-- ========= CHARTS ========= --}}
<div class="row">
    {{-- Revenue chart --}}
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card chart-card">

            <div class="card-header-modern">

                <div>
                    <h3 class="card-title-modern">
                        Desempenho
                    </h3>

                    <div class="card-subtitle-modern">
                        Receita dos últimos 12 meses
                    </div>

                </div>

                <select class="form-control form-control-sm" style="width:120px;">

                    <option>12 meses</option>
                    <option>6 meses</option>
                    <option>30 dias</option>

                </select>

            </div>

            <div class="chart-container">

                <canvas id="revenueChart"></canvas>

            </div>

        </div>
    </div>

    {{-- Distribution --}}
    <div class="col-lg-4 mb-4">
        <div class="dashboard-card chart-card">
            <div class="card-header-modern">
                <div>
                    <h3 class="card-title-modern">
                        Distribuição
                    </h3>

                    <div class="card-subtitle-modern">
                        Atividade por categoria
                    </div>

                </div>

            </div>

            <div class="chart-container">

                <canvas id="distributionChart"></canvas>

            </div>

        </div>
    </div>
</div>


{{-- ========= BOTTOM ========= --}}

<div class="row">
    {{-- Recent operations --}}
    <div class="col-lg-8 mb-4">

        <div class="dashboard-card chart-card">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Operações recentes
                    </h3>

                    <div class="card-subtitle-modern">
                        Últimas atividades da organização
                    </div>

                </div>

                <a href="#" class="text-primary" style="font-size:12px;">

                    Ver todas

                </a>

            </div>

            <div class="table-responsive">

                <table class="modern-table">

                    <thead>

                        <tr>

                            <th>
                                Operação
                            </th>

                            <th>
                                Utilizador
                            </th>

                            <th>
                                Data
                            </th>

                            <th>
                                Estado
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($recentOperations ?? [] as $operation)

                        <tr>

                            <td>
                                {{ $operation->description }}
                            </td>

                            <td>
                                {{ $operation->user->name ?? '-' }}
                            </td>

                            <td>
                                {{ $operation->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td>

                                <span class="status status-success">
                                    Concluído
                                </span>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="4" class="text-center text-muted">

                                Nenhuma operação encontrada.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- Activity --}}
    <div class="col-lg-4 mb-4">

        <div class="dashboard-card chart-card">

            <div class="card-header-modern">

                <div>

                    <h3 class="card-title-modern">
                        Atividade recente
                    </h3>

                    <div class="card-subtitle-modern">
                        Últimas ações
                    </div>

                </div>

            </div>

            @forelse ($activities ?? [] as $activity)

            <div class="activity-item">

                <div class="activity-icon">

                    <i class="{{ $activity->icon ?? 'fas fa-info' }}"></i>

                </div>

                <div>

                    <div class="activity-title">

                        {{ $activity->description }}

                    </div>

                    <div class="activity-time">

                        {{ $activity->created_at->diffForHumans() }}

                    </div>

                </div>

            </div>

            @empty

            <div class="text-center text-muted py-4">

                Nenhuma atividade recente.

            </div>

            @endforelse

        </div>

    </div>
</div>

@endsection

@push('scripts')

<script>
    const revenueCanvas = document.getElementById('revenueChart');

    if (revenueCanvas) {
        new Chart(revenueCanvas, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Receita',
                    data: [12000, 14500, 13200, 17800, 19500, 22000, 21000, 24500, 26800, 29100, 31500, 34200],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, .08)',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointBackgroundColor: '#2563eb',
                    tension: .4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            color: '#94a3b8'
                            , font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8'
                            , font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }

    const distributionCanvas = document.getElementById('distributionChart');
    if (distributionCanvas) {
        new Chart(distributionCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Operações', 'Utilizadores', 'Relatórios', 'Outros'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#94a3b8'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
</script>

@endpush
