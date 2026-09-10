{{-- ============ NAVBAR ========== --}}
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

                <i class="fas fa-chevron-down" style="font-size:9px;color:#64748b;"></i>
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
