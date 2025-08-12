<?php

namespace App\Models;

use App\Enum\CitizenEnum;
use App\Enum\AcademicEnum;
use App\Enum\MarriageEnum;
use App\Enum\NationalityEnum;
use App\Enum\RaceEnum;
use App\Enum\ReligionEnum;
use App\Enum\IntakeEnum;
use App\Enum\GenderEnum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $table = 'student'; 
    
    protected $fillable = [
        'matric_id',
        'nric',
        'phone_number',
        'full_name',
        'nationality',
        'citizen',
        'user_id',
        'nationality_type',
        'marriage_status',
        'academic_status',
        'gender',
        'race',
        'religion',
        'intake_month',
        'intake_year',
        'passport_no',
        'current_course',
    ];

    protected $casts = [
        'nationality_type' => NationalityEnum::class,
        'citizen' => CitizenEnum::class,
        'marriage_status' => MarriageEnum::class,
        'academic_status' => AcademicEnum::class,
        'gender' => GenderEnum::class,
        'race' => RaceEnum::class,
        'religion' => ReligionEnum::class,
        'intake_month' => IntakeEnum::class,
    ];

    /**
     * Get Nationality
     * @return BelongsTo
     */
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

   
    

    
    public function localAddress()
    {
        return $this->hasMany(\App\Models\Address::class, 'user_id', 'user_id')
            ->where('address_type', 'Local');
    }

    public function foreignAddress()
    {
        return $this->hasMany(Address::class, 'user_id', 'user_id')->where('address_type', 'Foreign');
    }


    public function address()
    {
        return $this->belongsTo(\App\Models\Address::class);
    }

    
    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class, 'current_course', 'prog_code');
    }


    

}
