<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RoleEnum: string implements HasLabel, HasColor
{
    case SA = 'SA';
    case AA = 'AA';
    case NAO = 'NAO';
    case AO = 'AO';
    case S = 'S';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SA => Color::Blue,
            self::AA => Color::Yellow,
            self::NAO => Color::Gray,
            self::AO => Color::Gray,
            self::S => Color::Green
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SA => 'Systems Admin',
            self::AA => 'Account Admin',
            self::NAO => 'Non Academic Officer',
            self::AO => 'Academic Officer',
            self::S => 'Student',
        };
    }
}
