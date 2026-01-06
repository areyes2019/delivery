<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClienteService
{
    /**
     * 📄 Listar todos los clientes (solo SuperAdmin)
     */
    public function listAll(): array
    {
        return Cliente::orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * ➕ Crear cliente (solo SuperAdmin)
     */
    public function create(array $data, User $auth): Cliente
    {
        if (!$auth->isSuperAdmin()) {
            throw new HttpException(403, 'No autorizado');
        }

        return Cliente::create([
            'nombre'         => $data['nombre'],
            'slug'           => $data['slug'],
            'email_contacto' => $data['email_contacto'] ?? null,
            'telefono'       => $data['telefono'] ?? null,
            'plan'           => $data['plan'],
            'activo'         => $data['activo'] ?? true,
        ]);
    }

    /**
     * 👁️ Ver cliente por ID (solo SuperAdmin)
     */
    public function findById(int $id): Cliente
    {
        return Cliente::findOrFail($id);
    }

    /**
     * ✏️ Actualizar cliente (solo SuperAdmin)
     */
    public function update(
        int $id,
        array $data,
        User $auth
    ): Cliente {
        if (!$auth->isSuperAdmin()) {
            throw new HttpException(403, 'No autorizado');
        }

        $cliente = Cliente::findOrFail($id);

        $cliente->update([
            'nombre'         => $data['nombre'] ?? $cliente->nombre,
            'slug'           => $data['slug'] ?? $cliente->slug,
            'email_contacto' => $data['email_contacto'] ?? $cliente->email_contacto,
            'telefono'       => $data['telefono'] ?? $cliente->telefono,
            'plan'           => $data['plan'] ?? $cliente->plan,
        ]);

        return $cliente;
    }

    /**
     * 🔒 Activar / Desactivar cliente (solo SuperAdmin)
     */
    public function toggleStatus(
        int $id,
        User $auth
    ): Cliente {
        if (!$auth->isSuperAdmin()) {
            throw new HttpException(403, 'No autorizado');
        }

        $cliente = Cliente::findOrFail($id);

        $cliente->update([
            'activo' => !$cliente->activo
        ]);

        return $cliente;
    }
}
