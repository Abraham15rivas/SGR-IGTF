<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\LogTrait;

class LogSuccessfulLogout
{
    use LogTrait;

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
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        // Vaciar el sesion_id del usuario que esta cerrando su sesión en el sistema
        auth()->user()->session_id = null;
        auth()->user()->save();

        // Log de eventos del usuario
        $this->registerLog('Cerró sesión en el sistema');
    }
}
