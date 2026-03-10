<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LevantamientoController extends Controller
{
    /**
     * Mostrar la vista principal de levantamientos
     */
    public function index(Request $request)
    {
        $estatus = $request->get('estatus', 'all');
        
        $query = DB::table('levantamientos')
            ->leftJoin('usuarios', 'levantamientos.id_usuarios', '=', 'usuarios.id_usuarios')
            ->leftJoin('clientes', 'levantamientos.Id_Cliente', '=', 'clientes.Id_Cliente')
            ->leftJoin('tipos_levantamiento', 'levantamientos.Id_Tipo_Levantamiento', '=', 'tipos_levantamiento.Id_Tipo_Levantamiento')
            ->select(
                'levantamientos.*',
                'usuarios.Nombres as usuario_nombre',
                'usuarios.ApellidosPat as usuario_apellido',
                'clientes.Nombre as cliente_nombre',
                'tipos_levantamiento.Nombre as tipo_nombre'
            );

        if ($estatus !== 'all') {
            $query->where('levantamientos.estatus', $estatus);
        }

        $levantamientos = $query->orderBy('levantamientos.fecha_creacion', 'desc')->get();

        $contadores = [
            'todos'      => DB::table('levantamientos')->count(),
            'pendiente'  => DB::table('levantamientos')->where('estatus', 'Pendiente')->count(),
            'proceso'    => DB::table('levantamientos')->where('estatus', 'En Proceso')->count(),
            'completado' => DB::table('levantamientos')->where('estatus', 'Completado')->count(),
            'cancelado'  => DB::table('levantamientos')->where('estatus', 'Cancelado')->count(),
        ];

        return view('admin.levantamientos.index', compact('levantamientos', 'contadores', 'estatus'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $tiposLevantamiento = DB::table('tipos_levantamiento')
            ->where('Activo', 1)->orderBy('Nombre')->get();

        $clientes = DB::table('clientes')
            ->where('Estatus', 'Activo')->orderBy('Nombre')->get();

        return view('admin.levantamientos.create', compact('tiposLevantamiento', 'clientes'));
    }

    /**
     * Obtener datos para el formulario según el tipo
     */
    public function getFormularioTipo($tipoId)
    {
        $tipo = DB::table('tipos_levantamiento')
            ->where('Id_Tipo_Levantamiento', $tipoId)->first();

        if (!$tipo) {
            return response()->json(['error' => 'Tipo no encontrado'], 404);
        }

        $campos = DB::table('tipo_levantamiento_campos')
            ->where('Id_Tipo_Levantamiento', $tipoId)->where('Activo', 1)
            ->orderBy('Orden')->get();

        $marcas               = DB::table('marcas')->orderBy('Nombre')->get();
        $modelos              = DB::table('modelo')->orderBy('Nombre')->get();
        $serviciosProfesionales = DB::table('tipo_servicioprofecional')->orderBy('Nombre')->get();

        return response()->json([
            'tipo'   => $tipo,
            'campos' => $campos,
            'marcas' => $marcas,
            'modelos' => $modelos,
            'serviciosProfesionales' => $serviciosProfesionales
        ]);
    }

    /**
     * Obtener artículos de un cliente
     */
    public function getArticulosCliente($clienteId)
    {
        $articulos = DB::table('cliente_articulos')
            ->join('articulos', 'cliente_articulos.Id_Articulo', '=', 'articulos.Id_Articulos')
            ->join('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
            ->leftJoin('modelo', 'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
            ->where('cliente_articulos.Id_Cliente', $clienteId)
            ->select(
                'articulos.*',
                'marcas.Id_Marca',
                'marcas.Nombre as marca_nombre',
                'modelo.Id_Modelo',
                DB::raw('COALESCE(modelo.Nombre, "Por Definir") as modelo_nombre'),
                'cliente_articulos.Es_Principal'
            )
            ->orderBy('cliente_articulos.Es_Principal', 'desc')
            ->get();

        return response()->json($articulos);
    }

    /**
     * Helper: hora actual en CDMX
     */
    private function ahoraCDMX(): string
    {
        return Carbon::now('America/Mexico_City')->format('Y-m-d H:i:s');
    }

    /**
     * Crear un nuevo levantamiento
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $tipoId    = $request->tipo_levantamiento_id;
            $clienteId = $request->cliente_id;
            $ahora     = $this->ahoraCDMX();

            $modeloPorDefinir = $request->has('modelo_por_definir') ? 1 : 0;
            $estatus          = $modeloPorDefinir ? 'Pendiente' : 'En Proceso';

            $levantamientoId = DB::table('levantamientos')->insertGetId([
                'Id_Tipo_Levantamiento' => $tipoId,
                'id_usuarios'           => Auth::id(),
                'Id_Cliente'            => $clienteId,
                'estatus'               => $estatus,
                'modelo_por_definir'    => $modeloPorDefinir,
                'fecha_creacion'        => $ahora,
            ]);

            $folio = 'LEV-' . str_pad($levantamientoId, 5, '0', STR_PAD_LEFT);

            if ($request->has('articulos') && is_array($request->articulos)) {
                foreach ($request->articulos as $articulo) {
                    if (isset($articulo['id_articulo']) && ($articulo['cantidad'] ?? 0) > 0) {
                        DB::table('levantamiento_articulos')->insert([
                            'Id_Levantamiento'   => $levantamientoId,
                            'Id_Articulo'        => $articulo['id_articulo'],
                            'Cantidad'           => $articulo['cantidad'],
                            'Precio_Unitario'    => $articulo['precio_unitario'] ?? 0,
                            'Subtotal'           => $articulo['cantidad'] * ($articulo['precio_unitario'] ?? 0),
                            'Notas'              => $articulo['notas'] ?? null,
                            'modelo_por_definir' => $articulo['modelo_por_definir'] ?? 0,
                            'fecha_registro'     => $ahora,
                        ]);
                        DB::table('articulos')->where('Id_Articulos', $articulo['id_articulo'])
                            ->increment('veces_solicitado', $articulo['cantidad']);
                    }
                }
            }

            $campos = DB::table('tipo_levantamiento_campos')
                ->where('Id_Tipo_Levantamiento', $tipoId)->where('Activo', 1)->get();

            foreach ($campos as $campo) {
                $excluidos = ['articulo','marca','modelo','cantidad','precio_unitario','servicio_profesional'];
                if (in_array($campo->Nombre_Campo, $excluidos)) continue;
                $valor = $request->input($campo->Nombre_Campo);
                if ($valor !== null && $valor !== '') {
                    DB::table('levantamiento_valores_dinamicos')->insert([
                        'Id_Levantamiento' => $levantamientoId,
                        'Id_Campo'         => $campo->Id_Campo,
                        'Valor'            => is_array($valor) ? json_encode($valor) : $valor,
                        'Fecha_Registro'   => $ahora,
                    ]);
                }
            }

            DB::table('actividades')->insert([
                'Tipo'          => 'levantamiento',
                'Accion'        => 'creado',
                'Descripcion'   => 'Nuevo levantamiento creado: ' . $folio,
                'Referencia_Id' => $levantamientoId,
                'Id_Usuario'    => Auth::id(),
                'Fecha'         => $ahora,
            ]);

            DB::commit();
            return redirect()->route('admin.levantamientos.show', $levantamientoId)
                ->with('success', 'Levantamiento creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el levantamiento: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $levantamiento = DB::table('levantamientos as l')
            ->join('clientes as c', 'l.Id_Cliente', '=', 'c.Id_Cliente')
            ->join('usuarios as u', 'l.id_usuarios', '=', 'u.id_usuarios')
            ->leftJoin('tipos_levantamiento as tl', 'l.Id_Tipo_Levantamiento', '=', 'tl.Id_Tipo_Levantamiento')
            ->where('l.Id_Levantamiento', $id)
            ->select('l.*', 'c.Nombre as cliente_nombre', 'tl.Nombre as tipo_nombre',
                     DB::raw("CONCAT(u.Nombres,' ',COALESCE(u.ApellidosPat,'')) as usuario_nombre"))
            ->first();

        if (!$levantamiento) abort(404, 'Levantamiento no encontrado');

        $articulosLevantamiento = DB::table('levantamiento_articulos as la')
            ->join('articulos as a', 'la.Id_Articulo', '=', 'a.Id_Articulos')
            ->join('marcas as m', 'a.Id_Marca', '=', 'm.Id_Marca')
            ->leftJoin('modelo as mo', 'a.Id_Modelo', '=', 'mo.Id_Modelo')
            ->where('la.Id_Levantamiento', $id)
            ->select('la.*', 'a.Nombre as articulo_nombre', 'a.Descripcion as articulo_descripcion',
                     'a.modelo_por_definir', 'm.Nombre as marca_nombre',
                     DB::raw('COALESCE(mo.Nombre, "Por Definir") as modelo_nombre'))
            ->get();

        $articulosCliente = DB::table('cliente_articulos as ca')
            ->join('articulos as a', 'ca.Id_Articulo', '=', 'a.Id_Articulos')
            ->join('marcas as m', 'a.Id_Marca', '=', 'm.Id_Marca')
            ->leftJoin('modelo as mo', 'a.Id_Modelo', '=', 'mo.Id_Modelo')
            ->where('ca.Id_Cliente', $levantamiento->Id_Cliente)
            ->select('a.*', 'm.Nombre as marca_nombre',
                     DB::raw('COALESCE(mo.Nombre, "Por Definir") as modelo_nombre'), 'ca.Es_Principal')
            ->get();

        $campos = DB::table('tipo_levantamiento_campos')
            ->where('Id_Tipo_Levantamiento', $levantamiento->Id_Tipo_Levantamiento)
            ->where('Activo', 1)->orderBy('Orden')->get();

        $valores = DB::table('levantamiento_valores_dinamicos')
            ->where('Id_Levantamiento', $id)->get();

        $marcas  = DB::table('marcas')->orderBy('Nombre')->get();
        $modelos = DB::table('modelo')->orderBy('Nombre')->get();

        return view('admin.levantamientos.edit', compact(
            'levantamiento', 'articulosLevantamiento', 'articulosCliente',
            'campos', 'valores', 'marcas', 'modelos'
        ));
    }

    /**
     * Actualizar levantamiento
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $lev = DB::table('levantamientos')->where('Id_Levantamiento', $id)->first();
            if (!$lev) throw new \Exception('Levantamiento no encontrado');

            $modeloPorDefinir = $request->has('modelo_por_definir') ? 1 : 0;
            $estatus          = $modeloPorDefinir ? 'Pendiente' : 'En Proceso';
            $ahora            = $this->ahoraCDMX();

            DB::table('levantamientos')->where('Id_Levantamiento', $id)->update([
                'modelo_por_definir' => $modeloPorDefinir,
                'estatus'            => $estatus,
            ]);

            $articulosAnteriores = DB::table('levantamiento_articulos')
                ->where('Id_Levantamiento', $id)->get();
            foreach ($articulosAnteriores as $ant) {
                DB::table('articulos')->where('Id_Articulos', $ant->Id_Articulo)
                    ->decrement('veces_solicitado', $ant->Cantidad);
            }

            DB::table('levantamiento_articulos')->where('Id_Levantamiento', $id)->delete();

            if ($request->has('articulos') && is_array($request->articulos)) {
                foreach ($request->articulos as $articulo) {
                    if (isset($articulo['id_articulo']) && ($articulo['cantidad'] ?? 0) > 0) {
                        DB::table('levantamiento_articulos')->insert([
                            'Id_Levantamiento'   => $id,
                            'Id_Articulo'        => $articulo['id_articulo'],
                            'Cantidad'           => $articulo['cantidad'],
                            'Precio_Unitario'    => $articulo['precio_unitario'] ?? 0,
                            'Subtotal'           => $articulo['cantidad'] * ($articulo['precio_unitario'] ?? 0),
                            'Notas'              => $articulo['notas'] ?? null,
                            'modelo_por_definir' => $articulo['modelo_por_definir'] ?? 0,
                            'fecha_registro'     => $ahora,
                        ]);
                        DB::table('articulos')->where('Id_Articulos', $articulo['id_articulo'])
                            ->increment('veces_solicitado', $articulo['cantidad']);
                    }
                }
            }

            DB::table('levantamiento_valores_dinamicos')->where('Id_Levantamiento', $id)->delete();

            $campos    = DB::table('tipo_levantamiento_campos')
                ->where('Id_Tipo_Levantamiento', $lev->Id_Tipo_Levantamiento)->where('Activo', 1)->get();
            $excluidos = ['articulo','marca','modelo','cantidad','precio_unitario','servicio_profesional'];
            foreach ($campos as $campo) {
                if (in_array($campo->Nombre_Campo, $excluidos)) continue;
                $valor = $request->input($campo->Nombre_Campo);
                if ($valor !== null && $valor !== '') {
                    DB::table('levantamiento_valores_dinamicos')->insert([
                        'Id_Levantamiento' => $id,
                        'Id_Campo'         => $campo->Id_Campo,
                        'Valor'            => is_array($valor) ? json_encode($valor) : $valor,
                        'Fecha_Registro'   => $ahora,
                    ]);
                }
            }

            $folio = 'LEV-' . str_pad($id, 5, '0', STR_PAD_LEFT);
            DB::table('actividades')->insert([
                'Tipo'          => 'levantamiento',
                'Accion'        => 'actualizado',
                'Descripcion'   => 'Levantamiento ' . $folio . ' actualizado',
                'Referencia_Id' => $id,
                'Id_Usuario'    => Auth::id(),
                'Fecha'         => $ahora,
            ]);

            DB::commit();
            return redirect()->route('admin.levantamientos.show', $id)
                ->with('success', 'Levantamiento actualizado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalles de un levantamiento
     */
    public function show($id)
    {
        $levantamiento = DB::table('levantamientos')
            ->leftJoin('usuarios', 'levantamientos.id_usuarios', '=', 'usuarios.id_usuarios')
            ->leftJoin('clientes', 'levantamientos.Id_Cliente', '=', 'clientes.Id_Cliente')
            ->leftJoin('tipos_levantamiento', 'levantamientos.Id_Tipo_Levantamiento', '=', 'tipos_levantamiento.Id_Tipo_Levantamiento')
            ->where('levantamientos.Id_Levantamiento', $id)
            ->select(
                'levantamientos.*',
                'usuarios.Nombres as usuario_nombre',
                'usuarios.ApellidosPat as usuario_apellido',
                'clientes.Nombre as cliente_nombre',
                'tipos_levantamiento.Nombre as tipo_nombre'
            )
            ->first();

        if (!$levantamiento) abort(404);

        $articulos = DB::table('levantamiento_articulos')
            ->join('articulos', 'levantamiento_articulos.Id_Articulo', '=', 'articulos.Id_Articulos')
            ->join('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
            ->leftJoin('modelo', 'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
            ->where('levantamiento_articulos.Id_Levantamiento', $id)
            ->select(
                'levantamiento_articulos.*',
                'articulos.Nombre as articulo_nombre',
                'articulos.Descripcion as articulo_descripcion',
                'articulos.modelo_por_definir',
                'marcas.Nombre as marca_nombre',
                DB::raw('COALESCE(modelo.Nombre, "Por Definir") as modelo_nombre')
            )
            ->get();

        $valores = DB::table('levantamiento_valores_dinamicos')
            ->join('tipo_levantamiento_campos', 'levantamiento_valores_dinamicos.Id_Campo', '=', 'tipo_levantamiento_campos.Id_Campo')
            ->where('levantamiento_valores_dinamicos.Id_Levantamiento', $id)
            ->select('tipo_levantamiento_campos.Etiqueta', 'tipo_levantamiento_campos.Nombre_Campo', 'levantamiento_valores_dinamicos.Valor')
            ->get();

        return view('admin.levantamientos.show', compact('levantamiento', 'valores', 'articulos'));
    }

    /**
     * Cambiar el estatus de un levantamiento
     */
    public function cambiarEstatus(Request $request, $id)
    {
        try {
            $nuevoEstatus = $request->estatus;

            DB::table('levantamientos')
                ->where('Id_Levantamiento', $id)
                ->update(['estatus' => $nuevoEstatus]);

            $folio = 'LEV-' . str_pad($id, 5, '0', STR_PAD_LEFT);

            DB::table('actividades')->insert([
                'Tipo'          => 'levantamiento',
                'Accion'        => 'cambio_estatus',
                'Descripcion'   => 'Levantamiento ' . $folio . ' cambió a ' . $nuevoEstatus,
                'Referencia_Id' => $id,
                'Id_Usuario'    => Auth::id(),
                'Fecha'         => $this->ahoraCDMX(),
            ]);

            return response()->json(['success' => true, 'message' => 'Estatus actualizado correctamente']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estatus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DATOS PARA PDF
     */
    public function datosPdf($id)
    {
        try {
            $lev = DB::table('levantamientos as l')
                ->join('clientes as c', 'l.Id_Cliente', '=', 'c.Id_Cliente')
                ->join('usuarios as u', 'l.id_usuarios', '=', 'u.id_usuarios')
                ->leftJoin('tipos_levantamiento as tl', 'l.Id_Tipo_Levantamiento', '=', 'tl.Id_Tipo_Levantamiento')
                ->where('l.Id_Levantamiento', $id)
                ->select(
                    'l.*',
                    'c.Nombre as cliente_nombre',
                    'c.Correo as cliente_correo',
                    'c.Telefono as cliente_telefono',
                    'tl.Nombre as tipo_nombre',
                    DB::raw("CONCAT(u.Nombres,' ',COALESCE(u.ApellidosPat,'')) as usuario_nombre")
                )
                ->first();

            if (!$lev) {
                return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
            }

            $articulos = DB::table('levantamiento_articulos as la')
                ->join('articulos as a', 'la.Id_Articulo', '=', 'a.Id_Articulos')
                ->join('marcas as m', 'a.Id_Marca', '=', 'm.Id_Marca')
                ->leftJoin('modelo as mo', 'a.Id_Modelo', '=', 'mo.Id_Modelo')
                ->where('la.Id_Levantamiento', $id)
                ->select(
                    'la.*',
                    'a.Nombre as nombre',
                    'a.modelo_por_definir',
                    'm.Nombre as marca',
                    DB::raw('COALESCE(mo.Nombre, "Por Definir") as modelo')
                )
                ->get();

            $valores = DB::table('levantamiento_valores_dinamicos as lv')
                ->join('tipo_levantamiento_campos as tc', 'lv.Id_Campo', '=', 'tc.Id_Campo')
                ->where('lv.Id_Levantamiento', $id)
                ->select('tc.Etiqueta as etiqueta', 'lv.Valor as valor')
                ->get();

            return response()->json([
                'success' => true,
                'datos'   => [
                    'folio'            => 'LEV-' . str_pad($id, 5, '0', STR_PAD_LEFT),
                    'fecha'            => Carbon::parse($lev->fecha_creacion)
                                            ->setTimezone('America/Mexico_City')
                                            ->format('d/m/Y H:i'),
                    'cliente_nombre'   => $lev->cliente_nombre,
                    'cliente_correo'   => $lev->cliente_correo,
                    'cliente_telefono' => $lev->cliente_telefono,
                    'tipo_nombre'      => $lev->tipo_nombre ?? 'Sin tipo',
                    'usuario_nombre'   => $lev->usuario_nombre,
                    'articulos'        => $articulos->map(fn($a) => [
                        'nombre'             => $a->nombre,
                        'marca'              => $a->marca,
                        'modelo'             => $a->modelo,
                        'cantidad'           => $a->Cantidad,
                        'precio'             => $a->Precio_Unitario,
                        'subtotal'           => $a->Subtotal,
                        'notas'              => $a->Notas,
                        'modelo_por_definir' => (bool) $a->modelo_por_definir,
                    ]),
                    'valores' => $valores->map(fn($v) => [
                        'etiqueta' => $v->etiqueta,
                        'valor'    => $v->valor,
                    ]),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Crear marca rápida
     */
    public function crearMarcaRapida(Request $request)
    {
        try {
            $request->validate(['nombre' => 'required|string|max:100']);

            $marcaId = DB::table('marcas')->insertGetId([
                'Nombre'      => $request->nombre,
                'Descripcion' => $request->descripcion ?? ''
            ]);

            $marca = DB::table('marcas')->where('Id_Marca', $marcaId)->first();

            return response()->json(['success' => true, 'message' => 'Marca creada exitosamente', 'marca' => $marca]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear la marca: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Crear modelo rápido
     */
    public function crearModeloRapido(Request $request)
    {
        try {
            $request->validate(['nombre' => 'required|string|max:100']);

            $modeloId = DB::table('modelo')->insertGetId([
                'Nombre'      => $request->nombre,
                'Descripcion' => $request->descripcion ?? ''
            ]);

            $modelo = DB::table('modelo')->where('Id_Modelo', $modeloId)->first();

            return response()->json(['success' => true, 'message' => 'Modelo creado exitosamente', 'modelo' => $modelo]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el modelo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Crear artículo desde levantamiento
     */
    public function crearArticuloDesdelevantamiento(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'nombre'     => 'required|string|max:100',
                'marca_id'   => 'required|integer|exists:marcas,Id_Marca',
                'cliente_id' => 'required|integer|exists:clientes,Id_Cliente'
            ]);

            $modeloPorDefinir = $request->has('modelo_por_definir') ? 1 : 0;

            if (!$modeloPorDefinir && !$request->modelo_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar un modelo o marcar "Modelo por definir"'
                ], 422);
            }

            $ahora = $this->ahoraCDMX();

            $articuloId = DB::table('articulos')->insertGetId([
                'Nombre'             => $request->nombre,
                'Descripcion'        => $request->descripcion ?? null,
                'Id_Marca'           => $request->marca_id,
                'Id_Modelo'          => $request->modelo_id ?? null,
                'modelo_por_definir' => $modeloPorDefinir,
                'fecha_creacion'     => $ahora,
                'veces_solicitado'   => 0
            ]);

            DB::table('cliente_articulos')->insert([
                'Id_Cliente'    => $request->cliente_id,
                'Id_Articulo'   => $articuloId,
                'Es_Principal'  => $request->has('es_principal') ? 1 : 0,
                'Fecha_Agregado'=> $ahora
            ]);

            DB::table('actividades')->insert([
                'Tipo'          => 'producto',
                'Accion'        => 'agregado',
                'Descripcion'   => 'Producto agregado: ' . $request->nombre,
                'Referencia_Id' => $articuloId,
                'Id_Usuario'    => Auth::id(),
                'Fecha'         => $ahora
            ]);

            DB::commit();

            $articulo = DB::table('articulos')
                ->join('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
                ->leftJoin('modelo', 'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
                ->where('articulos.Id_Articulos', $articuloId)
                ->select(
                    'articulos.*',
                    'marcas.Id_Marca',
                    'marcas.Nombre as marca_nombre',
                    'modelo.Id_Modelo',
                    DB::raw('COALESCE(modelo.Nombre, "Por Definir") as modelo_nombre')
                )
                ->first();

            return response()->json([
                'success'  => true,
                'message'  => 'Artículo creado y asociado al cliente correctamente',
                'articulo' => $articulo
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al crear el artículo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Listar todos los artículos
     */
    public function listarTodosArticulos()
    {
        try {
            $articulos = DB::table('articulos')
                ->join('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
                ->leftJoin('modelo', 'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
                ->select(
                    'articulos.*',
                    'marcas.Nombre as marca_nombre',
                    DB::raw('COALESCE(modelo.Nombre, "Por Definir") as modelo_nombre')
                )
                ->orderBy('articulos.Nombre')
                ->get();

            return response()->json(['success' => true, 'articulos' => $articulos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cargar artículos: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Asociar un artículo existente a un cliente
     */
    public function asociarArticuloACliente(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'articulo_id' => 'required|integer|exists:articulos,Id_Articulos',
                'cliente_id'  => 'required|integer|exists:clientes,Id_Cliente'
            ]);

            $yaAsociado = DB::table('cliente_articulos')
                ->where('Id_Cliente', $request->cliente_id)
                ->where('Id_Articulo', $request->articulo_id)
                ->exists();

            if ($yaAsociado) {
                return response()->json(['success' => false, 'message' => 'Este artículo ya está asociado al cliente'], 422);
            }

            DB::table('cliente_articulos')->insert([
                'Id_Cliente'    => $request->cliente_id,
                'Id_Articulo'   => $request->articulo_id,
                'Es_Principal'  => 0,
                'Fecha_Agregado'=> $this->ahoraCDMX()
            ]);

            $articulo = DB::table('articulos')->where('Id_Articulos', $request->articulo_id)->first();
            DB::table('actividades')->insert([
                'Tipo'          => 'producto',
                'Accion'        => 'asociado',
                'Descripcion'   => 'Artículo "' . $articulo->Nombre . '" asociado a cliente',
                'Referencia_Id' => $request->articulo_id,
                'Id_Usuario'    => Auth::id(),
                'Fecha'         => $this->ahoraCDMX()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Artículo asociado correctamente al cliente']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al asociar artículo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar modelo de un artículo
     */
    public function actualizarModeloArticulo(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'articulo_id' => 'required|integer|exists:articulos,Id_Articulos',
                'modelo_id'   => 'required|integer|exists:modelo,Id_Modelo'
            ]);

            DB::table('articulos')
                ->where('Id_Articulos', $request->articulo_id)
                ->update(['Id_Modelo' => $request->modelo_id, 'modelo_por_definir' => 0]);

            $articulo = DB::table('articulos')->where('Id_Articulos', $request->articulo_id)->first();
            $modelo   = DB::table('modelo')->where('Id_Modelo', $request->modelo_id)->first();

            DB::table('actividades')->insert([
                'Tipo'          => 'producto',
                'Accion'        => 'actualizado',
                'Descripcion'   => 'Modelo definido para artículo "' . $articulo->Nombre . '": ' . $modelo->Nombre,
                'Referencia_Id' => $request->articulo_id,
                'Id_Usuario'    => Auth::id(),
                'Fecha'         => $this->ahoraCDMX()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Modelo actualizado correctamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }
}