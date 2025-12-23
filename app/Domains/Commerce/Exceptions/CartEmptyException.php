<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Exceptions;

use Exception;

/**
 * Exception thrown when trying to perform an operation on an empty cart
 */
class CartEmptyException extends Exception
{
    public function __construct(string $message = 'سبد خرید خالی است.')
    {
        parent::__construct($message, 400);
    }
}

