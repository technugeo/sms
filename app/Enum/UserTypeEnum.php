<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserTypeEnum: string implements HasLabel, HasColor
{
    case EMPLOYEE = 'Employee';
    case STUDENT = 'Student';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::EMPLOYEE => Color::Blue,
            self::STUDENT => Color::Green
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EMPLOYEE => 'Employee',
            self::STUDENT => 'Student',
        };
    }
}
