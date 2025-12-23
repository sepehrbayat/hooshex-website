<?php

declare(strict_types=1);

namespace App\Enums;

enum PricingType: string
{
    case Free = 'free';
    case Freemium = 'freemium';
    case Paid = 'paid';
    case FreeTrial = 'free_trial';
    case Contact = 'contact';
}

