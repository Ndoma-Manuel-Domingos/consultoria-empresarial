<table class="modern-table">
    <thead>
        <tr>
            <th style="width:45px;">#</th>
            <th>Código</th>
            <th>Designação</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>IVA</th>
            <th>Estoque</th>
            <th>Estado</th>
            <th class="text-right">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
        <tr>
            {{-- ID --}}
            <td>
                <span style="color:#94a3b8;font-size:11px;">
                    #{{ $product->id }}
                </span>
            </td>
            {{-- CÓDIGO --}}
            <td>
                <span style="color:#475569;font-family:monospace;font-size:11px;">
                    {{ $product->code }}
                </span>
                @if($product->barcode)
                <div style="margin-top:2px;color:#94a3b8;font-size:9px;">
                    {{ $product->barcode }}
                </div>
                @endif
            </td>
            {{-- PRODUTO --}}
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm mr-2" style="
                            background:
                            {{ $product->type === 'service' ? 'linear-gradient(135deg,#7c3aed,#a855f7)' : 'linear-gradient(135deg,#2563eb,#0ea5e9)' }};
                         ">
                        <i class="fas {{ $product->type === 'service' ? 'fa-concierge-bell' : 'fa-box' }}"></i>
                    </div>

                    <div>

                        <div style="
                            color:#1e293b;
                            font-size:12px;
                            font-weight:700;
                        ">

                            {{ $product->name }}

                        </div>

                        @if($product->brand)

                        <div style="
                            margin-top:2px;
                            color:#94a3b8;
                            font-size:10px;
                        ">

                            {{ $product->brand }}

                        </div>

                        @endif

                    </div>

                </div>
            </td>

            {{-- CATEGORIA --}}
            <td>
                @if($product->category)
                <span class="status status-neutral">
                    {{ $product->category }}
                </span>
                @else
                <span style="color:#cbd5e1;font-size:11px;">
                    Sem categoria
                </span>
                @endif
            </td>
            {{-- PREÇO --}}
            <td>
                <div style="color:#1e293b;font-size:12px;font-weight:700;">
                    {{ number_format($product->sale_price, 2, ',', '.') }}
                    <span style="color:#94a3b8;font-size:9px;">
                        Kz
                    </span>
                </div>
                <div style="margin-top:2px;color:#94a3b8;font-size:9px;">
                    {{ $product->unit }}
                </div>
            </td>
            {{-- IVA --}}

            <td>

                @if($product->tax_type === 'standard')

                <span class="status status-info">

                    {{ number_format($product->tax_rate, 2, ',', '.') }}%

                </span>

                @elseif($product->tax_type === 'exempt')

                <span class="status status-success">

                    Isento

                </span>

                @else

                <span class="status status-neutral">

                    0%

                </span>

                @endif

            </td>


            {{-- ESTOQUE --}}

            <td>

                @if($product->type === 'service')

                <span class="status status-neutral">

                    Serviço

                </span>

                @elseif($product->manage_stock)

                <span class="status status-info">

                    <i class="fas fa-boxes mr-1"></i>

                    Controlado

                </span>

                @else

                <span class="status status-neutral">

                    Não controlado

                </span>

                @endif

            </td>


            {{-- ESTADO --}}

            <td>

                @if($product->is_active)

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


            {{-- AÇÕES --}}

            <td class="text-right">

                <div class="dropdown">

                    <button type="button" class="btn btn-secondary btn-sm" data-toggle="dropdown" aria-expanded="false">

                        <i class="fas fa-ellipsis-h"></i>

                    </button>

                    <div class="dropdown-menu dropdown-menu-right">
                        {{-- VER --}}
                        <a href="{{ route('tenant.products.show', $product) }}" class="dropdown-item">
                            <i class="fas fa-eye mr-2"></i>
                            Ver detalhes
                        </a>
                        {{-- EDITAR --}}
                        <a href="{{ route('tenant.products.edit', $product) }}" class="dropdown-item">
                            <i class="fas fa-edit mr-2"></i>
                            Editar
                        </a>
                        <div class="dropdown-divider"></div>
                        {{-- DESATIVAR --}}
                        @if($product->is_active)
                        <button type="button" class="dropdown-item text-danger" data-delete-product="{{ route('tenant.products.destroy', $product) }}" data-product-name="{{ $product->name }}">
                            <i class="fas fa-ban mr-2"></i>
                            Desativar
                        </button>
                        @endif
                        @if(!$product->is_active)
                        <button type="button" class="dropdown-item text-success" data-delete-product="{{ route('tenant.products.destroy', $product) }}" data-product-name="{{ $product->name }}">
                            <i class="fas fa-ban mr-2"></i>
                            Ativar
                        </button>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9">
                <div class="text-center py-5">
                    <div style="width:48px;height:48px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;border-radius:12px;background:#eff6ff;color:#2563eb;font-size:18px;">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div style="color:#475569;font-size:12px;font-weight:700;">
                        Nenhum produto encontrado
                    </div>
                    <div style="margin-top:4px;color:#94a3b8;font-size:10px;">
                        Tente alterar os filtros ou cadastre um novo produto.
                    </div>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>


{{-- PAGINAÇÃO --}}

@if($products->hasPages())

<div class="d-flex align-items-center justify-content-between mt-3">

    <div style="
        color:#94a3b8;
        font-size:10px;
    ">

        A mostrar

        <strong>
            {{ $products->firstItem() }}
        </strong>

        -

        <strong>
            {{ $products->lastItem() }}
        </strong>

        de

        <strong>
            {{ $products->total() }}
        </strong>

    </div>


    <div>

        {{ $products->onEachSide(1)->links() }}

    </div>

</div>

@endif
