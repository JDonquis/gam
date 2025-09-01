<?php

namespace Database\Seeders;

use App\Models\TypeActivity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Crear usuario'],
            ['name' => 'Actualizar usuario'],
            ['name' => 'Eliminar usuario'],

            ['name' => 'Crear medico'],
            ['name' => 'Actualizar medico'],
            ['name' => 'Eliminar medico'],

            ['name' => 'Insertar documento'],
            ['name' => 'Eliminar documento'],
            ['name' => 'Crear configuracion de documento'],
            ['name' => 'Actualizar configuracion de documento'],
            ['name' => 'Eliminar configuracion de documento'],

            ['name' => 'Eliminar incidencia'],
            ['name' => 'Eliminar multiples incidencias'],
            ['name' => 'Actualizar incidencia'],

            ['name' => 'Generar sanción'],
            ['name' => 'Editar sanción'],
            ['name' => 'Eliminar sanción'],





        ];

        TypeActivity::insert($data);
    }
}
