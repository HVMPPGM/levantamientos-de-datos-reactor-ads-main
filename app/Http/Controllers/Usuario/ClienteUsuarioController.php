<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClienteUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $usuario = Auth::user();
            
            $clientes = DB::table('clientes as c')
                ->leftJoin('direccion as d', 'c.Id_Direccion', '=', 'd.Id_Direccion')
                ->leftJoin('cliente_articulos as ca', function($join) {
                    $join->on('c.Id_Cliente', '=', 'ca.Id_Cliente')
                         ->where('ca.Es_Principal', '=', 1);
                })
                ->leftJoin('articulos as a', 'ca.Id_Articulo', '=', 'a.Id_Articulos')
                ->select(
                    'c.*',
                    'd.Estado',
                    'd.Municipio',
                    'd.Colonia',
                    'a.Nombre as ArticuloPrincipal'
                )
                ->orderBy('c.fecha_registro', 'DESC')
                ->get();

            foreach ($clientes as $cliente) {
                $levantamientos = DB::table('levantamientos')
                    ->where('Id_Cliente', $cliente->Id_Cliente)
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN estatus = "Pendiente" THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN estatus = "En Proceso" THEN 1 ELSE 0 END) as en_proceso,
                        SUM(CASE WHEN estatus = "Completado" THEN 1 ELSE 0 END) as completados
                    ')
                    ->first();
                
                $cliente->levantamientos = $levantamientos;
            }

            $tienePermisosEspeciales = $usuario->Permisos === 'si';

            return view('usuario.clientesU', compact('clientes', 'usuario', 'tienePermisosEspeciales'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar clientes: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return redirect()->route('usuario.clientesU')
                    ->with('error', 'No tienes permisos para crear clientes');
            }

            $articulos = DB::table('articulos as a')
                ->join('marcas as m', 'a.Id_Marca', '=', 'm.Id_Marca')
                ->leftJoin('modelo as mo', 'a.Id_Modelo', '=', 'mo.Id_Modelo')
                ->select(
                    'a.Id_Articulos',
                    'a.Nombre',
                    'a.Descripcion',
                    'm.Nombre as Marca',
                    'mo.Nombre as Modelo'
                )
                ->orderBy('a.Nombre')
                ->get();

            return view('usuario.clientes.create', compact('articulos', 'usuario'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para crear clientes'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'nombre'                  => 'required|string|max:100',
                'telefono'                => 'required|string|max:25',
                'correo'                  => 'nullable|email|max:255',
                'pais'                    => 'required|string|max:100',
                'estado'                  => 'required|string|max:100',
                'municipio'               => 'required|string|max:100',
                'codigo_postal'           => 'required|integer',
                'colonia'                 => 'nullable|string|max:100',
                'calle'                   => 'nullable|string|max:100',
                'no_interno'              => 'nullable|integer',
                'no_externo'              => 'nullable|integer',
                'articulo_principal'      => 'nullable|exists:articulos,Id_Articulos',
                'articulos_adicionales'   => 'nullable|array|max:9',
                'articulos_adicionales.*' => 'exists:articulos,Id_Articulos',
            ], [
                'nombre.required'             => 'El nombre del cliente es obligatorio',
                'telefono.required'           => 'El teléfono es obligatorio',
                'pais.required'               => 'El país es obligatorio',
                'estado.required'             => 'El estado es obligatorio',
                'municipio.required'          => 'La ciudad es obligatoria',
                'codigo_postal.required'      => 'El código postal es obligatorio',
                'correo.email'                => 'El correo debe ser válido',
                'articulos_adicionales.max'   => 'Solo puede seleccionar hasta 9 artículos adicionales',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            // ── Verificar duplicados ──────────────────────────────────────────────
            $camposDuplicados = $this->verificarDuplicadoCliente(
                $request->nombre,
                $request->telefono,
                $request->correo
            );

            if (!empty($camposDuplicados)) {
                return response()->json([
                    'success'           => false,
                    'message'           => 'Ya existe un cliente con el mismo ' . implode(', ', $camposDuplicados) . '.',
                    'campos_duplicados' => $camposDuplicados,
                ], 422);
            }

            DB::beginTransaction();

            // 1. Crear dirección
            $direccionId = DB::table('direccion')->insertGetId([
                'Pais'          => $request->pais,
                'Estado'        => $request->estado,
                'Municipio'     => $request->municipio,
                'Colonia'       => $request->colonia,
                'calle'         => $request->calle,
                'Codigo_Postal' => $request->codigo_postal,
                'No_In'         => $request->no_interno,
                'No_Ex'         => $request->no_externo,
            ]);

            // 2. Crear cliente
            $clienteId = DB::table('clientes')->insertGetId([
                'Nombre'         => $request->nombre,
                'Correo'         => $request->correo,
                'Telefono'       => $request->telefono,
                'Estatus'        => 'Activo',
                'Id_Articulos'   => $request->articulo_principal ?? null,
                'Id_Direccion'   => $direccionId,
                'fecha_registro' => now(),
            ]);

            // 3. Artículo principal
            if ($request->filled('articulo_principal')) {
                DB::table('cliente_articulos')->insert([
                    'Id_Cliente'     => $clienteId,
                    'Id_Articulo'    => $request->articulo_principal,
                    'Es_Principal'   => 1,
                    'Fecha_Agregado' => now(),
                ]);
            }

            // 4. Artículos adicionales
            if ($request->has('articulos_adicionales') && is_array($request->articulos_adicionales)) {
                foreach ($request->articulos_adicionales as $articuloId) {
                    if ($articuloId != $request->articulo_principal) {
                        DB::table('cliente_articulos')->insert([
                            'Id_Cliente'     => $clienteId,
                            'Id_Articulo'    => $articuloId,
                            'Es_Principal'   => 0,
                            'Fecha_Agregado' => now(),
                        ]);
                    }
                }
            }

            // 5. Actividad
            DB::table('actividades')->insert([
                'Tipo'          => 'cliente',
                'Accion'        => 'creado',
                'Descripcion'   => 'Nuevo cliente registrado: ' . $request->nombre,
                'Referencia_Id' => $clienteId,
                'Id_Usuario'    => $usuario->id_usuarios,
                'Fecha'         => now(),
            ]);

            // 6. Notificar admins
            $admins = DB::table('usuarios')->where('Rol', 'Admin')->get();
            foreach ($admins as $admin) {
                DB::table('notificaciones')->insert([
                    'Id_Usuario' => $admin->id_usuarios,
                    'Titulo'     => 'Nuevo Cliente',
                    'Mensaje'    => 'Se ha registrado el cliente ' . $request->nombre,
                    'Tipo'       => 'info',
                    'Fecha'      => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Cliente creado exitosamente',
                'cliente_id' => $clienteId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $usuario = Auth::user();

            $cliente = DB::table('clientes as c')
                ->join('direccion as d', 'c.Id_Direccion', '=', 'd.Id_Direccion')
                ->where('c.Id_Cliente', $id)
                ->select('c.*', 'd.*')
                ->first();

            if (!$cliente) {
                return back()->with('error', 'Cliente no encontrado');
            }

            $articulos = DB::table('cliente_articulos as ca')
                ->join('articulos as a', 'ca.Id_Articulo', '=', 'a.Id_Articulos')
                ->join('marcas as m', 'a.Id_Marca', '=', 'm.Id_Marca')
                ->leftJoin('modelo as mo', 'a.Id_Modelo', '=', 'mo.Id_Modelo')
                ->where('ca.Id_Cliente', $id)
                ->select('a.*', 'm.Nombre as Marca', 'mo.Nombre as Modelo', 'ca.Es_Principal')
                ->orderBy('ca.Es_Principal', 'DESC')
                ->get();

            $levantamientos = DB::table('levantamientos as l')
                ->leftJoin('tipos_levantamiento as tl', 'l.Id_Tipo_Levantamiento', '=', 'tl.Id_Tipo_Levantamiento')
                ->leftJoin('usuarios as u', 'l.id_usuarios', '=', 'u.id_usuarios')
                ->where('l.Id_Cliente', $id)
                ->select(
                    'l.*',
                    'tl.Nombre as TipoLevantamiento',
                    'tl.Icono',
                    DB::raw("CONCAT(u.Nombres, ' ', COALESCE(u.ApellidosPat, '')) as NombreUsuario")
                )
                ->orderBy('l.fecha_creacion', 'DESC')
                ->get();

            $levantamientosPorEstatus = [
                'Completado' => $levantamientos->where('estatus', 'Completado'),
                'En Proceso' => $levantamientos->where('estatus', 'En Proceso'),
                'Pendiente'  => $levantamientos->where('estatus', 'Pendiente'),
                'Cancelado'  => $levantamientos->where('estatus', 'Cancelado'),
            ];

            $estadisticas = [
                'total'       => $levantamientos->count(),
                'completados' => $levantamientos->where('estatus', 'Completado')->count(),
                'en_proceso'  => $levantamientos->where('estatus', 'En Proceso')->count(),
                'pendientes'  => $levantamientos->where('estatus', 'Pendiente')->count(),
                'cancelados'  => $levantamientos->where('estatus', 'Cancelado')->count(),
            ];

            return view('usuario.clientes.show', compact(
                'cliente', 'articulos', 'levantamientosPorEstatus', 'estadisticas', 'usuario'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return redirect()->route('usuario.clientesU')
                    ->with('error', 'No tienes permisos para editar clientes');
            }

            $cliente = DB::table('clientes as c')
                ->join('direccion as d', 'c.Id_Direccion', '=', 'd.Id_Direccion')
                ->where('c.Id_Cliente', $id)
                ->select('c.*', 'd.*')
                ->first();

            if (!$cliente) {
                return back()->with('error', 'Cliente no encontrado');
            }

            $clienteArticulos = DB::table('cliente_articulos')
                ->where('Id_Cliente', $id)
                ->pluck('Id_Articulo')
                ->toArray();

            $articuloPrincipal = DB::table('cliente_articulos')
                ->where('Id_Cliente', $id)
                ->where('Es_Principal', 1)
                ->value('Id_Articulo');

            $articulos = DB::table('articulos as a')
                ->join('marcas as m', 'a.Id_Marca', '=', 'm.Id_Marca')
                ->leftJoin('modelo as mo', 'a.Id_Modelo', '=', 'mo.Id_Modelo')
                ->select('a.Id_Articulos', 'a.Nombre', 'a.Descripcion', 'm.Nombre as Marca', 'mo.Nombre as Modelo')
                ->orderBy('a.Nombre')
                ->get();

            return view('usuario.clientes.edit', compact(
                'cliente', 'articulos', 'clienteArticulos', 'articuloPrincipal', 'usuario'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para editar clientes'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'nombre'                  => 'required|string|max:100',
                'telefono'                => 'required|string|max:25',
                'correo'                  => 'nullable|email|max:255',
                'pais'                    => 'required|string|max:100',
                'estado'                  => 'required|string|max:100',
                'municipio'               => 'required|string|max:100',
                'codigo_postal'           => 'required|integer',
                'colonia'                 => 'nullable|string|max:100',
                'calle'                   => 'nullable|string|max:100',
                'no_interno'              => 'nullable|integer',
                'no_externo'              => 'nullable|integer',
                'articulo_principal'      => 'nullable|exists:articulos,Id_Articulos',
                'articulos_adicionales'   => 'nullable|array|max:9',
                'articulos_adicionales.*' => 'exists:articulos,Id_Articulos',
            ], [
                'nombre.required'           => 'El nombre del cliente es obligatorio',
                'telefono.required'         => 'El teléfono es obligatorio',
                'pais.required'             => 'El país es obligatorio',
                'estado.required'           => 'El estado es obligatorio',
                'municipio.required'        => 'La ciudad es obligatoria',
                'codigo_postal.required'    => 'El código postal es obligatorio',
                'correo.email'              => 'El correo debe ser válido',
                'articulos_adicionales.max' => 'Solo puede seleccionar hasta 9 artículos adicionales',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            // ── Verificar duplicados excluyendo el cliente actual ─────────────────
            $camposDuplicados = $this->verificarDuplicadoCliente(
                $request->nombre,
                $request->telefono,
                $request->correo,
                $id
            );

            if (!empty($camposDuplicados)) {
                return response()->json([
                    'success'           => false,
                    'message'           => 'Ya existe un cliente con el mismo ' . implode(', ', $camposDuplicados) . '.',
                    'campos_duplicados' => $camposDuplicados,
                ], 422);
            }

            DB::beginTransaction();

            $cliente = DB::table('clientes')->where('Id_Cliente', $id)->first();
            if (!$cliente) {
                throw new \Exception('Cliente no encontrado');
            }

            // 1. Actualizar dirección
            DB::table('direccion')
                ->where('Id_Direccion', $cliente->Id_Direccion)
                ->update([
                    'Pais'          => $request->pais,
                    'Estado'        => $request->estado,
                    'Municipio'     => $request->municipio,
                    'Colonia'       => $request->colonia,
                    'calle'         => $request->calle,
                    'Codigo_Postal' => $request->codigo_postal,
                    'No_In'         => $request->no_interno,
                    'No_Ex'         => $request->no_externo,
                ]);

            // 2. Actualizar cliente
            DB::table('clientes')
                ->where('Id_Cliente', $id)
                ->update([
                    'Nombre'       => $request->nombre,
                    'Correo'       => $request->correo,
                    'Telefono'     => $request->telefono,
                    'Id_Articulos' => $request->articulo_principal ?? null,
                ]);

            // 3. Limpiar artículos anteriores
            DB::table('cliente_articulos')->where('Id_Cliente', $id)->delete();

            // 4. Artículo principal
            if ($request->filled('articulo_principal')) {
                DB::table('cliente_articulos')->insert([
                    'Id_Cliente'     => $id,
                    'Id_Articulo'    => $request->articulo_principal,
                    'Es_Principal'   => 1,
                    'Fecha_Agregado' => now(),
                ]);
            }

            // 5. Artículos adicionales
            if ($request->has('articulos_adicionales') && is_array($request->articulos_adicionales)) {
                foreach ($request->articulos_adicionales as $articuloId) {
                    if ($articuloId != $request->articulo_principal) {
                        DB::table('cliente_articulos')->insert([
                            'Id_Cliente'     => $id,
                            'Id_Articulo'    => $articuloId,
                            'Es_Principal'   => 0,
                            'Fecha_Agregado' => now(),
                        ]);
                    }
                }
            }

            // 6. Actividad
            DB::table('actividades')->insert([
                'Tipo'          => 'cliente',
                'Accion'        => 'actualizado',
                'Descripcion'   => 'Cliente actualizado: ' . $request->nombre,
                'Referencia_Id' => $id,
                'Id_Usuario'    => $usuario->id_usuarios,
                'Fecha'         => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estatus del cliente
     */
    public function toggleEstatus($id)
    {
        try {
            $usuario = Auth::user();
            
            if ($usuario->Permisos !== 'si') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para cambiar el estatus'
                ], 403);
            }

            $cliente = DB::table('clientes')->where('Id_Cliente', $id)->first();
            
            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $nuevoEstatus = $cliente->Estatus === 'Activo' ? 'Inactivo' : 'Activo';

            DB::table('clientes')
                ->where('Id_Cliente', $id)
                ->update(['Estatus' => $nuevoEstatus]);

            DB::table('actividades')->insert([
                'Tipo'          => 'cliente',
                'Accion'        => $nuevoEstatus === 'Activo' ? 'reactivado' : 'desactivado',
                'Descripcion'   => 'Cliente ' . ($nuevoEstatus === 'Activo' ? 'reactivado' : 'desactivado') . ': ' . $cliente->Nombre,
                'Referencia_Id' => $id,
                'Id_Usuario'    => $usuario->id_usuarios,
                'Fecha'         => now(),
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'Estatus actualizado correctamente',
                'nuevoEstatus' => $nuevoEstatus,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // HELPER: verificar duplicado nombre / teléfono / correo
    // ════════════════════════════════════════════════════════════════
    private function verificarDuplicadoCliente($nombre, $telefono, $correo = null, $excluirId = null)
    {
        $camposDuplicados = [];

        $query = DB::table('clientes');
        if ($excluirId) {
            $query->where('Id_Cliente', '!=', $excluirId);
        }

        // Verificar nombre
        $existeNombre = (clone $query)
            ->whereRaw('LOWER(TRIM(Nombre)) = ?', [strtolower(trim($nombre))])
            ->exists();
        if ($existeNombre) $camposDuplicados[] = 'nombre';

        // Verificar teléfono
        $existeTelefono = (clone $query)
            ->whereRaw('TRIM(Telefono) = ?', [trim($telefono)])
            ->exists();
        if ($existeTelefono) $camposDuplicados[] = 'teléfono';

        // Verificar correo (solo si se proporcionó)
        if (!empty($correo)) {
            $existeCorreo = (clone $query)
                ->whereNotNull('Correo')
                ->whereRaw('LOWER(TRIM(Correo)) = ?', [strtolower(trim($correo))])
                ->exists();
            if ($existeCorreo) $camposDuplicados[] = 'correo';
        }

        return $camposDuplicados;
    }
}