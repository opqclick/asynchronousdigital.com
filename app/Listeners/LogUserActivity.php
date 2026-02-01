<?php

namespace App\Listeners;

use App\Models\UserActivity;
use Illuminate\Auth\Events\Login;

class LogUserActivity
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        UserActivity::create([
            'user_id' => $event->user->id,
            'action' => 'login',
            'model' => null,
            'model_id' => null,
            'description' => 'User logged into the system',
            'changes' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
