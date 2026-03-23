<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documate_transactions', function (Blueprint $table) {
            $table->date('appointment_date')->nullable()->after('submitted_file_path');
            $table->string('appointment_session')->nullable()->after('appointment_date');
            $table->timestamp('appointment_booked_at')->nullable()->after('appointment_session');
            $table->index(['appointment_date', 'appointment_session'], 'documate_transactions_appointment_idx');
        });
    }

    public function down(): void
    {
        Schema::table('documate_transactions', function (Blueprint $table) {
            $table->dropIndex('documate_transactions_appointment_idx');
            $table->dropColumn([
                'appointment_date',
                'appointment_session',
                'appointment_booked_at',
            ]);
        });
    }
};
