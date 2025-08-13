<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGuardian extends Model
{
    use SoftDeletes;

    protected $table = 'student_guardians'; 

    protected $fillable = [
        'matric_id',
        'guardian_type',
        'full_name',
        'ic_passport_no',
        'nationality',
        'address',
        'phone_hp',
        'phone_house',
        'phone_office',
        'email',
        'occupation',
        'monthly_income',
        'is_emergency_contact',
    ];

}
