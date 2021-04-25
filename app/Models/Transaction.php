<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        $status = TransactionStatus::where('descEstat', 'confirmado')->value('pk_Estat');
        foreach(json_decode($json) as $transaction) {
            $get_trans = self::find($transaction->id);
            $get_trans->update([
                'fk_Estat' => $status
            ]);
        }
        return true;
    }
}
