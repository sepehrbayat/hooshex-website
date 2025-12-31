<?php

namespace Tests\Feature\Payment;

use App\Domains\Auth\Models\User;
use App\Domains\Commerce\Actions\CompletePaymentAction;
use App\Domains\Commerce\Events\OrderPaid;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Models\OrderItem;
use App\Domains\Courses\Actions\EnrollUserAction;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Teacher;
use App\Enums\CourseStatus;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete payment action updates order status.
     */
    public function test_complete_payment_updates_order_status(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::Pending,
            'total_amount' => 100000,
        ]);

        $action = app(CompletePaymentAction::class);
        $updatedOrder = $action->handle($order, 'TX123456');

        $this->assertEquals(OrderStatus::Paid, $updatedOrder->status);
        $this->assertEquals('TX123456', $updatedOrder->transaction_id);
    }

    /**
     * Test complete payment dispatches OrderPaid event.
     */
    public function test_complete_payment_dispatches_event(): void
    {
        Event::fake([OrderPaid::class]);

        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::Pending,
        ]);

        $action = app(CompletePaymentAction::class);
        $action->handle($order, 'TX123456');

        Event::assertDispatched(OrderPaid::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }

    /**
     * Test complete payment provisions course enrollments.
     */
    public function test_complete_payment_provisions_enrollments(): void
    {
        $user = User::factory()->create();
        $teacherUser = User::factory()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->user_id,
            'status' => CourseStatus::Published,
            'price' => 100000,
        ]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::Pending,
            'total_amount' => 100000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'orderable_type' => Course::class,
            'orderable_id' => $course->id,
            'price' => 100000,
            'quantity' => 1,
        ]);

        $action = app(CompletePaymentAction::class);
        $action->handle($order, 'TX123456');

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    /**
     * Test payment callback returns success view on valid payment.
     */
    public function test_payment_status_page_requires_authentication(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::Paid,
        ]);

        // Without authentication
        $response = $this->get("/payment/status/{$order->id}");
        $response->assertRedirect('/login');

        // With authentication
        $response = $this->actingAs($user)->get("/payment/status/{$order->id}");
        $response->assertStatus(200);
    }

    /**
     * Test user cannot view another user's order.
     */
    public function test_user_cannot_view_other_users_order(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $order = Order::factory()->create([
            'user_id' => $user1->id,
            'status' => OrderStatus::Paid,
        ]);

        $response = $this->actingAs($user2)->get("/payment/status/{$order->id}");
        $response->assertRedirect('/login');
    }
}
