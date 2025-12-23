<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Exceptions;

use Exception;

/**
 * Exception thrown when an order is not found
 */
class OrderNotFoundException extends Exception
{
    public function __construct(string $message = 'سفارش یافت نشد.')
    {
        parent::__construct($message, 404);
    }
}

