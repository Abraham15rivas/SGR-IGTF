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
}
