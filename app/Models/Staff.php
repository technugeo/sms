<?php

namespace App\Models;

use App\Enum\CitizenEnum;
use App\Enum\GenderEnum;
use App\Enum\MarriageEnum;
use App\Enum\NationalityEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Staff extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'phone_number',
        'nric',
        'nationality_id',
        'nationality_type',
        'citizen',
        'marriage_status',
        'gender',
        'address_id',
        'designation_id',
        'department_id',
    ];

    protected $casts = [
        'nationality_type' => NationalityEnum::class,
        'citizen' => CitizenEnum::class,
        'marriage_status' => MarriageEnum::class,
        'gender' => GenderEnum::class,
    ];

    /**
     * Get Nationality
     * @return BelongsTo
     */
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    /**
     * Get Addresses
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get Designation
     * @return BelongsTo
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Get Department
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();

        $this->addMediaCollection('background')
            ->singleFile();
    }
}
