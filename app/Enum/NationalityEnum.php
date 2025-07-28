<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum NationalityEnum: string implements HasLabel, HasColor
{
    case CITIZEN = 'Citizen';
    case NON_CITIZEN = 'Non Citizen';
    case PERMANENT_RESIDENT = 'Permanent Resident';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CITIZEN => Color::Blue,
            self::NON_CITIZEN => Color::Red,
            self::PERMANENT_RESIDENT => Color::Gray,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CITIZEN => 'Citizen',
            self::NON_CITIZEN => 'Non Citizen',
            self::PERMANENT_RESIDENT => 'Permanent Resident',
        };
    }
}
