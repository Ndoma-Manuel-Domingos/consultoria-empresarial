<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->enum('type', [
                'in',
                'purchase',
                'adjustment_in',
                'return_in',
                'transfer_in',
                'out',
                'sale',
                'adjustment_out',
                'return_out',
                'transfer_out',
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('type')->change();
        });
    }
};
