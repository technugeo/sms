<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RaceEnum: string implements HasLabel, HasColor
{
    case MELAYU   = 'Melayu';
    case CINA = 'Cina';
    case INDIA = 'India';
    case OTHER  = 'Other';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MELAYU => Color::Blue,
            self::CINA => Color::Green,
            self::INDIA => Color::Yellow,
            self::OTHER => Color::Gray,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MELAYU  => 'Melayu',
            self::CINA => 'Cina',
            self::INDIA => 'India',
            self::OTHER  => 'Other',
        };
    }
}
