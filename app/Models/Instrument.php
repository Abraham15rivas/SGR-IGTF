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

    static public function getInstrumentId($code_seniat) {
        if(strlen($code_seniat) > 2) {
            $code_seniat = '06';
        }
        $instrument_id = self::where('code_seniat', $code_seniat)->value('id');
        return $instrument_id;
    }
}
