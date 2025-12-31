<?php

declare(strict_types=1);

namespace App\Domains\Auth\Listeners;

use App\Domains\Auth\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Send Welcome Notification Listener
 * Sends welcome message to newly registered users
 */
class SendWelcomeNotification implements ShouldQueue
{
    /**
     * Handle the event
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        // Log the registration for now
        // TODO: Implement actual notification (SMS or Email)
        Log::info('New user registered', [
            'user_id' => $user->id,
            'username' => $user->username,
            'mobile' => $user->mobile,
        ]);

        // Example: Send welcome SMS
        // app(SmsIrClient::class)->sendUltraFastOtp(
        //     $user->mobile,
        //     config('services.sms_ir.welcome_template_id'),
        //     ['name' => $user->name ?? 'کاربر']
        // );
    }
}
