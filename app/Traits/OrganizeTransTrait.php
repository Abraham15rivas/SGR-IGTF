<?php

namespace App\Traits;

use Illuminate\Support\facades\DB;

trait OrganizeTransTrait {
    
    public function organize($date) {
        $response_organize = DB::select("SELECT trans_organiza('$date')");
        return $response_organize[0]->trans_organiza;
    }
}