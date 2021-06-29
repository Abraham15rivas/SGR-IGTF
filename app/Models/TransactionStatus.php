<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    use HasFactory;

    protected $table = 'EstatTrans';
    protected $primaryKey = 'pk_Estat';
    public $timestamps = false;
}
