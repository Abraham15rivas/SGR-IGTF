<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogTrait;
use App\Models\{
    Log,
    User
};

class ManagerController extends Controller
{
    use LogTrait;

    public function index() {
        $title    = 'Gestión de calendario';
        // Reahcer esta funcion fue eliminada
        $response = DB::select("SELECT cons_fer_list('true')");
        $dates    = collect();

        if($response) {
            foreach($response as $date) {
                $dates->push(json_decode($date->cons_fer_list, true));
            }
        } else {
            $dates = null;
        }

        // Log de eventos del usuario
        $this->registerLog('Consulta del calendario');

        return view('manager.calendar', compact('title', 'dates'));
    }

    public function log() {
        $title = 'Tabla de actividades de los usuarios';
        $logs  = Log::all()->load('user', 'user.role')->toJson();

        // Log de eventos del usuario
        $this->registerLog('Consulta Tabla de actividades de los usuarios (log)');

        return view('manager.audit.user_activity', compact('title', 'logs'));
    }

    public function statistics() {
        $title = 'Estadisticas';
        $users = User::all(); 

        $collection = collect();

        $collection->push([
            'sessions_active'       => $users->where('session_id', '!=', null)->count(),
            'sessions_deactivate'   => $users->where('session_id', null)->count(),
            'user_enabled'          => $users->where('status', 1)->count(),
            'user_disabled'         => $users->where('status', 0)->count(),
            'user_totals'           => $users->count()
        ]);

        $collection->toJson();
        
        // Log de eventos del usuario
        $this->registerLog('Consulta de estadisticas');

        return view('manager.audit.statistics', compact('title', 'collection'));
    }
}
