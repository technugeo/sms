<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum GenderEnum: string implements HasLabel, HasColor
{
    case MALE   = 'Male';
    case FEMALE = 'Female';
    case OTHER  = 'Other';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MALE   => Color::Blue,
            self::FEMALE => Color::Red,
            self::OTHER  => Color::Yellow,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MALE   => 'Male',
            self::FEMALE => 'Female',
            self::OTHER  => 'Other',
        };
    }
}
