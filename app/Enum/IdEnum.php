<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IdEnum: string implements HasLabel, HasColor
{

    case BIRTH_CERTIFICATE = 'Birth Certificate';
    case OLD_IDENTITY_CARD = 'Old Identity Card';
    case NEW_IDENTITY_CARD = 'New Identity Card';
    case PASSPORT_NUMBER = 'Passport Number';
    case ARMED_FORCES_NUMBER = 'Armed Forces Number';
    case POLICE_FORCE_NUMBER = 'Police Force Number';
    case VISA = 'Visa';
    case MALAYSIAN_EXAMINATIONS_BOARD_NUMBER = 'Malaysian Examinations Board Number';
    case OTHERS = 'Others';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BIRTH_CERTIFICATE, self::MALAYSIAN_EXAMINATIONS_BOARD_NUMBER, self::OLD_IDENTITY_CARD, self::NEW_IDENTITY_CARD => Color::Blue,
            self::PASSPORT_NUMBER, self::VISA => Color::Red,
            self::ARMED_FORCES_NUMBER => Color::Green,
            self::POLICE_FORCE_NUMBER => Color::Teal,
            self::OTHERS => Color::Gray,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BIRTH_CERTIFICATE => 'Birth Certificate',
            self::OLD_IDENTITY_CARD => 'Old Identity Card',
            self::NEW_IDENTITY_CARD => 'New Identity Card',
            self::PASSPORT_NUMBER => 'Passport Number',
            self::ARMED_FORCES_NUMBER => 'Armed Forces Number',
            self::POLICE_FORCE_NUMBER => 'Police Force Number',
            self::VISA => 'Visa',
            self::MALAYSIAN_EXAMINATIONS_BOARD_NUMBER => 'Malaysian Examinations Board Number',
            self::OTHERS => 'Others',
        };
    }
}
