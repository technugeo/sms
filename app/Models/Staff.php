<?php

namespace App\Models;

use App\Enum\CitizenEnum;
use App\Enum\GenderEnum;
use App\Enum\MarriageEnum;
use App\Enum\NationalityEnum;
use App\Enum\RoleEnum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Staff extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone_number',
        'nric',
        'nationality_id',
        'nationality_type',
        'citizen',
        'marriage_status',
        'gender',
        'address_id',
        'department_id',
        'position',
        'race',
        'religion',
        'institute_id',
        'access_level',
    ];

    protected $casts = [
        'nationality_type' => NationalityEnum::class,
        'citizen' => CitizenEnum::class,
        'marriage_status' => MarriageEnum::class,
        'gender' => GenderEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
     */
    public function addresses()
    {
        return $this->hasMany(\App\Models\Address::class, 'user_id', 'user_id');
    }

    public function address()
    {
        return $this->belongsTo(\App\Models\Address::class);
    }
    

    public function institute()
    {
        return $this->belongsTo(\App\Models\Institute::class, 'institute_id');
    }

    /**
     * Get Department
     * @return BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
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
