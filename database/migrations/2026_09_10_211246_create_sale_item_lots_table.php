<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_item_lots', function (Blueprint $table) {

            $table->id();

            $table->foreignId('sale_item_id')->constrained('sale_items')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_lot_id')->constrained('product_lots')->restrictOnDelete();
            
            // Quantidade retirada deste lote
            $table->decimal('quantity', 15, 3);

            // Custo do lote no momento da venda
            $table->decimal('unit_cost', 15, 2)->default(0);

            // Custo total
            $table->decimal('total_cost', 15, 2)->default(0);

            $table->timestamps();

            $table->index([
                'sale_item_id',
                'product_lot_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_item_lots');
    }
};
