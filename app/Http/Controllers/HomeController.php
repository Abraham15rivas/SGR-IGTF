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

        // $response_status = DB::select("SELECT trans_estatus('$date', 0)");

        if(!Cache::has('preliminaries')) {
            $response_organize = DB::select("SELECT trans_organiza('29/12/2017')");
            if($response_organize[0]->trans_organiza) {
                $preliminaries = DB::select("SELECT trans_reporte('29/12/2017')");
                Cache::add('preliminaries', $preliminaries);
            }
        } else {
            $preliminaries = Cache::get('preliminaries');
        }

        // Cache::flush();

        $temporary      = $this->organizeData($preliminaries);
        $transactions   = $this->filterTemporary($temporary);

        return response()->json([
            'success'       => true,
            'message'       => 'Datos filtrados correctamente!',
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

    public function indexXML() {
        $title      = 'Reportes XML';
        $details    = 'hola';
        return view('report.show_file_xml', compact('title'));
    }

    public function showXML(Request $request) {
        // $date = $request->date;
        $date = Carbon::parse('2017/12/29')->format('d-m-Y');
        if(!Cache::has('transactions')) {
            $transactions = DB::select("SELECT trans_banco_detalle('$date')");
            // Esta funcion de base de datos no sirve (revisar)
            // $res = DB::select("SELECT trans_banco('$date')");
            if($transactions[0]->trans_banco_detalle) {
                Cache::add('transactions', $transactions);
            }
        } else {
            $transactions = Cache::get('transactions');
        }

        // Cache::flush();

        // Organizar y crear archivo XML ITFBancoDetalle
        $transactions    = $this->organizeDataXML($transactions);
        $ITFBancoDetalle = $this->createITFBancoDetalle($transactions, $date);

        // Organizar para los nombres y rutas de los archivos
        $part_date  = explode('-', $date);
        $full_date  = str_replace('-', '', $date);
        $date_new   = Carbon::parse($date);
        $month      = ucfirst($date_new->translatedFormat("F"));
        $week       = $date_new->weekday();

        // Ruta ITFBancoDetalle y almacenar archivos en el servidor
        $name  = 'ITF_' . substr($date, 0, 6) . substr($date, 8, 10);
        $route = "IGTF/AÑO$part_date[2]/XML/$month$part_date[2]/XML_DETALLADO_DIARIO/SEMANA$week/$full_date/$name.xml";
        $disk  = Storage::disk('public')->put("$route", $ITFBancoDetalle);

        // Calcular hash del archivo
        if($disk) {
            $hash = sha1_file(public_path("storage/$route"));
        }

        // Crear archivo XML ITFBanco y ITFBancoConfirmacion
        $ITFBanco             = $this->createITFBanco($transactions, $date, $part_date, $hash);
        $ITFBancoConfirmacion = $this->createITFBancoConfirmacion($transactions, $date, $hash);

        // Ruta ITFBanco
        $route_general = "IGTF/AÑO$part_date[2]/XML/$month$part_date[2]/XML_RESUMEN_DIARIO/Semana_$week/$full_date";
        $name2         = 'ITF_Banco';
        $route2        = "$route_general/$name2.xml";
        $disk2         = Storage::disk('public')->put("$route2", $ITFBanco);

        // Ruta ITFBancoConfirmacion
        $name3  = 'ITFBancoConfirmacion';
        $route3 = "$route_general/confirmacion/$name3.xml";
        $disk3  = Storage::disk('public')->put("$route3", $ITFBancoConfirmacion);

        if($disk && $disk2 && $disk3) {
            return response()->json([
                'success'       => true,
                'message'       => 'Datos filtrados correctamente!',
                'files'         => [
                    'ITFBancoDetalle' => [
                        'file'  => $ITFBancoDetalle,
                        'route' => $route,
                        'name'  => $name
                    ],
                    'ITFBanco' => [
                        'file'  => $ITFBanco,
                        'route' => $route2,
                        'name'  => $name2
                    ],
                    'ITFBancoConfirmacion' => [
                        'file'  => $ITFBancoConfirmacion,
                        'route' => $route3,
                        'name'  => $name3
                    ]
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Algo salio mal en la base de datos!',
            ], 500);
        }
    }

    private function organizeDataXML($all_transactions) {
        $transactions = collect();
        foreach($all_transactions as $transaction) {
            $long_string = strlen(trim($transaction->trans_banco_detalle));
            $data        = substr(trim($transaction->trans_banco_detalle), 1, $long_string -2);
            $data        = explode(',', $data);

            $transactions->push([
                'exento'              => trim($data[0]),
                'numero'              => trim($data[1]),
                'rifcliente'          => trim($data[2]),
                'codigocuentacliente' => trim($data[3]),
                'fechaoperacion'      => trim($data[4]),
                'horaoperacion'       => trim($data[5]),
                'instrumentopago'     => trim($data[6]),
                'cantidadendosos'     => trim($data[7]),
                'concepto'            => trim($data[8]),
                'montooperacion'      => trim($data[9]),
                'montoimpuesto'       => trim($data[10]),
            ]);
        }
        return $transactions;
    }

    private function createITFBancoDetalle($transactions, $date) {
        $doc = new \DOMDocument('1.0', 'UTF-8');

        $root = $doc->createElement("ITFBancoDetalle");
        $root = $doc->appendChild($root);
        $root->setAttribute("RIF", "G200057955");
        $root->setAttribute("CodigoInstitucionFinanciera", "0166");
        $root->setAttribute("FechaInicio", "$date");
        $root->setAttribute("FechaFin", "$date");

        foreach($transactions as $trans) {
            $transaction = $doc->createElement("Transaccion");
            $transaction = $root->appendChild($transaction);
            $transaction->setAttribute("Numero", "$trans[numero]");
            $transaction->setAttribute("Exento", "$trans[exento]");
    
            $rif_client = $doc->createElement("RIFCliente");
            $rif_client = $transaction->appendChild($rif_client);
            $text_rif   = $doc->createTextNode("$trans[rifcliente]");
            $text_rif   = $rif_client->appendChild($text_rif);
    
            $code_bank = $doc->createElement("CodigoCuentaCliente");
            $code_bank = $transaction->appendChild($code_bank);
            $text_code = $doc->createTextNode("$trans[codigocuentacliente]");
            $text_code = $code_bank->appendChild($text_code);
    
            $date_operaction = $doc->createElement("FechaOperacion");
            $date_operaction = $transaction->appendChild($date_operaction);
            $text_date       = $doc->createTextNode("$trans[fechaoperacion]");
            $text_date       = $date_operaction->appendChild($text_date);
    
            $time_operaction = $doc->createElement("HoraOperacion");
            $time_operaction = $transaction->appendChild($time_operaction);
            $text_time       = $doc->createTextNode("$trans[horaoperacion]");
            $text_time       = $time_operaction->appendChild($text_time);
    
            $instrument      = $doc->createElement("InstrumentoPago");
            $instrument      = $transaction->appendChild($instrument);
            $text_instrument = $doc->createTextNode("$trans[instrumentopago]");
            $text_instrument = $instrument->appendChild($text_instrument);
    
            $endorsed      = $doc->createElement("CantidadEndosos");
            $endorsed      = $transaction->appendChild($endorsed);
            $text_endorsed = $doc->createTextNode("$trans[cantidadendosos]");
            $text_endorsed = $endorsed->appendChild($text_endorsed);
    
            $concept      = $doc->createElement("Concepto");
            $concept      = $transaction->appendChild($concept);
            $text_concept = $doc->createTextNode("$trans[concepto]");
            $text_concept = $concept->appendChild($text_concept);
    
            $amount      = $doc->createElement("MontoOperacion");
            $amount      = $transaction->appendChild($amount);
            $text_amount = $doc->createTextNode("$trans[montooperacion]");
            $text_amount = $amount->appendChild($text_amount);
    
            $tax      = $doc->createElement("MontoImpuesto");
            $tax      = $transaction->appendChild($tax);
            $text_tax = $doc->createTextNode("$trans[montoimpuesto]");
            $text_tax = $tax->appendChild($text_tax);
        }

        $doc->preserveWhiteSpace = false;
        $doc->formatOutput       = true;

        return $doc->saveXML();
    }

    private function createITFBanco($transactions, $date, $part_date, $hash) {
        $now              = Carbon::now();
        $transmition_date = $now->format('d-m-Y');
        $transmition_hour = $now->format('H:m:s');

        $doc = new \DOMDocument('1.0', 'UTF-8');

        $root = $doc->createElement("ITFBanco");
        $root = $doc->appendChild($root);
        $root->setAttribute("RIF", "G200057955");
        $root->setAttribute("CodigoInstitucionFinanciera", "0166");
        $root->setAttribute("Periodo", "$part_date[2]$part_date[1]");
        $root->setAttribute("FechaRecaudacion", "$date");
        $root->setAttribute("FechaTransmision", "$transmition_date");
        $root->setAttribute("HoraTransmision", "$transmition_hour");
        $root->setAttribute("TipoDeclaracion", "O");
        $root->setAttribute("Hash", "$hash");
        
        $transaction = $doc->createElement("Transaccion");
        $transaction = $root->appendChild($transaction);    

        $items = $this->transactionTotals($transactions);

        foreach($items as $key => $item_total) {
            $key += 1;
            
            $item = $doc->createElement("Item");
            $item = $transaction->appendChild($item);
            $item->setAttribute("id", "$key");

            $tax = $doc->createElement("GravadaEndosos");
            $tax = $item->appendChild($tax);
            $text_tax = $doc->createTextNode("$item_total[GravadaEndosos]");
            $text_tax = $tax->appendChild($text_tax);

            $tax = $doc->createElement("GravadaCantidad");
            $tax = $item->appendChild($tax);
            $text_tax = $doc->createTextNode("$item_total[GravadaCantidad]");
            $text_tax = $tax->appendChild($text_tax);

            $tax = $doc->createElement("GravadaBase");
            $tax = $item->appendChild($tax);
            $text_tax = $doc->createTextNode("$item_total[GravadaBase]");
            $text_tax = $tax->appendChild($text_tax);

            $tax = $doc->createElement("GravadaImpuesto");
            $tax = $item->appendChild($tax);
            $text_tax = $doc->createTextNode("$item_total[GravadaImpuesto]");
            $text_tax = $tax->appendChild($text_tax);

            $tax = $doc->createElement("NoGravadaCantidad");
            $tax = $item->appendChild($tax);
            $text_tax = $doc->createTextNode("$item_total[NoGravadaCantidad]");
            $text_tax = $tax->appendChild($text_tax);

            $tax = $doc->createElement("NoGravadaBase");
            $tax = $item->appendChild($tax);
            $text_tax = $doc->createTextNode("$item_total[NoGravadaBase]");
            $text_tax = $tax->appendChild($text_tax);
        }

        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        return $doc->saveXML();
    }

    private function transactionTotals($transactions) {
        $concept_totals = collect();
        $i = 1;

        while ($i <= 13):
            $concept_totals->push([
                "concept"           => $i < 10 ? "000$i" : "00$i",
                "GravadaEndosos"    => '0',
                "GravadaCantidad"   => '00',
                "GravadaBase"       => '0.00',
                "GravadaImpuesto"   => '0.00',
                "NoGravadaCantidad" => '0',
                "NoGravadaBase"     => '0.00'
            ]);
            $i++;
        endwhile;

        foreach($transactions as $transaction) {
            $concept = '0001';//trim($transaction['concepto']);
            if($concept != 'IMPUESTO') {                
                $concept_totals = $concept_totals->map(function($total) use($concept, $transaction) { 
                    if($total['concept'] == $concept) {
                        $total["GravadaEndosos"]    += 0;
                        $total["GravadaCantidad"]   += 1;
                        $total["GravadaBase"]       += $transaction['montooperacion'];
                        $total["GravadaImpuesto"]   += $transaction['montoimpuesto'];
                        $total["NoGravadaCantidad"] += 0;
                        $total["NoGravadaBase"]     += 0;
                    }
                    return $total;
                });
            }
        }

        return $concept_totals;
    }

    private function createITFBancoConfirmacion($transactions, $date, $hash) {
        $doc = new \DOMDocument('1.0', 'UTF-8');
      
        $root = $doc->createElement("ITFBancoDetalle");
        $root = $doc->appendChild($root);
        $root->setAttribute("Hash", "$hash");

        $numero_declaracion      = $doc->createElement("NumeroDeclaracion");
        $numero_declaracion      = $root->appendChild($numero_declaracion);
        $text_numero_declaracion = $doc->createTextNode("1990014453");
        $text_numero_declaracion = $numero_declaracion->appendChild($text_numero_declaracion);

        $rif      = $doc->createElement("RIF");
        $rif      = $root->appendChild($rif);
        $text_rif = $doc->createTextNode("G200057955");
        $text_rif = $rif->appendChild($text_rif);

        $codigo_banco      = $doc->createElement("CodigoBanco");
        $codigo_banco      = $root->appendChild($codigo_banco);
        $text_codigo_banco = $doc->createTextNode("0000");
        $text_codigo_banco = $codigo_banco->appendChild($text_codigo_banco);

        $doc->preserveWhiteSpace = false;
        $doc->formatOutput       = true;

        return $doc->saveXML();
    }    
}