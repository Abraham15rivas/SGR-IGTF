<?php

namespace App\Listeners;

use App\Models\Log;
use App\Events\LogEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LogEvent  $event
     * @return void
     */
    public function handle(LogEvent $event)
    {
        Log::create([
            'ip_address'    => $event->ip_address,
            'description'   => $event->description,
            'user_id'       => $event->user_id,
        ]);
    }
}
