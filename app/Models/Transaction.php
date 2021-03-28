<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
   
    protected $fillable = [
        'reference',
        'account_id',
        'date',
        'time',
        'description',
        'instrument_id',
        'endorsed_id',
        'concept_dailies_id',
        'branch',
        'amount',
        'tax',
        'endorsed',
        'category',
        'total_id'
    ];

    public function instrument() {
        return $this->belongsTo(Instrument::class);
    }

    public function conceptDaily() {
        return $this->belongsTo(ConceptDaily::class);
    }

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function total() {
        return $this->blongsTo(Total::class);
    }

    static public function setTransactions($transactions) {
        $operations = collect();
        foreach($transactions as $transaction) {
            $operation              = new Transaction();
            $operation->reference   = $transaction['REFERENCIA'];
            $operation->date        = $transaction['FECHA'];
            $operation->time        = $transaction['HORA'];
            $operation->description = $transaction['TRANSACCION'];
            $operation->branch      = $transaction['CENTRO_COSTO'];
            $operation->amount      = $transaction['MONTO'];
            $operation->tax         = $transaction['IMPUESTO'];
            $operation->category    = 'operation';

            dd($operation);
             
            $account_id             = Customer::setCustomer($transaction['RIF'], $transaction['CUENTA'], $transaction['CLIENTE'], $transaction['SUCURSAL']);
            $instrument_id          = Instrument::getInstrumentId($transaction['INSTRUMENTO']);
            $concept_dailies_id     = ConceptDaily::getConcepDailyId($transaction['CONCEPTO']);
            $endorsed_id            = Endorsed::getEndorsed($transaction['ENDOSO']);

            if($account_id) {
                $operation->account_id = $account_id;
            }

            if($instrument_id) {
                $operation->instrument_id = $instrument_id;
            }

            if($concept_dailies_id) {
                $operation->concept_dailies_id = $concept_dailies_id;
            }

            if($endorsed_id) {
                $operation->endorsed_id = $endorsed_id;
            }

            // $operation->save();
            $operations->push($operation);
        }
        return $operations;
    }
}
