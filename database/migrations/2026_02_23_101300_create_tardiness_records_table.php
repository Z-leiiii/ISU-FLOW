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
        Schema::create('tardiness_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('time_in');
            $table->time('scheduled_time_in');
            $table->decimal('minutes_late', 8, 2);
            $table->enum('status', ['late', 'very_late', 'extreme_late'])->default('late');
            $table->text('reason')->nullable();
            $table->boolean('is_excused')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tardiness_records');
    }
};
