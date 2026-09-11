<table class="modern-table">

    <thead>
        <tr>

            <th>Lote</th>

            <th>Produto</th>

            <th>Validade</th>

            <th>Stock</th>

            <th>Custo</th>

            <th>Estado</th>

            <th class="text-right">
                Ações
            </th>

        </tr>
    </thead>

    <tbody>

        @forelse($lots as $lot)

            <tr>

                {{-- LOTE --}}
                <td>

                    <div class="d-flex align-items-center">

                        <div class="stat-icon blue mr-2">
                            <i class="fas fa-box"></i>
                        </div>

                        <div>

                            <div style="font-weight:700;color:#1e293b;">
                                {{ $lot->lot_number }}
                            </div>

                            <div style="font-size:10px;color:#94a3b8;">
                                #{{ $lot->id }}
                            </div>

                        </div>

                    </div>

                </td>

                {{-- PRODUTO --}}
                <td>

                    <div style="font-weight:600;color:#334155;">
                        {{ $lot->product->name }}
                    </div>

                    <div style="font-size:10px;color:#94a3b8;">
                        {{ $lot->product->code }}
                    </div>

                </td>

                {{-- VALIDADE --}}
                <td>

                    @if($lot->expires_at)

                        @if($lot->is_expired)

                            <span class="status status-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                Expirado
                            </span>

                            <div style="font-size:10px;color:#94a3b8;margin-top:3px;">
                                {{ $lot->expires_at->format('d/m/Y') }}
                            </div>

                        @elseif($lot->days_to_expire <= 30)

                            <span class="status status-warning">
                                <i class="fas fa-clock"></i>
                                {{ $lot->days_to_expire }} dias
                            </span>

                            <div style="font-size:10px;color:#94a3b8;margin-top:3px;">
                                {{ $lot->expires_at->format('d/m/Y') }}
                            </div>

                        @else

                            <span class="status status-success">
                                {{ $lot->expires_at->format('d/m/Y') }}
                            </span>

                        @endif

                    @else

                        <span class="status status-neutral">
                            Sem validade
                        </span>

                    @endif

                </td>

                {{-- STOCK --}}
                <td>

                    <div style="font-weight:700;color:#1e293b;">
                        {{ number_format($lot->current_quantity, 3, ',', '.') }}
                    </div>

                    <div style="font-size:10px;color:#94a3b8;">
                        Disponível:
                        {{ number_format($lot->available_quantity, 3, ',', '.') }}
                    </div>

                </td>

                {{-- CUSTO --}}
                <td>

                    @if($lot->cost_price !== null)

                        {{ number_format($lot->cost_price, 2, ',', '.') }}
                        Kz

                    @else

                        <span style="color:#94a3b8;">
                            —
                        </span>

                    @endif

                </td>

                {{-- ESTADO --}}
                <td>

                    @if($lot->is_active)

                        <span class="status status-success">
                            Ativo
                        </span>

                    @else

                        <span class="status status-neutral">
                            Inativo
                        </span>

                    @endif

                </td>

                {{-- AÇÕES --}}
                <td class="text-right">

                    <div class="dropdown">

                        <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            data-toggle="dropdown"
                        >
                            <i class="fas fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right">

                            <a
                                href="{{ route('tenant.product-lots.show', $lot) }}"
                                class="dropdown-item"
                            >
                                <i class="fas fa-eye mr-2"></i>
                                Ver lote
                            </a>

                            <a
                                href="{{ route('tenant.product-lots.edit', $lot) }}"
                                class="dropdown-item"
                            >
                                <i class="fas fa-edit mr-2"></i>
                                Editar
                            </a>

                            @if($lot->current_quantity <= 0)

                                <div class="dropdown-divider"></div>

                                <form
                                    method="POST"
                                    action="{{ route('tenant.product-lots.destroy', $lot) }}"
                                    onsubmit="return confirm('Tem certeza que deseja eliminar este lote?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="dropdown-item text-danger"
                                    >
                                        <i class="fas fa-trash mr-2"></i>
                                        Eliminar
                                    </button>

                                </form>

                            @endif

                        </div>

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="7">

                    <div class="text-center py-5">

                        <div
                            style="
                                width:50px;
                                height:50px;
                                margin:0 auto 12px;
                                border-radius:12px;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                background:#eff6ff;
                                color:#2563eb;
                                font-size:18px;
                            "
                        >
                            <i class="fas fa-boxes"></i>
                        </div>

                        <div style="
                            color:#475569;
                            font-size:13px;
                            font-weight:600;
                        ">
                            Nenhum lote encontrado
                        </div>

                        <div style="
                            margin-top:4px;
                            color:#94a3b8;
                            font-size:10px;
                        ">
                            Ainda não existem lotes registados.
                        </div>

                    </div>

                </td>

            </tr>

        @endforelse

    </tbody>

</table>

@if($lots->hasPages())

<div class="d-flex justify-content-between align-items-center mt-3">

    <div style="font-size:11px;color:#94a3b8;">
        Mostrando {{ $lots->firstItem() }}–{{ $lots->lastItem() }}
        de {{ $lots->total() }}
    </div>

    {{ $lots->links() }}

</div>

@endif
