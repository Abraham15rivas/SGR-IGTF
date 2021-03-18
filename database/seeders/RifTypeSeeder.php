<?php

namespace Database\Seeders;

use App\Models\RifType;
use Illuminate\Database\Seeder;

class RifTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rif_types = [
            ["V", "Venezolano o venezolana."],
            ["E", "extranjero o extranjera (número de cédula mayor a 80 millones)."],
            ["P", "Pasaporte, por ejemplo es útil para los cantantes que se presentan en nuestro país y que hay que retenerles Impuesto sobre la renta."],
            ["J", "Persona jurídica, osea, compañías anónimas, sociedades anónimas, S.R.L., etc."],
            ["G", "Gobierno, entes gubernamentales, de cualquier Poder, estado, municipio e incluso organismos «autónomos» (ejemplo Universidad de Carabobo RIF G-20000041-4)."],
        ];

        foreach($rif_types as $type) {
            RifType::create([
                'name' => $type[0],
                'description' => $type[1]
            ]);
        }
    }
}
