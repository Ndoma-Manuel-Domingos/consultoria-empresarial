<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('sale_id')
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Não usamos obrigatoriamente product_lot_id aqui.
             *
             * Uma linha da venda pode consumir vários lotes
             * através da tabela sale_item_lots.
             */
            $table->decimal('quantity', 15, 3);

            $table->decimal('unit_price', 15, 2);

            $table->decimal('discount', 15, 2)->default(0);

            $table->decimal('tax_rate', 5, 2)->default(0);

            $table->decimal('tax_amount', 15, 2)->default(0);

            $table->decimal('subtotal', 15, 2)->default(0);

            $table->decimal('total', 15, 2)->default(0);

            $table->timestamps();

            $table->index(['sale_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
