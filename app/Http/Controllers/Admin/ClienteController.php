<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClienteController extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // INDEX — lista de clientes activos e inactivos
    // ════════════════════════════════════════════════════════════════
    public function index()
    {
        $clientesActivos = DB::table('clientes')
            ->where('Estatus', 'Activo')
            ->orderBy('fecha_registro', 'desc')
            ->get()
            ->map(fn($c) => $this->cargarRelaciones($c));

        $clientesInactivos = DB::table('clientes')
            ->where('Estatus', 'Inactivo')
            ->orderBy('fecha_registro', 'desc')
            ->get()
            ->map(fn($c) => $this->cargarRelaciones($c));

        $articulos = DB::table('articulos')
            ->leftJoin('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
            ->select('articulos.*', 'marcas.Nombre as marca_nombre')
            ->orderBy('articulos.Nombre')
            ->get()
            ->map(function ($articulo) {
                $articulo->marca = (object)['Nombre' => $articulo->marca_nombre];
                return $articulo;
            });

        return view('admin.clientes', compact('clientesActivos', 'clientesInactivos', 'articulos'));
    }

    // ════════════════════════════════════════════════════════════════
    // STORE — crear nuevo cliente
    // ════════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'nombre'             => 'required|string|max:100',
                'correo'             => 'nullable|email|max:255',
                'telefono'           => 'nullable|string|max:25',
                'pais'               => 'required|string|max:100',
                'estado'             => 'required|string|max:100',
                'municipio'          => 'nullable|string|max:100',
                'codigo_postal'      => 'nullable|numeric',
                'articulos'          => 'nullable|array|max:10',
                'articulo_principal' => 'nullable|integer',
            ]);

            // ── Validación de correo único (backend) ──────────────────
            if ($request->correo) {
                $correoExiste = DB::table('clientes')
                    ->whereRaw('LOWER(TRIM(Correo)) = ?', [strtolower(trim($request->correo))])
                    ->exists();

                if ($correoExiste) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe un cliente registrado con ese correo electrónico.'
                    ], 422);
                }
            }

            // ── Nombre e teléfono: solo aviso en frontend, no bloqueamos aquí ──

            $idDireccion = DB::table('direccion')->insertGetId([
                'Pais'          => $request->pais,
                'Estado'        => $request->estado,
                'Ciudad'        => $request->ciudad        ?: null,
                'Municipio'     => $request->municipio     ?: null,
                'Colonia'       => $request->colonia       ?: null,
                'calle'         => $request->calle         ?: null,
                'Codigo_Postal' => $request->codigo_postal ?: null,
                'No_Ex'         => $request->no_ex         ?: null,
                'No_In'         => $request->no_in         ?: null,
            ]);

            $articuloPrincipal = null;
            if ($request->has('articulos') && !empty($request->articulos)) {
                $articuloPrincipal = $request->articulo_principal ?? $request->articulos[0];
            }

            $idCliente = DB::table('clientes')->insertGetId([
                'Nombre'         => $request->nombre,
                'Correo'         => $request->correo    ?: null,
                'Telefono'       => $request->telefono  ?: null,
                'Estatus'        => 'Activo',
                'Id_Articulos'   => $articuloPrincipal,
                'Id_Direccion'   => $idDireccion,
                'fecha_registro' => now(),
            ]);

            if ($request->has('articulos') && is_array($request->articulos) && !empty($request->articulos)) {
                foreach ($request->articulos as $articuloId) {
                    DB::table('cliente_articulos')->insert([
                        'Id_Cliente'     => $idCliente,
                        'Id_Articulo'    => $articuloId,
                        'Es_Principal'   => ($articuloId == $articuloPrincipal) ? 1 : 0,
                        'Fecha_Agregado' => now(),
                    ]);
                    DB::table('articulos')->where('Id_Articulos', $articuloId)->increment('veces_solicitado');
                }
            }

            $this->registrarActividad('cliente', 'creado', "Nuevo cliente registrado: {$request->nombre}", $idCliente);
            $this->crearNotificacion('Nuevo Cliente', "Se ha registrado el cliente {$request->nombre}", 'info');

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cliente creado exitosamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear cliente: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear el cliente: ' . $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // EDIT — datos para el formulario de edición
    // ════════════════════════════════════════════════════════════════
    public function edit($id)
    {
        try {
            $cliente = DB::table('clientes')->where('Id_Cliente', $id)->first();

            if (!$cliente) {
                return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
            }

            $cliente->direccion = DB::table('direccion')
                ->where('Id_Direccion', $cliente->Id_Direccion)->first();

            $cliente->articulos = DB::table('cliente_articulos')
                ->join('articulos', 'cliente_articulos.Id_Articulo', '=', 'articulos.Id_Articulos')
                ->where('cliente_articulos.Id_Cliente', $id)
                ->select('articulos.*', 'cliente_articulos.Es_Principal')
                ->get()
                ->map(function ($a) {
                    $a->pivot = (object)['Es_Principal' => $a->Es_Principal];
                    return $a;
                });

            return response()->json(['success' => true, 'cliente' => $cliente]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener el cliente: ' . $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // UPDATE — actualizar cliente existente
    // ════════════════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'nombre'             => 'required|string|max:100',
                'correo'             => 'nullable|email|max:255',
                'telefono'           => 'nullable|string|max:25',
                'pais'               => 'required|string|max:100',
                'estado'             => 'required|string|max:100',
                'municipio'          => 'nullable|string|max:100',
                'codigo_postal'      => 'nullable|numeric',
                'articulos'          => 'nullable|array|max:10',
                'articulo_principal' => 'nullable|integer',
            ]);

            $cliente = DB::table('clientes')->where('Id_Cliente', $id)->first();
            if (!$cliente) {
                return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
            }

            // ── Validación de correo único excluyendo el propio cliente ──
            if ($request->correo) {
                $correoExiste = DB::table('clientes')
                    ->where('Id_Cliente', '!=', $id)
                    ->whereRaw('LOWER(TRIM(Correo)) = ?', [strtolower(trim($request->correo))])
                    ->exists();

                if ($correoExiste) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe otro cliente registrado con ese correo electrónico.'
                    ], 422);
                }
            }

            // ── Actualizar dirección ──────────────────────────────────
            DB::table('direccion')
                ->where('Id_Direccion', $cliente->Id_Direccion)
                ->update([
                    'Pais'          => $request->pais,
                    'Estado'        => $request->estado,
                    'Ciudad'        => $request->ciudad        ?: null,
                    'Municipio'     => $request->municipio     ?: null,
                    'Colonia'       => $request->colonia       ?: null,
                    'calle'         => $request->calle         ?: null,
                    'Codigo_Postal' => $request->codigo_postal ?: null,
                    'No_Ex'         => $request->no_ex         ?: null,
                    'No_In'         => $request->no_in         ?: null,
                ]);

            // ── Decrementar contadores de artículos anteriores ────────
            DB::table('cliente_articulos')
                ->where('Id_Cliente', $id)
                ->pluck('Id_Articulo')
                ->each(function ($artId) {
                    DB::table('articulos')
                        ->where('Id_Articulos', $artId)
                        ->where('veces_solicitado', '>', 0)
                        ->decrement('veces_solicitado');
                });

            $articuloPrincipal = null;
            if ($request->has('articulos') && !empty($request->articulos)) {
                $articuloPrincipal = $request->articulo_principal ?? $request->articulos[0];
            }

            DB::table('clientes')->where('Id_Cliente', $id)->update([
                'Nombre'       => $request->nombre,
                'Correo'       => $request->correo   ?: null,
                'Telefono'     => $request->telefono ?: null,
                'Id_Articulos' => $articuloPrincipal,
            ]);

            DB::table('cliente_articulos')->where('Id_Cliente', $id)->delete();

            if ($request->has('articulos') && is_array($request->articulos) && !empty($request->articulos)) {
                foreach ($request->articulos as $articuloId) {
                    DB::table('cliente_articulos')->insert([
                        'Id_Cliente'     => $id,
                        'Id_Articulo'    => $articuloId,
                        'Es_Principal'   => ($articuloId == $articuloPrincipal) ? 1 : 0,
                        'Fecha_Agregado' => now(),
                    ]);
                    DB::table('articulos')->where('Id_Articulos', $articuloId)->increment('veces_solicitado');
                }
            }

            $this->registrarActividad('cliente', 'actualizado', "Cliente actualizado: {$request->nombre}", $id);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cliente actualizado exitosamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar cliente: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar el cliente: ' . $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // SHOW — detalles de un cliente
    // ════════════════════════════════════════════════════════════════
    public function show($id)
    {
        try {
            $cliente = DB::table('clientes')->where('Id_Cliente', $id)->first();

            if (!$cliente) {
                return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
            }

            $cliente->direccion = DB::table('direccion')
                ->where('Id_Direccion', $cliente->Id_Direccion)->first();

            $cliente->articulos = DB::table('cliente_articulos')
                ->join('articulos', 'cliente_articulos.Id_Articulo', '=', 'articulos.Id_Articulos')
                ->leftJoin('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
                ->where('cliente_articulos.Id_Cliente', $id)
                ->select('articulos.*', 'cliente_articulos.Es_Principal', 'marcas.Nombre as marca_nombre')
                ->get()
                ->map(function ($a) {
                    $a->pivot = (object)['Es_Principal' => $a->Es_Principal];
                    return $a;
                });

            return response()->json(['success' => true, 'cliente' => $cliente]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener el cliente: ' . $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // INACTIVAR
    // ════════════════════════════════════════════════════════════════
    public function inactivar($id)
    {
        try {
            $cliente = DB::table('clientes')->where('Id_Cliente', $id)->first();
            if (!$cliente) {
                return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
            }

            DB::table('clientes')->where('Id_Cliente', $id)->update(['Estatus' => 'Inactivo']);
            $this->registrarActividad('cliente', 'desactivado', "Cliente desactivado: {$cliente->Nombre}", $id);
            $this->crearNotificacion('Cliente Desactivado', "Se ha desactivado el cliente {$cliente->Nombre}", 'warning');

            return response()->json(['success' => true, 'message' => 'Cliente inactivado exitosamente']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al inactivar el cliente: ' . $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // REACTIVAR
    // ════════════════════════════════════════════════════════════════
    public function reactivar($id)
    {
        try {
            $cliente = DB::table('clientes')->where('Id_Cliente', $id)->first();
            if (!$cliente) {
                return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
            }

            DB::table('clientes')->where('Id_Cliente', $id)->update(['Estatus' => 'Activo']);
            $this->registrarActividad('cliente', 'reactivado', "Cliente reactivado: {$cliente->Nombre}", $id);
            $this->crearNotificacion('Cliente Reactivado', "Se ha reactivado el cliente {$cliente->Nombre}", 'success');

            return response()->json(['success' => true, 'message' => 'Cliente reactivado exitosamente']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al reactivar el cliente: ' . $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // HELPERS PRIVADOS
    // ════════════════════════════════════════════════════════════════

    /**
     * Carga dirección y artículos de un cliente
     */
    private function cargarRelaciones($cliente)
    {
        $cliente->direccion = DB::table('direccion')
            ->where('Id_Direccion', $cliente->Id_Direccion)->first();

        $cliente->articulos = DB::table('cliente_articulos')
            ->join('articulos', 'cliente_articulos.Id_Articulo', '=', 'articulos.Id_Articulos')
            ->leftJoin('marcas', 'articulos.Id_Marca', '=', 'marcas.Id_Marca')
            ->where('cliente_articulos.Id_Cliente', $cliente->Id_Cliente)
            ->select('articulos.*', 'cliente_articulos.Es_Principal', 'marcas.Nombre as marca_nombre')
            ->get()
            ->map(function ($a) {
                $a->pivot = (object)['Es_Principal' => $a->Es_Principal];
                $a->marca = (object)['Nombre' => $a->marca_nombre];
                return $a;
            });

        return $cliente;
    }

    private function registrarActividad($tipo, $accion, $descripcion, $referenciaId = null)
    {
        DB::table('actividades')->insert([
            'Tipo'          => $tipo,
            'Accion'        => $accion,
            'Descripcion'   => $descripcion,
            'Referencia_Id' => $referenciaId,
            'Id_Usuario'    => Auth::id(),
            'Fecha'         => now(),
        ]);
    }

    private function crearNotificacion($titulo, $mensaje, $tipo = 'info')
    {
        $admins = DB::table('usuarios')
            ->where('Rol', 'Admin')->where('Estatus', 'Activo')->pluck('id_usuarios');

        foreach ($admins as $adminId) {
            DB::table('notificaciones')->insert([
                'Id_Usuario' => $adminId,
                'Titulo'     => $titulo,
                'Mensaje'    => $mensaje,
                'Tipo'       => $tipo,
                'Leida'      => 0,
                'Fecha'      => now(),
            ]);
        }
    }
}