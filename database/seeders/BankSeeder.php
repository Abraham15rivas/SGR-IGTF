<?php

namespace Database\Seeders;


use App\Models\{
    RifType,
    Bank
};
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rif_types = RifType::all();

        $banks = [
            ['0001','Banco Central de Venezuela','G200001100'],
            ['0102','Banco de Venezuela S.A.C.A. Banco Universal','G200099976'],
            ['0104','Venezolano de Crédito, S.A. Banco Universal','J000029709'],
            ['0105','Banco Mercantil, C.A. Banco Universal','J000029610'],
            ['0108','Banco Provincial, S.A. Banco Universal','J000029679'],
            ['0114','Bancaribe C.A. Banco Universal','J000029490'],
            ['0115','Banco Exterior C.A. Banco Universal','J000029504'],
            ['0116','Banco Occidental de Descuento, Banco Universal C.A','J300619460'],
            ['0128','Banco Caroní C.A. Banco Universal','J095048551'],
            ['0134','Banesco Banco Universal S.A.C.A.','J070133805'],
            ['0137','Banco Sofitasa, Banco Universal','J090283846'],
            ['0138','Banco Plaza, Banco Universal','J002970553'],
            ['0146','Banco de la Gente Emprendedora C.A','J301442040'],
            ['0151','BFC Banco Fondo Común C.A. Banco Universal','J000723060'],
            ['0156','100% Banco, Banco Universal C.A.','J085007768'],
            ['0157','DelSur Banco Universal C.A.','J000797234'],
            ['0163','Banco del Tesoro, C.A. Banco Universal','G200051876'],
            ['0166','Banco Agrícola de Venezuela, C.A. Banco Universal','G200057955'],
            ['0168','Bancrecer, S.A. Banco Microfinanciero','J316374173'],
            ['0169','Mi Banco, Banco Microfinanciero C.A.','J315941023'],
            ['0171','Banco Activo, Banco Universal','J080066227'],
            ['0172','Bancamiga, Banco Microfinanciero C.A.','J316287599'],
            ['0173','Banco Internacional de Desarrollo, C.A. Banco Universal','J294640109'],
            ['0174','Banplus Banco Universal, C.A','J000423032'],
            ['0175','Banco Bicentenario del Pueblo de la Clase Obrera, Mujer y Comunas B.U.','G200091487'],
            ['0176','Novo Banco, S.A. Sucursal Venezuela Banco Universal','J308918644'],
            ['0177','Banco de la Fuerza Armada Nacional Bolivariana, B.U.','G200106573'],
            ['0190','Citibank N.A.','J000526621'],
            ['0191','Banco Nacional de Crédito, C.A. Banco Universal','J309841327'],
            ['0601','Instituto Municipal de Crédito Popular','G200068973']
        ];

        foreach($banks as $bank) {
            $bank_new               = new Bank();
            $bank_new->code_cce     = $bank[0];
            $bank_new->ibp          = $bank[1];
            $bank_new->rif          = substr($bank[2], 1);
            $bank_new->rif_type_id  = $this->idDeterminator(substr($bank[2], -10, 1));
            $bank_new->save();
        }
    }

    private function idDeterminator($lirycs) {
        switch ($lirycs) {
            case "V":
                $rif_type_id = 1;
                break;
            case "E":
                $rif_type_id = 2;
                break;
            case "P":
                $rif_type_id = 3;
                break;
            case "J":
                $rif_type_id = 4;
                break;
            case "G":
                $rif_type_id = 5;
                break;
        }
        return $rif_type_id;
    }
} 
