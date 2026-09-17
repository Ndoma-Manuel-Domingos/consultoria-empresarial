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
        Schema::create('reception_triages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reception_appointment_id')->constrained('reception_appointments')->cascadeOnDelete();
            $table->string('business_area')->nullable();
            $table->string('company_size')->nullable();
            $table->string('need_type')->nullable();
            $table->string('urgency')->nullable();
            $table->text('business_situation')->nullable();
            $table->text('presented_problem')->nullable();
            $table->text('requested_solution')->nullable();
            $table->text('documents_presented')->nullable();
            $table->text('documents_pending')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reception_triages');
    }
};
