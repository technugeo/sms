<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SubjectStatusEnum: string implements HasLabel
{
    case DISCIPLINE = 'Discipline';
    case COMMON = 'Common';
    case MPU = 'Mpu';
    case LI = 'L.I';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::DISCIPLINE => 'Discipline',
            self::COMMON => 'Common',
            self::MPU => 'Mpu',
            self::LI => 'L.I',
        };
    }
}
