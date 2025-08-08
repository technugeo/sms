<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SubjectTypeEnum: string implements HasLabel
{
    case CORE = 'Core';
    case ELECTIVE = 'Elective';
    case REPEAT = 'Repeat';
    case OTHER = 'Other';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::CORE => 'Core',
            self::ELECTIVE => 'Elective',
            self::REPEAT => 'Repeat',
            self::OTHER => 'Other',
        };
    }
}
