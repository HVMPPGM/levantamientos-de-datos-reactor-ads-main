<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TipoLevantamientoController extends Controller
{
    /**
     * Lista de tipos de levantamiento
     */
    public function index()
    {
        try {
            $usuario = Auth::user();
            
            // Verificar que sea admin
            if ($usuario->Rol !== 'Admin') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'No tienes permisos para acceder a esta sección');
            }

            // Obtener todos los tipos con conteo de campos
            $tipos = DB::table('tipos_levantamiento as tl')
                ->leftJoin('tipo_levantamiento_campos as tlc', function($join) {
                    $join->on('tl.Id_Tipo_Levantamiento', '=', 'tlc.Id_Tipo_Levantamiento')
                         ->where('tlc.Activo', '=', 1);
                })
                ->select(
                    'tl.*',
                    DB::raw('COUNT(DISTINCT tlc.Id_Campo) as total_campos')
                )
                ->groupBy(
                    'tl.Id_Tipo_Levantamiento',
                    'tl.Nombre',
                    'tl.Descripcion',
                    'tl.Icono',
                    'tl.Activo',
                    'tl.Fecha_Creacion'
                )
                ->orderBy('tl.Fecha_Creacion', 'DESC')
                ->get();

            // Contar cuántos levantamientos usan cada tipo
            foreach ($tipos as $tipo) {
                $tipo->levantamientos_count = DB::table('levantamientos')
                    ->where('Id_Tipo_Levantamiento', $tipo->Id_Tipo_Levantamiento)
                    ->count();
            }

            return view('admin.tipos-levantamiento.index', compact('tipos', 'usuario'));
            
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
        
        if ($usuario->Rol !== 'Admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No tienes permisos');
        }

        $iconos = $this->obtenerIconosDisponibles();
        
        return view('admin.tipos-levantamiento.create', compact('usuario', 'iconos'));
    }

    /**
     * Guardar nuevo tipo
     */
    public function store(Request $request)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Rol !== 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255',
                'icono' => 'required|string|max:50'
            ], [
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.max' => 'El nombre no puede exceder 100 caracteres',
                'icono.required' => 'Debe seleccionar un icono'
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

            // Crear notificación
            DB::table('notificaciones')->insert([
                'Id_Usuario' => $usuario->id_usuarios,
                'Titulo' => 'Nuevo Tipo de Levantamiento',
                'Mensaje' => 'Se ha creado el tipo: ' . $request->nombre,
                'Tipo' => 'success',
                'Fecha' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de levantamiento creado exitosamente',
                'tipo_id' => $tipoId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalles del tipo
     */
    public function show($id)
    {
        $usuario = Auth::user();
        
        if ($usuario->Rol !== 'Admin') {
            return redirect()->route('admin.dashboard')
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

        return view('admin.tipos-levantamiento.show', compact(
            'tipo',
            'campos',
            'levantamientosCount',
            'usuario'
        ));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $usuario = Auth::user();
        
        if ($usuario->Rol !== 'Admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No tienes permisos');
        }

        $tipo = DB::table('tipos_levantamiento')
            ->where('Id_Tipo_Levantamiento', $id)
            ->first();

        if (!$tipo) {
            return back()->with('error', 'Tipo no encontrado');
        }

        $iconos = $this->obtenerIconosDisponibles();

        return view('admin.tipos-levantamiento.edit', compact('tipo', 'usuario', 'iconos'));
    }

    /**
     * Actualizar tipo
     */
    public function update(Request $request, $id)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Rol !== 'Admin') {
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipo actualizado exitosamente'
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
     * Cambiar estatus del tipo
     */
    public function toggleEstatus($id)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Rol !== 'Admin') {
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

            // Registrar actividad
            $accion = $nuevoEstatus ? 'activado' : 'desactivado';
            DB::table('actividades')->insert([
                'Tipo' => 'levantamiento',
                'Accion' => 'tipo_' . $accion,
                'Descripcion' => 'Tipo de levantamiento ' . $accion . ': ' . $tipo->Nombre,
                'Referencia_Id' => $id,
                'Id_Usuario' => $usuario->id_usuarios,
                'Fecha' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => $nuevoEstatus ? 'Tipo activado correctamente' : 'Tipo desactivado correctamente',
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
     * Eliminar tipo (solo si no tiene levantamientos asociados)
     */
    public function destroy($id)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Rol !== 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos'
                ], 403);
            }

            // Verificar si tiene levantamientos asociados
            $levantamientosCount = DB::table('levantamientos')
                ->where('Id_Tipo_Levantamiento', $id)
                ->count();

            if ($levantamientosCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar. Este tipo tiene ' . $levantamientosCount . ' levantamiento(s) asociado(s)'
                ], 400);
            }

            $tipo = DB::table('tipos_levantamiento')->where('Id_Tipo_Levantamiento', $id)->first();

            DB::beginTransaction();

            // Eliminar campos asociados
            DB::table('tipo_levantamiento_campos')
                ->where('Id_Tipo_Levantamiento', $id)
                ->delete();

            // Eliminar tipo
            DB::table('tipos_levantamiento')
                ->where('Id_Tipo_Levantamiento', $id)
                ->delete();

            // Registrar actividad
            DB::table('actividades')->insert([
                'Tipo' => 'levantamiento',
                'Accion' => 'tipo_eliminado',
                'Descripcion' => 'Tipo de levantamiento eliminado: ' . ($tipo->Nombre ?? 'Desconocido'),
                'Referencia_Id' => $id,
                'Id_Usuario' => $usuario->id_usuarios,
                'Fecha' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipo eliminado exitosamente'
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
     * Gestionar campos del formulario
     */
    public function gestionarCampos($id)
    {
        $usuario = Auth::user();
        
        if ($usuario->Rol !== 'Admin') {
            return redirect()->route('admin.dashboard')
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

        return view('admin.tipos-levantamiento.campos', compact(
            'tipo',
            'campos',
            'tiposInput',
            'usuario'
        ));
    }

    /**
     * Guardar nuevo campo
     */
    public function storeCampo(Request $request, $tipoId)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Rol !== 'Admin') {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = Validator::make($request->all(), [
                'nombre_campo' => 'required|string|max:100',
                'etiqueta' => 'required|string|max:100',
                'tipo_input' => 'required|in:text,number,textarea,select,checkbox,date,email,tel',
                'placeholder' => 'nullable|string|max:255',
                'valor_default' => 'nullable|string',
                'orden' => 'required|integer|min:0'
            ], [
                'nombre_campo.required' => 'El nombre del campo es obligatorio',
                'etiqueta.required' => 'La etiqueta es obligatoria',
                'tipo_input.required' => 'El tipo de input es obligatorio',
                'orden.required' => 'El orden es obligatorio'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

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

            // Registrar actividad
            DB::table('actividades')->insert([
                'Tipo' => 'levantamiento',
                'Accion' => 'campo_creado',
                'Descripcion' => 'Nuevo campo creado: ' . $request->etiqueta,
                'Referencia_Id' => $tipoId,
                'Id_Usuario' => $usuario->id_usuarios,
                'Fecha' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Campo creado exitosamente',
                'campo_id' => $campoId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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
            
            if ($usuario->Rol !== 'Admin') {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = Validator::make($request->all(), [
                'nombre_campo' => 'required|string|max:100',
                'etiqueta' => 'required|string|max:100',
                'tipo_input' => 'required|in:text,number,textarea,select,checkbox,date,email,tel',
                'orden' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
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

            return response()->json(['success' => true, 'message' => 'Campo actualizado exitosamente']);

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
            
            if ($usuario->Rol !== 'Admin') {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            DB::beginTransaction();

            // Obtener info del campo antes de eliminar
            $campo = DB::table('tipo_levantamiento_campos')
                ->where('Id_Campo', $campoId)
                ->first();

            // Eliminar valores dinámicos asociados
            DB::table('levantamiento_valores_dinamicos')
                ->where('Id_Campo', $campoId)
                ->delete();

            // Eliminar campo
            DB::table('tipo_levantamiento_campos')
                ->where('Id_Campo', $campoId)
                ->where('Id_Tipo_Levantamiento', $tipoId)
                ->delete();

            // Registrar actividad
            DB::table('actividades')->insert([
                'Tipo' => 'levantamiento',
                'Accion' => 'campo_eliminado',
                'Descripcion' => 'Campo eliminado: ' . ($campo->Etiqueta ?? 'Desconocido'),
                'Referencia_Id' => $tipoId,
                'Id_Usuario' => $usuario->id_usuarios,
                'Fecha' => now()
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Campo eliminado exitosamente']);

        } catch (\Exception $e) {
            DB::rollBack();
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
            
            if ($usuario->Rol !== 'Admin') {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $campo = DB::table('tipo_levantamiento_campos')
                ->where('Id_Campo', $campoId)
                ->first();

            if (!$campo) {
                return response()->json(['success' => false, 'message' => 'Campo no encontrado'], 404);
            }

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
     * Reordenar campos
     */
    public function reordenarCampos(Request $request, $tipoId)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Rol !== 'Admin') {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            DB::beginTransaction();

            foreach ($request->orden as $index => $campoId) {
                DB::table('tipo_levantamiento_campos')
                    ->where('Id_Campo', $campoId)
                    ->where('Id_Tipo_Levantamiento', $tipoId)
                    ->update(['Orden' => $index + 1]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Campos reordenados exitosamente']);

        } catch (\Exception $e) {
            DB::rollBack();
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
            'fa-volume-high' => 'Audio',
            'fa-microphone' => 'Micrófono',
            'fa-headphones' => 'Audífonos',
            'fa-phone' => 'Teléfono',
            'fa-envelope' => 'Correo',
            'fa-calendar' => 'Calendario',
            'fa-clock' => 'Reloj',
            'fa-map-marker-alt' => 'Ubicación',
            'fa-file-alt' => 'Documento',
            'fa-folder' => 'Carpeta',
            'fa-image' => 'Imagen',
            'fa-camera' => 'Cámara Fotográfica',
            'fa-video-camera' => 'Videocámara',
            'fa-print' => 'Impresora',
            'fa-barcode' => 'Código de barras'
        ];
    }
}