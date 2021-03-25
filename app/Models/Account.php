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
        'bank_id',
        // 'branch_office_id'
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

    static public function setAccount($account_number, $branch_office, $customer_id) {
        if(strlen($account_number) == 19) {
            $account_number = "0$account_number";
        }

        $account = self::where('cta20', $account_number)->first();

        if(empty($account)) {
            $bank_id = bank::getBankId(substr($account_number, 0, 4));
            $account = self::create([
                'cta20'             => $account_number,
                'customer_id'       => $customer_id,
                'bank_id'           => $bank_id,
                // 'branch_office_id'  => $branch_office
            ]);
        }
        return $account;
    }
}
