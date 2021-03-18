<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'name_secondary',
        'surname',
        'surname_secondary',
        'rif',
        'rif_type_id'
    ];
    
    public function rifType() {
        return $this->belongsTo(RifType::class);
    }

    public function accounts() {
        return $this->hasMany(Account::class);
    }
}
