<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'Transaccion';
    protected $primaryKey = 'pk_Trans';
    public $timestamps = false;

    protected $fillable = [
        'fk_Estat'
    ];

    static public function trans_confirma($date, $json) {
        // $date   = Carbon::parse($date)->format('Y-m-d');
        $status = TransactionStatus::where('descEstat', 'confirmado')->value('pk_Estat');
        foreach(json_decode($json) as $transaction) {
            $get_trans = self::find($transaction->id, ['pk_Trans', 'fk_Estat', 'dateTrans']);
       
            // if(Carbon::parse($get_trans->dateTrans)->format('Y-m-d') == $date) {
                $get_trans->update([
                    'fk_Estat' => $status
                ]);
            // }
        }
        return true;
    }

    static public function transactionMonth() {
        $month          = Carbon::now()->format('m');
        $transactions   = self::get('dateTrans');
        
        // Meses
        $months = collect([
            "enero"         => 0,
            "febrero"       => 0,
            "marzo"         => 0,
            "abril"         => 0,
            "mayo"          => 0,
            "junio"         => 0,
            "julio"         => 0,
            "agosto"        => 0,
            "septiembre"    => 0,
            "obtubre"       => 0,
            "noviembre"     => 0,
            "diciembre"     => 0
        ]);

        foreach($transactions as $trans) {
            $m = substr($trans->dateTrans, 5, 2);

            switch ($m):
                case '01':
                    $months['enero'] += 1;
                    break;
                case '02':
                    $months['febrero'] += 1;
                    break;
                case '03':
                    $months['marzo'] += 1;
                    break;
                case '04':
                    $months['abril'] += 1;
                    break;
                case '05':
                    $months['mayo'] += 1;
                    break;
                case '06':
                    $months['junio'] += 1;
                    break;
                case '07':
                    $months['julio'] += 1;
                    break;
                case '08':
                    $months['agosto'] += 1;
                    break;
                case '09':
                    $months['septiembre'] += 1;
                    break;
                case '10':
                    $months['obtubre'] += 1;
                    break;
                case '11':
                    $months['noviembre'] += 1;
                    break;
                case '12':
                    $months['diciembre'] += 1;
                    break;
            endswitch;
        }

        return collect([
            'name'    => $months,
            'total'     => count($transactions)
        ]);
    }
}
