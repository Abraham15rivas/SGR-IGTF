<?php

namespace Database\Seeders;

use App\Models\Rule;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rules = [
            ['tax_percentage', '2']
        ];

        foreach($rules as $rule) {
            Rule::create([
                'code'        => $rule[0],
                'value'       => $rule[1],
                'description' => $rule[0]
            ]);
        }
    }
}
