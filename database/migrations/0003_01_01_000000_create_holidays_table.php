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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('type', ['public', 'observance', 'school', 'optional'])->default('public');
            $table->boolean('is_day_off')->default(true); // Выходной день
            $table->timestamps();
            
            // Indexes
            $table->index('date');
            $table->index('is_day_off');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};