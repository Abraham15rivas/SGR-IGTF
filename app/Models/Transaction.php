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
        'date',
        'time',
        'amount',
        'tax',
        'category',
        'endorsed_id',
        'instrument_id',
        'transaction_type_id',
        'account_id'
    ];

    public function endorsed() {
        return $this->belongsTo(Endorsed::class);
    }

    public function instrument() {
        return $this->belongsTo(Instrument::class);
    }

    public function transactionType() {
        return $this->belongsTo(transactionType::class);
    }

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function total() {
        return $this->blongsTo(Total::class);
    }

    static public function setTransactions($transactions) {
        $preliminaries = collect();
        foreach($transactions as $transaction) {
            $preliminary            = new Transaction();
            $preliminary->reference = $transaction['REFERENCIA'];
            $preliminary->date      = $transaction['FECHA'];
            $preliminary->time      = $transaction['HORA'];
            $preliminary->amount    = $transaction['MONTO'];
            $preliminary->tax       = $transaction['IMPUESTO'];
            $preliminary->category  = 'preliminary';

            $account_id             = Customer::setCustomer($transaction['RIF'], $transaction['CUENTA'], $transaction['CLIENTE'], $transaction['SUCURSAL']);
            $instrument_id          = Instrument::getInstrumentId($transaction['INSTRUMENTO']);
            $concept_dailies_id     = ConceptDaily::getConcepDailyId($transaction['CONCEPTO']);
            $endorsed_id            = Endorsed::getEndorsed($transaction['ENDOSO']);

            if($account_id) {
                $preliminary->account_id = $account_id;
            }

            if($instrument_id) {
                $preliminary->instrument_id = $instrument_id;
            }

            if($concept_dailies_id) {
                $preliminary->concept_dailies_id = $concept_dailies_id;
            }

            if($endorsed_id) {
                $preliminary->endorsed_id = $endorsed_id;
            }

            // $preliminary->save();
            $preliminaries->push($preliminary);
        }
        return $preliminaries;
    }
}
