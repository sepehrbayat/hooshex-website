<?php

declare(strict_types=1);

namespace App\Enums;

enum ContractType: string
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Contract = 'contract';
    case Internship = 'internship';

    public function label(): string
    {
        return match($this) {
            self::FullTime => 'تمام وقت',
            self::PartTime => 'پاره وقت',
            self::Contract => 'قراردادی',
            self::Internship => 'کارآموزی',
        };
    }

    /**
     * Map to Schema.org employmentType values
     */
    public function toSchemaOrg(): string
    {
        return match($this) {
            self::FullTime => 'FULL_TIME',
            self::PartTime => 'PART_TIME',
            self::Contract => 'CONTRACTOR',
            self::Internship => 'INTERN',
        };
    }
}

