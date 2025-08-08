<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ReligionEnum: string implements HasLabel, HasColor
{
    case ISLAM   = 'Islam/Muslim';
    case BUDDHA = 'Buddha';
    case HINDU = 'Hindu';
    case CHRISTIAN = 'Christian';
    case OTHER  = 'Other';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ISLAM => Color::Blue,
            self::BUDDHA => Color::Green,
            self::HINDU => Color::Yellow,
            self::CHRISTIAN => Color::Red,
            self::OTHER => Color::Gray,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ISLAM  => 'Islam / Muslim',
            self::BUDDHA => 'Buddha',
            self::HINDU => 'Hindu',
            self::CHRISTIAN => 'Christian',
            self::OTHER  => 'Other',
        };
    }
}
