<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $instruments = [
            ['cheque', '01'],
            ['cheque de gerencia', '02'],
            ['transferencia bancaria', '03'], 
            ['debito en cuenta', '04'],
            ['cartas de crédito', '05'],
            ['Otros', '06']
        ];

        foreach($instruments as $instrument) {
            Instrument::create([
                'code_seniat' => $instrument[1],
                'description' => $instrument[0]
            ]);
        }
    }
}
