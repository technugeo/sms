<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // allow listing all departments
    }

    public function view(User $user, Department $department): bool
    {
        return true; // allow viewing a single department
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Department $department): bool
    {
        return true;
    }

    public function delete(User $user, Department $department): bool
    {
        return true;
    }

}
