<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $casts = [
        'id' => 'integer', // mediumIncrements
        'region_id' => 'integer', // unsignedMediumInteger
        'subregion_id' => 'integer', // unsignedMediumInteger
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'flag' => 'boolean',
        'timezones' => 'array', // Assuming timezones is stored as JSON
        'translations' => 'array', // Assuming translations is stored as JSON
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'iso3',
        'numeric_code',
        'iso2',
        'phonecode',
        'capital',
        'currency',
        'currency_name',
        'currency_symbol',
        'tld',
        'native',
        'region', // This is redundant if region_id is used for relationship
        'region_id',
        'subregion', // This is redundant if subregion_id is used for relationship
        'subregion_id',
        'nationality',
        'timezones',
        'translations',
        'latitude',
        'longitude',
        'emoji',
        'emojiU',
        'flag',
        'wikiDataId',
    ];

    /**
     * Get the region that owns the country.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the subregion that owns the country.
     */
    public function subregion(): BelongsTo
    {
        return $this->belongsTo(Subregion::class);
    }

    /**
     * Get the states for the country.
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
