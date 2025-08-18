<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEduhistory extends Model
{
    use SoftDeletes;

    protected $table = 'student_eduhistory'; 

    protected $fillable = [
        'matric_id',
        'institution_name',
        'country',
        'level',
        'subject_name',
        'grade',
        'programme_name',
        'cgpa',
        'start_year',
        'end_year',
        'created_by',
        'updated_by',
        'deleted_by',
    ];



}
