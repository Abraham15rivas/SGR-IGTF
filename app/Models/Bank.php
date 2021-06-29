<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'Banco';
    protected $primaryKey = 'pk_Banco';
    public $timestamps = false;
    
    public function holidays() {
        return $this->hasMany(Holiday::class);
    }
}
