<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $casts = [
        'id' => 'integer', // mediumIncrements
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
        'flag',
        'wikiDataId',
    ];

    /**
     * Get the subregions for the region.
     */
    public function subregions(): HasMany
    {
        return $this->hasMany(Subregion::class);
    }

    /**
     * Get the countries for the region.
     */
    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }
}
