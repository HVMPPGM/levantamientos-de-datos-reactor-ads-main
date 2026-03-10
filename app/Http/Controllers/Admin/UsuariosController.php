<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuariosController extends Controller
{
    /**
     * Mostrar la lista de usuarios
     */
    public function index()
    {
        $usuarios = DB::table('usuarios')
            ->orderBy('fecha_registro', 'desc')
            ->get();

        return view('admin.usuarios', compact('usuarios'));
    }

    /**
     * Obtener un usuario específico
     */
    public function show($id)
    {
        $usuario = DB::table('usuarios')
            ->where('id_usuarios', $id)
            ->first();

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }

    /**
     * Crear un nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nombres' => 'required|string|max:100',
            'Correo' => 'required|email|unique:usuarios,Correo',
            'Contrasena' => 'required|string|min:6',
            'Rol' => 'required|in:Admin,Usuario',
            'Estatus' => 'required|in:Activo,Inactivo'
        ], [
            'Nombres.required' => 'El nombre es obligatorio',
            'Correo.required' => 'El correo es obligatorio',
            'Correo.unique' => 'Este correo ya está registrado',
            'Contrasena.required' => 'La contraseña es obligatoria',
            'Contrasena.min' => 'La contraseña debe tener al menos 6 caracteres'
        ]);

        $userId = DB::table('usuarios')->insertGetId([
            'Nombres' => $request->Nombres,
            'ApellidosPat' => $request->ApellidosPat,
            'ApellidoMat' => $request->ApellidoMat,
            'Telefono' => $request->Telefono,
            'Correo' => $request->Correo,
            'Contrasena' => Hash::make($request->Contrasena),
            'Rol' => $request->Rol,
            'Estatus' => $request->Estatus,
            'Permisos' => $request->Permisos ?? 'no',
            'fecha_registro' => now(),
            'ultima_actividad' => now()
        ]);

        // Registrar actividad
        $this->registrarActividad('usuario', 'registro', "Nuevo usuario registrado: {$request->Nombres} {$request->ApellidosPat}", $userId);

        // Crear notificación para administradores
        $this->notificarAdministradores(
            'Nuevo Usuario',
            "Se ha registrado el usuario {$request->Nombres} {$request->ApellidosPat}",
            'info'
        );

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'id' => $userId
        ], 201);
    }

    /**
     * Actualizar un usuario
     */
    public function update(Request $request, $id)
    {
        $usuario = DB::table('usuarios')->where('id_usuarios', $id)->first();

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'Nombres' => 'required|string|max:100',
            'Correo' => 'required|email|unique:usuarios,Correo,' . $id . ',id_usuarios',
            'Rol' => 'required|in:Admin,Usuario',
            'Estatus' => 'required|in:Activo,Inactivo'
        ]);

        $updateData = [
            'Nombres' => $request->Nombres,
            'ApellidosPat' => $request->ApellidosPat,
            'ApellidoMat' => $request->ApellidoMat,
            'Telefono' => $request->Telefono,
            'Correo' => $request->Correo,
            'Rol' => $request->Rol,
            'Estatus' => $request->Estatus,
            'ultima_actividad' => now()
        ];

        // Solo actualizar contraseña si se proporcionó una nueva
        if ($request->filled('Contrasena')) {
            $request->validate([
                'Contrasena' => 'string|min:6'
            ]);
            $updateData['Contrasena'] = Hash::make($request->Contrasena);
        }

        DB::table('usuarios')
            ->where('id_usuarios', $id)
            ->update($updateData);

        // Registrar actividad
        $this->registrarActividad('usuario', 'actualizado', "Usuario actualizado: {$request->Nombres} {$request->ApellidosPat}", $id);

        return response()->json([
            'message' => 'Usuario actualizado correctamente'
        ]);
    }

    /**
     * Alternar el estado del usuario (Activo/Inactivo)
     */
    public function toggleStatus(Request $request, $id)
    {
        $usuario = DB::table('usuarios')->where('id_usuarios', $id)->first();

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $nuevoEstatus = $request->Estatus;

        DB::table('usuarios')
            ->where('id_usuarios', $id)
            ->update([
                'Estatus' => $nuevoEstatus,
                'ultima_actividad' => now()
            ]);

        $accion = $nuevoEstatus === 'Activo' ? 'reactivado' : 'desactivado';
        $nombreCompleto = trim("{$usuario->Nombres} {$usuario->ApellidosPat} {$usuario->ApellidoMat}");

        // Registrar actividad
        $this->registrarActividad('usuario', $accion, "Usuario {$accion}: {$nombreCompleto}", $id);

        // Notificar a administradores
        $this->notificarAdministradores(
            "Usuario " . ucfirst($accion),
            "Se ha {$accion} el usuario {$nombreCompleto}",
            $nuevoEstatus === 'Activo' ? 'success' : 'warning'
        );

        return response()->json([
            'message' => 'Estado del usuario actualizado correctamente',
            'nuevo_estatus' => $nuevoEstatus
        ]);
    }

    /**
     * Actualizar permisos del usuario
     */
    public function updatePermissions(Request $request, $id)
    {
        $usuario = DB::table('usuarios')->where('id_usuarios', $id)->first();

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $permisos = $request->Permisos ?? 'no';

        DB::table('usuarios')
            ->where('id_usuarios', $id)
            ->update([
                'Permisos' => $permisos,
                'ultima_actividad' => now()
            ]);

        $accion = $permisos === 'si' ? 'permisos_otorgados' : 'permisos_revocados';
        $nombreCompleto = trim("{$usuario->Nombres} {$usuario->ApellidosPat} {$usuario->ApellidoMat}");

        // Registrar actividad
        $this->registrarActividad(
            'usuario',
            $accion,
            "Permisos especiales " . ($permisos === 'si' ? 'otorgados para' : 'revocados para') . ": {$nombreCompleto}",
            $id
        );

        return response()->json([
            'message' => 'Permisos actualizados correctamente',
            'permisos' => $permisos
        ]);
    }

    /**
     * Eliminar un usuario (cambiar a inactivo)
     */
    public function destroy($id)
    {
        $usuario = DB::table('usuarios')->where('id_usuarios', $id)->first();

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // No permitir eliminar el usuario actual
        if (Auth::id() == $id) {
            return response()->json(['message' => 'No puede eliminar su propia cuenta'], 403);
        }

        DB::table('usuarios')
            ->where('id_usuarios', $id)
            ->update([
                'Estatus' => 'Inactivo',
                'ultima_actividad' => now()
            ]);

        $nombreCompleto = trim("{$usuario->Nombres} {$usuario->ApellidosPat} {$usuario->ApellidoMat}");

        // Registrar actividad
        $this->registrarActividad('usuario', 'eliminado', "Usuario desactivado: {$nombreCompleto}", $id);

        return response()->json([
            'message' => 'Usuario desactivado correctamente'
        ]);
    }

    /**
     * Registrar actividad en la tabla de actividades
     */
    private function registrarActividad($tipo, $accion, $descripcion, $referenciaId = null)
    {
        DB::table('actividades')->insert([
            'Tipo' => $tipo,
            'Accion' => $accion,
            'Descripcion' => $descripcion,
            'Referencia_Id' => $referenciaId,
            'Id_Usuario' => Auth::id(),
            'Fecha' => now()
        ]);
    }

    /**
     * Notificar a todos los administradores
     */
    private function notificarAdministradores($titulo, $mensaje, $tipo = 'info')
    {
        $administradores = DB::table('usuarios')
            ->where('Rol', 'Admin')
            ->where('Estatus', 'Activo')
            ->pluck('id_usuarios');

        foreach ($administradores as $adminId) {
            DB::table('notificaciones')->insert([
                'Id_Usuario' => $adminId,
                'Titulo' => $titulo,
                'Mensaje' => $mensaje,
                'Tipo' => $tipo,
                'Leida' => 0,
                'Fecha' => now()
            ]);
        }
    }
}