<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes;

    protected $table = 'lib_semester'; 

    protected $fillable = [
        'prog_code',
        'semester_code',
        'semester_name',
        'prerequisite_code',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'prog_code');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'semester_id');
    }


}
