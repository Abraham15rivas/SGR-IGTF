<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    DB,
    Validator
};
use Carbon\Carbon;
use App\Traits\LogTrait;
use App\Models\{
    Log,
    User,
    Holiday,
    Transaction
};

class ManagerController extends Controller
{
    use LogTrait;

    public function index() {
        $title  = 'Gestión de calendario';
        $dates  = Holiday::get();
        
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

        $data       = collect();
        $collection = collect();

        $data->push([
            'sessions_active'       => $users->where('session_id', '!=', null)->count(),
            'sessions_deactivate'   => $users->where('session_id', null)->count(),
            'user_enabled'          => $users->where('status', 1)->count(),
            'user_disabled'         => $users->where('status', 0)->count(),
            'user_totals'           => $users->count()
        ]);

        $data->toJson();

        // Cantidad de transacciones por meses
        $months = Transaction::transactionMonth();
        $months->toJson();

        $collection->push([
            'user'      => $data[0],
            'months'    => $months
        ]);
        
        // Log de eventos del usuario
        $this->registerLog('Consulta de estadisticas');

        return view('manager.audit.statistics', compact('title', 'collection'));
    }

    public function store(Request $request) {
        if($request->ajax()) {

            $validator = Validator::make($request->all(), [
                'title'         => ['required', 'string', 'max:255'],
                'description'   => ['required', 'string'],
                'status'        => ['required', 'boolean'],
                'date'          => ['required', 'date']
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success'   => false,
                    'errors'    => $validator->errors(),
                    'message'   => 'Revise los campos correspondientes ',
                ], 202);
            }

            $date = Holiday::create([
                'title'         => $request->title,
                'description'   => $request->description,
                'date'          => $request->date,
                'status'        => $request->status,
                'bank_id'       => 1
            ]);
            
            // Log de eventos del usuario
            $this->registerLog("Registro de fecha en el calendario: $request->date");
            
            return response()->json([
                'success'   => true,
                'message'   => 'Día feriado registrado correctamente!',
                'date'     =>  $date
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Algo salio mal!',
            ], 400);
        }
    }

    public function destroy(Request $request, Holiday $holiday) {
        if($request->ajax()) {
            $holiday->delete();
            // Log de eventos del usuario
            $this->registerLog("Fecha del calendario: $holiday->date eliminada");
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Algo salio mal!',
            ], 400);
        }
    }
}
