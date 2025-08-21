<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'institute_code',
        'name',
        'code',
        'description',
        'status',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class, 'institute_code', 'mqa_institute_id');
    }
    public function courses()
    {
        return $this->hasMany(Course::class, 'faculty_id', 'code'); 
    }

}
