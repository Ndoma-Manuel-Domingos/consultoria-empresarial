<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', config('app.name', 'Sistema de Consultoria'))
    </title>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    {{-- AdminLTE --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- CSS principal --}}
    <link rel="stylesheet" href="{{ asset('css/progress.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')

</head>


<body class="hold-transition sidebar-mini layout-fixed">
    <div x-data="ajaxSystem()" x-init="init()" x-cloak>
        {{-- ======== AJAX PROGRESS BAR ======== --}}
        <div class="ajax-progress" x-show="progress.visible" x-transition.opacity>
            <div class="ajax-progress-bar" :style="'width:' + progress.value + '%'"></div>
        </div>

        {{--======== AJAX TOAST ======= --}}

        <div class="ajax-toast" x-show="toast.visible" x-transition :class="'ajax-toast-' + toast.type">
            <div class="ajax-toast-icon">

                <i class="fas" :class="{
                    'fa-check-circle': toast.type === 'success',
                    'fa-exclamation-circle': toast.type === 'error',
                    'fa-info-circle': toast.type === 'info',
                    'fa-spinner fa-spin': toast.type === 'loading'
                }"></i>

            </div>
            <div class="ajax-toast-content">
                <div class="ajax-toast-title" x-text="toast.title"></div>
                <div class="ajax-toast-message" x-text="toast.message"></div>
            </div>
            <button type="button" class="ajax-toast-close" @click="toast.visible = false">
                <i class="fas fa-times"></i>
            </button>
        </div>


        {{-- =========================================================
         DELETE / CONFIRM MODAL
    ========================================================== --}}

        <div class="ajax-modal-backdrop" x-show="confirm.visible" x-transition.opacity @keydown.escape.window="cancelConfirm()" @click.self="cancelConfirm()">

            <div class="ajax-modal" x-show="confirm.visible" x-transition:enter="ajax-modal-enter" x-transition:leave="ajax-modal-leave">

                <div class="ajax-modal-icon">

                    <i class="fas fa-trash-alt"></i>

                </div>


                <h3 class="ajax-modal-title" x-text="confirm.title"></h3>


                <p class="ajax-modal-message" x-text="confirm.message"></p>


                <div class="ajax-modal-actions">

                    <button type="button" class="btn btn-secondary" @click="cancelConfirm()" :disabled="confirm.loading">

                        <i class="fas fa-times mr-1"></i>

                        Cancelar

                    </button>


                    <button type="button" class="btn btn-danger" @click="confirmAction()" :disabled="confirm.loading">

                        <template x-if="!confirm.loading">

                            <span>

                                <i class="fas fa-trash mr-1"></i>

                                Eliminar

                            </span>

                        </template>


                        <template x-if="confirm.loading">

                            <span>

                                <i class="fas fa-spinner fa-spin mr-1"></i>

                                A eliminar...

                            </span>

                        </template>

                    </button>

                </div>

            </div>

        </div>



        {{-- =========================================================
         ADMINLTE WRAPPER
    ========================================================== --}}

        <div class="wrapper">


            {{-- =====================================================
             NAVBAR
        ====================================================== --}}

            <nav class="main-header navbar navbar-expand navbar-white navbar-light">

                {{-- Sidebar toggle --}}
                <ul class="navbar-nav">

                    <li class="nav-item">

                        <a class="nav-link" data-widget="pushmenu" href="#" role="button">

                            <i class="fas fa-bars"></i>

                        </a>

                    </li>

                </ul>


                {{-- Search --}}
                <form class="form-inline ml-3 navbar-search" method="GET" action="{{ url()->current() }}" data-ajax-form data-ajax-search>

                    <div class="input-group input-group-sm">

                        <input class="form-control form-control-navbar" type="search" name="search" placeholder="Pesquisar..." value="{{ request('search') }}">

                        <div class="input-group-append">

                            <button class="btn btn-navbar" type="submit">

                                <i class="fas fa-search"></i>

                            </button>

                        </div>

                    </div>

                </form>


                {{-- Right navbar --}}
                <ul class="navbar-nav ml-auto">


                    {{-- Tenant --}}
                    <li class="nav-item">

                        <div class="tenant-selector">

                            <div class="tenant-avatar">

                                {{ strtoupper(substr(auth()->user()->tenant->name ?? 'T', 0, 1)) }}

                            </div>

                            <div class="tenant-info">

                                <div class="tenant-name">

                                    {{ auth()->user()->tenant->name ?? 'Meu Tenant' }}

                                </div>

                                <div class="tenant-label">

                                    Organização atual

                                </div>

                            </div>

                            <i class="fas fa-chevron-down" style="font-size:9px;color:#64748b;">
                            </i>

                        </div>

                    </li>


                    {{-- Notifications --}}
                    <li class="nav-item dropdown">

                        <a class="nav-link" href="#" data-toggle="dropdown">

                            <i class="far fa-bell"></i>

                            <span class="badge badge-danger navbar-badge">
                                3
                            </span>

                        </a>

                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                            <span class="dropdown-item dropdown-header">
                                3 Notificações
                            </span>

                            <div class="dropdown-divider"></div>

                            <a href="#" class="dropdown-item">

                                <i class="fas fa-info-circle mr-2"></i>

                                Novas notificações

                            </a>

                        </div>

                    </li>


                    {{-- User --}}
                    <li class="nav-item dropdown">

                        <a class="nav-link" href="#" data-toggle="dropdown">

                            <div class="user-menu">

                                <div class="user-avatar">

                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}

                                </div>

                                <div class="d-none d-md-block">

                                    <div style="font-size:12px;font-weight:600;color:#334155;">

                                        {{ auth()->user()->name }}

                                    </div>

                                </div>

                                <i class="fas fa-chevron-down ml-1" style="font-size:9px;">
                                </i>

                            </div>

                        </a>


                        <div class="dropdown-menu dropdown-menu-right">

                            <a href="#" class="dropdown-item">

                                <i class="fas fa-user mr-2"></i>

                                Meu perfil

                            </a>


                            <div class="dropdown-divider"></div>


                            <form method="POST" action="{{ route('logout') }}" data-no-ajax>

                                @csrf

                                <button type="submit" class="dropdown-item">

                                    <i class="fas fa-sign-out-alt mr-2"></i>

                                    Terminar sessão

                                </button>

                            </form>

                        </div>

                    </li>

                </ul>

            </nav>



            {{-- =====================================================
             SIDEBAR
        ====================================================== --}}

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

                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">


                            {{-- PRINCIPAL --}}
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



                            {{-- ORGANIZAÇÃO --}}
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



                            {{-- =================================================
                             GESTÃO DE ACESSOS
                        ================================================== --}}

                            @php
                            $accessMenuActive =
                            request()->routeIs('roles.*') ||
                            request()->routeIs('permissions.*');
                            @endphp


                            <li class="nav-item has-treeview {{ $accessMenuActive ? 'menu-open' : '' }}">

                                <a href="#" class="nav-link {{ $accessMenuActive ? 'active' : '' }}">

                                    <i class="nav-icon fas fa-user-lock"></i>

                                    <p>

                                        Gestão de acessos

                                        <i class="right fas fa-angle-left"></i>

                                    </p>

                                </a>


                                <ul class="nav nav-treeview">


                                    {{-- Perfis --}}
                                    <li class="nav-item">

                                        <a href="{{ route('tenant.roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">

                                            <i class="far fa-circle nav-icon"></i>

                                            <p>
                                                Perfis de acesso
                                            </p>

                                        </a>

                                    </li>


                                    {{-- Permissões --}}
                                    <li class="nav-item">

                                        <a href="{{ route('tenant.permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">

                                            <i class="far fa-circle nav-icon"></i>

                                            <p>
                                                Permissões
                                            </p>

                                        </a>

                                    </li>

                                </ul>

                            </li>

                            {{-- CONFIGURAÇÕES --}}
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



            {{-- =====================================================
             CONTENT
        ====================================================== --}}

            <div class="content-wrapper">


                {{-- Page Header --}}
                <section class="content-header">

                    @yield('page_header')

                </section>


                {{-- Page Content --}}
                <section class="content">

                    @yield('content')

                </section>


            </div>



            {{-- =====================================================
             FOOTER
        ====================================================== --}}

            <footer class="main-footer">

                <div class="float-right d-none d-sm-block">

                    v1.0.0

                </div>

                <strong>

                    © {{ date('Y') }}
                    {{ config('app.name', 'Sistema de Consultoria') }}.

                </strong>

                Todos os direitos reservados.

            </footer>


        </div>


    </div>


    {{-- =============================================================
     JAVASCRIPT
============================================================= --}}

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>

    
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    {{-- Aplicação --}}
    <script src="{{ asset('js/app.js') }}"></script>


    @stack('scripts')

</body>

</html>
