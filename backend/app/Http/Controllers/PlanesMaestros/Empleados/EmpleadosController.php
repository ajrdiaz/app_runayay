<?php

namespace App\Http\Controllers\PlanesMaestros\Empleados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class EmpleadosController extends Controller
{
    public function inicio(Request $request)
    {
        $buscar = trim($request->query('buscar'));
    }
}