<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StaffTypeEnum: string implements HasLabel, HasColor
{
    case FullTime = 'Full-Time';
    case Contract = 'Contract';
    case Intern = 'Intern';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FullTime => Color::Green,
            self::Contract => Color::Yellow,
            self::Intern => Color::Red,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FullTime => 'Full-Time',
            self::Contract => 'Contract',
            self::Intern => 'Intern',
        };
    }
}
