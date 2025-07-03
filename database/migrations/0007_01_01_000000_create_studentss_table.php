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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique(); // ID из внешней системы
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->date('birth_date')->nullable();
            $table->string('student_card_number')->nullable(); // Номер студенческого билета
            $table->boolean('is_active')->default(true);
            $table->json('additional_data')->nullable(); // Дополнительные данные из API
            $table->timestamp('synced_at')->nullable(); // Время последней синхронизации
            $table->timestamps();
            
            // Indexes
            $table->index('external_id');
            $table->index('group_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};