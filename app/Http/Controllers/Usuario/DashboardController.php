<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard de usuario
     */
    public function index()
    {
        $usuario = Auth::user();
        
        // Obtener estadísticas del usuario
        $estadisticas = $this->obtenerEstadisticas($usuario->id_usuarios);
        
        // Verificar permisos
        $tienePermisosEspeciales = ($usuario->Permisos === 'si');
        
        return view('usuario.dashboard', compact('usuario', 'estadisticas', 'tienePermisosEspeciales'));
    }

    /**
     * Obtener estadísticas del usuario
     */
    private function obtenerEstadisticas($usuarioId)
    {
        // Levantamientos del usuario
        $levantamientos = DB::table('levantamientos')
            ->where('id_usuarios', $usuarioId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN estatus = "Pendiente" THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estatus = "En Proceso" THEN 1 ELSE 0 END) as en_proceso,
                SUM(CASE WHEN estatus = "Completado" THEN 1 ELSE 0 END) as completados
            ')
            ->first();

        // Clientes creados por el usuario (si tiene permisos)
        $totalClientes = DB::table('clientes')->count();

        // Cotizaciones (si existen)
        $totalCotizaciones = DB::table('cotizaciones')->count();

        // Actividades recientes
        $actividadesRecientes = DB::table('actividades')
            ->where('Id_Usuario', $usuarioId)
            ->orderBy('Fecha', 'desc')
            ->limit(5)
            ->get();

        return [
            'levantamientos' => $levantamientos,
            'total_clientes' => $totalClientes,
            'total_cotizaciones' => $totalCotizaciones,
            'actividades_recientes' => $actividadesRecientes
        ];
    }

    /**
     * Verificar si el usuario tiene permisos para una acción específica
     */
    public function verificarPermiso()
    {
        $usuario = Auth::user();
        
        if ($usuario->Permisos !== 'si') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción. Contacta al administrador.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tienes permisos para continuar'
        ]);
    }
}