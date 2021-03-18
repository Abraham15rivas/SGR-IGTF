<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endorsed extends Model
{
    use HasFactory;

    protected $table = 'endorseds';

    protected $fillable = [
        'code_seniat',
        'description'
    ];
    
    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
