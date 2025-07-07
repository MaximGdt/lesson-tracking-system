<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->truncate(); // очищаем таблицу, если нужно

        DB::table('roles')->insert([
            [
                'name' => 'super_admin',
                'display_name' => 'Суперадминистратор',
                'translation_key' => 'role_super_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Администратор',
                'translation_key' => 'role_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'teacher',
                'display_name' => 'Преподаватель',
                'translation_key' => 'role_teacher',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
