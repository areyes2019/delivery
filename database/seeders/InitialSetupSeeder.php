<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Flotilla;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 🏢 CLIENTE (TENANT)
        |--------------------------------------------------------------------------
        */
        $cliente = Cliente::firstOrCreate(
            ['slug' => 'delivery-demo'],
            [
                'nombre' => 'Delivery Demo',
                'email_contacto' => 'contacto@delivery.test',
                'telefono' => '4610000000',
                'plan' => 'PRO',
                'activo' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 👑 SUPER ADMIN (NIVEL DIOS)
        |--------------------------------------------------------------------------
        */
        User::firstOrCreate(
            ['email' => 'superadmin@delivery.test'],
            [
                'name' => 'Super',
                'lastname' => 'Admin',
                'phone' => '4611111111',
                'password' => Hash::make('12345678'),
                'rol' => 'super_admin',
                'activo' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 🧑‍💼 ADMIN CLIENTE
        |--------------------------------------------------------------------------
        */
        $adminCliente = User::firstOrCreate(
            ['email' => 'admin@delivery.test'],
            [
                'cliente_id' => $cliente->id,
                'name' => 'Admin',
                'lastname' => 'Cliente',
                'phone' => '4612222222',
                'password' => Hash::make('12345678'),
                'rol' => 'admin_cliente',
                'activo' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 🚚 FLOTILLA
        |--------------------------------------------------------------------------
        */
        $flotilla = Flotilla::firstOrCreate(
            [
                'cliente_id' => $cliente->id,
                'nombre' => 'Flotilla Centro',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 🧭 DESPACHADOR
        |--------------------------------------------------------------------------
        */
        User::firstOrCreate(
            ['email' => 'despachador@delivery.test'],
            [
                'cliente_id' => $cliente->id,
                'flotilla_id' => $flotilla->id,
                'name' => 'Despachador',
                'lastname' => 'Uno',
                'phone' => '4613333333',
                'password' => Hash::make('12345678'),
                'rol' => 'despachador',
                'activo' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 🏍️ DRIVER
        |--------------------------------------------------------------------------
        */
        User::firstOrCreate(
            ['email' => 'driver@delivery.test'],
            [
                'cliente_id' => $cliente->id,
                'flotilla_id' => $flotilla->id,
                'name' => 'Driver',
                'lastname' => 'Uno',
                'phone' => '4614444444',
                'password' => Hash::make('12345678'),
                'rol' => 'driver',
                'activo' => true,
            ]
        );
    }
}
