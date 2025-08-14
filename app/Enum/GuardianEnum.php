<?php

namespace App\Enum;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum GuardianEnum: string implements HasLabel
{
    case Father   = 'Father';
    case Mother = 'Mother';
    case Guardian = 'Guardian';
    case Spouse = 'Spouse';
    case Sibling = 'Sibling';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::Father  => 'Father',
            self::Mother => 'Mother',
            self::Guardian => 'Guardian',
            self::Spouse => 'Spouse',
            self::Sibling => 'Sibling',
        };
    }
}
