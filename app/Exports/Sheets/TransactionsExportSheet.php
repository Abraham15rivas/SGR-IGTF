<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\{
    FromArray,
    WithTitle,
    WithMapping,
    WithHeadings,
    WithStrictNullComparison,
    WithEvents
};

class TransactionsExportSheet implements FromArray, WithTitle, WithMapping, WithHeadings, WithStrictNullComparison
{
    private $transaction;
    private $title;

    public function __construct($transaction = null, $title = null)
    {
        $this->transaction  = $transaction;
        $this->title        = $title;
    }

    /**
    * @param mixed $row
    *
    * @return array
    */
    public function map($row): array
    {
        return [
            $row['referencia'],
            $row['rif'],
            $row['cuenta'],
            $row['cliente'],
            $row['fecha'],
            $row['hora'],
            $row['tansaccion'],
            $row['instrumento'],
            $row['endoso'],
            $row['concepto'],
            $row['monto'],
            $row['impuesto'],
            isset($row['estatus']) ? $row['estatus'] : ''
        ];
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'REFERENCIA',
            'RIF',
            'CUENTA',
            'CLIENTE',
            'FECHA',
            'HORA',
            'TANSACCION',
            'INSTRUMENTO',
            'ENDOSO',
            'CONCEPTO',
            'MONTO',
            'IMPUESTO'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        return $this->transaction;
    }
    
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->getSheet()->autoSize();
                $event->getSheet()->getDelegate()->getStyle('A1:C11')
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
