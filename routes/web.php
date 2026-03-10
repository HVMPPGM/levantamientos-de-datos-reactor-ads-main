<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\Admin\ProductosController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\LevantamientoController;
use App\Http\Controllers\Admin\ArticulosController;
use App\Http\Controllers\Usuario\DashboardController;
use App\Http\Controllers\Usuario\LevantamientoUsuarioController;
use App\Http\Controllers\Usuario\ClienteUsuarioController;
use App\Http\Controllers\Admin\TipoLevantamientoController;
use App\Http\Controllers\Usuario\ProductoUsuarioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================================================
// RUTAS PÚBLICAS
// ============================================================================

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================================================
// RUTAS PROTEGIDAS - DASHBOARD SEGÚN ROL
// ============================================================================

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->Rol === 'Admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('usuario.dashboard');
    })->name('dashboard');
});

// ============================================================================
// RUTAS DE ADMINISTRADOR
// ============================================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // ── USUARIOS ────────────────────────────────────────────────────────────
    Route::get('/usuarios',                      [UsuariosController::class, 'index'])->name('usuarios');
    Route::get('/usuarios/{id}',                 [UsuariosController::class, 'show'])->name('usuarios.show');
    Route::post('/usuarios',                     [UsuariosController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{id}',                 [UsuariosController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}',              [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
    Route::post('/usuarios/{id}/toggle-status',  [UsuariosController::class, 'toggleStatus'])->name('usuarios.toggle-status');
    Route::post('/usuarios/{id}/permissions',    [UsuariosController::class, 'updatePermissions'])->name('usuarios.permissions');

    // ── PRODUCTOS (ARTÍCULOS) ────────────────────────────────────────────────
    // Alias para route('admin.productos') usado en sidebar y redirects
    Route::get('/productos', [ProductosController::class, 'index'])->name('productos');

    Route::prefix('productos')->name('productos.')->group(function () {
        // ⚠️  Las rutas estáticas SIEMPRE antes de las que tienen {id}
        Route::get('/check-duplicado', [ProductosController::class, 'checkDuplicado'])->name('check-duplicado');
        Route::get('/',                [ProductosController::class, 'index'])->name('index');
        Route::get('/create',          [ProductosController::class, 'create'])->name('create');
        Route::post('/',               [ProductosController::class, 'store'])->name('store');
        Route::get('/{id}',            [ProductosController::class, 'show'])->name('show');
        Route::get('/{id}/edit',       [ProductosController::class, 'edit'])->name('edit');
        Route::put('/{id}',            [ProductosController::class, 'update'])->name('update');
        Route::delete('/{id}',         [ProductosController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/detalles',   [ProductosController::class, 'detalles'])->name('detalles');
    });

    // Marcas
    Route::post('/marcas',           [ProductosController::class, 'storeMarca'])->name('marcas.store');
    Route::put('/marcas/{id}',       [ProductosController::class, 'updateMarca'])->name('marcas.update');
    Route::delete('/marcas/{id}',    [ProductosController::class, 'destroyMarca'])->name('marcas.destroy');

    // Modelos
    Route::post('/modelos',          [ProductosController::class, 'storeModelo'])->name('modelos.store');
    Route::put('/modelos/{id}',      [ProductosController::class, 'updateModelo'])->name('modelos.update');
    Route::delete('/modelos/{id}',   [ProductosController::class, 'destroyModelo'])->name('modelos.destroy');

    // ── CLIENTES ─────────────────────────────────────────────────────────────
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes');

    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/',                [ClienteController::class, 'index'])->name('index');
        Route::post('/',               [ClienteController::class, 'store'])->name('store');
        Route::get('/{id}',            [ClienteController::class, 'show'])->name('show');
        Route::get('/{id}/edit',       [ClienteController::class, 'edit'])->name('edit');
        Route::put('/{id}',            [ClienteController::class, 'update'])->name('update');
        Route::post('/{id}/inactivar', [ClienteController::class, 'inactivar'])->name('inactivar');
        Route::post('/{id}/reactivar', [ClienteController::class, 'reactivar'])->name('reactivar');
    });

    // ── LEVANTAMIENTOS ───────────────────────────────────────────────────────
    Route::prefix('levantamientos')->name('levantamientos.')->group(function () {
        Route::get('/',                          [LevantamientoController::class, 'index'])->name('index');
        Route::get('/create',                    [LevantamientoController::class, 'create'])->name('create');
        Route::post('/',                         [LevantamientoController::class, 'store'])->name('store');
        Route::get('/tipo/{tipoId}/formulario',  [LevantamientoController::class, 'getFormularioTipo'])->name('formulario-tipo');
        Route::get('/cliente/{clienteId}/articulos', [LevantamientoController::class, 'getArticulosCliente'])->name('articulos-cliente');
        Route::get('/{id}',                      [LevantamientoController::class, 'show'])->name('show');
        Route::get('/{id}/edit',                 [LevantamientoController::class, 'edit'])->name('edit');
        Route::put('/{id}',                      [LevantamientoController::class, 'update'])->name('update');
        Route::get('/{id}/datos-pdf',            [LevantamientoController::class, 'datosPdf'])->name('datos-pdf');
        Route::put('/{id}/estatus',              [LevantamientoController::class, 'cambiarEstatus'])->name('cambiar-estatus');
    });

    // ── ARTÍCULOS DESDE LEVANTAMIENTOS ───────────────────────────────────────
    Route::prefix('articulos')->name('articulos.')->group(function () {
        Route::post('/crear-desde-levantamiento', [LevantamientoController::class, 'crearArticuloDesdelevantamiento'])->name('crear-desde-levantamiento');
        Route::post('/crear-marca-rapida',        [LevantamientoController::class, 'crearMarcaRapida'])->name('crear-marca-rapida');
        Route::post('/crear-modelo-rapido',       [LevantamientoController::class, 'crearModeloRapido'])->name('crear-modelo-rapido');
        Route::get('/listar-todos',               [LevantamientoController::class, 'listarTodosArticulos'])->name('listar-todos');
        Route::post('/asociar-a-cliente',         [LevantamientoController::class, 'asociarArticuloACliente'])->name('asociar-a-cliente');
        Route::post('/actualizar-modelo',         [LevantamientoController::class, 'actualizarModeloArticulo'])->name('actualizar-modelo');
    });

    // ── TIPOS DE LEVANTAMIENTO ───────────────────────────────────────────────
    Route::prefix('tipos-levantamiento')->name('tipos-levantamiento.')->group(function () {
        Route::get('/',                          [TipoLevantamientoController::class, 'index'])->name('index');
        Route::get('/create',                    [TipoLevantamientoController::class, 'create'])->name('create');
        Route::post('/',                         [TipoLevantamientoController::class, 'store'])->name('store');
        Route::get('/{id}',                      [TipoLevantamientoController::class, 'show'])->name('show');
        Route::get('/{id}/edit',                 [TipoLevantamientoController::class, 'edit'])->name('edit');
        Route::put('/{id}',                      [TipoLevantamientoController::class, 'update'])->name('update');
        Route::delete('/{id}',                   [TipoLevantamientoController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-estatus',      [TipoLevantamientoController::class, 'toggleEstatus'])->name('toggle-estatus');
        Route::get('/{id}/campos',               [TipoLevantamientoController::class, 'gestionarCampos'])->name('campos');
        Route::post('/{id}/campos',              [TipoLevantamientoController::class, 'storeCampo'])->name('campos.store');
        Route::put('/{id}/campos/{campoId}',     [TipoLevantamientoController::class, 'updateCampo'])->name('campos.update');
        Route::delete('/{id}/campos/{campoId}',  [TipoLevantamientoController::class, 'destroyCampo'])->name('campos.destroy');
        Route::post('/{id}/campos/{campoId}/toggle', [TipoLevantamientoController::class, 'toggleCampoEstatus'])->name('campos.toggle');
        Route::post('/{id}/campos/reordenar',    [TipoLevantamientoController::class, 'reordenarCampos'])->name('campos.reordenar');
    });
});

// ============================================================================
// RUTAS DE USUARIO ESTÁNDAR
// ============================================================================

Route::middleware(['auth'])->prefix('usuario')->name('usuario.')->group(function () {

    // ── DASHBOARD ────────────────────────────────────────────────────────────
    Route::get('/dashboard',         [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/verificar-permiso', [DashboardController::class, 'verificarPermiso'])->name('verificar-permiso');

    // ── CLIENTES ─────────────────────────────────────────────────────────────
    Route::get('/clientes', [ClienteUsuarioController::class, 'index'])->name('clientesU');

    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/create',               [ClienteUsuarioController::class, 'create'])->name('create');
        Route::post('/',                    [ClienteUsuarioController::class, 'store'])->name('store');
        Route::get('/{id}',                 [ClienteUsuarioController::class, 'show'])->name('show');
        Route::get('/{id}/edit',            [ClienteUsuarioController::class, 'edit'])->name('edit');
        Route::put('/{id}',                 [ClienteUsuarioController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-estatus', [ClienteUsuarioController::class, 'toggleEstatus'])->name('toggle-estatus');
    });

    // ── LEVANTAMIENTOS ───────────────────────────────────────────────────────
    Route::get('/levantamientos', [LevantamientoUsuarioController::class, 'index'])->name('levantamientos');

    Route::prefix('levantamientos')->name('levantamientos.')->group(function () {
        Route::get('/',                              [LevantamientoUsuarioController::class, 'index'])->name('index');
        Route::get('/create',                        [LevantamientoUsuarioController::class, 'create'])->name('create');
        Route::post('/',                             [LevantamientoUsuarioController::class, 'store'])->name('store');
        Route::get('/tipo/{tipoId}/formulario',      [LevantamientoUsuarioController::class, 'getFormularioTipo'])->name('formulario-tipo');
        Route::get('/cliente/{clienteId}/articulos', [LevantamientoUsuarioController::class, 'getArticulosCliente'])->name('articulos-cliente');
        Route::post('/{id}/definir-modelo',          [LevantamientoUsuarioController::class, 'definirModelo'])->name('definir-modelo');
        Route::get('/{id}',                          [LevantamientoUsuarioController::class, 'show'])->name('show');
        Route::get('/{id}/edit',                     [LevantamientoUsuarioController::class, 'edit'])->name('edit');
        Route::put('/{id}',                          [LevantamientoUsuarioController::class, 'update'])->name('update');
    });

    // ── ARTÍCULOS ────────────────────────────────────────────────────────────
    Route::prefix('articulos')->name('articulos.')->group(function () {
        Route::post('/crear-desde-levantamiento', [LevantamientoUsuarioController::class, 'crearArticuloDesdelevantamiento'])->name('crear-desde-levantamiento');
        Route::post('/crear-marca-rapida',        [LevantamientoUsuarioController::class, 'crearMarcaRapida'])->name('crear-marca-rapida');
        Route::post('/crear-modelo-rapido',       [LevantamientoUsuarioController::class, 'crearModeloRapido'])->name('crear-modelo-rapido');
        Route::get('/buscar',                     [LevantamientoUsuarioController::class, 'buscarArticulos'])->name('buscar');
        Route::post('/asociar-a-cliente',         [LevantamientoUsuarioController::class, 'asociarArticuloACliente'])->name('asociar-a-cliente');
    });

    // ── TIPOS DE LEVANTAMIENTO ───────────────────────────────────────────────
    Route::prefix('tipos-levantamiento')->name('tipos-levantamiento.')->group(function () {
        Route::get('/',         [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'index'])->name('index');
        Route::get('/create',   [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'create'])->name('create');
        Route::post('/',        [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'store'])->name('store');
        Route::get('/{id}',                          [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'show'])->name('show');
        Route::get('/{id}/edit',                     [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'edit'])->name('edit');
        Route::put('/{id}',                          [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-estatus',          [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'toggleEstatus'])->name('toggle-estatus');
        Route::get('/{id}/campos',                   [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'gestionarCampos'])->name('campos');
        Route::post('/{id}/campos',                  [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'storeCampo'])->name('campos.store');
        Route::put('/{id}/campos/{campoId}',         [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'updateCampo'])->name('campos.update');
        Route::delete('/{id}/campos/{campoId}',      [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'destroyCampo'])->name('campos.destroy');
        Route::post('/{id}/campos/{campoId}/toggle', [\App\Http\Controllers\Usuario\TipoLevantamientoUsuarioController::class, 'toggleCampoEstatus'])->name('campos.toggle');
    });

    // ── PRODUCTOS (USUARIO) ───────────────────────────────────────────────────
    Route::prefix('productos')->name('productos.')->group(function () {
        // ⚠️  Rutas estáticas PRIMERO
        Route::get('/check-dup', [ProductoUsuarioController::class, 'checkDuplicado'])->name('check-dup');
        Route::get('/',          [ProductoUsuarioController::class, 'index'])->name('index');
        Route::get('/create',    [ProductoUsuarioController::class, 'create'])->name('create');
        Route::post('/',         [ProductoUsuarioController::class, 'store'])->name('store');
        Route::get('/{id}',      [ProductoUsuarioController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ProductoUsuarioController::class, 'edit'])->name('edit');
        Route::put('/{id}',      [ProductoUsuarioController::class, 'update'])->name('update');

        // Marcas y modelos rápidos (AJAX desde el formulario)
        Route::post('/marcas',   [ProductoUsuarioController::class, 'storeMarca'])->name('marcas.store');
        Route::post('/modelos',  [ProductoUsuarioController::class, 'storeModelo'])->name('modelos.store');
    });
});