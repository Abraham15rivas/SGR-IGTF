<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    use HasFactory;

    protected $table = 'instruments';

    protected $fillable = [
        'code_seniat',
        'description'
    ];

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
