<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subregion extends Model
{
    protected $casts = [
        'id' => 'integer', // mediumIncrements
        'region_id' => 'integer', // unsignedMediumInteger
        'flag' => 'boolean',
        'translations' => 'array', // Assuming translations is stored as JSON
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'translations',
        'region_id',
        'flag',
        'wikiDataId',
    ];

    /**
     * Get the region that owns the subregion.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the countries for the subregion.
     */
    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }
}
