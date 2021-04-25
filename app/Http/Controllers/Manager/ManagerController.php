<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;

class ManagerController extends Controller
{
    public function index() {
        $title    = 'Gestión de calendario';
        $response = DB::select("SELECT cons_fer_list('true')");
        $dates    = collect();

        if($response) {
            foreach($response as $date) {
                $dates->push(json_decode($date->cons_fer_list, true));
            }
        } else {
            $dates = null;
        }

        return view('manager.calendar', compact('title', 'dates'));
    }
}
