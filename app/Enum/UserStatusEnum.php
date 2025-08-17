<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserStatusEnum: string implements HasLabel, HasColor
{
    case PROSPECT = 'Prospect';
    case REGISTERED = 'Registered';
    case PENDING_ACTIVATION = 'Pending Activation';
    case ACTIVATED = 'Activated';
    case SUSPENDED = 'Suspended';
    case DELETED = 'Deleted';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PROSPECT, self::PENDING_ACTIVATION => Color::Gray,
            self::REGISTERED => Color::Blue,
            self::ACTIVATED => Color::Green,
            self::SUSPENDED => Color::Red,
            self::DELETED => Color::Red,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROSPECT => 'Prospect',
            self::REGISTERED => 'Registered',
            self::PENDING_ACTIVATION => 'Pending Activation',
            self::ACTIVATED => 'Activated',
            self::SUSPENDED => 'Suspended',
            self::DELETED => 'Deleted',
        };
    }
}
