<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('document_type', 30)->default('invoice');
            $table->string('series', 30)->nullable();
            $table->string('fiscal_number', 100)->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->string('currency', 10)->default('AOA');
            $table->decimal('taxable_amount', 15, 2)->default(0);
            $table->decimal('exempt_amount', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'document_type',
                'series',
                'fiscal_number',
                'issued_at',
                'currency',
                'taxable_amount',
                'exempt_amount',
                'balance_due',
            ]);
        });
    }
};
