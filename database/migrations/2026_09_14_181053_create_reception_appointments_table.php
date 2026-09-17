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
        Schema::create('reception_appointments', function (Blueprint $table) {
            $table->id();$table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->date('appointment_date');
            $table->time('appointment_time')->nullable();
            $table->enum('status', ['waiting', 'triage', 'awaiting_payment', 'paid', 'in_progress', 'completed', 'cancelled'])->default('waiting');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->string('service_type')->nullable();
            $table->text('reason')->nullable();
            $table->text('reception_notes')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'appointment_date']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reception_appointments');
    }
};
