<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchOffice extends Model
{
    use HasFactory;

    protected $table = 'branch_offices';

    protected $fillable = [
        'code_office',
        'description',
        'bank_id'
    ];

    public function bank() {
        return $this->belongsTo(Bank::class);
    }

    public function accounts() {
        return $this->hasMany(Account::class);
    }
}
