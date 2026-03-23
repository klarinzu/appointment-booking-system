<?php

use App\Models\DocumateTransactionType;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documate_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(DocumateTransactionType::class, 'transaction_type_id')->constrained('documate_transaction_types')->cascadeOnDelete();
            $table->string('reference_no')->unique();
            $table->string('status')->default('pending_admin_approval');
            $table->text('student_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('submitted_file_path')->nullable();
            $table->json('form_payload')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('admin_approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['transaction_type_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documate_transactions');
    }
};
