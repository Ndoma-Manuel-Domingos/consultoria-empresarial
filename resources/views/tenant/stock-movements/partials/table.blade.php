<table class="modern-table">

    <thead>

        <tr>

            <th>
                Data
            </th>

            <th>
                Produto
            </th>

            <th>
                Lote
            </th>

            <th>
                Tipo
            </th>

            <th class="text-right">
                Quantidade
            </th>

            <th class="text-right">
                Stock
            </th>

            <th class="text-right">
                Custo
            </th>

            <th>
                Referência
            </th>

            <th class="text-right">
                Ação
            </th>

        </tr>

    </thead>


    <tbody>

        @forelse($movements as $movement)

        <tr>

            {{-- DATA --}}

            <td>

                <div style="font-weight:600;color:#334155;">

                    {{ $movement->movement_date?->format('d/m/Y') }}

                </div>

                <div style="font-size:10px;color:#94a3b8;">

                    {{ $movement->movement_date?->format('H:i') }}

                </div>

            </td>


            {{-- PRODUTO --}}

            <td>

                <div class="d-flex align-items-center">

                    <div class="avatar avatar-sm mr-2">

                        {{ strtoupper(
                                substr($movement->product->name ?? 'P', 0, 1)
                            ) }}

                    </div>

                    <div>

                        <div style="
                                font-weight:600;
                                color:#334155;
                            ">

                            {{ $movement->product->name ?? '-' }}

                        </div>

                        @if($movement->product?->code)

                        <div style="
                                    font-size:10px;
                                    color:#94a3b8;
                                ">

                            {{ $movement->product->code }}

                        </div>

                        @endif

                    </div>

                </div>

            </td>


            {{-- LOTE --}}

            <td>

                @if($movement->productLot)

                <span class="status status-info">

                    {{ $movement->productLot->lot_number }}

                </span>

                @else

                <span style="
                            color:#94a3b8;
                            font-size:11px;
                        ">

                    Sem lote

                </span>

                @endif

            </td>


            {{-- TIPO --}}

            <td>

                @switch($movement->type)

                @case('entry')

                <span class="status status-success">

                    <i class="fas fa-arrow-down"></i>

                    Entrada

                </span>

                @break

                @case('exit')

                <span class="status status-danger">

                    <i class="fas fa-arrow-up"></i>

                    Saída

                </span>

                @break

                @case('adjustment')

                <span class="status status-warning">

                    <i class="fas fa-sliders-h"></i>

                    Ajuste

                </span>

                @break

                @case('transfer')

                <span class="status status-info">

                    <i class="fas fa-exchange-alt"></i>

                    Transferência

                </span>

                @break

                @default

                <span class="status status-neutral">

                    {{ $movement->type }}

                </span>

                @endswitch

            </td>


            {{-- QUANTIDADE --}}

            <td class="text-right">

                <strong style="color:#334155;">

                    {{ number_format(
                            $movement->quantity,
                            3,
                            ',',
                            '.'
                        ) }}

                </strong>

            </td>


            {{-- STOCK --}}

            <td class="text-right">

                <div style="
                        font-size:11px;
                        color:#94a3b8;
                    ">

                    {{ number_format(
                            $movement->stock_before,
                            3,
                            ',',
                            '.'
                        ) }}

                    →

                </div>

                <strong style="
                        color:#334155;
                        font-size:12px;
                    ">

                    {{ number_format(
                            $movement->stock_after,
                            3,
                            ',',
                            '.'
                        ) }}

                </strong>

            </td>


            {{-- CUSTO --}}

            <td class="text-right">

                <div style="
                        color:#475569;
                        font-size:11px;
                    ">

                    {{ number_format(
                            $movement->unit_cost,
                            2,
                            ',',
                            '.'
                        ) }}
                    Kz

                </div>

                <div style="
                        color:#94a3b8;
                        font-size:10px;
                    ">

                    Total:
                    {{ number_format(
                            $movement->total_cost,
                            2,
                            ',',
                            '.'
                        ) }}
                    Kz

                </div>

            </td>


            {{-- REFERÊNCIA --}}

            <td>

                @if($movement->reference)

                <span style="
                            color:#475569;
                            font-size:11px;
                        ">

                    {{ $movement->reference }}

                </span>

                @else

                <span style="
                            color:#cbd5e1;
                        ">
                    —
                </span>

                @endif

            </td>


            {{-- ACTION --}}

            <td class="text-right">

                <a href="{{ route(
                            'tenant.stock-movements.show',
                            $movement
                        ) }}" class="btn btn-outline-primary btn-sm" title="Ver movimento">

                    <i class="fas fa-eye"></i>

                </a>

            </td>

        </tr>

        @empty

        <tr>

            <td colspan="9" class="text-center py-5">

                <div style="
                        width:50px;
                        height:50px;
                        margin:0 auto 12px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        border-radius:12px;
                        background:#eff6ff;
                        color:#2563eb;
                    ">

                    <i class="fas fa-boxes"></i>

                </div>

                <div style="
                        color:#475569;
                        font-size:12px;
                        font-weight:600;
                    ">

                    Nenhum movimento encontrado.

                </div>

                <div style="
                        margin-top:4px;
                        color:#94a3b8;
                        font-size:10px;
                    ">

                    Registe uma entrada ou saída de stock
                    para começar o histórico.

                </div>

            </td>

        </tr>

        @endforelse

    </tbody>

</table>


@if($movements->hasPages())

<div class="d-flex justify-content-between align-items-center mt-4">

    <div style="
        color:#94a3b8;
        font-size:11px;
    ">

        A mostrar
        {{ $movements->firstItem() }}
        -
        {{ $movements->lastItem() }}
        de
        {{ $movements->total() }}

    </div>

    <div>

        {{ $movements->links() }}

    </div>

</div>

@endif
