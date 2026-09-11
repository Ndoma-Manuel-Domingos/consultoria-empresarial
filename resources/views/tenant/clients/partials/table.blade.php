<table class="modern-table">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Contacto</th>
            <th>Localização</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th class="text-right">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($clients as $client)
        <tr>
            {{-- CLIENT --}}
            <td>
                <div class="d-flex align-items-center">
                    @php
                        $name = $client->name ?? 'Cliente';
                        $words = preg_split('/\s+/', trim($name));
                        if (count($words) >= 2) {
                            $initials = mb_substr($words[0], 0, 1) . mb_substr($words[count($words) - 1], 0, 1);
                        } else {
                            $initials = mb_substr($name, 0, 2);
                        }
                        $initials = strtoupper($initials);
                    @endphp

                    <div class="avatar avatar-sm mr-3">
                        @if ($client->type === 'company')
                        <i class="fas fa-building"></i>
                        @else
                        {{ $initials }}
                        @endif
                    </div>
                    <div>
                        <div style="color:var(--dark);font-size:12px;font-weight:600;">
                            {{ $client->name }}
                        </div>
                        @if ($client->type === 'company' && $client->commercial_name)
                        <div style="margin-top:3px;color:var(--text-muted);font-size:10px;">
                            {{ $client->commercial_name }}
                        </div>
                        @elseif ($client->nif)
                        <div style="margin-top:3px;color:var(--text-muted);font-size:10px;">
                            NIF: {{ $client->nif }}
                        </div>
                        @endif
                    </div>
                </div>
            </td>
            {{-- CONTACT --}}
            <td>
                @if ($client->phone)
                <div style="font-size:11px;">
                    <i class="fas fa-phone mr-1" style="color:var(--text-light);font-size:10px;"></i>
                    {{ $client->phone }}
                </div>
                @endif
                @if ($client->email)
                <div class="mt-1" style="color:var(--text-muted);font-size:10px;">
                    <i class="fas fa-envelope mr-1" style="color:var(--text-light);font-size:10px;"></i>
                    {{ $client->email }}
                </div>
                @endif
                @if (!$client->phone && !$client->email)
                <span class="text-muted">
                    —
                </span>
                @endif
            </td>
            {{-- LOCATION --}}
            <td>
                @if ($client->city || $client->municipality)
                <div style="font-size:11px;">
                    <i class="fas fa-map-marker-alt mr-1" style="color:var(--text-light);font-size:10px;"></i>
                    {{ $client->city ?? $client->municipality }}
                </div>
                @if ($client->province)
                <div class="mt-1" style="color:var(--text-muted);font-size:10px;">
                    {{ $client->province }}
                </div>
                @endif
                @else
                <span class="text-muted">
                    —
                </span>
                @endif
            </td>
            {{-- TYPE --}}
            <td>
                @if ($client->type === 'company')
                <span class="status status-info">
                    <i class="fas fa-building"></i>
                    Empresa
                </span>
                @else
                <span class="status status-neutral">
                    <i class="fas fa-user"></i>
                    Pessoa singular
                </span>
                @endif
            </td>
            {{-- STATUS --}}
            <td>
                @if ($client->is_active)
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
            </td>
            {{-- ACTIONS --}}
            <td class="text-right">
                <div class="dropdown">
                    <button type="button" class="btn btn-secondary btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ações">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        {{-- SHOW --}}
                        <a href="{{ route('tenant.clients.show', $client) }}" class="dropdown-item">
                            <i class="fas fa-eye mr-2" style="color:var(--primary);"></i>
                            Ver cliente
                        </a>
                        {{-- EDIT --}}
                        <a href="{{ route('tenant.clients.edit', $client) }}" class="dropdown-item">
                            <i class="fas fa-edit mr-2" style="color:var(--warning);"></i>
                            Editar
                        </a>
                        <div class="modern-divider my-1"></div>
                        {{-- DELETE --}}
                        <button type="button" class="dropdown-item" data-delete-client="{{ $client->id }}" data-client-name="{{ $client->name }}">
                            <i class="fas fa-trash mr-2" style="color:var(--danger);"></i>
                            Eliminar
                        </button>
                    </div>
                </div>
            </td>
        </tr>
        @empty
        {{-- EMPTY STATE --}}
        <tr>
            <td colspan="6">
                <div class="text-center py-5">
                    <div class="stat-icon blue mx-auto mb-3" style="width:50px;height:50px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div style="color:var(--dark);font-size:13px;font-weight:600;">
                        Nenhum cliente encontrado
                    </div>
                    <div style="margin-top:4px;color:var(--text-muted);font-size:11px;">
                        @if (request()->hasAny(['search', 'type', 'status']))
                        Não encontramos clientes
                        com os filtros selecionados.
                        @else
                        Ainda não existem clientes
                        registados.
                        @endif
                    </div>
                    @if (request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('tenant.clients.index') }}" class="btn btn-secondary btn-sm mt-3">
                        <i class="fas fa-times"></i>
                        Limpar filtros
                    </a>
                    @else
                    <a href="{{ route('tenant.clients.create') }}" class="btn btn-primary btn-sm mt-3">
                        <i class="fas fa-user-plus"></i>
                        Novo cliente
                    </a>
                    @endif
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
