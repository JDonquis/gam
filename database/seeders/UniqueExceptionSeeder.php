<?php

namespace Database\Seeders;

use App\Models\UniqueException;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UniqueExceptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UniqueException::insert([
            ['name' => 'NO APLICA'],
            ['name' => 'NO TIENE'],

        ]);
    }
}
