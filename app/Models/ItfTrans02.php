<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ItfTrans02 extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';

    protected $table = 'ITFTRANS02';
}