<?php

namespace App\Enum;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProgrammeEnum: string implements HasLabel
{
    case DIPLOMA   = 'Diploma';
    case COMPETENCY = 'Certificate of Competency';
    case ATTENDANCE = 'Certificate of Attendance';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::DIPLOMA  => 'Diploma',
            self::COMPETENCY => 'Certificate of Competency',
            self::ATTENDANCE => 'Certificate of Attendance',
        };
    }
}
