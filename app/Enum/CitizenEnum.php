<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CitizenEnum: string implements HasLabel, HasColor
{
    case BUMIPUTERA = 'Bumiputera';
    case NON_BUMIPUTERA = 'Non Bumiputera';
    case FOREIGN = 'Foreign';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BUMIPUTERA => Color::Blue,
            self::NON_BUMIPUTERA => Color::Red,
            self::FOREIGN => Color::Gray,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BUMIPUTERA => 'Bumiputera',
            self::NON_BUMIPUTERA => 'Non Bumiputera',
            self::FOREIGN => 'Foreign',
        };
    }
}
