<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->unique()->constrained()->cascadeOnDelete();
            $table->string('student_number')->unique();
            $table->string('course');
            $table->string('college');
            $table->string('year_level');
            $table->string('section')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('address');
            $table->string('guardian_name');
            $table->string('guardian_contact');
            $table->string('emergency_contact')->nullable();
            $table->string('clearance_status')->default('pending');
            $table->text('clearance_notes')->nullable();
            $table->foreignId('tagged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tagged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
