<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptDaily extends Model
{
    use HasFactory;

    protected $table = 'concept_dailies';

    protected $fillable = [
        'description'
    ];

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
    
    static public function getConcepDailyId($code_concept) {
        $id = substr($code_concept, -1, 2);
        if(!is_string($id)) {
            $concept = self::where('id', $id)->value('id');
        } else {
            $concept = 0;
        }
        return $concept;
    }
}
