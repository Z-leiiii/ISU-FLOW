<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->decimal('total_earned', 5, 2)->default(0);
            $table->decimal('total_used', 5, 2)->default(0);
            $table->decimal('balance', 5, 2)->virtualAs('total_earned - total_used');
            $table->date('as_of_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'leave_type_id', 'as_of_date']);
            $table->index(['user_id', 'leave_type_id']);
            $table->index(['as_of_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_credits');
    }
};
