<?php

namespace Tests\Unit\Actions\Commerce;

use App\Domains\Auth\Models\User;
use App\Domains\Commerce\Actions\CreateOrderFromCartAction;
use App\Domains\Commerce\Exceptions\CartEmptyException;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Services\Cart;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Teacher;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderFromCartActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateOrderFromCartAction $action;
    private Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cart = app(Cart::class);
        $this->action = new CreateOrderFromCartAction($this->cart);
    }

    /**
     * Test order creation from cart items.
     */
    public function test_creates_order_from_cart_items(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'price' => 100000,
        ]);

        // Add item to cart
        $this->cart->add($course->id, Course::class, 100000, 1);

        $order = $this->action->handle($user, 'zarinpal');

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals(100000, $order->total_amount);
        $this->assertEquals('zarinpal', $order->gateway);
        $this->assertCount(1, $order->items);
    }

    /**
     * Test order creation throws exception for empty cart.
     */
    public function test_throws_exception_for_empty_cart(): void
    {
        $user = User::factory()->create();

        $this->expectException(CartEmptyException::class);

        $this->action->handle($user, 'zarinpal');
    }

    /**
     * Test order calculates correct total for multiple items.
     */
    public function test_calculates_correct_total_for_multiple_items(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        
        $course1 = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'price' => 50000,
        ]);
        $course2 = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'price' => 75000,
        ]);

        $this->cart->add($course1->id, Course::class, 50000, 1);
        $this->cart->add($course2->id, Course::class, 75000, 1);

        $order = $this->action->handle($user, 'zarinpal');

        $this->assertEquals(125000, $order->total_amount);
        $this->assertCount(2, $order->items);
    }

    /**
     * Test order items are linked to correct orderable.
     */
    public function test_order_items_are_linked_to_orderable(): void
    {
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'price' => 100000,
        ]);

        $this->cart->add($course->id, Course::class, 100000, 1);

        $order = $this->action->handle($user, 'zarinpal');

        $orderItem = $order->items->first();
        $this->assertEquals(Course::class, $orderItem->orderable_type);
        $this->assertEquals($course->id, $orderItem->orderable_id);
        $this->assertEquals(100000, $orderItem->price);
    }
}
