<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Total extends Model
{
    use HasFactory;

    protected $table = 'totals';

    protected $fillable = [
        'gravada_endosos',
        'gravada_cantidad',
        'gravada_base',
        'gravada_impuesto',
        'no_gravada_cantidad',
        'no_gravada_base'
    ];

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
