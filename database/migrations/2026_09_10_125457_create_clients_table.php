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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // MULTI-TENANT
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            // TIPO individual = Pessoa singular | company    = Empresa
            $table->string('type', 20)->default('individual');

            // IDENTIFICAÇÃO
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('commercial_name')->nullable();

            // DADOS FISCAIS
            $table->string('nif', 30)->nullable();
            $table->string('nif_type', 30)->nullable();
            $table->string('tax_regime', 50)->nullable();
            $table->boolean('vat_payer')->default(false);

            // CONTACTOS
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('phone_secondary', 30)->nullable();
            $table->string('website')->nullable();

            // ENDEREÇO
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('municipality', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->default('Angola');

            // PESSOA DE CONTACTO
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone', 30)->nullable();
            $table->string('contact_person_email')->nullable();

            // CONDIÇÕES COMERCIAIS
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->unsignedInteger('payment_terms')->default(0);

            // OBSERVAÇÕES
            $table->text('notes')->nullable();

            // ESTADO
            $table->boolean('is_active')->default(true);

            // AUDITORIA
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            //INDEXES
            $table->index(['tenant_id', 'name']);
            $table->index(['tenant_id', 'nif']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
