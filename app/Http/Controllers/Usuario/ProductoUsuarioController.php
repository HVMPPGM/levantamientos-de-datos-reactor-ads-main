<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductoUsuarioController extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // INDEX — listado de artículos con búsqueda y filtros
    // ════════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        try {
            $usuario = Auth::user();

            if ($usuario->Permisos !== 'si') {
                return redirect()->route('usuario.dashboard')
                    ->with('error', 'No tienes permisos para acceder a Productos.');
            }

            $busqueda    = $request->get('busqueda', '');
            $marcaFiltro = $request->get('marca', '');

            $query = DB::table('articulos')
                ->leftJoin('marcas',  'articulos.Id_Marca',  '=', 'marcas.Id_Marca')
                ->leftJoin('modelo',  'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
                ->select(
                    'articulos.*',
                    'marcas.Nombre as marca_nombre',
                    'modelo.Nombre as modelo_nombre'
                );

            if ($busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('articulos.Nombre', 'LIKE', "%{$busqueda}%")
                      ->orWhere('marcas.Nombre',  'LIKE', "%{$busqueda}%")
                      ->orWhere('modelo.Nombre',  'LIKE', "%{$busqueda}%");
                });
            }

            if ($marcaFiltro) {
                $query->where('articulos.Id_Marca', $marcaFiltro);
            }

            $articulos = $query->orderBy('articulos.veces_solicitado', 'desc')
                               ->orderBy('articulos.Nombre')
                               ->paginate(12)
                               ->appends($request->query());

            $marcas = DB::table('marcas')->orderBy('Nombre')->get();

            return view('usuario.productos.index', compact('articulos', 'marcas', 'busqueda', 'marcaFiltro', 'usuario'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar productos: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════════════════
    // CREATE — formulario de nuevo artículo
    // ════════════════════════════════════════════════════════════════
    public function create()
    {
        try {
            $usuario = Auth::user();

            if ($usuario->Permisos !== 'si') {
                return redirect()->route('usuario.productos.index')
                    ->with('error', 'No tienes permisos para crear productos.');
            }

            $marcas  = DB::table('marcas')->orderBy('Nombre')->get();
            $modelos = DB::table('modelo')->orderBy('Nombre')->get();

            return view('usuario.productos.create', compact('marcas', 'modelos', 'usuario'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════════════════
    // STORE — guardar nuevo artículo
    // ════════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        try {
            $usuario = Auth::user();

            if ($usuario->Permisos !== 'si') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para crear productos.'
                ], 403);
            }

            $request->validate([
                'nombre'              => 'required|string|max:500',
                'descripcion'         => 'nullable|string|max:500',
                'id_marca'            => 'required|integer|exists:marcas,Id_Marca',
                'id_modelo'           => 'nullable|integer|exists:modelo,Id_Modelo',
                'modelo_por_definir'  => 'nullable|boolean',
            ]);

            $porDefinir = $request->boolean('modelo_por_definir');
            $duplicado  = $this->verificarDuplicado(
                $request->nombre,
                $request->id_marca,
                $request->id_modelo,
                $porDefinir
            );

            if ($duplicado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un artículo con el mismo nombre, marca y modelo.'
                ], 422);
            }

            $idArticulo = DB::table('articulos')->insertGetId([
                'Nombre'             => $request->nombre,
                'Descripcion'        => $request->descripcion ?: null,
                'Id_Marca'           => $request->id_marca,
                'Id_Modelo'          => $porDefinir ? null : ($request->id_modelo ?: null),
                'modelo_por_definir' => $porDefinir ? 1 : 0,
                'fecha_creacion'     => now(),
                'veces_solicitado'   => 0,
            ]);

            DB::table('actividades')->insert([
                'Tipo'          => 'producto',
                'Accion'        => 'agregado',
                'Descripcion'   => "Producto agregado: {$request->nombre}",
                'Referencia_Id' => $idArticulo,
                'Id_Usuario'    => Auth::id(),
                'Fecha'         => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Artículo creado exitosamente', 'id' => $idArticulo]);
            }

            return redirect()->route('usuario.productos.index')
                ->with('success', 'Artículo creado exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al crear artículo (usuario): ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al crear el artículo: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al crear el artículo.')->withInput();
        }
    }

    // ════════════════════════════════════════════════════════════════
    // SHOW — detalle de un artículo
    // ════════════════════════════════════════════════════════════════
    public function show($id)
    {
        try {
            $usuario = Auth::user();

            if ($usuario->Permisos !== 'si') {
                return redirect()->route('usuario.dashboard')
                    ->with('error', 'No tienes permisos para ver productos.');
            }

            $articulo = DB::table('articulos')
                ->leftJoin('marcas', 'articulos.Id_Marca',  '=', 'marcas.Id_Marca')
                ->leftJoin('modelo', 'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
                ->select(
                    'articulos.*',
                    'marcas.Nombre      as marca_nombre',
                    'marcas.Descripcion as marca_descripcion',
                    'modelo.Nombre      as modelo_nombre',
                    'modelo.Descripcion as modelo_descripcion'
                )
                ->where('articulos.Id_Articulos', $id)
                ->first();

            if (!$articulo) {
                return redirect()->route('usuario.productos.index')
                    ->with('error', 'Artículo no encontrado.');
            }

            $clientesQueUsan = DB::table('cliente_articulos')
                ->join('clientes', 'cliente_articulos.Id_Cliente', '=', 'clientes.Id_Cliente')
                ->where('cliente_articulos.Id_Articulo', $id)
                ->where('clientes.Estatus', 'Activo')
                ->select('clientes.Nombre', 'cliente_articulos.Es_Principal')
                ->limit(5)
                ->get();

            $levantamientosRecientes = DB::table('levantamiento_articulos')
                ->join('levantamientos', 'levantamiento_articulos.Id_Levantamiento', '=', 'levantamientos.Id_Levantamiento')
                ->join('clientes',       'levantamientos.Id_Cliente', '=', 'clientes.Id_Cliente')
                ->where('levantamiento_articulos.Id_Articulo', $id)
                ->select(
                    'levantamientos.Id_Levantamiento',
                    'levantamientos.fecha_creacion',
                    'levantamientos.estatus',
                    'clientes.Nombre as cliente_nombre',
                    'levantamiento_articulos.Cantidad',
                    'levantamiento_articulos.Precio_Unitario'
                )
                ->orderBy('levantamientos.fecha_creacion', 'desc')
                ->limit(5)
                ->get();

            return view('usuario.productos.show', compact('articulo', 'clientesQueUsan', 'levantamientosRecientes', 'usuario'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════════════════
    // EDIT — formulario de edición
    // ════════════════════════════════════════════════════════════════
    public function edit($id)
    {
        try {
            $usuario = Auth::user();

            if ($usuario->Permisos !== 'si') {
                return redirect()->route('usuario.productos.index')
                    ->with('error', 'No tienes permisos para editar productos.');
            }

            $articulo = DB::table('articulos')
                ->leftJoin('marcas', 'articulos.Id_Marca',  '=', 'marcas.Id_Marca')
                ->leftJoin('modelo', 'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
                ->select(
                    'articulos.*',
                    'marcas.Nombre as marca_nombre',
                    'modelo.Nombre as modelo_nombre'
                )
                ->where('articulos.Id_Articulos', $id)
                ->first();

            if (!$articulo) {
                return redirect()->route('usuario.productos.index')
                    ->with('error', 'Artículo no encontrado.');
            }

            $marcas  = DB::table('marcas')->orderBy('Nombre')->get();
            $modelos = DB::table('modelo')->orderBy('Nombre')->get();

            return view('usuario.productos.edit', compact('articulo', 'marcas', 'modelos', 'usuario'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════════════════
    // UPDATE — actualizar artículo existente
    // ════════════════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        try {
            $usuario = Auth::user();

            if ($usuario->Permisos !== 'si') {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para editar productos.'
                ], 403);
            }

            $request->validate([
                'nombre'             => 'required|string|max:500',
                'descripcion'        => 'nullable|string|max:500',
                'id_marca'           => 'required|integer|exists:marcas,Id_Marca',
                'id_modelo'          => 'nullable|integer|exists:modelo,Id_Modelo',
                'modelo_por_definir' => 'nullable|boolean',
            ]);

            $articulo = DB::table('articulos')->where('Id_Articulos', $id)->first();
            if (!$articulo) {
                return response()->json(['success' => false, 'message' => 'Artículo no encontrado.'], 404);
            }

            $porDefinir = $request->boolean('modelo_por_definir');
            $duplicado  = $this->verificarDuplicado(
                $request->nombre,
                $request->id_marca,
                $request->id_modelo,
                $porDefinir,
                $id
            );

            if ($duplicado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe otro artículo con el mismo nombre, marca y modelo.'
                ], 422);
            }

            DB::table('articulos')->where('Id_Articulos', $id)->update([
                'Nombre'             => $request->nombre,
                'Descripcion'        => $request->descripcion ?: null,
                'Id_Marca'           => $request->id_marca,
                'Id_Modelo'          => $porDefinir ? null : ($request->id_modelo ?: null),
                'modelo_por_definir' => $porDefinir ? 1 : 0,
            ]);

            DB::table('actividades')->insert([
                'Tipo'          => 'producto',
                'Accion'        => 'actualizado',
                'Descripcion'   => "Producto actualizado: {$request->nombre}",
                'Referencia_Id' => $id,
                'Id_Usuario'    => Auth::id(),
                'Fecha'         => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Artículo actualizado exitosamente']);
            }

            return redirect()->route('usuario.productos.show', $id)
                ->with('success', 'Artículo actualizado exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar artículo (usuario): ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al actualizar el artículo.')->withInput();
        }
    }

    // ════════════════════════════════════════════════════════════════
    // STORE MARCA RÁPIDA (AJAX)
    // ════════════════════════════════════════════════════════════════
    public function storeMarca(Request $request)
    {
        try {
            $usuario = Auth::user();

            if ($usuario->Permisos !== 'si') {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para esta acción.'], 403);
            }

            $request->validate(['nombre' => 'required|string|max:100']);

            $existe = DB::table('marcas')
                ->whereRaw('LOWER(TRIM(Nombre)) = ?', [strtolower(trim($request->nombre))])
                ->first();

            if ($existe) {
                return response()->json(['success' => false, 'message' => 'Ya existe una marca con ese nombre.', 'marca' => $existe], 422);
            }

            $id = DB::table('marcas')->insertGetId([
                'Nombre'      => $request->nombre,
                'Descripcion' => $request->descripcion ?: null,
            ]);

            $marca = DB::table('marcas')->where('Id_Marca', $id)->first();
            return response()->json(['success' => true, 'message' => 'Marca creada exitosamente', 'marca' => $marca]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear la marca.'], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // STORE MODELO RÁPIDO (AJAX)
    // ════════════════════════════════════════════════════════════════
    public function storeModelo(Request $request)
    {
        try {
            $usuario = Auth::user();

            if ($usuario->Permisos !== 'si') {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para esta acción.'], 403);
            }

            $request->validate(['nombre' => 'required|string|max:100']);

            $existe = DB::table('modelo')
                ->whereRaw('LOWER(TRIM(Nombre)) = ?', [strtolower(trim($request->nombre))])
                ->first();

            if ($existe) {
                return response()->json(['success' => false, 'message' => 'Ya existe un modelo con ese nombre.', 'modelo' => $existe], 422);
            }

            $id = DB::table('modelo')->insertGetId([
                'Nombre'      => $request->nombre,
                'Descripcion' => $request->descripcion ?: null,
            ]);

            $modelo = DB::table('modelo')->where('Id_Modelo', $id)->first();
            return response()->json(['success' => true, 'message' => 'Modelo creado exitosamente', 'modelo' => $modelo]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el modelo.'], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // CHECK DUPLICADO (AJAX)
    // ════════════════════════════════════════════════════════════════
    public function checkDuplicado(Request $request)
    {
        $porDefinir = $request->boolean('modelo_por_definir');
        $existe     = $this->verificarDuplicado(
            $request->nombre,
            $request->id_marca,
            $request->id_modelo,
            $porDefinir,
            $request->excluir_id
        );

        return response()->json(['duplicado' => $existe]);
    }

    // ════════════════════════════════════════════════════════════════
    // HELPER: verificar duplicado nombre+marca+modelo
    // ════════════════════════════════════════════════════════════════
    private function verificarDuplicado($nombre, $marcaId, $modeloId, $porDefinir, $excluirId = null)
    {
        $query = DB::table('articulos')
            ->whereRaw('LOWER(TRIM(Nombre)) = ?', [strtolower(trim($nombre))])
            ->where('Id_Marca', $marcaId);

        if ($excluirId) {
            $query->where('Id_Articulos', '!=', $excluirId);
        }

        if ($porDefinir) {
            $query->where(function ($q) {
                $q->where('modelo_por_definir', 1)->orWhereNull('Id_Modelo');
            });
        } elseif ($modeloId) {
            $query->where('Id_Modelo', $modeloId);
        } else {
            return false;
        }

        return $query->exists();
    }
}