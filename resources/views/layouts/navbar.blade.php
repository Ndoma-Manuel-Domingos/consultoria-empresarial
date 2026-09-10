
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
