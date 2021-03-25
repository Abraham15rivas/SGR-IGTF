<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ItfTrans02, Transaction};
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = 'Vista principal';
        return view('home', compact('title'));
    }

    public function showCoreExcel() {
        if(!Cache::has('transactions')) {
            $transactions = ItfTrans02::getItfReportDaily();
            $date         = $transactions->first()['FECHA'];
            Cache::add('transactions', $transactions);
        } else {
            $transactions = Cache::get('transactions');
            $date         = $transactions->first()['FECHA'];
        }
        // Cache::flush();
        $title = 'Datos del Core Bancario: ' . $date;
        return view('report.show_core_excel', compact('transactions', 'title'));
    }

    public function showPreliminaryExcel() {
        if(!Cache::has('preliminaries')) {
            $transactions   = ItfTrans02::getItfReportDaily();
            $preliminaries  = Transaction::setTransactions($transactions);
            $date           = $transactions->first()['FECHA'];
            Cache::add('transactions', $transactions);
            Cache::add('preliminaries', $preliminaries);
        } else {
            $transactions   = Cache::get('transactions');
            $preliminaries  = Cache::get('preliminaries');
            $date           = $transactions->first()['FECHA'];
        }        
        // Cache::flush();
        // dd($preliminaries);

        $title = 'Datos reporte preliminar: ' . $date;
        return view('report.show_preliminary_excel', compact('preliminaries', 'title'));
    }

    public function showDefinitiveExcel() {
        $date = Carbon::now()->subDay();
        $title = 'Datos reporte definitivo: ' . $date->format('d-m-Y');
        return view('report.show_definitive_excel', compact('title'));
    }
}
