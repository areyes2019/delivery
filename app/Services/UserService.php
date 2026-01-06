<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Flotilla;

class UserService
{
    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */

    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new HttpException(401, 'Credenciales incorrectas');
        }

        if (! $user->activo) {
            throw new HttpException(403, 'Usuario inactivo');
        }

        return [
            'token' => $user->createToken('api-token')->plainTextToken,
            'user'  => $this->userPayload($user),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CREACIÓN DE USUARIOS (POR ROL)
    |--------------------------------------------------------------------------
    */

    // 🔱 SuperAdmin → Admin Cliente
    public function createAdminCliente(array $data, User $auth): User
    {
        if (! $auth->isSuperAdmin()) {
            throw new HttpException(403, 'No autorizado');
        }

        return User::create([
            'cliente_id' => $data['cliente_id'],
            'parent_id'  => $auth->id,
            'name'       => $data['name'],
            'lastname'   => $data['lastname'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'password'   => Hash::make($data['password']),
            'rol'        => 'admin_cliente',
            'activo'     => true,
        ]);
    }

    // 🧑‍💼 Admin Cliente → Driver
    public function createDriver(array $data, User $auth): User
    {
        if (!$auth->isAdminCliente()) {
            throw new HttpException(403, 'No autorizado');
        }

        if (User::where('email', $data['email'])->exists()) {
            throw new HttpException(422, 'El email ya está en uso');
        }

        return User::create([
            'name'        => $data['name'],
            'lastname'    => $data['lastname'],
            'email'       => $data['email'],
            'phone'       => $data['phone'] ?? null,
            'password'    => Hash::make($data['password']),

            'rol'         => 'driver',
            'activo'      => true,

            'cliente_id'  => $auth->cliente_id,
            'flotilla_id' => null,          // 👈 se asigna después
            'parent_id'   => $auth->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LECTURA SEGURA
    |--------------------------------------------------------------------------
    */

    public function findByIdForUser(int $id, User $auth): User
    {
        $user = User::findOrFail($id);

        if ($auth->isSuperAdmin()) {
            return $user;
        }

        if ($auth->isAdminCliente() && $user->cliente_id === $auth->cliente_id) {
            return $user;
        }

        if ($auth->isDespachador() && $user->parent_id === $auth->id) {
            return $user;
        }

        if ($auth->isDriver() && $user->id === $auth->id) {
            return $user;
        }

        throw new HttpException(403, 'No autorizado');
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZACIÓN SEGURA
    |--------------------------------------------------------------------------
    */

    public function updateByRole(int $id, array $data, User $auth): User
    {
        $user = $this->findByIdForUser($id, $auth);

        return DB::transaction(function () use ($user, $data) {
            $allowed = ['name', 'lastname', 'phone', 'image'];

            foreach ($allowed as $field) {
                if (array_key_exists($field, $data)) {
                    $user->{$field} = $data[$field];
                }
            }

            $user->save();

            return $user;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PAYLOAD
    |--------------------------------------------------------------------------
    */

    private function userPayload(User $user): array
    {
        return [
            'id'          => $user->id,
            'name'        => $user->name,
            'lastname'    => $user->lastname,
            'email'       => $user->email,
            'phone'       => $user->phone,
            'image'       => $user->image ? url($user->image) : null,
            'cliente_id'  => $user->cliente_id,
            'flotilla_id' => $user->flotilla_id,
            'rol'         => $user->rol,
        ];
    }
    /**
 * ➕ Crear Despachador (solo Admin Cliente)
 */
    public function createDespachador(array $data, User $auth): User
    {
        if (!$auth->isAdminCliente()) {
            throw new HttpException(403, 'No autorizado');
        }

        if (User::where('email', $data['email'])->exists()) {
            throw new HttpException(422, 'El email ya está en uso');
        }

        return User::create([
            'name'        => $data['name'],
            'lastname'    => $data['lastname'],
            'email'       => $data['email'],
            'phone'       => $data['phone'] ?? null,
            'password'    => Hash::make($data['password']),

            'rol'         => 'despachador',
            'activo'      => true,

            'cliente_id'  => $auth->cliente_id,
            'flotilla_id' => null,
            'parent_id'   => $auth->id,
        ]);
    }
    public function assignDriverToFlotilla(
        int $driverId,
        int $flotillaId,
        User $auth
    ): User {
        if (!$auth->isAdminCliente()) {
            throw new HttpException(403, 'No autorizado');
        }

        $driver = User::where('id', $driverId)
            ->where('rol', 'driver')
            ->where('cliente_id', $auth->cliente_id)
            ->firstOrFail();

        $flotilla = Flotilla::where('id', $flotillaId)
            ->where('cliente_id', $auth->cliente_id)
            ->firstOrFail();

        $driver->update([
            'flotilla_id' => $flotilla->id
        ]);

        return $driver;
    }

}
