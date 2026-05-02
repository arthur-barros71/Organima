<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserindo os 3 status padrão na tabela stats
        DB::table('tb_tipos_de_projeto')->insert([
            ['nm_tipo' => 'Animação 2D ou 3D'],
            ['nm_tipo' => 'Animação Stop Motion'],
            ['nm_tipo' => 'Produção cinematográfica']
        ]);
    }
}
