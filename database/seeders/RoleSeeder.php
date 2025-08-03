<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         collect(['admin', 'veterinario', 'cliente'])->each(function ($name) {
            Role::firstOrCreate(
                ['name' => $name],
                ['display_name' => ucfirst($name)]
            );
        });
    }
}
