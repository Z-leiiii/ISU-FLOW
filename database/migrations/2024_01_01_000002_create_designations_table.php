<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('earns_vacation_leave')->default(true);
            $table->boolean('earns_sick_leave')->default(true);
            $table->decimal('vacation_leave_rate', 5, 2)->default(1.25); // days per month
            $table->decimal('sick_leave_rate', 5, 2)->default(1.25); // days per month
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
