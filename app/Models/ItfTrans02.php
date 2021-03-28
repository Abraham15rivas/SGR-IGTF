<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ItfTrans02 extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';

    protected $table = 'ITFTRANS02';

    static public function getItfReportDaily() {
        $transactions = ItfTrans02::all();
        $transactions_filter = collect();

        foreach($transactions as $transaction) {
            $amount         = (int) trim($transaction->TTRAMT);
            $tax_percentage = Rule::where('code', 'tax_percentage')->value('value');
            $tax            = (((int) $amount * (int) $tax_percentage) / 100);
            $time           = self::timeInt(trim($transaction->TTRTIM));
            $instrument     = self::getInstrument($transaction->TTRNAR, $transaction->TTRCDE);
            $endorsed       = self::getEndorsed($instrument);
            $concept        = self::getConcept($instrument);

            $transactions_filter->push([
                'REFERENCIA'    => trim($transaction->CTACKN),
                'RIF'           => trim($transaction->CUSIDN),
                'CUENTA'        => trim($transaction->CTA20),
                'CLIENTE'       => trim($transaction->CUSNA1),
                'FECHA'         => trim($transaction->FECHAS),
                'HORA'          => $time,
                'TRANSACCION'   => trim($transaction->TTRNAR),
                'INSTRUMENTO'   => $instrument,
                'ENDOSO'        => $endorsed,
                'CONCEPTO'      => $concept,
                'CENTRO_COSTO'  => trim($transaction->TTRCCN),
                'MONTO'         => $amount,
                'IMPUESTO'      => $tax
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

    static private function getInstrument($description, $code) {
        if(preg_match("/TRANSF CTA T O\/B IB|TRANSF O\/B CTA IB/", substr($description, 0, 19))) {
           return '03';
        }
        if(preg_match("/ND POR IG|ND IMP AL DEBITO BANCARIO|IMPUESTO |COB\/IMP\/A|IMP. G.T.|ITF X COM|ITF X TRA|IGTF. 0.7|ND POR IG|IMP.IGTF |IGTF     /", substr($description, 0, 9))) {
            return "IMPUESTO";
        }        
        if(trim($code) == 'TX') {
            return 'IMPUESTO';
        } elseif(preg_match("/59|DB|FI|ID|MD|FI|ND|RE/", trim($code))) {
            return '04';
        } elseif(trim($code) == 'WD') {
            return '03';
        } elseif(preg_match("/CK|CQ/", trim($code))) {
            return '01';
        }
        return $code;
    }

    static private function getEndorsed($instrument) {
        if($instrument == '01') {
            $endorsed = 1;
        } else {
            $endorsed = 0;
        }
        return $endorsed;
    }

    static private function getConcept($instrument) {
        if($instrument == '02') {
            $concept = '0010';
        } else {
            $concept = '0001';
        }
        return $concept;
    }
}