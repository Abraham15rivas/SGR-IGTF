<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItfTrans02 extends Model
{
    use HasFactory;

    protected $connection = 'db_core_bav';

    protected $table = 'itftrans02';
}
