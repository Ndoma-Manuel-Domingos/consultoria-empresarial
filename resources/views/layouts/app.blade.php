<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @yield('title', config('app.name', 'Meu Sistema'))
    </title>

    {{-- AdminLTE / FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">
        {{-- ======= NAVBAR ======== --}}
        <nav class="main-header navbar navbar-expand">

            {{-- Sidebar toggle --}}
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            {{-- Search --}}
            <form class="navbar-search ml-3 d-none d-md-block">
                <div class="input-group">
                    <input type="search" class="form-control" placeholder="Pesquisar...">
                    <div class="input-group-append">
                        <button class="btn">
                            <i class="fas fa-search text-muted"></i>
                        </button>
                    </div>
                </div>
            </form>
            <ul class="navbar-nav ml-auto">
                {{-- Tenant --}}
                <li class="nav-item">
                    <div class="tenant-selector dropdown-toggle" data-toggle="dropdown">
                        <div class="tenant-avatar">
                            {{ strtoupper(substr($currentTenant->name ?? 'T', 0, 1)) }}
                        </div>
                        <div class="tenant-info">
                            <div class="tenant-name">
                                {{ $currentTenant->name ?? 'Meu Tenant' }}
                            </div>
                            <div class="tenant-label">
                                Organização atual
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-menu dropdown-menu-right">
                        <h6 class="dropdown-header">
                            Mudar organização
                        </h6>
                        {{-- Aqui podes listar os tenants aos quais o utilizador pertence. --}}

                        @isset($tenants)

                        @foreach ($tenants as $tenant)
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-building mr-2 text-muted"></i>
                            {{ $tenant->name }}
                        </a>

                        @endforeach
                        @endisset

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cog mr-2 text-muted"></i>
                            Configurações da organização
                        </a>
                    </div>
                </li>

                {{-- Notifications --}}

                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-danger navbar-badge">
                            3
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">
                            3 notificações
                        </span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user-plus mr-2"></i>
                            Novo utilizador
                            <span class="float-right text-muted text-sm">
                                5 min
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file-invoice mr-2"></i>
                            Nova operação
                            <span class="float-right text-muted text-sm">
                                1h
                            </span>
                        </a>
                    </div>
                </li>
                {{-- User --}}
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <div class="user-menu">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline">
                                {{ auth()->user()->name ?? 'Utilizador' }}
                            </span>
                            <i class="fas fa-chevron-down fa-xs"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i>
                            Meu perfil
                        </a>
                        <a href="{{ route('tenant.settings.edit') }}" class="dropdown-item">
                            <i class="fas fa-cog mr-2"></i>
                            Configurações
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Sair
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        {{-- ======= SIDEBAR ======== --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-0">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <div class="brand-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <span class="brand-text">
                    {{ config('app.name', 'Meu Sistema') }}
                </span>
            </a>
            <div class="sidebar">

                <nav>

                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                        <li class="nav-header">
                            PRINCIPAL
                        </li>

                        <li class="nav-item">

                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                                <i class="nav-icon fas fa-chart-pie"></i>

                                <p>
                                    Dashboard
                                </p>

                            </a>

                        </li>

                        <li class="nav-item">

                            <a href="#" class="nav-link">

                                <i class="nav-icon fas fa-users"></i>

                                <p>
                                    Utilizadores
                                </p>

                            </a>

                        </li>

                        <li class="nav-item">

                            <a href="#" class="nav-link">

                                <i class="nav-icon fas fa-file-alt"></i>

                                <p>
                                    Operações
                                </p>

                            </a>

                        </li>

                        <li class="nav-item">

                            <a href="#" class="nav-link">

                                <i class="nav-icon fas fa-chart-line"></i>

                                <p>
                                    Relatórios
                                </p>

                            </a>

                        </li>

                        <li class="nav-header">
                            ORGANIZAÇÃO
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('tenant.settings.edit') }}" class="nav-link {{ request()->routeIs('tenant.settings.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>
                                    Organização
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">

                            <a href="#" class="nav-link">

                                <i class="nav-icon fas fa-user-shield"></i>

                                <p>
                                    Equipa
                                </p>

                            </a>

                        </li>

                        <li class="nav-item">
                            <a href="{{ route('tenant.settings.edit') }}" class="nav-link {{ request()->routeIs('tenant.settings.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>
                                    Configurações
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- ======= CONTENT ======== --}}
        <div class="content-wrapper">
            @hasSection('page_header')
            <div class="content-header">
                @yield('page_header')
            </div>
            @endif
            <section class="content">
                @yield('content')
            </section>
        </div>

        {{-- == FOOTER ======== --}}
        <footer class="main-footer border-0 bg-transparent text-muted">
            <div class="float-right d-none d-sm-inline">
                v1.0.0
            </div>
            <strong>
                &copy; {{ date('Y') }}
                {{ config('app.name', 'Meu Sistema') }}.
            </strong>
            Todos os direitos reservados.
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('scripts')

</body>
</html>
