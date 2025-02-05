<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Failed;
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
    public function handle(object $event): void
    {
        $credentials = $event->credentials;
        Log::warning('Intento de inicio de sesión fallido para el usuario con email: ' . $credentials['email']);
    }
}
