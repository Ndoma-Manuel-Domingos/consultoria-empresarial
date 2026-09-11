<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {

            $table->id();

            // MULTI TENANT
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            // CLIENTE - opcional no POS
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();

            // DOCUMENTO
            $table->string('number', 50);

            // ESTADO
            // draft      = rascunho
            // completed  = concluída
            // cancelled  = anulada
            $table->string('status', 30)->default('completed');

            // DATAS
            $table->dateTime('sale_date');

            // VALORES
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            // PAGAMENTO
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);

            // MÉTODO PRINCIPAL
            // cash
            // multicaixa
            // mixed
            // credit
            $table->string('payment_method', 30)->nullable();

            // OBSERVAÇÕES
            $table->text('notes')->nullable();

            // AUDITORIA
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('cancelled_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'number'],
                'sales_tenant_number_unique'
            );

            $table->index(['tenant_id', 'sale_date']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
