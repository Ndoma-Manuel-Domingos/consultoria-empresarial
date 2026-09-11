<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductLotController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');

        $lots = ProductLot::with('product')->where('tenant_id', $tenantId)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('lot_number', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($product) use ($search) {
                            $product->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->product_id, function ($query, $productId) {
                $query->where('product_id', $productId);
            })
            ->when($request->status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($request->status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $products = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.product-lots.index', compact(
            'lots',
            'products'
        ));
    }

    public function create()
    {
        $tenantId = session('tenant_id');

        $products = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.product-lots.form', compact(
            'products'
        ));
    }

    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'product_id' => [
                'required',
                Rule::exists('products', 'id')
                    ->where('tenant_id', $tenantId),
            ],

            'lot_number' => [
                'required',
                'string',
                'max:100',
            ],

            'manufactured_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:manufactured_at',
            ],

            'initial_quantity' => [
                'required',
                'numeric',
                'min:0',
            ],

            'cost_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $exists = ProductLot::where('tenant_id', $tenantId)
            ->where('product_id', $validated['product_id'])
            ->where('lot_number', $validated['lot_number'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'lot_number' => 'Este lote já existe para o produto selecionado.'
                ]);
        }

        $quantity = $validated['initial_quantity'];

        ProductLot::create([
            'tenant_id' => $tenantId,
            'product_id' => $validated['product_id'],
            'lot_number' => $validated['lot_number'],
            'manufactured_at' => $validated['manufactured_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'initial_quantity' => $quantity,
            'current_quantity' => $quantity,
            'reserved_quantity' => 0,
            'cost_price' => $validated['cost_price'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        return redirect()
            ->route('tenant.product-lots.index')
            ->with('success', 'Lote criado com sucesso.');
    }

    public function show(ProductLot $productLot)
    {
        $this->authorizeTenant($productLot);

        $productLot->load('product');

        return view(
            'tenant.product-lots.show',
            compact('productLot')
        );
    }

    public function edit(ProductLot $productLot)
    {
        $tenantId = session('tenant_id');

        $this->authorizeTenant($productLot);

        $products = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'tenant.product-lots.form',
            compact('productLot', 'products')
        );
    }

    public function update(Request $request, ProductLot $productLot)
    {
        $tenantId = session('tenant_id');

        $this->authorizeTenant($productLot);

        $validated = $request->validate([
            'product_id' => [
                'required',
                Rule::exists('products', 'id')
                    ->where('tenant_id', $tenantId),
            ],

            'lot_number' => [
                'required',
                'string',
                'max:100',
            ],

            'manufactured_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:manufactured_at',
            ],

            'current_quantity' => [
                'required',
                'numeric',
                'min:0',
            ],

            'cost_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $exists = ProductLot::where('tenant_id', $tenantId)
            ->where('product_id', $validated['product_id'])
            ->where('lot_number', $validated['lot_number'])
            ->where('id', '!=', $productLot->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'lot_number' => 'Este lote já existe para o produto selecionado.'
                ]);
        }

        $productLot->update([
            'product_id' => $validated['product_id'],
            'lot_number' => $validated['lot_number'],
            'manufactured_at' => $validated['manufactured_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'current_quantity' => $validated['current_quantity'],
            'cost_price' => $validated['cost_price'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'notes' => $validated['notes'] ?? null,
            'updated_by' => Auth::user()->id,
        ]);

        return redirect()
            ->route('tenant.product-lots.show', $productLot)
            ->with('success', 'Lote atualizado com sucesso.');
    }

    public function destroy(ProductLot $productLot)
    {
        $this->authorizeTenant($productLot);

        if ($productLot->current_quantity > 0) {
            return back()->withErrors([
                'delete' => 'Não é possível eliminar um lote que possui stock.'
            ]);
        }

        $productLot->delete();

        return redirect()
            ->route('tenant.product-lots.index')
            ->with('success', 'Lote eliminado com sucesso.');
    }

    private function authorizeTenant(ProductLot $productLot): void
    {
        $tenantId = session('tenant_id');

        abort_unless($productLot->tenant_id === $tenantId, 403);
    }
}
