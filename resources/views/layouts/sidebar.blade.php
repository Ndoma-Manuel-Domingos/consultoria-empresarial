{{-- ======== SIDEBAR ======== --}}
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

                {{-- ================= OPERACOES ================= --}}
                <li class="nav-item {{ request()->routeIs('tenant.products.*') 
                        || request()->routeIs('tenant.product-lots.*') 
                        || request()->routeIs('tenant.stock-movements.*')
                        ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('tenant.products.*') 
                    || request()->routeIs('tenant.product-lots.*') 
                    || request()->routeIs('tenant.stock-movements.*') 
                    ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>
                            Operações
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        {{-- PRODUTOS --}}
                        <li class="nav-item">
                            <a href="{{ route('tenant.products.index') }}" class="nav-link {{ request()->routeIs('tenant.products.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-box"></i>
                                <p>
                                    Produtos
                                </p>
                            </a>
                        </li>
                        {{-- LOTES --}}
                        <li class="nav-item">
                            <a href="{{ route('tenant.product-lots.index') }}" class="nav-link {{ request()->routeIs('tenant.product-lots.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>
                                    Lotes
                                </p>
                            </a>
                        </li>
                        {{-- MOVIMENTOS DE STOCK --}}
                        <li class="nav-item">
                            <a href="{{ route('tenant.stock-movements.index') }}" class="nav-link {{ request()->routeIs('tenant.stock-movements.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-arrows-alt-v"></i>
                                <p>
                                    Movimentos de stock
                                </p>
                            </a>
                        </li>
                    </ul>
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

                <li class="nav-header">
                    GESTÃO DE CLIENTES
                </li>

                <li class="nav-item has-treeview {{ request()->routeIs('tenant.clients.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('tenant.clients.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Clientes
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('tenant.clients.index') }}" class="nav-link {{ request()->routeIs('tenant.clients.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Listar clientes
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('tenant.clients.create') }}" class="nav-link {{ request()->routeIs('tenant.clients.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Criar cliente
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- =============  GESTÃO DE ACESSOS =============== --}}
                @php
                $accessMenuActive = request()->routeIs('tenant.roles.*') || request()->routeIs('tenant.permissions.*');
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

                <li class="nav-item">
                    <a href="{{ route('tenant.users.index') }}" class="nav-link {{ request()->routeIs('tenant.users.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Utilizadores
                        </p>
                    </a>
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


                {{-- ========================= --}}
                {{-- POS --}}
                {{-- ========================= --}}
                <li class="nav-item">
                    <a href="{{ route('tenant.pos.index') }}" class="nav-link {{ request()->routeIs('tenant.pos.*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i>
                        <span>
                            POS
                        </span>
                        <span class="ml-auto"
                            style="
                                background:#2563eb;
                                color:#fff;
                                font-size:9px;
                                font-weight:700;
                                padding:3px 7px;
                                border-radius:20px;
                            ">
                            F2
                        </span>
                    </a>
                </li>

                {{-- ========================= --}}
                {{-- VENDAS --}}
                {{-- ========================= --}}
                <li class="nav-item">
                    <a href="{{ route('tenant.sales.index') }}" class="nav-link {{ request()->routeIs('tenant.sales.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i>
                        <span>
                            Vendas
                        </span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
