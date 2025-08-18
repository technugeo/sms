<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum EducationLevelEnum: string implements HasLabel
{
    case CERTIFICATE_OF_ATTENDANCE = 'Certificate of Attendance / Short Courses';
    case TIADA_TAHAP = 'Tiada Tahap';
    case DOCTORAL = 'Doctoral';
    case MASTERS = 'Masters';
    case POSTGRADUATE_DIPLOMA = 'Postgraduate Diploma';
    case POSTGRADUATE_CERTIFICATE = 'Postgraduate Certificate';
    case MASTERS_EQUIVALENT_BACHELORS = 'Masters (Equivalent to Bachelors)';
    case BACHELORS = 'Bachelors';
    case GRADUATE_DIPLOMA = 'Graduate Diploma';
    case GRADUATE_CERTIFICATE = 'Graduate Certificate';
    case ADVANCED_DIPLOMA = 'Advanced Diploma';
    case DIPLOMA = 'Diploma';
    case EXECUTIVE_DIPLOMA = 'Executive Diploma';
    case CERTIFICATE = 'Certificate';
    case FOUNDATION = 'Foundation';
    case PROFESSIONAL_CERTIFICATE = 'Professional Certificate';
    case SPM = 'SPM';
    case STPM = 'STPM';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CERTIFICATE_OF_ATTENDANCE => 'Certificate of Attendance / Short Courses',
            self::TIADA_TAHAP => 'Tiada Tahap',
            self::DOCTORAL => 'Doctoral',
            self::MASTERS => 'Masters',
            self::POSTGRADUATE_DIPLOMA => 'Postgraduate Diploma',
            self::POSTGRADUATE_CERTIFICATE => 'Postgraduate Certificate',
            self::MASTERS_EQUIVALENT_BACHELORS => 'Masters (Equivalent to Bachelors)',
            self::BACHELORS => 'Bachelors',
            self::GRADUATE_DIPLOMA => 'Graduate Diploma',
            self::GRADUATE_CERTIFICATE => 'Graduate Certificate',
            self::ADVANCED_DIPLOMA => 'Advanced Diploma',
            self::DIPLOMA => 'Diploma',
            self::EXECUTIVE_DIPLOMA => 'Executive Diploma',
            self::CERTIFICATE => 'Certificate',
            self::FOUNDATION => 'Foundation',
            self::PROFESSIONAL_CERTIFICATE => 'Professional Certificate',
            self::SPM => 'SPM',
            self::STPM => 'STPM',
        };
    }
}
