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
        'created_by',        
        'updated_by',        
        'deleted_by',        
    ];

    protected $casts = [
        'programme_type' => ProgrammeEnum::class,
        'status' => StatusEnum::class,
    ];
    
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'code');
    }



    public function semesters()
    {
        return $this->hasMany(Semester::class, 'prog_code', 'prog_code');
    }

    




    //
}
