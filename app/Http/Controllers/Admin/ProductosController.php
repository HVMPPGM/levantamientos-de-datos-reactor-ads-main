<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductosController extends Controller
{
    public function index()
    {
        $productos = DB::table('articulos')
            ->leftJoin('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
            ->leftJoin('modelo', 'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
            ->select(
                'articulos.*',
                'marcas.Nombre as marca_nombre',
                'marcas.Descripcion as marca_descripcion',
                'modelo.Nombre as modelo_nombre',
                'modelo.Descripcion as modelo_descripcion'
            )
            ->orderBy('articulos.fecha_creacion', 'desc')
            ->get();

        $productos = $productos->map(function ($producto) {
            $producto->marca = (object)[
                'Nombre'      => $producto->marca_nombre,
                'Descripcion' => $producto->marca_descripcion,
            ];
            $producto->modelo = (object)[
                'Nombre'      => $producto->modelo_nombre,
                'Descripcion' => $producto->modelo_descripcion,
            ];
            return $producto;
        });

        $marcas  = DB::table('marcas')->orderBy('Nombre')->get();
        $modelos = DB::table('modelo')->orderBy('Nombre')->get();

        $totalProductos = DB::table('articulos')->count();
        $totalMarcas    = DB::table('marcas')->count();
        $totalModelos   = DB::table('modelo')->count();
        $masVendido     = DB::table('articulos')->orderBy('veces_solicitado', 'desc')->first();

        return view('admin.productos.index', compact(
            'productos', 'marcas', 'modelos',
            'totalProductos', 'totalMarcas', 'totalModelos', 'masVendido'
        ));
    }

    public function create()
    {
        $marcas  = DB::table('marcas')->orderBy('Nombre')->get();
        $modelos = DB::table('modelo')->orderBy('Nombre')->get();
        return view('admin.productos.create', compact('marcas', 'modelos'));
    }

    // ────────────────────────── CHECK DUPLICADO PRODUCTO (AJAX) ──────────────────────────

    /**
     * Verifica si ya existe un producto con la misma combinación
     * de Nombre + Marca + Modelo. Usado vía AJAX desde el formulario.
     * Acepta ?exclude_id=X para ignorar el registro actual al editar.
     */
    public function checkDuplicado(Request $request)
    {
        $existe = DB::table('articulos')
            ->whereRaw('LOWER(Nombre) = ?', [strtolower($request->nombre)])
            ->where('Id_Marca', $request->marca_id)
            ->where('Id_Modelo', $request->modelo_id)
            ->when($request->exclude_id, function ($q) use ($request) {
                $q->where('Id_Articulos', '!=', $request->exclude_id);
            })
            ->exists();

        return response()->json(['duplicado' => $existe]);
    }

    // ────────────────────────── PRODUCTOS ──────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:500',
            'descripcion' => 'nullable|string|max:500',
            'marca_id'    => 'required|exists:marcas,Id_Marca',
            'modelo_id'   => 'required|exists:modelo,Id_Modelo',
        ], [
            'nombre.required'    => 'El nombre del producto es obligatorio.',
            'nombre.max'         => 'El nombre no puede superar los 500 caracteres.',
            'descripcion.max'    => 'La descripción no puede superar los 500 caracteres.',
            'marca_id.required'  => 'Debes seleccionar una marca.',
            'marca_id.exists'    => 'La marca seleccionada no existe.',
            'modelo_id.required' => 'Debes seleccionar un modelo.',
            'modelo_id.exists'   => 'El modelo seleccionado no existe.',
        ]);

        // ── Validación: no permitir duplicado exacto (Nombre + Marca + Modelo) ──
        $duplicado = DB::table('articulos')
            ->whereRaw('LOWER(Nombre) = ?', [strtolower($request->nombre)])
            ->where('Id_Marca', $request->marca_id)
            ->where('Id_Modelo', $request->modelo_id)
            ->exists();

        if ($duplicado) {
            $error = 'Ya existe un producto con ese mismo Nombre, Marca y Modelo. Los tres campos en conjunto no pueden repetirse.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }
            return redirect()->back()
                ->withErrors(['nombre' => $error])
                ->withInput();
        }

        try {
            $productoId = DB::table('articulos')->insertGetId([
                'Nombre'           => $request->nombre,
                'Descripcion'      => $request->descripcion,
                'Id_Marca'         => $request->marca_id,
                'Id_Modelo'        => $request->modelo_id,
                'fecha_creacion'   => Carbon::now(),
                'veces_solicitado' => 0,
            ]);

            DB::table('actividades')->insert([
                'Tipo'          => 'producto',
                'Accion'        => 'agregado',
                'Descripcion'   => 'Producto agregado: ' . $request->nombre,
                'Referencia_Id' => $productoId,
                'Id_Usuario'    => auth()->id(),
                'Fecha'         => Carbon::now(),
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Producto creado exitosamente']);
            }

            return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al crear el producto: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al crear el producto: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $producto = DB::table('articulos')
            ->leftJoin('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
            ->leftJoin('modelo', 'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
            ->select('articulos.*',
                'marcas.Nombre as marca_nombre', 'marcas.Descripcion as marca_descripcion',
                'modelo.Nombre as modelo_nombre', 'modelo.Descripcion as modelo_descripcion')
            ->where('articulos.Id_Articulos', $id)
            ->first();

        if (!$producto) {
            abort(404, 'Producto no encontrado');
        }

        $producto->marca  = (object)['Nombre' => $producto->marca_nombre,  'Descripcion' => $producto->marca_descripcion];
        $producto->modelo = (object)['Nombre' => $producto->modelo_nombre, 'Descripcion' => $producto->modelo_descripcion];

        $levantamientos = DB::table('levantamiento_articulos')
            ->join('levantamientos', 'levantamiento_articulos.Id_Levantamiento', '=', 'levantamientos.Id_Levantamiento')
            ->join('clientes', 'levantamientos.Id_Cliente', '=', 'clientes.Id_Cliente')
            ->select('levantamientos.*', 'clientes.Nombre as cliente_nombre',
                'levantamiento_articulos.Cantidad', 'levantamiento_articulos.Precio_Unitario')
            ->where('levantamiento_articulos.Id_Articulo', $id)
            ->orderBy('levantamientos.fecha_creacion', 'desc')
            ->get();

        if (request()->ajax()) {
            return response()->json($producto);
        }

        return view('admin.productos.show', compact('producto', 'levantamientos'));
    }

    public function edit($id)
    {
        $producto = DB::table('articulos')->where('Id_Articulos', $id)->first();

        if (!$producto) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }
            abort(404, 'Producto no encontrado');
        }

        if (request()->ajax()) {
            return response()->json($producto);
        }

        $marcas  = DB::table('marcas')->orderBy('Nombre')->get();
        $modelos = DB::table('modelo')->orderBy('Nombre')->get();

        return view('admin.productos.edit', compact('producto', 'marcas', 'modelos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'      => 'required|string|max:500',
            'descripcion' => 'nullable|string|max:500',
            'marca_id'    => 'required|exists:marcas,Id_Marca',
            'modelo_id'   => 'required|exists:modelo,Id_Modelo',
        ], [
            'nombre.required'    => 'El nombre del producto es obligatorio.',
            'nombre.max'         => 'El nombre no puede superar los 500 caracteres.',
            'descripcion.max'    => 'La descripción no puede superar los 500 caracteres.',
            'marca_id.required'  => 'Debes seleccionar una marca.',
            'marca_id.exists'    => 'La marca seleccionada no existe.',
            'modelo_id.required' => 'Debes seleccionar un modelo.',
            'modelo_id.exists'   => 'El modelo seleccionado no existe.',
        ]);

        // ── Validación: no permitir duplicado exacto al editar (excluye el propio registro) ──
        $duplicado = DB::table('articulos')
            ->whereRaw('LOWER(Nombre) = ?', [strtolower($request->nombre)])
            ->where('Id_Marca', $request->marca_id)
            ->where('Id_Modelo', $request->modelo_id)
            ->where('Id_Articulos', '!=', $id)
            ->exists();

        if ($duplicado) {
            $error = 'Ya existe otro producto con ese mismo Nombre, Marca y Modelo. Los tres campos en conjunto no pueden repetirse.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }
            return redirect()->back()
                ->withErrors(['nombre' => $error])
                ->withInput();
        }

        try {
            $producto = DB::table('articulos')->where('Id_Articulos', $id)->first();
            if (!$producto) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
                }
                abort(404, 'Producto no encontrado');
            }

            DB::table('articulos')->where('Id_Articulos', $id)->update([
                'Nombre'      => $request->nombre,
                'Descripcion' => $request->descripcion,
                'Id_Marca'    => $request->marca_id,
                'Id_Modelo'   => $request->modelo_id,
            ]);

            DB::table('actividades')->insert([
                'Tipo'          => 'producto',
                'Accion'        => 'actualizado',
                'Descripcion'   => 'Producto actualizado: ' . $request->nombre,
                'Referencia_Id' => $id,
                'Id_Usuario'    => auth()->id(),
                'Fecha'         => Carbon::now(),
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Producto actualizado exitosamente']);
            }

            return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado exitosamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al actualizar el producto: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $producto = DB::table('articulos')->where('Id_Articulos', $id)->first();

            if (!$producto) {
                return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
            }

            DB::table('actividades')->insert([
                'Tipo'          => 'producto',
                'Accion'        => 'eliminado',
                'Descripcion'   => 'Producto eliminado: ' . $producto->Nombre,
                'Referencia_Id' => $id,
                'Id_Usuario'    => auth()->id(),
                'Fecha'         => Carbon::now(),
            ]);

            DB::table('articulos')->where('Id_Articulos', $id)->delete();

            return response()->json(['success' => true, 'message' => 'Producto eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    public function detalles($id)
    {
        $producto = DB::table('articulos')
            ->leftJoin('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
            ->leftJoin('modelo', 'articulos.Id_Modelo', '=', 'modelo.Id_Modelo')
            ->select('articulos.*', 'marcas.Nombre as marca_nombre', 'marcas.Descripcion as marca_descripcion',
                     'modelo.Nombre as modelo_nombre', 'modelo.Descripcion as modelo_descripcion')
            ->where('articulos.Id_Articulos', $id)
            ->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $producto->marca  = (object)['Nombre' => $producto->marca_nombre,  'Descripcion' => $producto->marca_descripcion];
        $producto->modelo = (object)['Nombre' => $producto->modelo_nombre, 'Descripcion' => $producto->modelo_descripcion];

        return response()->json($producto);
    }

    // ────────────────────────── MARCAS ──────────────────────────

    public function storeMarca(Request $request)
    {
        $request->validate([
            'nombre_marca'      => 'required|string|max:100',
            'descripcion_marca' => 'nullable|string|max:250',
        ], [
            'nombre_marca.required' => 'El nombre de la marca es obligatorio.',
            'nombre_marca.max'      => 'El nombre no puede superar los 100 caracteres.',
        ]);

        // ── Validación: no permitir marca duplicada (case-insensitive) ──
        $existe = DB::table('marcas')
            ->whereRaw('LOWER(Nombre) = ?', [strtolower($request->nombre_marca)])
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una marca con ese nombre. Elige un nombre diferente.',
            ], 422);
        }

        try {
            $marcaId = DB::table('marcas')->insertGetId([
                'Nombre'      => $request->nombre_marca,
                'Descripcion' => $request->descripcion_marca,
            ]);

            $marca = DB::table('marcas')->where('Id_Marca', $marcaId)->first();

            return response()->json([
                'success' => true,
                'marca'   => $marca,
                'message' => 'Marca creada exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear la marca: ' . $e->getMessage()], 500);
        }
    }

    public function updateMarca(Request $request, $id)
    {
        $request->validate([
            'nombre_marca'      => 'required|string|max:100',
            'descripcion_marca' => 'nullable|string|max:250',
        ]);

        // ── Validación: no permitir duplicado al editar (excluye la propia marca) ──
        $existe = DB::table('marcas')
            ->whereRaw('LOWER(Nombre) = ?', [strtolower($request->nombre_marca)])
            ->where('Id_Marca', '!=', $id)
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe otra marca con ese nombre. Elige un nombre diferente.',
            ], 422);
        }

        try {
            DB::table('marcas')->where('Id_Marca', $id)->update([
                'Nombre'      => $request->nombre_marca,
                'Descripcion' => $request->descripcion_marca,
            ]);

            $marca = DB::table('marcas')->where('Id_Marca', $id)->first();

            return response()->json([
                'success' => true,
                'marca'   => $marca,
                'message' => 'Marca actualizada exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar la marca: ' . $e->getMessage()], 500);
        }
    }

    public function destroyMarca($id)
    {
        try {
            $marca = DB::table('marcas')->where('Id_Marca', $id)->first();

            if (!$marca) {
                return response()->json(['success' => false, 'message' => 'Marca no encontrada'], 404);
            }

            $productosAsociados = DB::table('articulos')->where('Id_Marca', $id)->count();
            if ($productosAsociados > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar: la marca tiene {$productosAsociados} producto(s) asociado(s). Reasígnalos primero.",
                ], 422);
            }

            DB::table('marcas')->where('Id_Marca', $id)->delete();

            return response()->json(['success' => true, 'message' => 'Marca eliminada exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar la marca: ' . $e->getMessage()], 500);
        }
    }

    // ────────────────────────── MODELOS ──────────────────────────

    public function storeModelo(Request $request)
    {
        $request->validate([
            'nombre_modelo'      => 'required|string|max:100',
            'descripcion_modelo' => 'nullable|string|max:250',
        ], [
            'nombre_modelo.required' => 'El nombre del modelo es obligatorio.',
            'nombre_modelo.max'      => 'El nombre no puede superar los 100 caracteres.',
        ]);

        // ── Validación: no permitir modelo duplicado (case-insensitive) ──
        $existe = DB::table('modelo')
            ->whereRaw('LOWER(Nombre) = ?', [strtolower($request->nombre_modelo)])
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un modelo con ese nombre. Elige un nombre diferente.',
            ], 422);
        }

        try {
            $modeloId = DB::table('modelo')->insertGetId([
                'Nombre'      => $request->nombre_modelo,
                'Descripcion' => $request->descripcion_modelo,
            ]);

            $modelo = DB::table('modelo')->where('Id_Modelo', $modeloId)->first();

            return response()->json([
                'success' => true,
                'modelo'  => $modelo,
                'message' => 'Modelo creado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el modelo: ' . $e->getMessage()], 500);
        }
    }

    public function updateModelo(Request $request, $id)
    {
        $request->validate([
            'nombre_modelo'      => 'required|string|max:100',
            'descripcion_modelo' => 'nullable|string|max:250',
        ]);

        // ── Validación: no permitir duplicado al editar (excluye el propio modelo) ──
        $existe = DB::table('modelo')
            ->whereRaw('LOWER(Nombre) = ?', [strtolower($request->nombre_modelo)])
            ->where('Id_Modelo', '!=', $id)
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe otro modelo con ese nombre. Elige un nombre diferente.',
            ], 422);
        }

        try {
            DB::table('modelo')->where('Id_Modelo', $id)->update([
                'Nombre' => $request->nombre_modelo,
            ]);

            $modelo = DB::table('modelo')->where('Id_Modelo', $id)->first();

            return response()->json([
                'success' => true,
                'modelo'  => $modelo,
                'message' => 'Modelo actualizado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el modelo: ' . $e->getMessage()], 500);
        }
    }

    public function destroyModelo($id)
    {
        try {
            $modelo = DB::table('modelo')->where('Id_Modelo', $id)->first();

            if (!$modelo) {
                return response()->json(['success' => false, 'message' => 'Modelo no encontrado'], 404);
            }

            $productosAsociados = DB::table('articulos')->where('Id_Modelo', $id)->count();
            if ($productosAsociados > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar: el modelo tiene {$productosAsociados} producto(s) asociado(s). Reasígnalos primero.",
                ], 422);
            }

            DB::table('modelo')->where('Id_Modelo', $id)->delete();

            return response()->json(['success' => true, 'message' => 'Modelo eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el modelo: ' . $e->getMessage()], 500);
        }
    }
}