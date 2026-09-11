<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // =========================================================
            // MULTI-TENANT
            // =========================================================
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            // =========================================================
            // IDENTIFICAÇÃO
            // =========================================================

            // Código interno do produto
            $table->string('code', 50);

            // Código de barras / EAN / GTIN
            $table->string('barcode', 50)->nullable();

            // Nome comercial
            $table->string('name');

            // Nome alternativo / designação curta
            $table->string('short_name')->nullable();

            // =========================================================
            // TIPO
            // =========================================================

            // product = produto físico
            // service = serviço
            $table->string('type', 20)->default('product');

            // =========================================================
            // CLASSIFICAÇÃO
            // =========================================================

            // Categoria do produto
            $table->string('category', 100)->nullable();

            // Subcategoria
            $table->string('subcategory', 100)->nullable();

            // Marca
            $table->string('brand', 100)->nullable();

            // Modelo / referência do fabricante
            $table->string('model', 100)->nullable();

            // =========================================================
            // UNIDADE
            // =========================================================

            // UN = unidade
            // CX = caixa
            // KG = quilograma
            // L = litro
            // etc.
            $table->string('unit', 20)->default('UN');

            // =========================================================
            // DESCRIÇÃO
            // =========================================================

            $table->text('description')->nullable();

            // =========================================================
            // PREÇOS
            // =========================================================

            // Preço de compra / custo atual
            $table->decimal('cost_price', 15, 2)->default(0);

            // Preço de venda sem IVA
            $table->decimal('sale_price', 15, 2)->default(0);

            // Preço de venda com IVA
            $table->decimal('sale_price_with_tax', 15, 2)->default(0);

            // Margem percentual
            $table->decimal('margin_percent', 8, 2)->default(0);

            // =========================================================
            // FISCAL / IVA
            // =========================================================

            // standard = taxa normal
            // exempt   = isento
            // zero     = taxa 0
            $table->string('tax_type', 20)->default('standard');

            // Taxa aplicável ao produto.
            // 14.00 = taxa geral atual do IVA.
            $table->decimal('tax_rate', 5, 2)->default(14);

            // Código / motivo de isenção, quando aplicável
            $table->string('tax_exemption_code', 50)->nullable();

            // Motivo / descrição da isenção
            $table->string('tax_exemption_reason')->nullable();

            // =========================================================
            // ESTOQUE - CONFIGURAÇÃO
            // =========================================================

            // Controla estoque?
            $table->boolean('manage_stock')->default(true);

            // Permitir venda quando estoque chegar a zero?
            $table->boolean('allow_negative_stock')->default(false);

            // Estoque mínimo para alerta
            $table->decimal('minimum_stock', 15, 3)->default(0);

            // Estoque máximo recomendado
            $table->decimal('maximum_stock', 15, 3)->nullable();

            // =========================================================
            // LOTES / VALIDADE - PREPARAÇÃO FUTURA
            // =========================================================

            // Produto necessita controle por lote?
            $table->boolean('manage_lots')->default(false);
            // Produto possui validade?
            $table->boolean('has_expiration')->default(false);

            // Número de dias de alerta antes da validade
            $table->unsignedInteger('expiration_alert_days')->default(30);

            // =========================================================
            // FORNECEDOR PRINCIPAL
            // =========================================================
            $table->foreignId('supplier_id')->nullable()->constrained('clients')->nullOnDelete();

            // =========================================================
            // ESTADO
            // =========================================================
            $table->boolean('is_active')->default(true);
            // Produto pode ser vendido?
            $table->boolean('is_sellable')->default(true);
            // Produto pode ser comprado?
            $table->boolean('is_purchasable')->default(true);
            // =========================================================
            // OBSERVAÇÕES
            // =========================================================

            $table->text('notes')->nullable();

            // =========================================================
            // AUDITORIA
            // =========================================================
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // =========================================================
            // INDEXES
            // =========================================================

            $table->unique(['tenant_id', 'code']);

            $table->index(['tenant_id', 'name']);
            $table->index(['tenant_id', 'barcode']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'category']);
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'manage_stock']);
            $table->index(['tenant_id', 'manage_lots']);
            $table->index(['tenant_id', 'has_expiration']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
