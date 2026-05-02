<?php

namespace Database\Seeders;

use App\Models\tb_usuario;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Inserindo os 3 status padrão na tabela stats
        DB::table('tb_tipo')->insert([
            ['nm_tipo' => 'Animação 2D ou 3D'],
            ['nm_tipo' => 'Animação Stop Motion'],
            ['nm_tipo' => 'Produção cinematográfica']
        ]);

        DB::table('tb_cargo')->insert([
            ['nm_cargo' => 'Leitor'],
            ['nm_cargo' => 'Comentador'],
            ['nm_cargo' => 'Editor']
        ]);
    }
}
