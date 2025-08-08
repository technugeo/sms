<?php

namespace App\Enum;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IntakeEnum: string implements HasLabel
{
    case JANUARY   = 'January';
    case APRIL = 'April';
    case AUGUST = 'August';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::JANUARY  => 'January',
            self::APRIL => 'April',
            self::AUGUST => 'August',
        };
    }
}
