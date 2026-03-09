<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->string('application_number')->unique();
            $table->date('date_filed');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('number_of_days');
            $table->text('reason');
            $table->enum('status', ['pending', 'recommended', 'approved', 'disapproved', 'cancelled'])->default('pending');
            $table->boolean('is_without_pay')->default(false);
            $table->text('hr_remarks')->nullable();
            $table->text('department_head_remarks')->nullable();
            $table->timestamp('recommended_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disapproved_at')->nullable();
            $table->foreignId('recommended_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('disapproved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('document_path')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['date_filed']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
