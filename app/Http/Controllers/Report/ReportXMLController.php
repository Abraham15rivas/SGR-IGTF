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

class ReportXMLController extends Controller
{
    use LogTrait, RouteFileTrait;

    public function indexXML() {
        $title      = 'Reportes XML';
        return view('report.show_file_xml', compact('title'));
    }

    public function showXML(Request $request) {
        $date = Carbon::parse("$request->date")->format('Y-m-d');
        
        // Definir variables
        $ITFBancoDetalle        = 0;
        $ITFBanco               = 0;
        $ITFBancoConfirmacion   = 0;
        
        // Generar rutas
        $route  = $this->routeITFBancoDetalle($request->date);
        $route2 = $this->routeITFBanco($request->date);
        $route3 = $this->routeITFBancoConfirmacion($request->date);
        
        // Validar si la fecha es feriada
        $holiday = $this->verifyHoliday($date, [$route[0], $route2[0], $route3[0]]);

        if($holiday != null) {
            $ITFBancoDetalle        = $holiday[0];
            $ITFBanco               = $holiday[1];
            $ITFBancoConfirmacion   = $holiday[2];

            $done       = true;
            $message    = 'Reportes generados en blanco, la fecha escogida es feriada, si desea puede descargarlos';
        } else {
            // Consultar si los archivos ya existen en el servidor
            if(!empty($route) && !empty($route2) && !empty($route3)) {
                if(
                    Storage::disk('public')->exists("$route[0]") &&
                    Storage::disk('public')->exists("$route2[0]") &&
                    Storage::disk('public')->exists("$route3[0]")
                ) {
                    $ITFBancoDetalle        = Storage::disk('public')->get("$route[0]");
                    $ITFBanco               = Storage::disk('public')->get("$route2[0]");
                    $ITFBancoConfirmacion   = Storage::disk('public')->get("$route3[0]");

                    $done       = true;
                    $message    = 'Los reportes ya existen';
                } else {
                    // Buscar las trasacciones con la fecha enviada desde el client
                    $transactions = DB::select("SELECT trans_banco_detalle('$date')");
                    
                    if(empty($transactions)) {
                        $done       = false;
                        $message    = 'No hay registros de esta fecha, por favor escoga otra e intente nuevamente';
                    } else {
                        // Organizar y crear archivo XML ITFBancoDetalle
                        $transactions   = $this->organizeDataXML($transactions);
                        $export         = $this->exportITFBancoDetalle($transactions, $date, $route[0]);
                        
                        $ITFBancoDetalle    = $export[0];
                        $disk               = $export[1];
                        $hash               = $export[2];

                        if($hash) {
                            $export2 = $this->exportXML($transactions, $date, $hash, $route2[0], $route3[0]);

                            $ITFBanco               = $export2[0][0];
                            $disk2                  = $export2[0][1];
                            $ITFBancoConfirmacion   = $export2[1][0];
                            $disk3                  = $export2[1][1];

                            if($disk2 && $disk3) {
                                $done       = true;
                                $message    = 'Los reportes fueron creados y almacenados satisfactoriamente';
                            }
                        }
                    }
                }
            } 
        }

        // Log de eventos del usuario
        $this->registerLog("Consulta de datos de transacciones para reportes xml (ITFBancoDetalle, ITFBanco y ITFBancoConfirmacion) fecha: $date");

        return response()->json([
            'success'       => $done,
            'message'       => $message,
            'files'         => [
                'ITFBancoDetalle' => [
                    'file'  => $ITFBancoDetalle,
                    'route' => $route[0],
                    'name'  => $route[1]
                ],
                'ITFBanco' => [
                    'file'  => $ITFBanco,
                    'route' => $route2[0],
                    'name'  => $route2[1]
                ],
                'ITFBancoConfirmacion' => [
                    'file'  => $ITFBancoConfirmacion,
                    'route' => $route3[0],
                    'name'  => $route3[1]
                ]
            ]
        ], 200);
    }

    private function exportITFBancoDetalle($transactions, $date, $route) {
        // Crear reporte
        $ITFBancoDetalle = $this->createITFBancoDetalle($transactions, $date);
                
        // Almacenar archivos en el servidor
        $disk  = Storage::disk('public')->put("$route", $ITFBancoDetalle);

        // Calcular hash del archivo
        if($disk) {
            $hash = sha1_file(public_path("storage/$route"));
        }

        return [$ITFBancoDetalle, $disk, $hash];
    }

    private function exportXML($transactions, $date, $hash, $route2, $route3, $type = null) {
        // Crear archivo XML ITFBanco y ITFBancoConfirmacion
        $ITFBanco             = $this->createITFBanco($transactions, $date, $hash, $type);
        $ITFBancoConfirmacion = $this->createITFBancoConfirmacion($transactions, $hash);

        // Almacenar ITFBanco y ITFBancoConfirmacion en el servidor
        $disk2  = Storage::disk('public')->put("$route2", $ITFBanco);
        $disk3  = Storage::disk('public')->put("$route3", $ITFBancoConfirmacion);

        return [
            [$ITFBanco, $disk2],
            [$ITFBancoConfirmacion, $disk3]
        ];
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
        $date = Carbon::parse("$date")->format('d-m-Y');
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

    private function createITFBanco($transactions, $date, $hash, $type) {
        $date = Carbon::parse("$date")->format('d-m-Y');
        $part_date        = explode('-', $date);
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

        $items = $this->transactionTotals($transactions, $type);

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

    private function transactionTotals($transactions, $type) {
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

        if($type == null) {
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
        }

        return $concept_totals;
    }

    private function createITFBancoConfirmacion($transactions, $hash) {
        $doc = new \DOMDocument('1.0', 'UTF-8');
      
        $root = $doc->createElement("ITFBancoConfirmacion");
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

    private function verifyHoliday($date, $routes = null) {
        $holiday    = Holiday::where('date', $date)->get();
        
        if(count($holiday) > 0) {
            // collection empty
            $transactions = collect();

            $transactions->push([
                'exento'              => 0,
                'numero'              => 0,
                'rifcliente'          => 0,
                'codigocuentacliente' => 0,
                'fechaoperacion'      => 0,
                'horaoperacion'       => 0,
                'instrumentopago'     => 0,
                'cantidadendosos'     => 0,
                'concepto'            => 0,
                'montooperacion'      => 0,
                'montoimpuesto'       => 0
            ]);

            $export = $this->exportITFBancoDetalle($transactions, $date, $routes[0]);

            $ITFBancoDetalle    = $export[0];
            $disk               = $export[1];
            $hash               = $export[2];

            if($hash) {
                $export2 = $this->exportXML($transactions, $date, $hash, $routes[1], $routes[2], 'Holiday');

                $ITFBanco               = $export2[0][0];
                $disk2                  = $export2[0][1];
                $ITFBancoConfirmacion   = $export2[1][0];
                $disk3                  = $export2[1][1];

                if($disk2 && $disk3) {
                    return [$ITFBancoDetalle, $ITFBanco, $ITFBancoConfirmacion];
                }
            }
        }

        return null;
    } 
}
