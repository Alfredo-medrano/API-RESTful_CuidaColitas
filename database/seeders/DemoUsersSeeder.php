<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pet;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Crear veterinarios
        $vet1 = User::create([
            'name' => 'Dra. Ana Vet',
            'email' => 'ana@vet.com',
            'password' => Hash::make('password'),
        ]);
        $vet1->assignRole('veterinario');

        $vet2 = User::create([
            'name' => 'Dr. Juan Vet',
            'email' => 'juan@vet.com',
            'password' => Hash::make('password'),
        ]);
        $vet2->assignRole('veterinario');

        // Crear clientes y sus mascotas
        foreach (range(1, 4) as $i) {
            $client = User::create([
                'name' => "Cliente $i",
                'email' => "cliente$i@email.com",
                'password' => Hash::make('password'),
            ]);
            $client->assignRole('cliente');

            $pet = Pet::create([
                'name'       => "Mascota $i",
                'species'    => 'perro',
                'breed'      => 'mestizo',
                'birth_date' => now()->subYears(2),
                'sex'        => 'macho',
                'color'      => 'marrón',
                'user_id'    => $client->id,
            ]);

            // Crear una cita asignada a un veterinario alternadamente
            Appointment::create([
                'pet_id'         => $pet->id,
                'client_id'      => $client->id,
                'veterinarian_id'=> $i % 2 == 0 ? $vet2->id : $vet1->id,
                'date'           => now()->addDays($i),
                'time'           => '10:00',
                'reason'         => 'Chequeo general',
                'status'         => 'pendiente',
            ]);
        }
    }
}
