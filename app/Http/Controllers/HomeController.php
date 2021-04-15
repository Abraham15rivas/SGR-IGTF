<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ItfTrans02, Transaction};
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\facades\DB;
use App\Exports\TransactionsExport;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = 'Vista principal';
        return view('home', compact('title'));
    }

    public function indexTransaction() {        
        $title = 'Reportes de transacciones';
        return view('report.show_transaction_excel', compact('title'));
    }

    public function showTransactionExcel(Request $request) {
        // $date = $request->date;
        $date = '2017/12/29';
        if(!Cache::has('preliminaries')) {
            // $response_status = DB::select("SELECT trans_estatus('$date', 0)");
            $response_organize = DB::select("SELECT trans_organiza('29/12/2017')");
            if($response_organize[0]->trans_organiza) {
                $preliminaries = DB::select("SELECT trans_reporte('29/12/2017')");
                Cache::add('preliminaries', $preliminaries);
            }
        } else {
            $preliminaries = Cache::get('preliminaries');
        }

        // Cache::flush();

        $temporary = collect();
        foreach($preliminaries as $preliminary) {
            $long_string = strlen(trim($preliminary->trans_reporte));
            $data        = substr(trim($preliminary->trans_reporte), 1, $long_string -2);
            $data        = explode(',', $data);
            $count       = count($data);

            if($count == 14) {
                $data[4] = "$data[4],$data[5]";
                array_splice($data, 5, 1);
            } elseif($count == 15) {
                $data[4] = "$data[4],$data[5],$data[6]";
                array_splice($data, 5, 2);
            }

            $temporary->push([
                'id'            => $data[0],
                'referencia'    => $data[1],
                'rif'           => $data[2],
                'cuenta'        => $data[3],
                'cliente'       => $data[4],
                'fecha'         => $data[5],
                'hora'          => $data[6],
                'tansaccion'    => $data[7],
                'instrumento'   => $data[8],
                'endoso'        => $data[9],
                'concepto'      => $data[10],
                'monto'         => $data[11],
                'impuesto'      => $data[12],
            ]);
        }
        $transactions = $this->filterTemporary($temporary);
        return response()->json([
            'success'       => true,
            'message'       => 'Datos filtrados correctamente!',
            'transactions'  => $transactions
        ], 200);
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
            // Pasar datos a Excel y guardar
            $part_date   = explode('-', $date_req);
            $full_date   = str_replace('-', '', $date_req);
            $date        = Carbon::parse($date_req);
            $month       = ucfirst($date->translatedFormat("F"));
            $name        = 'IGTF_' . substr($date_req, 0, 6) . substr($date_req, 8, 10);
            $route       = "IGTF/REPORTE_EXCEL/AÑO$part_date[2]/$month$part_date[2]/$full_date/$name.xls";
            $excel       = (new TransactionsExport($all_data))->store("$route", 'public');

            // Esta funcion de base de datos no sirve (revisar)
            // $response = DB::select("SELECT trans_confirma('$date', '$json')");

            if($response) {
                return response()->json([
                    'success'       => true,
                    'message'       => 'Confirmación emitida correctamente!',
                    'route_file'    => $route,
                    'name_file'     => $name
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

    public function showXML(Request $request) {
        $date       = '2017/12/29';
        $title      = 'Reportes XML: ' . $date;
        $details    = 'hola';
        return view('report.show_file_xml', compact('details', 'title'));
    }

    private function transactionTotals($transactions) {
        $amountTransactions  = count($transactions);
        $conceptAmountGlobal = 0;
        $conceptTaxGlobal    = 0;
        $conceptTotales      = collect();

        foreach($transactions as $transaction) {
            $conceptAmountGlobal += $transaction['monto'];
            $conceptTaxGlobal    += $transaction['impuesto'];
            $concept             = trim($transaction['concepto']);
            if($concept != 'IMPUESTO') {
                $query = $conceptTotales->where('monto_$concept');
                if($query->first() != null) {
                    $query["cantidad_$concept"] += 1;
                    $query["monto_$concept"]    += $transaction['monto'];
                    $query["impuesto_$concept"] += $transaction['impuesto'];
                } else {
                    $conceptTotales->push([
                        "cantidad_$concept"  => +1,
                        "monto_$concept"     => $transaction['monto'],
                        "impuesto_$concept"  => $transaction['impuesto']
                    ]);
                }
            }
        
        }
    }
}