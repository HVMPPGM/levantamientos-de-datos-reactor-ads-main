<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        // Validar los datos
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'correo.required' => 'El correo electrónico es obligatorio',
            'correo.email' => 'Debe ingresar un correo electrónico válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        ]);

        // Buscar usuario por correo
        $user = User::where('Correo', $request->correo)->first();

        // Verificar si el usuario existe
        if (!$user) {
            return back()
                ->withErrors(['correo' => 'Las credenciales proporcionadas no coinciden con nuestros registros.'])
                ->withInput($request->only('correo'));
        }

        // Verificar si el usuario está activo
        if ($user->Estatus !== 'Activo') {
            return back()
                ->withErrors(['correo' => 'Tu cuenta está inactiva. Contacta al administrador.'])
                ->withInput($request->only('correo'));
        }

        // Verificar la contraseña
        if (!Hash::check($request->password, $user->Contrasena)) {
            return back()
                ->withErrors(['password' => 'La contraseña es incorrecta.'])
                ->withInput($request->only('correo'));
        }

        // Actualizar última actividad
        DB::table('usuarios')
            ->where('id_usuarios', $user->id_usuarios)
            ->update(['ultima_actividad' => now()]);

        // Registrar actividad de login
        DB::table('actividades')->insert([
            'Tipo' => 'usuario',
            'Accion' => 'login',
            'Descripcion' => "Inicio de sesión: {$user->nombre_completo}",
            'Referencia_Id' => $user->id_usuarios,
            'Id_Usuario' => $user->id_usuarios,
            'Fecha' => now(),
        ]);

        // Autenticar al usuario
        Auth::login($user, $request->filled('remember'));

        // Regenerar sesión por seguridad
        $request->session()->regenerate();

        // Redirigir según el rol
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('usuario.dashboard'));
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // Registrar actividad de logout
            DB::table('actividades')->insert([
                'Tipo' => 'usuario',
                'Accion' => 'logout',
                'Descripcion' => "Cierre de sesión: {$user->nombre_completo}",
                'Referencia_Id' => $user->id_usuarios,
                'Id_Usuario' => $user->id_usuarios,
                'Fecha' => now(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión correctamente.');
    }
}
