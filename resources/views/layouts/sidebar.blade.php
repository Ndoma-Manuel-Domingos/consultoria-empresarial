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
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                {{-- ========== PRINCIPAL ====== --}}
                <li class="nav-header">
                    PRINCIPAL
                </li>
                {{-- Dashboard --}}
                <li class="nav-item">

                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-chart-pie"></i>

                        <p>
                            Dashboard
                        </p>

                    </a>

                </li>
                {{-- Utilizadores --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">

                        <i class="nav-icon fas fa-users"></i>

                        <p>
                            Utilizadores
                        </p>

                    </a>
                </li>

                {{-- Operações --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Operações
                        </p>
                    </a>
                </li>

                {{-- Relatórios --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Relatórios
                        </p>
                    </a>
                </li>

                {{-- ======= ORGANIZAÇÃO === --}}
                <li class="nav-header">
                    ORGANIZAÇÃO
                </li>
                
                {{-- Organização --}}
                <li class="nav-item">
                    <a href="{{ route('tenant.settings.edit') }}" class="nav-link {{ request()->routeIs('tenant.settings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Organização
                        </p>
                    </a>
                </li>

                {{-- Equipa --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>
                            Equipa
                        </p>
                    </a>
                </li>

                {{-- ====== GESTÃO DE ACESSOS ====== --}}

                @php
                $permissionsMenuActive = request()->routeIs('tenant.permissions.*') || request()->routeIs('tenant.roles.*');
                @endphp
                <li class="nav-item has-treeview {{ $permissionsMenuActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $permissionsMenuActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-lock"></i>
                        <p>
                            Gestão de acessos
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- Perfis de acesso --}}
                        <li class="nav-item">
                            <a href="{{ route('tenant.roles.index') }}" class="nav-link {{ request()->routeIs('tenant.roles.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Perfis de acesso
                                </p>
                            </a>
                        </li>

                        {{-- Permissões --}}
                        <li class="nav-item">
                            <a href="{{ route('tenant.permissions.index') }}" class="nav-link {{ request()->routeIs('tenant.permissions.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Permissões
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Configurações --}}
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