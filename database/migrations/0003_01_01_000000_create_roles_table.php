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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'super_admin',
                'display_name' => 'Суперадминистратор',
                'description' => 'Полный доступ ко всем функциям системы',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'admin',
                'display_name' => 'Администратор',
                'description' => 'Управление пользователями, группами и расписанием',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'teacher',
                'display_name' => 'Преподаватель',
                'description' => 'Отметка занятий и просмотр расписания',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};