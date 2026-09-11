<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Lista de produtos.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');

        $products = Product::query()
            ->where('tenant_id', $tenantId)
            ->with('supplier')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active',$request->status === 'active');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Product::query()
            ->where('tenant_id', $tenantId)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        if ($request->ajax()) {
            return response()->json([
                'html' => view('tenant.products.partials.table',compact('products'))->render(),
            ]);
        }
        return view('tenant.products.index', compact('products', 'categories'));
    }

    /**
     * Formulário de criação.
     */
    public function create()
    {
        $tenantId = session('tenant_id');

        $suppliers = Client::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('type', 'company')
            ->orderBy('name')
            ->get();

        return view('tenant.products.form', compact('suppliers'));
    }

    /**
     * Guardar produto.
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        $validated = $this->validateProduct($request);

        $validated['tenant_id'] = $tenantId;
        $validated['created_by'] = Auth::user()->id;
        $validated['updated_by'] = Auth::user()->id;

        $validated = $this->normalizeProductData($validated);

        Product::create($validated);

        return redirect()->route('tenant.products.index')->with('success', 'Produto criado com sucesso.');
    }

    /**
     * Visualizar produto.
     */
    public function show(Product $product)
    {
        $this->authorizeTenant($product);
        
        $product->load(['supplier','creator','updater']);

        return view('tenant.products.show', compact('product'));
    }

    /**
     * Formulário de edição.
     */
    public function edit(Product $product)
    {
        $tenantId = session('tenant_id');
        
        $this->authorizeTenant($product);
        
        $suppliers = Client::query()
        ->where('tenant_id', $tenantId)
        ->where('is_active', true)
        ->where('type', 'company')
        ->orderBy('name')
        ->get();
        
        return view('tenant.products.form', compact(
            'product',
            'suppliers'
        ));
    }

    /**
     * Atualizar produto.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorizeTenant($product);

        $validated = $this->validateProduct($request,$product);

        $validated['updated_by'] = Auth::user()->id;

        $validated = $this->normalizeProductData($validated);

        $product->update($validated);

        return redirect()->route('tenant.products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    /**
     * Eliminar produto.
     */
    public function destroy(Product $product)
    {
        $this->authorizeTenant($product);

        /*
         * Futuramente, quando existirem vendas, compras,
         * lotes ou movimentos de estoque, não devemos
         * eliminar fisicamente o produto.
         *
         * Nesse momento podemos apenas desativá-lo.
         */

        $product->update([
            'is_active' => !$product->is_active,
            'updated_by' => Auth::user()->id,
        ]);

        return redirect()->route('tenant.products.index')->with('success', 'Produto desativado com sucesso.');
    }

    /**
     * Validação.
     */
    private function validateProduct(Request $request,?Product $product = null): array 
    {
        $tenantId = session('tenant_id');

        return $request->validate([
            'code' => ['required','string','max:50',Rule::unique('products', 'code')->where(fn ($query) => $query->where('tenant_id', $tenantId))->ignore($product?->id),],
            'barcode' => ['nullable','string','max:50',Rule::unique('products', 'barcode')->where(fn ($query) => $query->where('tenant_id', $tenantId))->ignore($product?->id)],
            'name' => ['required','string','max:255'],
            'short_name' => ['nullable','string','max:255'],
            'type' => [
                'required',
                Rule::in([
                    'product',
                    'service',
                ]),
            ],
            'category' => ['nullable','string','max:100',],
            'subcategory' => ['nullable','string','max:100',],
            'brand' => ['nullable','string','max:100',],
            'model' => ['nullable','string','max:100',],
            'unit' => ['required','string','max:20',],
            'description' => ['nullable','string',],
            'cost_price' => ['nullable','numeric','min:0',],
            'sale_price' => ['required','numeric','min:0',],
            'sale_price_with_tax' => ['nullable','numeric','min:0',],
            'margin_percent' => ['nullable','numeric','min:0',],
            'tax_type' => ['required',Rule::in(['standard','exempt','zero',]),],
            'tax_rate' => ['required','numeric','min:0','max:100',],
            'tax_exemption_code' => ['nullable','string','max:50',],
            'tax_exemption_reason' => ['nullable','string','max:255',],
            'manage_stock' => ['nullable','boolean',],
            'allow_negative_stock' => ['nullable','boolean',],
            'minimum_stock' => ['nullable','numeric','min:0',],
            'maximum_stock' => ['nullable','numeric','min:0',],
            'manage_lots' => ['nullable','boolean',],
            'has_expiration' => ['nullable','boolean',],
            'expiration_alert_days' => ['nullable','integer','min:0','max:3650',],
            'supplier_id' => ['nullable','integer','exists:clients,id',],
            'is_active' => ['nullable','boolean',],
            'is_sellable' => ['nullable','boolean',],
            'is_purchasable' => ['nullable','boolean',],
            'notes' => ['nullable','string',],
        ]);
    }

    /**
     * Normalização dos dados.
     */
    private function normalizeProductData(array $data): array
    {
        $data['manage_stock'] = !empty($data['manage_stock']);
        $data['allow_negative_stock'] = !empty($data['allow_negative_stock']);

        $data['manage_lots'] = !empty($data['manage_lots']);
        $data['has_expiration'] = !empty($data['has_expiration']);

        $data['is_active'] = !empty($data['is_active']);
        $data['is_sellable'] = !empty($data['is_sellable']);
        $data['is_purchasable'] = !empty($data['is_purchasable']);

        /*
         * Se não for tributado pela taxa normal,
         * a taxa não deve continuar a ser 14%.
         */
        if (($data['tax_type'] ?? null) !== 'standard') {
            $data['tax_rate'] = 0;
        }

        /*
         * Calcula preço com IVA.
         */
        if (isset($data['sale_price'])) {
            $salePrice = (float) $data['sale_price'];
            $taxRate = (float) ($data['tax_rate'] ?? 0);

            $data['sale_price_with_tax'] = round($salePrice + ($salePrice * $taxRate / 100),2);
        }

        /*
         * Calcula margem.
         */
        if (isset($data['cost_price']) &&isset($data['sale_price']) &&(float) $data['cost_price'] > 0) 
        {
            $cost = (float) $data['cost_price'];
            $sale = (float) $data['sale_price'];

            $data['margin_percent'] = round((($sale - $cost) / $cost) * 100,2);
        }

        return $data;
    }

    /**
     * Garantir que o produto pertence ao tenant atual.
     */
    private function authorizeTenant(Product $product): void
    {
        $tenantId = session('tenant_id');

        abort_unless(
            $product->tenant_id === $tenantId,
            404
        );
    }
}
