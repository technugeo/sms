<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dun extends Model
{
    protected $fillable = [
        'u_dun_id',
        'dun',
        'u_parliament_id',
        'code_dun',
        'code_parliament',
        'code_state',
        'code_dun2',
        'parliament_id',
    ];

    public function parliament(): BelongsTo
    {
        return $this->belongsTo(Parliament::class, 'u_parliament_id', 'u_parliament_id');
    }
}
