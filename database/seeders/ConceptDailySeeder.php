<?php

namespace Database\Seeders;

use App\Models\ConceptDaily;
use Illuminate\Database\Seeder;

class ConceptDailySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $concept_dailies = [
            ['Débitos en Cuentas Bancarias'],
            ['Débitos en Cuentas de Corresponsalía'],
            ['Depósitos en Custodia o cualquier otra clase de Depósito'],
            ['Depósitos a la vista'],
            ['Fondos de Activos Líquidos'],
            ['Depósitos Fiduciario'],
            ['Otros Fondos o Instrumentos del Mercado Financiero'],
            ['Movimientos en Cuentas con más de un Endoso (Cesión de cheques, valores, y cualquier otro instrumento negociable)'],
            ['Depósitos en custodia Pagados en Efectivo'],
            ['Adquisición de Cheques de Gerencia'],
            ['Transferencia de valores en custodia entre distintos titulares'],
            ['Débitos en cuentas para pagos Transfronterizos'],
            ['Operaciones Activas Realizadas por propio Agente de Percepción superior a dos (2) días hábiles.']
        ];

        foreach($concept_dailies as $concept) {
            ConceptDaily::create([
                'description' => $concept[0]
            ]);
        }
    }
}
