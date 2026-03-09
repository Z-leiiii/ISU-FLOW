<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designation_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('designation_id')->constrained()->onDelete('cascade');
            $table->string('document_title');
            $table->string('file_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('hr_remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['is_current']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designation_documents');
    }
};
