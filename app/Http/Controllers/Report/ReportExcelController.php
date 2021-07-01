<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\TransactionsExport;
use App\Models\{
    Transaction,
    Holiday
};
use Illuminate\Support\Facades\{
    DB,
    Storage
};
use App\Traits\{
    LogTrait,
    RouteFileTrait
};

class ReportExcelController extends Controller
{
    use LogTrait, RouteFileTrait;

    public function indexTransaction() {
        $title = 'Reportes de transacciones';
        return view('report.show_transaction_excel', compact('title'));
    }
    
    public function showTransactionExcel(Request $request) {
        $transactions   = 0;
        $date           = Carbon::parse($request->date)->format('Y-m-d');
        $holiday        = $this->verifyHoliday($date);

        if($holiday != null) {
            array_push($holiday, 'storage');
            $transactions   = $holiday;
            $done           = true;
            $message        = "La fecha $date, solicitada es feriada, descargue el reporte vació si desea";
        } else {
            // Validar si existen transacciones con la fecha solicitada
            $verify_status  = Transaction::where('dateTrans', $date)->take(5)->get('fk_Estat');

            if(empty($verify_status->first())) {
                $done       = false;
                $message    = 'No hay registros de esta fecha, por favor escoga otra e intente nuevamente';
            } else {
                $filtered  = $verify_status->filter(function($status) {
                    return $status->fk_Estat > 0;
                })->first();
    
                if(empty($filtered)) {
                    $preliminaries  = DB::select("SELECT trans_reporte('$date')");
    
                    if($preliminaries) {
                        $temporary      = $this->organizeData($preliminaries);
                        $transactions   = $this->filterTemporary($temporary);
                        
                        $done       = true;
                        $message    = 'Datos filtrados correctamente';
                    } else {
                        $done       = false;
                        $message    = 'Algo salió mal contactar al administrador del sistema';
                    }         
                } else {
                    // Buscar el archivo en el Storage
                    $route = $this->routeForExcel($date);
    
                    if (Storage::disk('public')->exists("$route[0]")) {
                        array_push($route, 'storage');
                        $transactions   = $route;
                        $done           = true;
                        $message        = 'Reporte encontrado correctamente, descarguelo si desea';
                    } else {
                        $done       = false;
                        $message    = 'No hay reportes con esta fecha, por favor escoga otra e intente nuevamente';
                    }
                }
            }
        }

        // Log de eventos del usuario
        $this->registerLog("Consulta de datos de transacciones para reporte en excel fecha: $date");

        return response()->json([
            'success'       => $done,
            'message'       => $message,
            'transactions'  => $transactions
        ], 200);
    }

    private function organizeData($all_transactions) {
        $transactions = collect();
        foreach($all_transactions as $transaction) {
            $long_string = strlen(trim($transaction->trans_reporte));
            $data        = substr(trim($transaction->trans_reporte), 1, $long_string -2);
            $data        = explode(',', $data);
            $count       = count($data);

            if($count == 14) {
                $data[4] = "$data[4],$data[5]";
                array_splice($data, 5, 1);
            } elseif($count == 15) {
                $data[4] = "$data[4],$data[5],$data[6]";
                array_splice($data, 5, 2);
            }

            $transactions->push([
                'id'            => trim($data[0]),
                'referencia'    => trim($data[1]),
                'rif'           => trim($data[2]),
                'cuenta'        => trim($data[3]),
                'cliente'       => trim($data[4]),
                'fecha'         => trim($data[5]),
                'hora'          => trim($data[6]),
                'tansaccion'    => trim($data[7]),
                'instrumento'   => trim($data[8]),
                'endoso'        => trim($data[9]),
                'concepto'      => trim($data[10]),
                'monto'         => trim($data[11]),
                'impuesto'      => trim($data[12]),
            ]);
        }
        return $transactions;
    }

    private function filterTemporary($temporary) {
        $gruopTax   = $temporary->where('instrumento', 'IMPUESTO');
        $gruopTrans = $temporary->where('instrumento', '!=', 'IMPUESTO');//->where('instrumento', '!=', 'Indefini');
        $filters    = collect();

        foreach($gruopTrans as $trans) {
            $impuesto = $trans['impuesto'];
            $tax      = $gruopTax->where('monto', $impuesto);

            if($tax->first() != null) {
                $trans['estatus'] = 'OK';
                $filters->push([
                    'operacion' => $trans,
                    'impuesto'  => $tax
                ]);
            } else {
                $trans['estatus'] = 'ERROR';
                $filters->push([
                    'operacion' => $trans,
                    'impuesto'  => $tax
                ]); 
            }
        }

        return $filters;
    }

    public function confirmationExcel(Request $request) {
        if($request->ajax()) {
            // Confirmar transacciones
            $all_data    = $request->all();
            $definitives = collect($all_data['definitives']);
            $date_req    = $definitives[0]['fecha'];
            $json        = $definitives->toJson();
            $response    = Transaction::trans_confirma($date_req, $json);
            
            if($response) {
                // Pasar datos a Excel y guardar
                $route = $this->exportExcel($all_data, $date_req);

                return response()->json([
                    'success'       => true,
                    'message'       => 'Confirmación emitida correctamente!',
                    'route_file'    => $route[0],
                    'name_file'     => $route[1]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Algo salio mal en la base de datos!',
                ], 500);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Algo salio mal!',
            ], 400);
        }
    }

    private function exportExcel($all_data, $date_req) {
        $route = $this->routeForExcel($date_req);
        $excel = (new TransactionsExport($all_data))->store("$route[0]", 'public');

        if($excel) {
            return $route;
        } else {
            return response()->json('Fallo al escribir el reporte en el storage');
        }
    }

    private function verifyHoliday($date) {
        $holiday    = Holiday::where('date', $date)->get();
        $route      = null;
        
        if(count($holiday) > 0) {
            $data = collect([
                "definitives" => [],
                "operactions" => [],
                "temporalies" => []
            ])->toArray();
            
            $route = $this->exportExcel($data, $date);
        }

        return $route;
    }
}
