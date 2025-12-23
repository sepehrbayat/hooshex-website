<?php

declare(strict_types=1);

namespace App\Domains\Courses\Exceptions;

use Exception;

/**
 * Exception thrown when a user tries to enroll in a course they are already enrolled in
 */
class AlreadyEnrolledException extends Exception
{
    public function __construct(string $message = 'کاربر قبلاً در این دوره ثبت‌نام کرده است.')
    {
        parent::__construct($message, 409);
    }
}

