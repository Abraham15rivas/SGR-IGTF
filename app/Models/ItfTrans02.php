<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ItfTrans02 extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';

    protected $table = 'itftrans02';

    static public function getItfReportDaily() {
        $transactions = ItfTrans02::all();
        $transactions_filter = collect();

        foreach($transactions as $transaction) {
            $amount     = (int) trim($transaction->ttramt);
            $tax        = (((int) $amount * 2) / 100);
            $time       = self::timeInt(trim($transaction->ttrtim));
            $instrument = self::getInstrument($transaction->ttrnar);
            $endorsed   = self::getEndorsed($transaction->ttrnar);
            $concept    = self::getConcept($transaction->ttrnar);

            $transactions_filter->push([
                'REFERENCIA'    => trim($transaction->ctackn),
                'RIF'           => trim($transaction->cusidn),
                'CUENTA'        => trim($transaction->cta20),
                'CLIENTE'       => trim($transaction->cusna1),
                'FECHA'         => trim($transaction->fechas),
                'HORA'          => $time,
                'TRANSACCION'   => trim($transaction->ttrnar),
                'INSTRUMENTO'   => $instrument,
                'ENDOSO'        => $endorsed,
                'CONCEPTO'      => $concept,
                'CENTRO_COSTO'  => trim($transaction->ttrccn),
                'MONTO'         => $amount,
                'IMPUESTO'      => $tax,
                'SUCURSAL'      => trim($transaction->ttrbrn)
            ]);
        }
        return $transactions_filter;
    }

    static private function timeInt($time) {
        if(substr($time, 0, 1) == '-') {
            $time = "16:00:00";
        } else {
            if(strlen($time) == 5) {
                $time = "0$time";
            }
            $hh     = substr($time, 0, 2);
            $mm     = substr($time, 2, 2);
            $ss     = substr($time, 4, 2);
            $time   = "$hh:$mm:$ss";
        }
        return $time;
    }

    static private function getInstrument($instrument) {
        $value = self::conditions('instrument', $instrument);
        return $value;
    }

    static private function getEndorsed($endorsed) {
        $value = self::conditions('endorsed', $endorsed);
        return $value;
    }

    static private function getConcept($concept) {
        $value = self::conditions('concept', $concept);
        return $value;
    }

    static private function conditions($type, $elements) {
        if(substr($elements, 0, 19) == 'TRANSF CTA T O/B IB') {
            $elements = self::getType($type, ['01', '1', '0001']);
        }
        if(substr($elements, 0, 17) == 'OB/COM/ALTOVALOR ') {
            $elements = self::getType($type, ['01', '1', '0001']);
        }
        if(preg_match("/CHEQ|PAGO|PROC|INCL/", substr($elements, 0, 4))) {
            $elements = self::getType($type, ['01', '1', '0001']);
        }
        if(substr($elements, 0, 4) == 'EMIS') {
            $elements = self::getType($type, ['02', '1', '0010']);
        }
        if(preg_match("/TRAN|TRAS/", substr($elements, 0, 4))) {
            $elements = self::getType($type, ['03', '0', '0001']);
        }
        if(preg_match("/COMPR|COM.T|COM B|COM.T|ND SO|COM.T|RETIR|REG R|ND SO|COB\/C|COMIS|NOTA |ND EM|COM  |DB.NP|COBRO|COM X|DB.NO|ND EMIS Y|CARGO|ND RE|STAME|DB.BO/", substr($elements, 0, 5))) {
            $elements = self::getType($type, ['04', '0', '0001']);
        }
        if(preg_match("/ND POR IG|ND IMP AL DEBITO BANCARIO|IMPUESTO |COB\/IMP\/A|IMP. G.T.|ITF X COM|ITF X TRA|IGTF. 0.7|ND POR IG|IMP.IGTF |IGTF     /", substr($elements, 0, 9))) {
            $elements = "IMPUESTO";
        }
        return $elements;
    }

    static private function getType($type, $values) {
        if($type == 'instrument') {
            $value_return = "$values[0]";
        } elseif($type == 'endorsed') {
            $value_return = "$values[1]";
        } else {
            $value_return = "$values[2]";
        }
        return $value_return;
    }
}