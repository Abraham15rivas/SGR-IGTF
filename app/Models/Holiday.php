<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $table = 'holidays';

    protected $fillable = [
        'date',
        'title',
        'description',
        'status',
        'bank_id'
    ];

    public function bank() {
        return $this->belongsTo(Bank::class, 'pk_Banco', 'bank_id');
    }
}
