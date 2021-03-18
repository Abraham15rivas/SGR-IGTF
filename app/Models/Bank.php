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

    public function branchOffices() {
        return $this->hasMany(BranchOfficce::class);
    }
}
