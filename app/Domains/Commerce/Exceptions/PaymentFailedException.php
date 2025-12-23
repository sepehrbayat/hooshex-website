<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Exceptions;

use Exception;

/**
 * Exception thrown when a payment operation fails
 */
class PaymentFailedException extends Exception
{
    public function __construct(string $message = 'پرداخت ناموفق بود.')
    {
        parent::__construct($message, 402);
    }
}

