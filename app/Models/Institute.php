<?php

namespace App\Models;

use App\Enum\ProgrammeEnum;
use App\Enum\StatusEnum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institute extends Model
{
    use SoftDeletes;

    protected $table = 'institutions'; 

    protected $fillable = [
        'mqa_institute_id',
        'name',
        'abbreviation',
        'category',
        'country', 
        'state', 
        'district', 
        'dun', 
        'parliament', 
        'status',        
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];
    
    public function category_institute(): BelongsTo
    {
        return $this->belongsTo(CategoryInstitute::class, 'category'); 
    }

    
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country');
    }
    
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state');
    }
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'district');
    }
    public function parliament(): BelongsTo
    {
        return $this->belongsTo(Parliament::class, 'parliament',);
    }
    public function dun(): BelongsTo
    {
        return $this->belongsTo(Dun::class, 'dun',);
    }
        

    //
}
