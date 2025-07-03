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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->boolean('is_conducted')->default(false); // Проведено ли занятие
            $table->timestamp('marked_at')->nullable(); // Когда отмечено
            $table->foreignId('marked_by')->nullable()->references('id')->on('users')->onDelete('set null'); // Кто отметил
            $table->text('notes')->nullable(); // Примечания к занятию
            $table->integer('students_present')->nullable(); // Количество присутствующих студентов
            $table->timestamps();
            
            // Unique constraint - one lesson per schedule
            $table->unique('schedule_id');
            
            // Indexes
            $table->index('is_conducted');
            $table->index('marked_at');
            $table->index('marked_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};