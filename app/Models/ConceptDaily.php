<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptDaily extends Model
{
    use HasFactory;

    protected $table = 'concept_dailies';

    protected $fillable = [
        'description',
        'transaction_type_id'
    ];

    public function transactionTypes() {
        return $this->hasMany(TransactionType::class);
    }
}
