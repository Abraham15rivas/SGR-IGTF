<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RifType extends Model
{
    use HasFactory;

    protected $table = 'rif_types';

    protected $fillable = [
       'name',
       'description'
    ];

    public function banks() {
        return $this->hasMany(Bank::class);
    }

    public function customers() {
        return $this->hasMany(Customer::class);
    }
}
