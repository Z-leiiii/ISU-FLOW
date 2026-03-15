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
        Schema::create('compensatory_time_offs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('hours_earned', 5, 2);
            $table->decimal('hours_used', 5, 2)->default(0);
            $table->string('reason');
            $table->date('work_date');
            $table->date('earned_date');
            $table->date('expires_at');
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['expires_at']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensatory_time_offs');
    }
};
