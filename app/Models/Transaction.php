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
        'date_time',
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
}
