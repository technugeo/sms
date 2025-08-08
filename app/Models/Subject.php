<?php

namespace App\Models;

use App\Enum\StatusEnum;
use App\Enum\SubjectTypeEnum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    protected $table = 'lib_sem_subjects'; 

    protected $fillable = [
        'subject_code',
        'subject_name',
        'semester_id',
        'faculty_code',
        'prog_code',
        'subject_type',
        'status',
        'is_core',
    ];

    protected $casts = [
        'subject_type' => SubjectTypeEnum::class,
        'status' => StatusEnum::class,
    ];


}
