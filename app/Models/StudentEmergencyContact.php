<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEmergencyContact extends Model
{
    use SoftDeletes;

    protected $table = 'student_emergency_contact'; 

    protected $fillable = [
        'matric_id',
        'relationship',
        'full_name',
        'address',
        'phone_number',
        'alt_phone_number',
        'address',
        'is_primary',
    ];

}
