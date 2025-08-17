<?php

namespace App\Models;

use App\Enum\CitizenEnum;
use App\Enum\GenderEnum;
use App\Enum\MarriageEnum;
use App\Enum\NationalityEnum;
use App\Enum\StaffTypeEnum;
use App\Enum\StatusEnum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model implements HasMedia
{
    use SoftDeletes;

    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone_number',
        'nric',
        'nationality',
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
        'emplloyment_status',
        'staff_type',
        'deleted_by',
    ];

    protected $casts = [
        'nationality_type' => NationalityEnum::class,
        'citizen' => CitizenEnum::class,
        'marriage_status' => MarriageEnum::class,
        'gender' => GenderEnum::class,
        'employment_stats' => StatusEnum::class,
        'staff_type' => StaffTypeEnum::class,
    ];

    protected static function booted()
    {
        static::deleting(function ($staff) {
            // Only run on soft delete, not force delete
            if (! $staff->isForceDeleting()) {
                // Track who deleted
                if (auth()->check()) {
                    $staff->deleted_by = auth()->user()->email;
                    $staff->saveQuietly(); // prevents triggering events again
                }

                // Update related user status
                if ($staff->user) {
                    $staff->user->updateQuietly([
                        'status' => 'DELETED',
                    ]);
                }
            }
        });
    }

    

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
        return $this->belongsTo(\App\Models\Institute::class, 'institute_id', 'mqa_institute_id');
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
