<?php

namespace App\Models;

use App\Enum\ProgrammeEnum;
use App\Enum\StatusEnum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $table = 'lib_course_prog'; 

    protected $fillable = [
        'prog_code',
        'prog_name',
        'faculty_id',
        'sponsoring_body',
        'programme_type', 
        'status',        
    ];

    protected $casts = [
        'programme_type' => ProgrammeEnum::class,
        'status' => StatusEnum::class,
    ];
    
    public function department()
    {
        return $this->belongsTo(Department::class, 'faculty_id');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'prog_code', 'prog_code');
    }

    public function institute()
    {
        return $this->belongsTo(\App\Models\Institute::class, 'faculty_id', 'id');
    }




    //
}
