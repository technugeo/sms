<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'institute_id',
        'name',
        'code',
        'description',
        'status',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class, 'institute_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'faculty_id');
    }



    public function designations(): HasMany
    {
        return $this->hasMany(Designation::class);
    }
}
