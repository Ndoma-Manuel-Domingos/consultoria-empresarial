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
        Schema::table('reception_appointments', function (Blueprint $table) {
            $table->timestamp('referred_at')->nullable()->after('completed_at');

            $table->foreignId('referred_by')
                ->nullable()
                ->after('referred_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('consultation_status', 30)
                ->default('pending')
                ->after('referred_by');

            $table->text('referral_notes')
                ->nullable()
                ->after('consultation_status');

            $table->index(['tenant_id', 'consultation_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reception_appointments', function (Blueprint $table) {
            $table->dropIndex([
                'tenant_id',
                'consultation_status'
            ]);

            $table->dropForeign(['referred_by']);

            $table->dropColumn([
                'referred_at',
                'referred_by',
                'consultation_status',
                'referral_notes',
            ]);
        });
    }
};
