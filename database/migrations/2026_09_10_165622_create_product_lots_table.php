<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_lots', function (Blueprint $table) {
            $table->id();

            // MULTI-TENANT
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            // PRODUTO
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // IDENTIFICAÇÃO DO LOTE
            $table->string('lot_number', 100);

            // DATAS
            $table->date('manufactured_at')->nullable();
            $table->date('expires_at')->nullable();

            // STOCK
            $table->decimal('initial_quantity', 15, 3)->default(0);
            $table->decimal('current_quantity', 15, 3)->default(0);
            $table->decimal('reserved_quantity', 15, 3)->default(0);

            // PREÇO DO LOTE
            $table->decimal('cost_price', 15, 2)->nullable();

            // ESTADO
            $table->boolean('is_active')->default(true);

            // OBSERVAÇÕES
            $table->text('notes')->nullable();

            // AUDITORIA
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'product_id']);
            $table->index(['tenant_id', 'lot_number']);
            $table->index(['tenant_id', 'expires_at']);
            $table->index(['tenant_id', 'is_active']);

            // Evita o mesmo lote duplicado para o mesmo produto
            $table->unique(
                ['tenant_id', 'product_id', 'lot_number'],
                'product_lots_tenant_product_lot_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_lots');
    }
};
