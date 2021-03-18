<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';
        
    protected $fillable = [
        'cta20',
        'customer_id',
        'branch_office_id'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function branchOffice() {
        return $this->belongsTo(BranchOfficce::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
