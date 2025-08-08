<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusEnum: string implements HasLabel, HasColor
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case DEACTIVATE = 'Deactivate';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => Color::Green,
            self::INACTIVE => Color::Yellow,
            self::DEACTIVATE => Color::Red,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::DEACTIVATE => 'Deactivate',
        };
    }
}
