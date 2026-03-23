<?php

use App\Models\DocumateTransaction;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documate_transaction_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DocumateTransaction::class, 'transaction_id')->constrained('documate_transactions')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_role')->nullable();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('remarks')->nullable();
            $table->string('file_path')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documate_transaction_status_logs');
    }
};
