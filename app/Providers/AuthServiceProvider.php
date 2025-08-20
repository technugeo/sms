<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Institute;
use App\Models\Subject;
use App\Models\Role;

use App\Policies\StaffPolicy;
use App\Policies\StudentPolicy;
use App\Policies\InstitutePolicy;
use App\Policies\SubjectPolicy;
use App\Policies\RolePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Staff::class => StaffPolicy::class,
        Student::class => StudentPolicy::class,
        Institute::class => InstitutePolicy::class,
        Subject::class => SubjectPolicy::class,
        Role::class => RolePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
