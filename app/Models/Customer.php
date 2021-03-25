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

    static public function setCustomer($rif, $account_number, $name, $branch_office) {
        $customer = self::where('rif',substr($rif, 1))->first();

        if(empty($customer)) {
            $customer       = new self();
            $customer->name = $name;
            $customer->rif  = substr($rif, 1);
            $lirycs_rif     = substr($rif, -10, 1);

            if($lirycs_rif) {
                $rif_type_id = RifType::where('name', $lirycs_rif)->value('id');
            }

            $customer->rif_type_id = $rif_type_id;
            $customer->save();
        }
        $account = Account::setAccount($account_number, $branch_office, $customer->id);
        return $account->id;
    }
}
