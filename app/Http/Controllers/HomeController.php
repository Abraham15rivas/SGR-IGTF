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

    public function showTemporaryExcel() {
        if(!Cache::has('temporary')) {
            $temporary  = ItfTrans02::getItfReportDaily();
            $date       = $temporary->first()['FECHA'];
            Cache::add('temporary', $temporary);
        } else {
            $temporary = Cache::get('temporary');
            $date      = $temporary->first()['FECHA'];
        }
        Cache::flush();
        $title = 'Temporal: ' . $date;
        return view('report.show_temporary_excel', compact('temporary', 'title'));
    }

    public function storeOperationExcel(Request $request) {
        if($request->ajax()) {
            $transactions = $request->transactions;
            $operations = Transaction::setTransactions($transactions);
            return response()->json([
                'success' => true,
                'message' => '¡Operaciones emitido correctamente!',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Algo salio mal!',
            ], 400);
        }
    }

    public function showOperationExcel(Request $request) {

        // if(!Cache::has('operation')) {
        //     $transactions   = ItfTrans02::getItfReportDaily();
        //     $date           = $transactions->first()['FECHA'];
        //     Cache::add('transactions', $transactions);
        //     Cache::add('temporary', $temporary);
        // } else {
        //     $transactions   = Cache::get('transactions');
        //     $temporary  = Cache::get('temporary');
        //     $date           = $transactions->first()['FECHA'];
        // }        
        // Cache::flush();
        $title = 'Datos reporte temporal: ' . $date;
        return view('report.show_temporary_excel', compact('preliminaries', 'title'));
    }

    public function showDefinitiveExcel() {
        $date = Carbon::now()->subDay();
        $title = 'Datos reporte definitivo: ' . $date->format('d-m-Y');
        return view('report.show_definitive_excel', compact('title'));
    }
}
