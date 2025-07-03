<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Lesson;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Admin gates
        Gate::define('admin', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'admin']);
        });

        Gate::define('super-admin', function (User $user) {
            return $user->hasRole('super_admin');
        });

        // Teacher gates
        Gate::define('teacher', function (User $user) {
            return $user->hasRole('teacher') || $user->hasAnyRole(['super_admin', 'admin']);
        });

        // Schedule gates
        Gate::define('mark-lesson', function (User $user, Schedule $schedule) {
            if ($user->hasAnyRole(['super_admin', 'admin'])) {
                return true;
            }
            
            return $user->id === $schedule->teacher_id;
        });

        Gate::define('edit-past-lesson', function (User $user) {
            return $user->hasRole('super_admin');
        });
    }
}