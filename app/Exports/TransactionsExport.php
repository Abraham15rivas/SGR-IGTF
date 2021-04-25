<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\TransactionsExportSheet;

class TransactionsExport implements WithMultipleSheets
{
    use Exportable;

    private $transactions;
    private $name_sheets = ['Definitivo', 'Operaciones', 'Temporal'];

    public function __construct($transactions = null)
    {
        $this->transactions = $transactions;
    }

    public function sheets(): array 
    {
        $sheets = [];
        $key    = 0;
        foreach($this->transactions as $transaction) {
            $sheets[] = new TransactionsExportSheet($transaction, $this->name_sheets[$key]);
            $key++;
        }
        return $sheets;
    }
}
