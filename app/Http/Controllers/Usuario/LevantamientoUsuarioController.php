<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LevantamientoUsuarioController extends Controller
{
    /**
     * Listado de levantamientos del usuario autenticado
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();
        $estatus = $request->get('estatus', 'all');

        $query = DB::table('levantamientos as l')
            ->leftJoin('clientes as c',           'l.Id_Cliente',           '=', 'c.Id_Cliente')
            ->leftJoin('tipos_levantamiento as tl','l.Id_Tipo_Levantamiento','=', 'tl.Id_Tipo_Levantamiento')
            ->leftJoin('usuarios as u',            'l.id_usuarios',          '=', 'u.id_usuarios')
            ->select([
                'l.Id_Levantamiento', 'l.estatus', 'l.fecha_creacion',
                'c.Nombre as cliente_nombre',
                'tl.Nombre as tipo_nombre',
                'u.Nombres as usuario_nombre', 'u.ApellidosPat as usuario_apellido',
            ])
            ->where('l.id_usuarios', $usuario->id_usuarios)
            ->orderBy('l.fecha_creacion', 'desc');

        if ($estatus !== 'all') {
            $query->where('l.estatus', $estatus);
        }

        $levantamientos = $query->get();

        $contadores = [
            'todos'      => DB::table('levantamientos')->where('id_usuarios', $usuario->id_usuarios)->count(),
            'pendiente'  => DB::table('levantamientos')->where('id_usuarios', $usuario->id_usuarios)->where('estatus', 'Pendiente')->count(),
            'proceso'    => DB::table('levantamientos')->where('id_usuarios', $usuario->id_usuarios)->where('estatus', 'En Proceso')->count(),
            'completado' => DB::table('levantamientos')->where('id_usuarios', $usuario->id_usuarios)->where('estatus', 'Completado')->count(),
            'cancelado'  => DB::table('levantamientos')->where('id_usuarios', $usuario->id_usuarios)->where('estatus', 'Cancelado')->count(),
        ];

        $tiposLevantamiento = DB::table('tipos_levantamiento')
            ->where('Activo', 1)->orderBy('Nombre')->get();

        $clientes = DB::table('clientes')
            ->where('Estatus', 'Activo')->orderBy('Nombre')->get();

        return view('usuario.levantamientos.index', compact(
            'levantamientos', 'contadores', 'estatus',
            'tiposLevantamiento', 'clientes', 'usuario'
        ));
    }

    /**
     * Formulario dinámico de un tipo de levantamiento (AJAX)
     */
    public function getFormularioTipo($tipoId)
    {
        try {
            $campos = DB::table('tipo_levantamiento_campos')
                ->where('Id_Tipo_Levantamiento', $tipoId)
                ->where('Activo', 1)
                ->orderBy('Orden')
                ->get();

            $marcas  = DB::table('marcas')->orderBy('Nombre')->get();
            $modelos = DB::table('modelo')->orderBy('Nombre')->get();
            $serviciosProfesionales = DB::table('tipo_servicioprofecional')->orderBy('Nombre')->get();

            return response()->json([
                'success' => true,
                'campos'  => $campos,
                'marcas'  => $marcas,
                'modelos' => $modelos,
                'serviciosProfesionales' => $serviciosProfesionales,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de tipo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al cargar el formulario'], 500);
        }
    }

    /**
     * Artículos de un cliente (AJAX)
     */
    public function getArticulosCliente($clienteId)
    {
        try {
            $articulosCliente = DB::table('cliente_articulos as ca')
                ->join('articulos as a',   'ca.Id_Articulo', '=', 'a.Id_Articulos')
                ->leftJoin('marcas as m',  'a.Id_Marca',     '=', 'm.Id_Marca')
                ->leftJoin('modelo as mo', 'a.Id_Modelo',    '=', 'mo.Id_Modelo')
                ->where('ca.Id_Cliente', $clienteId)
                ->select([
                    'a.Id_Articulos', 'a.Nombre', 'a.Descripcion',
                    'a.Id_Marca', 'a.Id_Modelo', 'a.modelo_por_definir',
                    'm.Nombre as marca_nombre', 'mo.Nombre as modelo_nombre',
                    'ca.Es_Principal',
                ])
                ->orderByDesc('ca.Es_Principal')
                ->orderBy('a.Nombre')
                ->get();

            return response()->json($articulosCliente);
        } catch (\Exception $e) {
            Log::error('Error al cargar artículos del cliente: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Buscar artículos existentes (AJAX)
     */
    public function buscarArticulos(Request $request)
    {
        try {
            $q         = $request->get('q', '');
            $clienteId = $request->get('cliente_id');

            if (strlen($q) < 2) {
                return response()->json([]);
            }

            $articulos = DB::table('articulos as a')
                ->leftJoin('marcas as m',  'a.Id_Marca',  '=', 'm.Id_Marca')
                ->leftJoin('modelo as mo', 'a.Id_Modelo', '=', 'mo.Id_Modelo')
                ->where(function ($query) use ($q) {
                    $query->where('a.Nombre',  'like', "%{$q}%")
                          ->orWhere('m.Nombre', 'like', "%{$q}%");
                })
                ->select([
                    'a.Id_Articulos', 'a.Nombre', 'a.Descripcion',
                    'a.modelo_por_definir',
                    'm.Nombre as marca_nombre',
                    'mo.Nombre as modelo_nombre',
                ])
                ->orderBy('a.Nombre')
                ->limit(20)
                ->get();

            if ($clienteId) {
                $articulosDelCliente = DB::table('cliente_articulos')
                    ->where('Id_Cliente', $clienteId)
                    ->pluck('Id_Articulo')
                    ->toArray();

                $articulos = $articulos->map(function ($art) use ($articulosDelCliente) {
                    $art->ya_en_cliente = in_array($art->Id_Articulos, $articulosDelCliente);
                    return $art;
                });
            }

            return response()->json($articulos);
        } catch (\Exception $e) {
            Log::error('Error al buscar artículos: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Asociar artículo existente a un cliente
     */
    public function asociarArticuloACliente(Request $request)
    {
        try {
            $request->validate([
                'cliente_id'  => 'required|exists:clientes,Id_Cliente',
                'articulo_id' => 'required|exists:articulos,Id_Articulos',
            ]);

            $yaExiste = DB::table('cliente_articulos')
                ->where('Id_Cliente',  $request->cliente_id)
                ->where('Id_Articulo', $request->articulo_id)
                ->exists();

            if ($yaExiste) {
                return response()->json(['success' => true, 'ya_asociado' => true]);
            }

            DB::table('cliente_articulos')->insert([
                'Id_Cliente'     => $request->cliente_id,
                'Id_Articulo'    => $request->articulo_id,
                'Es_Principal'   => 0,
                'Fecha_Agregado' => now(),
            ]);

            return response()->json(['success' => true, 'ya_asociado' => false]);
        } catch (\Exception $e) {
            Log::error('Error al asociar artículo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al asociar el artículo'], 500);
        }
    }

    /**
     * Crear nuevo levantamiento
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $usuario = Auth::user();

            $request->validate([
                'tipo_levantamiento_id' => 'required|exists:tipos_levantamiento,Id_Tipo_Levantamiento',
                'cliente_id'            => 'required|exists:clientes,Id_Cliente',
                'articulos'             => 'required|array|min:1',
            ]);

            $tienePorDefinir = collect($request->articulos)->contains(fn($a) => !empty($a['modelo_por_definir']));

            $levantamientoId = DB::table('levantamientos')->insertGetId([
                'Id_Tipo_Levantamiento' => $request->tipo_levantamiento_id,
                'Id_Cliente'            => $request->cliente_id,
                'id_usuarios'           => $usuario->id_usuarios,
                'estatus'               => 'Pendiente',
                'modelo_por_definir'    => $tienePorDefinir ? 1 : 0,
                'fecha_creacion'        => now(),
            ]);

            foreach ($request->articulos as $articulo) {
                DB::table('levantamiento_articulos')->insert([
                    'Id_Levantamiento'   => $levantamientoId,
                    'Id_Articulo'        => $articulo['id_articulo'],
                    'Cantidad'           => $articulo['cantidad']        ?? 1,
                    'Precio_Unitario'    => $articulo['precio_unitario'] ?? 0,
                    'Subtotal'           => ($articulo['cantidad'] ?? 1) * ($articulo['precio_unitario'] ?? 0),
                    'Notas'              => $articulo['notas']           ?? null,
                    'modelo_por_definir' => !empty($articulo['modelo_por_definir']) ? 1 : 0,
                    'fecha_registro'     => now(),
                ]);

                DB::table('articulos')
                    ->where('Id_Articulos', $articulo['id_articulo'])
                    ->increment('veces_solicitado');
            }

            $campos = DB::table('tipo_levantamiento_campos')
                ->where('Id_Tipo_Levantamiento', $request->tipo_levantamiento_id)
                ->where('Activo', 1)->get();

            $camposEspeciales = ['articulo','cantidad','marca','modelo','precio_unitario','servicio_profesional'];

            foreach ($campos as $campo) {
                if (in_array($campo->Nombre_Campo, $camposEspeciales)) continue;
                if ($request->has($campo->Nombre_Campo)) {
                    $valor = $request->input($campo->Nombre_Campo);
                    if ($valor !== null && $valor !== '') {
                        DB::table('levantamiento_valores_dinamicos')->insert([
                            'Id_Levantamiento' => $levantamientoId,
                            'Id_Campo'         => $campo->Id_Campo,
                            'Valor'            => $valor,
                            'Fecha_Registro'   => now(),
                        ]);
                    }
                }
            }

            DB::table('actividades')->insert([
                'Tipo'          => 'levantamiento',
                'Accion'        => 'creado',
                'Descripcion'   => 'Nuevo levantamiento creado: LEV-' . str_pad($levantamientoId, 5, '0', STR_PAD_LEFT),
                'Referencia_Id' => $levantamientoId,
                'Id_Usuario'    => $usuario->id_usuarios,
                'Fecha'         => now(),
            ]);

            $admins = DB::table('usuarios')->where('Rol', 'Admin')->where('Estatus', 'Activo')->pluck('id_usuarios');
            foreach ($admins as $adminId) {
                DB::table('notificaciones')->insert([
                    'Id_Usuario' => $adminId,
                    'Titulo'     => 'Nuevo Levantamiento',
                    'Mensaje'    => 'Se ha creado el levantamiento LEV-' . str_pad($levantamientoId, 5, '0', STR_PAD_LEFT),
                    'Tipo'       => 'info',
                    'Fecha'      => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success'          => true,
                'message'          => 'Levantamiento creado exitosamente',
                'levantamiento_id' => $levantamientoId,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Datos inválidos', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear levantamiento: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear el levantamiento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Detalle de un levantamiento
     */
    public function show($id)
    {
        try {
            $usuario = Auth::user();

            $levantamiento = DB::table('levantamientos as l')
                ->leftJoin('clientes as c',           'l.Id_Cliente',           '=', 'c.Id_Cliente')
                ->leftJoin('tipos_levantamiento as tl','l.Id_Tipo_Levantamiento','=', 'tl.Id_Tipo_Levantamiento')
                ->leftJoin('usuarios as u',            'l.id_usuarios',          '=', 'u.id_usuarios')
                ->select([
                    'l.*',
                    'c.Nombre as cliente_nombre', 'c.Correo as cliente_correo', 'c.Telefono as cliente_telefono',
                    'tl.Nombre as tipo_nombre', 'tl.Descripcion as tipo_descripcion',
                    'u.Nombres as usuario_nombre', 'u.ApellidosPat as usuario_apellido',
                ])
                ->where('l.Id_Levantamiento', $id)
                ->where('l.id_usuarios', $usuario->id_usuarios)
                ->first();

            if (!$levantamiento) abort(404, 'Levantamiento no encontrado');

            $articulos = DB::table('levantamiento_articulos as la')
                ->join('articulos as a',   'la.Id_Articulo', '=', 'a.Id_Articulos')
                ->leftJoin('marcas as m',  'a.Id_Marca',     '=', 'm.Id_Marca')
                ->leftJoin('modelo as mo', 'a.Id_Modelo',    '=', 'mo.Id_Modelo')
                ->where('la.Id_Levantamiento', $id)
                ->select([
                    'la.*',
                    'a.Nombre as articulo_nombre', 'a.Descripcion as articulo_descripcion',
                    'm.Nombre as marca_nombre', 'mo.Nombre as modelo_nombre',
                ])
                ->get();

            $valoresDinamicos = DB::table('levantamiento_valores_dinamicos as lvd')
                ->join('tipo_levantamiento_campos as tlc', 'lvd.Id_Campo', '=', 'tlc.Id_Campo')
                ->where('lvd.Id_Levantamiento', $id)
                ->select(['tlc.Nombre_Campo', 'tlc.Etiqueta', 'tlc.Tipo_Input', 'lvd.Valor'])
                ->get();

            return view('usuario.levantamientos.show', compact('levantamiento', 'articulos', 'valoresDinamicos'));
        } catch (\Exception $e) {
            Log::error('Error al ver detalle: ' . $e->getMessage());
            abort(500, 'Error al cargar el levantamiento');
        }
    }

    /**
     * Formulario de edición
     */
    public function edit($id)
    {
        $usuario = Auth::user();

        $levantamiento = DB::table('levantamientos as l')
            ->leftJoin('clientes as c',           'l.Id_Cliente',           '=', 'c.Id_Cliente')
            ->leftJoin('tipos_levantamiento as tl','l.Id_Tipo_Levantamiento','=', 'tl.Id_Tipo_Levantamiento')
            ->select(['l.*', 'c.Nombre as cliente_nombre', 'tl.Nombre as tipo_nombre'])
            ->where('l.Id_Levantamiento', $id)
            ->where('l.id_usuarios', $usuario->id_usuarios)
            ->first();

        if (!$levantamiento) abort(404, 'Levantamiento no encontrado');

        if (in_array($levantamiento->estatus, ['Cancelado', 'Completado'])) {
            return redirect()->route('usuario.levantamientos.show', $id)
                ->with('error', 'No se pueden editar levantamientos ' . strtolower($levantamiento->estatus) . 's');
        }

        $articulos = DB::table('levantamiento_articulos as la')
            ->join('articulos as a',   'la.Id_Articulo', '=', 'a.Id_Articulos')
            ->leftJoin('marcas as m',  'a.Id_Marca',     '=', 'm.Id_Marca')
            ->leftJoin('modelo as mo', 'a.Id_Modelo',    '=', 'mo.Id_Modelo')
            ->where('la.Id_Levantamiento', $id)
            ->select([
                'la.Id_Levantamiento_Articulo', 'la.Id_Articulo', 'la.Cantidad',
                'la.Precio_Unitario', 'la.Notas', 'la.modelo_por_definir',
                'a.Nombre as articulo_nombre',
                'a.Id_Marca', 'a.Id_Modelo',
                'm.Nombre as marca_nombre', 'mo.Nombre as modelo_nombre',
            ])
            ->get();

        $valoresDinamicos = DB::table('levantamiento_valores_dinamicos')
            ->where('Id_Levantamiento', $id)
            ->select(['Id_Campo', 'Valor'])
            ->get()
            ->keyBy('Id_Campo');

        $campos = DB::table('tipo_levantamiento_campos')
            ->where('Id_Tipo_Levantamiento', $levantamiento->Id_Tipo_Levantamiento)
            ->where('Activo', 1)
            ->orderBy('Orden')
            ->get();

        $marcas  = DB::table('marcas')->orderBy('Nombre')->get();
        $modelos = DB::table('modelo')->orderBy('Nombre')->get();

        return view('usuario.levantamientos.edit', compact(
            'levantamiento', 'articulos', 'valoresDinamicos', 'campos', 'marcas', 'modelos'
        ));
    }

    /**
     * Actualizar levantamiento
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $usuario = Auth::user();

            $levantamiento = DB::table('levantamientos')
                ->where('Id_Levantamiento', $id)
                ->where('id_usuarios', $usuario->id_usuarios)
                ->first();

            if (!$levantamiento) {
                return response()->json(['success' => false, 'message' => 'Levantamiento no encontrado'], 404);
            }

            if (in_array($levantamiento->estatus, ['Cancelado'])) {
                return response()->json(['success' => false, 'message' => 'No se pueden editar levantamientos cancelados'], 403);
            }

            $request->validate(['articulos' => 'required|array|min:1']);

            $tienePorDefinir = collect($request->articulos)->contains(fn($a) => !empty($a['modelo_por_definir']));

            DB::table('levantamientos')->where('Id_Levantamiento', $id)->update([
                'modelo_por_definir' => $tienePorDefinir ? 1 : 0,
            ]);

            DB::table('levantamiento_articulos')->where('Id_Levantamiento', $id)->delete();

            foreach ($request->articulos as $articulo) {
                DB::table('levantamiento_articulos')->insert([
                    'Id_Levantamiento'   => $id,
                    'Id_Articulo'        => $articulo['id_articulo'],
                    'Cantidad'           => $articulo['cantidad']        ?? 1,
                    'Precio_Unitario'    => $articulo['precio_unitario'] ?? 0,
                    'Subtotal'           => ($articulo['cantidad'] ?? 1) * ($articulo['precio_unitario'] ?? 0),
                    'Notas'              => $articulo['notas']           ?? null,
                    'modelo_por_definir' => !empty($articulo['modelo_por_definir']) ? 1 : 0,
                    'fecha_registro'     => now(),
                ]);
            }

            DB::table('levantamiento_valores_dinamicos')->where('Id_Levantamiento', $id)->delete();

            $campos = DB::table('tipo_levantamiento_campos')
                ->where('Id_Tipo_Levantamiento', $levantamiento->Id_Tipo_Levantamiento)
                ->where('Activo', 1)->get();

            $camposEspeciales = ['articulo','cantidad','marca','modelo','precio_unitario','servicio_profesional'];

            foreach ($campos as $campo) {
                if (in_array($campo->Nombre_Campo, $camposEspeciales)) continue;
                if ($request->has($campo->Nombre_Campo)) {
                    $valor = $request->input($campo->Nombre_Campo);
                    if ($valor !== null && $valor !== '') {
                        DB::table('levantamiento_valores_dinamicos')->insert([
                            'Id_Levantamiento' => $id,
                            'Id_Campo'         => $campo->Id_Campo,
                            'Valor'            => $valor,
                            'Fecha_Registro'   => now(),
                        ]);
                    }
                }
            }

            DB::table('actividades')->insert([
                'Tipo'          => 'levantamiento',
                'Accion'        => 'actualizado',
                'Descripcion'   => 'Levantamiento LEV-' . str_pad($id, 5, '0', STR_PAD_LEFT) . ' actualizado',
                'Referencia_Id' => $id,
                'Id_Usuario'    => $usuario->id_usuarios,
                'Fecha'         => now(),
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Levantamiento actualizado exitosamente']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Datos inválidos', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar levantamiento: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Definir modelo de un artículo "por definir"
     */
    public function definirModelo(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $usuario = Auth::user();

            $levantamiento = DB::table('levantamientos')
                ->where('Id_Levantamiento', $id)
                ->where('id_usuarios', $usuario->id_usuarios)
                ->first();

            if (!$levantamiento) {
                return response()->json(['success' => false, 'message' => 'Levantamiento no encontrado'], 404);
            }

            $request->validate([
                'articulo_id' => 'required|exists:articulos,Id_Articulos',
                'modelo_id'   => 'required|exists:modelo,Id_Modelo',
            ]);

            $modelo = DB::table('modelo')->where('Id_Modelo', $request->modelo_id)->first();

            DB::table('articulos')
                ->where('Id_Articulos', $request->articulo_id)
                ->update([
                    'Id_Modelo'          => $request->modelo_id,
                    'modelo_por_definir' => 0,
                ]);

            DB::table('levantamiento_articulos')
                ->where('Id_Levantamiento', $id)
                ->where('Id_Articulo', $request->articulo_id)
                ->update(['modelo_por_definir' => 0]);

            $quedanPorDefinir = DB::table('levantamiento_articulos')
                ->where('Id_Levantamiento', $id)
                ->where('modelo_por_definir', 1)
                ->count();

            DB::table('levantamientos')
                ->where('Id_Levantamiento', $id)
                ->update([
                    'estatus'            => 'En Proceso',
                    'modelo_por_definir' => $quedanPorDefinir > 0 ? 1 : 0,
                ]);

            DB::table('actividades')->insert([
                'Tipo'          => 'levantamiento',
                'Accion'        => 'modelo_definido',
                'Descripcion'   => 'Modelo definido en LEV-' . str_pad($id, 5, '0', STR_PAD_LEFT) . ': ' . $modelo->Nombre,
                'Referencia_Id' => $id,
                'Id_Usuario'    => $usuario->id_usuarios,
                'Fecha'         => now(),
            ]);

            DB::commit();

            return response()->json([
                'success'       => true,
                'modelo_nombre' => $modelo->Nombre,
                'message'       => 'Modelo definido exitosamente',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al definir modelo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // CREAR ARTÍCULO DESDE LEVANTAMIENTO
    // - Nombre y descripción: máx 500 caracteres con aviso
    // ════════════════════════════════════════════════════════════════
    public function crearArticuloDesdelevantamiento(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'cliente_id'         => 'required|exists:clientes,Id_Cliente',
                'nombre'             => 'required|string|max:500',
                'marca_id'           => 'required|exists:marcas,Id_Marca',
                'modelo_id'          => 'nullable|exists:modelo,Id_Modelo',
                'descripcion'        => 'nullable|string|max:500',
                'es_principal'       => 'nullable',
                'modelo_por_definir' => 'nullable',
            ], [
                'nombre.required' => 'El nombre del artículo es obligatorio.',
                'nombre.max'      => 'El nombre no puede superar los 500 caracteres.',
                'marca_id.required' => 'Debes seleccionar una marca.',
                'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
            ]);

            $modeloPorDefinir = $request->has('modelo_por_definir') ? 1 : 0;

            $articuloId = DB::table('articulos')->insertGetId([
                'Nombre'             => $request->nombre,
                'Descripcion'        => $request->descripcion,
                'Id_Marca'           => $request->marca_id,
                'Id_Modelo'          => $modeloPorDefinir ? null : $request->modelo_id,
                'modelo_por_definir' => $modeloPorDefinir,
                'fecha_creacion'     => now(),
                'veces_solicitado'   => 0,
            ]);

            DB::table('cliente_articulos')->insert([
                'Id_Cliente'     => $request->cliente_id,
                'Id_Articulo'    => $articuloId,
                'Es_Principal'   => $request->has('es_principal') ? 1 : 0,
                'Fecha_Agregado' => now(),
            ]);

            DB::table('actividades')->insert([
                'Tipo'          => 'producto',
                'Accion'        => 'agregado',
                'Descripcion'   => 'Artículo creado: ' . $request->nombre,
                'Referencia_Id' => $articuloId,
                'Id_Usuario'    => Auth::id(),
                'Fecha'         => now(),
            ]);

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Artículo creado exitosamente',
                'articulo' => [
                    'Id_Articulos'       => $articuloId,
                    'Nombre'             => $request->nombre,
                    'modelo_por_definir' => $modeloPorDefinir,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            // Devolver el primer error de validación de forma clara
            $errores = $e->errors();
            $primerError = collect($errores)->first()[0] ?? 'Error de validación.';
            return response()->json([
                'success' => false,
                'message' => $primerError,
                'errors'  => $errores,
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear artículo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear el artículo: ' . $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // CREAR MARCA RÁPIDA — valida duplicado por nombre
    // ════════════════════════════════════════════════════════════════
    public function crearMarcaRapida(Request $request)
    {
        try {
            $request->validate([
                'nombre'      => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:250',
            ], [
                'nombre.required' => 'El nombre de la marca es obligatorio.',
                'nombre.max'      => 'El nombre no puede superar los 100 caracteres.',
            ]);

            // Verificar duplicado (insensible a mayúsculas y espacios)
            $existe = DB::table('marcas')
                ->whereRaw('LOWER(TRIM(Nombre)) = ?', [strtolower(trim($request->nombre))])
                ->first();

            if ($existe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una marca con el nombre "' . $existe->Nombre . '".',
                    'duplicado' => true,
                    'marca' => $existe,
                ], 422);
            }

            $id    = DB::table('marcas')->insertGetId([
                'Nombre'      => trim($request->nombre),
                'Descripcion' => $request->descripcion ?: null,
            ]);
            $marca = DB::table('marcas')->where('Id_Marca', $id)->first();

            return response()->json(['success' => true, 'message' => 'Marca creada exitosamente', 'marca' => $marca]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $primerError = collect($e->errors())->first()[0] ?? 'Error de validación.';
            return response()->json(['success' => false, 'message' => $primerError], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear marca: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear la marca'], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // CREAR MODELO RÁPIDO — valida duplicado por nombre
    // ════════════════════════════════════════════════════════════════
    public function crearModeloRapido(Request $request)
    {
        try {
            $request->validate([
                'nombre'      => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:250',
            ], [
                'nombre.required' => 'El nombre del modelo es obligatorio.',
                'nombre.max'      => 'El nombre no puede superar los 100 caracteres.',
            ]);

            // Verificar duplicado
            $existe = DB::table('modelo')
                ->whereRaw('LOWER(TRIM(Nombre)) = ?', [strtolower(trim($request->nombre))])
                ->first();

            if ($existe) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Ya existe un modelo con el nombre "' . $existe->Nombre . '".',
                    'duplicado' => true,
                    'modelo'    => $existe,
                ], 422);
            }

            $id     = DB::table('modelo')->insertGetId([
                'Nombre'      => trim($request->nombre),
                'Descripcion' => $request->descripcion ?: null,
            ]);
            $modelo = DB::table('modelo')->where('Id_Modelo', $id)->first();

            return response()->json(['success' => true, 'message' => 'Modelo creado exitosamente', 'modelo' => $modelo]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $primerError = collect($e->errors())->first()[0] ?? 'Error de validación.';
            return response()->json(['success' => false, 'message' => $primerError], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear modelo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear el modelo'], 500);
        }
    }
}