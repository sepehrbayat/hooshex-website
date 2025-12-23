<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Actions;

use App\Domains\Commerce\Services\Cart;
use App\Domains\Courses\Models\Course;
use Illuminate\Support\Facades\Session;

/**
 * Action to add a product (course) to cart
 * Following Single Responsibility Principle
 */
class AddProductToCartAction
{
    public function __construct(
        private readonly Cart $cart
    ) {
    }

    /**
     * Add a course to cart
     *
     * @param Course $course The course to add
     * @param int $quantity Quantity (default: 1)
     * @return void
     */
    public function handle(Course $course, int $quantity = 1): void
    {
        $this->cart->addCourse($course, $quantity);
    }
}

