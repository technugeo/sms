<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CoreEnum: string implements HasLabel, HasColor
{
    case TRUE = 'Core';
    case FALSE = 'ELECTIVE';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TRUE => Color::Green,
            self::FALSE => Color::Red,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TRUE => 'Core',
            self::FALSE => 'Elective',
        };
    }
}
