<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parliament extends Model
{
    protected $fillable = [
        'u_parliament_id',
        'parliament',
        'code_parliament',
        'u_state_id',
        'state_id',
    ];

    public function duns(): HasMany
    {
        return $this->hasMany(Dun::class, 'u_parliament_id', 'u_parliament_id');
    }
}
