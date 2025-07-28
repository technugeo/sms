<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AcademicEnum: string implements HasLabel, HasColor
{

    case REGISTERED = 'Registered';
    case ENROLLED   = 'Enrolled';
    case DEFERRED   = 'Deferred';
    case WITHDRAWN  = 'Withdrawn';
    case SUSPENDED  = 'Suspended';
    case TERMINATED = 'Terminated';
    case GRADUATED  = 'Graduated';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::REGISTERED => Color::Gray,
            self::ENROLLED   => Color::Blue,
            self::DEFERRED   => Color::Yellow,
            self::WITHDRAWN,
            self::SUSPENDED,
            self::TERMINATED => Color::Red,
            self::GRADUATED  => Color::Green,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::REGISTERED => 'Registered',
            self::ENROLLED   => 'Enrolled',
            self::DEFERRED   => 'Deferred',
            self::WITHDRAWN  => 'Withdrawn',
            self::SUSPENDED  => 'Suspended',
            self::TERMINATED => 'Terminated',
            self::GRADUATED  => 'Graduated',
        };
    }
}
