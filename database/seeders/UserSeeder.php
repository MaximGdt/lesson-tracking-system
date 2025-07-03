<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();

        // Create super admin
        $superAdmin = User::create([
            'first_name' => 'Супер',
            'last_name' => 'Администратор',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->roles()->attach($superAdminRole);

        // Create regular admin
        $admin = User::create([
            'first_name' => 'Иван',
            'last_name' => 'Иванов',
            'middle_name' => 'Иванович',
            'email' => 'ivan.admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '+380501234567',
            'email_verified_at' => now(),
        ]);
        $admin->roles()->attach($adminRole);

        // Create teachers
        $teachers = [
            [
                'first_name' => 'Петр',
                'last_name' => 'Петров',
                'middle_name' => 'Петрович',
                'email' => 'teacher@example.com',
                'phone' => '+380502345678',
            ],
            [
                'first_name' => 'Мария',
                'last_name' => 'Сидорова',
                'middle_name' => 'Ивановна',
                'email' => 'maria.teacher@example.com',
                'phone' => '+380503456789',
            ],
            [
                'first_name' => 'Александр',
                'last_name' => 'Александров',
                'middle_name' => 'Александрович',
                'email' => 'alex.teacher@example.com',
                'phone' => '+380504567890',
            ],
        ];

        foreach ($teachers as $teacherData) {
            $teacher = User::create(array_merge($teacherData, [
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]));
            $teacher->roles()->attach($teacherRole);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Default credentials:');
        $this->command->info('Super Admin: admin@example.com / password');
        $this->command->info('Teacher: teacher@example.com / password');
    }
}