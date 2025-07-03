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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Уникальный код группы
            $table->text('description')->nullable();
            $table->integer('course')->default(1); // Курс (1-6)
            $table->string('speciality')->nullable(); // Специальность
            $table->date('start_date')->nullable(); // Дата начала обучения
            $table->date('end_date')->nullable(); // Дата окончания обучения
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index('course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};