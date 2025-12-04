<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogFailedLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $ip = request()->ip();
        $email = $event->credentials['email'] ?? 'unknown';
        
        Log::warning("Failed login attempt from {$ip} for email: {$email}", [
            'ip' => $ip,
            'email' => $email,
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }
}
