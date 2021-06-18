<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\LogTrait;
use Illuminate\Support\Facades\DB;

class AnalystController extends Controller
{
    use LogTrait;

    public function changeStatusTransaction($date, $value) {
        $response_status = DB::select("SELECT trans_estatus('$date', '$value')");
        
        // Log de eventos del usuario
        $this->registerLog("Cambio de status de las transacciones de la fecha: $date a el status de valor: $value");

        return $response_status;
    }
}
