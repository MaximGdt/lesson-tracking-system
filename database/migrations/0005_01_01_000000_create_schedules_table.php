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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('subject'); // Предмет
            $table->date('date'); // Дата занятия
            $table->time('start_time'); // Время начала
            $table->time('end_time'); // Время окончания
            $table->string('room')->nullable(); // Аудитория
            $table->enum('type', ['lecture', 'practice', 'lab', 'exam', 'consultation'])->default('lecture'); // Тип занятия
            $table->text('notes')->nullable(); // Примечания
            $table->boolean('is_cancelled')->default(false); // Отменено ли занятие
            $table->string('cancellation_reason')->nullable(); // Причина отмены
            $table->timestamps();
            
            // Indexes
            $table->index('group_id');
            $table->index('teacher_id');
            $table->index('date');
            $table->index(['date', 'group_id']);
            $table->index(['date', 'teacher_id']);
            $table->index('is_cancelled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};