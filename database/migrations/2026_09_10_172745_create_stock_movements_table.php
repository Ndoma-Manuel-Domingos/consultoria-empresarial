<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            // MULTI-TENANT
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            // PRODUTO
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // LOTE
            $table->foreignId('product_lot_id')->nullable()->constrained('product_lots')->nullOnDelete();
            // TIPO DO MOVIMENTO
            $table->string('type', 30);

            // QUANTIDADE
            $table->decimal('quantity', 15, 3);

            // STOCK ANTES / DEPOIS
            $table->decimal('stock_before', 15, 3)->default(0);
            $table->decimal('stock_after', 15, 3)->default(0);

            // PREÇO / CUSTO
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);

            // REFERÊNCIA
            $table->string('reference', 100)->nullable();

            // DOCUMENTO
            $table->string('document_type', 50)->nullable();
            $table->string('document_number', 100)->nullable();

            // MOTIVO
            $table->string('reason', 255)->nullable();

            // OBSERVAÇÕES
            $table->text('notes')->nullable();

            // DATA
            $table->dateTime('movement_date');

            // AUDITORIA
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // INDEXES
            $table->index(['tenant_id', 'product_id']);
            $table->index(['tenant_id', 'product_lot_id']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'movement_date']);
            $table->index(['tenant_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
