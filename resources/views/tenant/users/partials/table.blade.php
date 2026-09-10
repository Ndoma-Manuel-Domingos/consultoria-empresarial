<?php
use Illuminate\Support\Facades\Auth;
?>
<div class="table-responsive">
    <table class="modern-table">
        <thead>
            <tr>
                <th>Utilizador</th>
                <th>Contacto</th>
                <th>Perfil</th>
                <th>Estado</th>
                <th>Registado em</th>
                <th style="width:110px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)

            @php
            $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(fn ($word) => strtoupper(substr($word, 0, 1)))->implode('');
            $role = $user->roles->first();
            @endphp
            <tr>
                {{-- === USER ====== --}}
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm mr-3">
                            {{ $initials ?: 'U' }}
                        </div>
                        <div>
                            <div style="color:#1e293b;font-size:12px;font-weight:600;">
                                {{ $user->name }}
                            </div>
                            <div style="margin-top:2px;color:#94a3b8;font-size:10px;">
                                ID #{{ $user->id }}
                            </div>
                        </div>
                    </div>
                </td>
                {{-- === CONTACT ======= --}}
                <td>
                    <div style="color:#475569;font-size:11px;">
                        <i class="fas fa-envelope mr-1" style="color:#94a3b8;"></i>
                        {{ $user->email }}
                    </div>
                    @if ($user->phone)
                    <div style="margin-top:4px;color:#94a3b8;font-size:10px;">
                        <i class="fas fa-phone mr-1"></i>
                        {{ $user->phone }}
                    </div>
                    @endif
                </td>

                {{-- ==== ROLE ========= --}}
                <td>
                    @if ($role)
                    <span class="status status-info">
                        <i class="fas fa-user-shield"></i>
                        {{ $role->name }}
                    </span>
                    @else
                    <span class="status status-neutral">
                        Sem perfil
                    </span>
                    @endif
                </td>

                {{-- ====== STATUS ======= --}}
                <td>
                    @if (($user->status ?? 'active') === 'active')
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

                {{-- ======= DATE  =========== --}}
                <td>
                    <div style="color:#64748b;font-size:11px;">
                        {{ $user->created_at?->format('d/m/Y') }}
                    </div>
                    <div style="margin-top:2px;color:#94a3b8;font-size:9px;">
                        {{ $user->created_at?->format('H:i') }}
                    </div>
                </td>

                {{-- === ACTIONS ========== --}}
                <td>
                    <div class="dropdown">
                        <button type="button" class="btn btn-secondary btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width:34px;padding:7px 9px;">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            {{-- VER --}}
                            <a href="{{ route('tenant.users.show', $user) }}" class="dropdown-item">
                                <i class="fas fa-eye mr-2" style="width:15px;"></i>
                                Ver detalhes
                            </a>
                            {{-- EDITAR --}}
                            <a href="{{ route('tenant.users.edit', $user) }}" class="dropdown-item">
                                <i class="fas fa-edit mr-2" style="width:15px;"></i>
                                Editar
                            </a>
                            <div class="dropdown-divider"></div>
                            {{-- ELIMINAR --}}
                            @if (Auth::user()->id !== $user->id)
                            <button type="button" class="dropdown-item text-danger delete-user-btn" data-url="{{ route('tenant.users.destroy', $user) }}" data-name="{{ $user->name }}">
                                <i class="fas fa-trash mr-2" style="width:15px;"></i>
                                Eliminar
                            </button>
                            @else
                            <button type="button" class="dropdown-item" disabled title="Não pode eliminar a sua própria conta">
                                <i class="fas fa-lock mr-2" style="width:15px;"></i>
                                A sua conta
                            </button>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding:60px 20px;">
                    <div style="width:60px;height:60px;margin:0 auto 15px;display:flex;align-items:center;justify-content:center;background:#eff6ff;color:#2563eb;border-radius:15px;font-size:22px;">
                        <i class="fas fa-users"></i>
                    </div>

                    <div style="color:#1e293b;font-size:13px;font-weight:700;">
                        Nenhum utilizador encontrado
                    </div>

                    <div style="max-width:350px;margin:5px auto 15px;color:#94a3b8;font-size:11px;">
                        Não existem utilizadores que correspondam
                        aos filtros selecionados.
                    </div>
                    <a href="{{ route('tenant.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i>
                        Adicionar utilizador
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- =========================================================
     PAGINATION
========================================================= --}}

@if ($users->hasPages())
<div class="d-flex justify-content-between align-items-center mt-4">
    <div style="color:#94a3b8;font-size:10px;">
        A mostrar
        <strong style="color:#64748b;">
            {{ $users->firstItem() ?? 0 }}
        </strong>
        até
        <strong style="color:#64748b;">
            {{ $users->lastItem() ?? 0 }}
        </strong>
        de
        <strong style="color:#64748b;">
            {{ $users->total() }}
        </strong>
    </div>
    <div>
        {{ $users->links() }}
    </div>
</div>
@endif
