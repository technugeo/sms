<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MarriageEnum: string implements HasLabel, HasColor
{
    case SINGLE = 'Single';
    case MARRIED = 'Married';
    case WIDOW_WIDOWER = 'Widow / Widower';
    case DIVORCED = 'Divorced';
    case SEPARATED = 'Separated';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SINGLE => Color::Blue,
            self::MARRIED => Color::Green,
            self::WIDOW_WIDOWER => Color::Yellow,
            self::DIVORCED => Color::Red,
            self::SEPARATED => Color::Gray,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SINGLE => 'Single',
            self::MARRIED => 'Married',
            self::WIDOW_WIDOWER => 'Widow / Widower',
            self::DIVORCED => 'Divorced',
            self::SEPARATED => 'Separated',
        };
    }
}
