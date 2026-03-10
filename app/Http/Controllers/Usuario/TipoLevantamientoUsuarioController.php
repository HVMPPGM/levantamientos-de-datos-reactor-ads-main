<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TipoLevantamientoUsuarioController extends Controller
{
    /**
     * Lista de tipos de levantamiento
     */
    public function index()
    {
        try {
            $usuario = Auth::user();
            
            // Verificar permisos
            $tienePermisosEspeciales = ($usuario->Permisos === 'si');
            
            if (!$tienePermisosEspeciales) {
                return redirect()->route('usuario.dashboard')
                    ->with('error', 'No tienes permisos para gestionar tipos de levantamiento');
            }

            // Obtener todos los tipos con conteo de campos
            $tipos = DB::table('tipos_levantamiento as tl')
                ->leftJoin('tipo_levantamiento_campos as tlc', 'tl.Id_Tipo_Levantamiento', '=', 'tlc.Id_Tipo_Levantamiento')
                ->select(
                    'tl.*',
                    DB::raw('COUNT(tlc.Id_Campo) as total_campos'),
                    DB::raw('SUM(CASE WHEN tlc.Activo = 1 THEN 1 ELSE 0 END) as campos_activos')
                )
                ->groupBy('tl.Id_Tipo_Levantamiento')
                ->orderBy('tl.Fecha_Creacion', 'DESC')
                ->get();

            // Contar cuántos levantamientos usan cada tipo
            foreach ($tipos as $tipo) {
                $tipo->levantamientos_count = DB::table('levantamientos')
                    ->where('Id_Tipo_Levantamiento', $tipo->Id_Tipo_Levantamiento)
                    ->count();
            }

            return view('usuario.tipos-levantamiento.index', compact('tipos', 'usuario', 'tienePermisosEspeciales'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $usuario = Auth::user();
        
        if ($usuario->Permisos !== 'si') {
            return redirect()->route('usuario.dashboard')
                ->with('error', 'No tienes permisos para crear tipos de levantamiento');
        }

        $tienePermisosEspeciales = ($usuario->Permisos === 'si');
        $iconos = $this->obtenerIconosDisponibles();
        
        return view('usuario.tipos-levantamiento.create', compact('usuario', 'iconos', 'tienePermisosEspeciales'));
    }

    /**
     * Guardar nuevo tipo
     */
    public function store(Request $request)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255',
                'icono' => 'required|string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $tipoId = DB::table('tipos_levantamiento')->insertGetId([
                'Nombre' => $request->nombre,
                'Descripcion' => $request->descripcion,
                'Icono' => $request->icono,
                'Activo' => 1,
                'Fecha_Creacion' => now()
            ]);

            // Registrar actividad
            DB::table('actividades')->insert([
                'Tipo' => 'levantamiento',
                'Accion' => 'tipo_creado',
                'Descripcion' => 'Nuevo tipo de levantamiento creado: ' . $request->nombre,
                'Referencia_Id' => $tipoId,
                'Id_Usuario' => $usuario->id_usuarios,
                'Fecha' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipo creado exitosamente',
                'tipo_id' => $tipoId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalles del tipo
     */
    public function show($id)
    {
        $usuario = Auth::user();
        
        if ($usuario->Permisos !== 'si') {
            return redirect()->route('usuario.dashboard')
                ->with('error', 'No tienes permisos');
        }

        $tipo = DB::table('tipos_levantamiento')
            ->where('Id_Tipo_Levantamiento', $id)
            ->first();

        if (!$tipo) {
            return back()->with('error', 'Tipo no encontrado');
        }

        // Obtener campos del formulario
        $campos = DB::table('tipo_levantamiento_campos')
            ->where('Id_Tipo_Levantamiento', $id)
            ->orderBy('Orden')
            ->get();

        // Contar levantamientos que usan este tipo
        $levantamientosCount = DB::table('levantamientos')
            ->where('Id_Tipo_Levantamiento', $id)
            ->count();

        $tienePermisosEspeciales = ($usuario->Permisos === 'si');

        return view('usuario.tipos-levantamiento.show', compact(
            'tipo',
            'campos',
            'levantamientosCount',
            'usuario',
            'tienePermisosEspeciales'
        ));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $usuario = Auth::user();
        
        if ($usuario->Permisos !== 'si') {
            return redirect()->route('usuario.dashboard')
                ->with('error', 'No tienes permisos');
        }

        $tipo = DB::table('tipos_levantamiento')
            ->where('Id_Tipo_Levantamiento', $id)
            ->first();

        if (!$tipo) {
            return back()->with('error', 'Tipo no encontrado');
        }

        $tienePermisosEspeciales = ($usuario->Permisos === 'si');
        $iconos = $this->obtenerIconosDisponibles();

        return view('usuario.tipos-levantamiento.edit', compact('tipo', 'usuario', 'iconos', 'tienePermisosEspeciales'));
    }

    /**
     * Actualizar tipo
     */
    public function update(Request $request, $id)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255',
                'icono' => 'required|string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::table('tipos_levantamiento')
                ->where('Id_Tipo_Levantamiento', $id)
                ->update([
                    'Nombre' => $request->nombre,
                    'Descripcion' => $request->descripcion,
                    'Icono' => $request->icono
                ]);

            // Registrar actividad
            DB::table('actividades')->insert([
                'Tipo' => 'levantamiento',
                'Accion' => 'tipo_actualizado',
                'Descripcion' => 'Tipo de levantamiento actualizado: ' . $request->nombre,
                'Referencia_Id' => $id,
                'Id_Usuario' => $usuario->id_usuarios,
                'Fecha' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estatus del tipo
     */
    public function toggleEstatus($id)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos'
                ], 403);
            }

            $tipo = DB::table('tipos_levantamiento')
                ->where('Id_Tipo_Levantamiento', $id)
                ->first();

            if (!$tipo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo no encontrado'
                ], 404);
            }

            $nuevoEstatus = $tipo->Activo ? 0 : 1;

            DB::table('tipos_levantamiento')
                ->where('Id_Tipo_Levantamiento', $id)
                ->update(['Activo' => $nuevoEstatus]);

            return response()->json([
                'success' => true,
                'message' => $nuevoEstatus ? 'Tipo activado' : 'Tipo desactivado',
                'nuevo_estatus' => $nuevoEstatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gestionar campos del formulario
     */
    public function gestionarCampos($id)
    {
        $usuario = Auth::user();
        
        if ($usuario->Permisos !== 'si') {
            return redirect()->route('usuario.dashboard')
                ->with('error', 'No tienes permisos');
        }

        $tipo = DB::table('tipos_levantamiento')
            ->where('Id_Tipo_Levantamiento', $id)
            ->first();

        if (!$tipo) {
            return back()->with('error', 'Tipo no encontrado');
        }

        $campos = DB::table('tipo_levantamiento_campos')
            ->where('Id_Tipo_Levantamiento', $id)
            ->orderBy('Orden')
            ->get();

        $tiposInput = [
            'text' => 'Texto',
            'number' => 'Número',
            'textarea' => 'Área de texto',
            'select' => 'Selector',
            'checkbox' => 'Casilla',
            'date' => 'Fecha',
            'email' => 'Email',
            'tel' => 'Teléfono'
        ];

        $tienePermisosEspeciales = ($usuario->Permisos === 'si');

        return view('usuario.tipos-levantamiento.campos', compact(
            'tipo',
            'campos',
            'tiposInput',
            'usuario',
            'tienePermisosEspeciales'
        ));
    }

    /**
     * Guardar nuevo campo
     */
    public function storeCampo(Request $request, $tipoId)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = Validator::make($request->all(), [
                'nombre_campo' => 'required|string|max:100',
                'etiqueta' => 'required|string|max:100',
                'tipo_input' => 'required|in:text,number,textarea,select,checkbox,date,email,tel',
                'placeholder' => 'nullable|string|max:255',
                'valor_default' => 'nullable|string',
                'orden' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $campoId = DB::table('tipo_levantamiento_campos')->insertGetId([
                'Id_Tipo_Levantamiento' => $tipoId,
                'Nombre_Campo' => $request->nombre_campo,
                'Etiqueta' => $request->etiqueta,
                'Tipo_Input' => $request->tipo_input,
                'Es_Requerido' => $request->has('es_requerido') ? 1 : 0,
                'Placeholder' => $request->placeholder,
                'Valor_Default' => $request->valor_default,
                'Orden' => $request->orden,
                'Activo' => 1,
                'Fecha_Creacion' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Campo creado exitosamente',
                'campo_id' => $campoId
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar campo
     */
    public function updateCampo(Request $request, $tipoId, $campoId)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            DB::table('tipo_levantamiento_campos')
                ->where('Id_Campo', $campoId)
                ->where('Id_Tipo_Levantamiento', $tipoId)
                ->update([
                    'Nombre_Campo' => $request->nombre_campo,
                    'Etiqueta' => $request->etiqueta,
                    'Tipo_Input' => $request->tipo_input,
                    'Es_Requerido' => $request->has('es_requerido') ? 1 : 0,
                    'Placeholder' => $request->placeholder,
                    'Valor_Default' => $request->valor_default,
                    'Orden' => $request->orden
                ]);

            return response()->json(['success' => true, 'message' => 'Campo actualizado']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar campo
     */
    public function destroyCampo($tipoId, $campoId)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            DB::table('tipo_levantamiento_campos')
                ->where('Id_Campo', $campoId)
                ->where('Id_Tipo_Levantamiento', $tipoId)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Campo eliminado']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle estatus de campo
     */
    public function toggleCampoEstatus($tipoId, $campoId)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $campo = DB::table('tipo_levantamiento_campos')
                ->where('Id_Campo', $campoId)
                ->first();

            $nuevoEstatus = $campo->Activo ? 0 : 1;

            DB::table('tipo_levantamiento_campos')
                ->where('Id_Campo', $campoId)
                ->update(['Activo' => $nuevoEstatus]);

            return response()->json([
                'success' => true,
                'message' => $nuevoEstatus ? 'Campo activado' : 'Campo desactivado',
                'nuevo_estatus' => $nuevoEstatus
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener iconos disponibles
     */
    private function obtenerIconosDisponibles()
    {
        return [
            'fa-video' => 'Cámara',
            'fa-server' => 'Servidor',
            'fa-wifi' => 'Wi-Fi',
            'fa-wrench' => 'Herramienta',
            'fa-exchange-alt' => 'Cambio',
            'fa-network-wired' => 'Red',
            'fa-clipboard-list' => 'Lista',
            'fa-cogs' => 'Configuración',
            'fa-tools' => 'Herramientas',
            'fa-laptop' => 'Laptop',
            'fa-desktop' => 'Desktop',
            'fa-mobile-alt' => 'Móvil',
            'fa-database' => 'Base de datos',
            'fa-shield-alt' => 'Seguridad',
            'fa-chart-line' => 'Análisis',
            'fa-airplay-audio' => 'Audio'
        ];
    }
}