<?php

namespace App\Enum;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IntakeEnum: string implements HasLabel
{
    case JANUARY   = 'January';
    case FEBRUARY  = 'February';
    case MARCH     = 'March';
    case APRIL     = 'April';
    case MAY       = 'May';
    case JUNE      = 'June';
    case JULY      = 'July';
    case AUGUST    = 'August';
    case SEPTEMBER = 'September';
    case OCTOBER   = 'October';
    case NOVEMBER  = 'November';
    case DECEMBER  = 'December';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
