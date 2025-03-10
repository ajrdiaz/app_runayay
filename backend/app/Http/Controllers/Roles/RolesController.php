<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function inicio(Request $request)
    {
        $buscar = trim($request->query('buscar'));

        $roles  = Role::where('name', 'LIKE', "%$buscar%")
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'roles' => $roles->map(function ($rol) {
                return [
                    'id' => $rol->id,
                    'nombre' => $rol->name,
                    'creado_en' => $rol->created_at->format('Y-m-d H:i:s'),
                    'permisos' => $rol->permissions->map(function ($permiso) {
                        return [
                            'id' => $permiso->id,
                            'nombre' => $permiso->name,
                        ];
                    }),
                    'permisos_nombre' => $rol->permissions->pluck('name'),
                ];
            }),
        ]);
    }

    public function guardar(Request $request, $id = null)
    {
        $actualizando = $id ? true : false;

        if (!$actualizando) {
            $existeRol = Role::where('name', $request->nombre)->first();
        } else {
            $existeRol = Role::where('name', $request->nombre)->where('id', '!=', $id)->first();
        }

        if ($existeRol) {
            return response()->json([
                'mensaje' => 'El nombre del rol ya existe',
                'codigo' => 403,
            ]);
        }


        if (!$actualizando) {
            $rol = Role::create([
                'name' => $request->nombre,
                'guard_name' => 'api',
            ]);
        } else {
            $rol = Role::findOrFail($id);
            $rol->update([
                'name' => $request->nombre,
            ]);
        }

        $rol->syncPermissions($request->permisos);

        return response()->json([
            'mensaje' => 'Rol creado correctamente',
            'codigo' => 200,
        ]);
    }

    public function eliminar(string $id)
    {
        $rol = Role::findOrFail($id);
        $rol->delete();

        return response()->json([
            'mensaje' => 'Rol eliminado correctamente',
            'codigo' => 200,
        ]);
    }
}
