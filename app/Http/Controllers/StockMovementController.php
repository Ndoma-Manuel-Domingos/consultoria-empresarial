<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductLot;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockMovementController extends Controller
{
    /**
     * Lista os movimentos.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');

        $query = StockMovement::query()
            ->with([
                'product',
                'productLot',
                'creator',
            ])
            ->where('tenant_id', $tenantId);

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")

                    ->orWhereHas('product', function ($product) use ($search) {
                        $product
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->orWhere('barcode', 'like', "%{$search}%");
                    })

                    ->orWhereHas('productLot', function ($lot) use ($search) {
                        $lot->where('lot_number', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        /*
        |--------------------------------------------------------------------------
        | TYPE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'movement_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'movement_date',
                '<=',
                $request->date_to
            );
        }

        $movements = $query
            ->latest('movement_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */

        $products = Product::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code',
            ]);

        return view(
            'tenant.stock-movements.index',
            compact(
                'movements',
                'products'
            )
        );
    }

    /**
     * Formulário.
     */
    public function create()
    {
        $tenantId = session('tenant_id');

        $products = Product::query()
            ->with('lots')
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $lots = ProductLot::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('lot_number')
            ->get();

        return view(
            'tenant.stock-movements.form',
            compact('products', 'lots')
        );
    }

    /**
     * Guardar movimento.
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'product_id' => ['required', Rule::exists('products', 'id')->where('tenant_id', $tenantId),],
            'product_lot_id' => ['nullable', Rule::exists('product_lots', 'id')->where('tenant_id', $tenantId),],
            'type' => [
                'required',
                Rule::in([
                    'in','purchase','adjustment_in','return_in','transfer_in','out','sale','adjustment_out','return_out','transfer_out'
                ]),
            ],
            'quantity' => ['required','numeric','gt:0',],
            'unit_cost' => ['nullable','numeric','min:0',],
            'reference' => ['nullable','string','max:100',],
            'document_type' => ['nullable','string','max:50',],
            'document_number' => ['nullable','string','max:100',],
            'reason' => ['nullable','string','max:255',],
            'notes' => ['nullable','string',],
            'movement_date' => ['required','date',],
        ]);

        DB::transaction(function () use ( $validated, $tenantId) {
            /*
            |--------------------------------------------------------------------------
            | LOCK PRODUCT
            |--------------------------------------------------------------------------
            */

            $product = Product::query()->where('tenant_id', $tenantId)->where('id', $validated['product_id'])->lockForUpdate()->firstOrFail();

            if (!$product->manage_stock) {
                throw ValidationException::withMessages([
                    'product_id' => 'Este produto não possui gestão de estoque.'
                ]);
            }

            $lot = null;

            /*
            |--------------------------------------------------------------------------
            | CURRENT STOCK
            |--------------------------------------------------------------------------
            */

            $stockBefore = (float) ($product->stock_quantity ?? 0);

            $quantity = (float) $validated['quantity'];

            if ($product->manage_lots) {
                if (!$validated['product_lot_id']) {
                    throw ValidationException::withMessages([
                        'product_lot_id' => 'Selecione o lote.'
                    ]);
                }

                $lot = ProductLot::lockForUpdate()
                ->where('id', $validated['product_lot_id'])
                ->where('product_id', $product->id)
                ->firstOrFail();

                $stockBefore = (float) $lot->current_quantity;

                $stockAfter = $stockBefore + $quantity;

                $lot->update([
                    'current_quantity' => $stockAfter,
                ]);

            } else {
                $balance = StockBalance::lockForUpdate()->firstOrCreate([
                    'tenant_id' => $product->tenant_id,
                    'product_id' => $product->id,
                ], [
                    'quantity' => 0,
                ]); 
                
                $stockBefore = $balance->quantity;
                $stockAfter = $stockBefore + $quantity;

                $balance->update([
                    'quantity' => $stockAfter,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CALCULATE STOCK
            |--------------------------------------------------------------------------
            */

            switch ($validated['type']) {
                case 'in':
                    $stockAfter = $stockBefore + $quantity;
                    break;
                case 'out':
                    $stockAfter = $stockBefore - $quantity;
                    if ( !$product->allow_negative_stock && $stockAfter < 0) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'quantity' => 'Stock insuficiente para realizar esta saída.',
                        ]);
                    }
                    break;
                case 'adjustment':
                    /*
                    | Para ajuste, quantity representa a quantidade
                    | que será adicionada/removida.
                    |
                    | O formulário pode futuramente ter um campo
                    | adjustment_type.
                    */
                    $adjustmentType = request('adjustment_type', 'increase');
                    if ($adjustmentType === 'decrease') {
                        $stockAfter = $stockBefore - $quantity;
                        if (!$product->allow_negative_stock&& $stockAfter < 0) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'quantity' => 'O ajuste resultaria em stock negativo.',
                            ]);
                        }
                    } else {
                        $stockAfter = $stockBefore + $quantity;
                    }
                    break;
                case 'transfer_in':
                    /*
                    | Transferência será expandida quando
                    | implementarmos armazéns.
                    |
                    | Por enquanto não altera o stock.
                    */
                    $stockAfter = $stockBefore;
                    break;
                default:
                    $stockAfter = $stockBefore;
                    break;
            }

            /*
            |--------------------------------------------------------------------------
            | COST
            |--------------------------------------------------------------------------
            */

            $unitCost = (float) ($validated['unit_cost']?? $product->cost_price?? 0);

            $totalCost = $quantity * $unitCost;

            /*
            |--------------------------------------------------------------------------
            | CREATE MOVEMENT
            |--------------------------------------------------------------------------
            */

            StockMovement::create([
                'tenant_id' => $tenantId,
                'product_id' => $product->id,
                'product_lot_id' => $validated['product_lot_id'] ?? null,
                'type' => $validated['type'],
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'reference' => $validated['reference'] ?? null,
                'document_type' => $validated['document_type'] ?? null,
                'document_number' => $validated['document_number'] ?? null,
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'movement_date' => $validated['movement_date'],

                'created_by' => Auth::user()->id,
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE PRODUCT STOCK
            |--------------------------------------------------------------------------
            */

            $product->update([
                'stock_quantity' => $stockAfter,
                'updated_by' => Auth::user()->id,
            ]);
        });

        return redirect()
            ->route('tenant.stock-movements.index')
            ->with(
                'success',
                'Movimento de stock registado com sucesso.'
            );
    }

    /**
     * Mostrar movimento.
     */
    public function show(StockMovement $stockMovement)
    {
        $this->authorizeMovement($stockMovement);

        $stockMovement->load([
            'product',
            'productLot',
            'creator',
        ]);

        return view(
            'tenant.stock-movements.show',
            compact('stockMovement')
        );
    }

    /**
     * Não recomendo editar movimentos.
     *
     * Em contabilidade/stock é melhor criar um novo movimento
     * de correção em vez de alterar o histórico.
     */

    public function destroy(StockMovement $stockMovement)
    {
        $this->authorizeMovement($stockMovement);

        abort(
            403,
            'Movimentos de stock não podem ser eliminados.'
        );
    }

    public function productLots(Product $product)
    {
        $tenantId = session('tenant_id');

        abort_unless($product->tenant_id === $tenantId, 403);

        $lots = $product->lots()
            ->where('is_active', true)
            ->orderByRaw('
                CASE
                    WHEN expires_at IS NULL THEN 1
                    ELSE 0
                END
            ')
            ->orderBy('expires_at')
            ->get([
                'id',
                'lot_number',
                'expires_at',
                'current_quantity',
                'cost_price',
                'is_active',
            ]);

        $lots->transform(function ($lot) {
            $isExpired = false;
            $isExpiringSoon = false;
            if ($lot->expires_at) {
                $isExpired = $lot->expires_at->isPast();
                $alertDays = $lot->product->expiration_alert_days ?? 30;
                $isExpiringSoon = !$isExpired && $lot->expires_at->lte(now()->addDays($alertDays));
            }
            return [
                'id' => $lot->id,
                'lot_number' => $lot->lot_number,
                'expires_at' => $lot->expires_at?->format('Y-m-d'),
                'current_quantity' => $lot->current_quantity,
                'cost_price' => $lot->cost_price,
                'is_active' => $lot->is_active,
                'is_expired' => $isExpired,
                'is_expiring_soon' => $isExpiringSoon,
            ];
        });

        return response()->json([
            'lots' => $lots,
        ]);
    }

    /**
     * Segurança multi-tenant.
     */
    private function authorizeMovement(StockMovement $stockMovement): void 
    {

        $tenantId = session('tenant_id');

        abort_unless(
            $stockMovement->tenant_id === $tenantId,
            403
        );
    }
}
