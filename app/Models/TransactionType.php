<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;

    protected $table = 'transaction_types';
   
    protected $fillable = [
        'operation_code',
        'description'
    ];

    public function conceptDaily() {
        return $this->belongsTo(ConceptDaily::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
