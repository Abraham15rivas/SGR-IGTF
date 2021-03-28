<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';

    protected $fillable = [
        'code_cce',
        'ibp',
        'rif',
        'rif_type_id'
    ];

    public function rifType() {
        return $this->belongsTo(RifType::class);
    }

    public function holidays() {
        return $this->hasMany(Holiday::class);
    }

    public function accounts() {
        return $this->hasMany(Account::class);
    }
    
    static public function getBankId($code_cce) {
        $bank_id = self::where('code_cce', $code_cce)->value('id');
        return $bank_id;
    }
}
