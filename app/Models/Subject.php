<?php

namespace App\Models;

use App\Enum\SubjectStatusEnum;

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
        'semester',
        'subject_status',
        'credit_hour',
        'is_core',
        'status'
    ];

    protected $casts = [
        'subject_status' => SubjectStatusEnum::class,
    ];



}
