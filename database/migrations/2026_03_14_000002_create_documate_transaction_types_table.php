<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documate_transaction_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_name')->nullable();
            $table->text('description')->nullable();
            $table->json('required_signatories')->nullable();
            $table->json('workflow_steps')->nullable();
            $table->json('required_profile_fields')->nullable();
            $table->boolean('requires_notary')->default(false);
            $table->boolean('admin_approval_required')->default(true);
            $table->boolean('allow_student_status_updates')->default(true);
            $table->boolean('requires_clearance')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documate_transaction_types');
    }
};
