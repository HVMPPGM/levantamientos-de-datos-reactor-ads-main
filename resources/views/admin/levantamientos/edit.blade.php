<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Levantamiento - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary-color: #1D67A8; --sidebar-width: 280px; --sidebar-collapsed: 70px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .sidebar { position: fixed; left: 0; top: 0; height: 100vh; width: var(--sidebar-width); background: linear-gradient(135deg, #1D67A8 0%, #1D67A8 100%); color: white; transition: all 0.3s ease; z-index: 1000; overflow-y: auto; }
        .sidebar.collapsed { width: var(--sidebar-collapsed); }
        .sidebar-header { padding: 25px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar.collapsed .sidebar-header h4, .sidebar.collapsed .menu-text { display: none; }
        .sidebar-menu { list-style: none; padding: 15px 0; }
        .menu-item { margin: 5px 15px; }
        .menu-link { display: flex; align-items: center; padding: 12px 15px; color: rgba(255,255,255,0.8); text-decoration: none; border-radius: 8px; transition: all 0.3s; }
        .menu-link:hover, .menu-link.active { background: rgba(255,255,255,0.1); color: white; }
        .menu-icon { width: 20px; margin-right: 15px; text-align: center; }
        .sidebar.collapsed .menu-link { justify-content: center; }
        .sidebar.collapsed .menu-icon { margin-right: 0; }
        .main-content { margin-left: var(--sidebar-width); transition: all 0.3s ease; min-height: 100vh; padding: 20px; }
        .main-content.expanded { margin-left: var(--sidebar-collapsed); }
        .top-bar { background: white; padding: 15px 25px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .toggle-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #333; }
        .por-definir-box { background: #fffbe6; border: 1px solid #ffc107; border-radius: 8px; padding: 12px 16px; }
        .char-counter { font-size: .78rem; }
        .char-counter.warn  { color: #1D67A8; font-weight: 600; }
        .char-counter.error { color: #dc3545; font-weight: 600; }
        .input-duplicado { border-color: #dc3545 !important; box-shadow: 0 0 0 .2rem rgba(220,53,69,.25) !important; }
        .btn-primary { background-color: #1D67A8; border-color: #1D67A8; }
        .btn-primary:hover { background-color: #175d96; border-color: #175d96; }
        .bg-primary { background-color: #1D67A8 !important; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
            <h4>Sistema</h4>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-item"><a href="{{ route('admin.dashboard') }}" class="menu-link"><i class="fas fa-home menu-icon"></i><span class="menu-text">Dashboard</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.usuarios') }}" class="menu-link"><i class="fas fa-users menu-icon"></i><span class="menu-text">Usuarios</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.levantamientos.index') }}" class="menu-link active"><i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Levantamientos</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.clientes.index') }}" class="menu-link"><i class="fas fa-building menu-icon"></i><span class="menu-text">Clientes</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.productos.index') }}" class="menu-link"><i class="fas fa-box menu-icon"></i><span class="menu-text">Productos</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.tipos-levantamiento.index') }}" class="menu-link"><i class="fa-solid fa-gear menu-icon"></i><span class="menu-text">Tipos de Levantamientos</span></a></li>
        </ul>
        <div class="sidebar-footer p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-link w-100 border-0 bg-transparent">
                    <i class="fas fa-sign-out-alt menu-icon"></i>
                    <span class="menu-text">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content" id="mainContent">
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
            <div>
                <h2 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Editar Levantamiento
                    <span class="badge bg-secondary">LEV-{{ str_pad($levantamiento->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</span>
                </h2>
            </div>
            <div>
                <a href="{{ route('admin.levantamientos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        <form id="formEditarLevantamiento" action="{{ route('admin.levantamientos.update', $levantamiento->Id_Levantamiento) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- ══ Columna Principal ══ -->
                <div class="col-lg-8">

                    <!-- Información General -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Levantamiento</label>
                                    <input type="text" class="form-control bg-light" value="{{ $levantamiento->tipo_nombre ?? 'Sin tipo' }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" class="form-control bg-light" value="{{ $levantamiento->cliente_nombre }}" readonly>
                                </div>
                            </div>
                            <div id="camposDinamicos">
                                @foreach($campos as $campo)
                                    @php
                                        $excluidos = ['articulo', 'cantidad', 'marca', 'modelo', 'precio_unitario', 'servicio_profesional'];
                                        if (in_array($campo->Nombre_Campo, $excluidos)) continue;
                                        $valor = $valores->where('Id_Campo', $campo->Id_Campo)->first();
                                        $valorActual = $valor ? $valor->Valor : '';
                                    @endphp
                                    <div class="campo-dinamico mb-3">
                                        <label class="form-label">
                                            {{ $campo->Etiqueta }}
                                            @if($campo->Es_Requerido)<span class="text-danger">*</span>@endif
                                        </label>
                                        @if($campo->Tipo_Input === 'textarea')
                                            <textarea class="form-control" name="{{ $campo->Nombre_Campo }}" rows="3"
                                                {{ $campo->Es_Requerido ? 'required' : '' }}>{{ $valorActual }}</textarea>
                                        @elseif($campo->Tipo_Input === 'number')
                                            <input type="number" class="form-control" name="{{ $campo->Nombre_Campo }}"
                                                {{ $campo->Es_Requerido ? 'required' : '' }} value="{{ $valorActual }}" step="0.01">
                                        @else
                                            <input type="{{ $campo->Tipo_Input }}" class="form-control" name="{{ $campo->Nombre_Campo }}"
                                                {{ $campo->Es_Requerido ? 'required' : '' }} value="{{ $valorActual }}">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Artículos Disponibles del Cliente -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i>Artículos del Cliente</h5>
                            <button type="button" class="btn btn-light btn-sm" onclick="abrirModalCrearArticulo()">
                                <i class="fas fa-plus me-1"></i>Crear / Asociar Artículo
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="articulos-disponibles">
                                @if($articulosCliente->count() > 0)
                                    <div class="list-group">
                                        @foreach($articulosCliente as $art)
                                            @php $yaAgregado = $articulosLevantamiento->contains('Id_Articulo', $art->Id_Articulos); @endphp
                                            <div class="list-group-item {{ $yaAgregado ? 'disabled' : '' }}" id="item-disponible-{{ $art->Id_Articulos }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $art->Nombre }}
                                                            @if($art->Es_Principal)<span class="badge bg-primary ms-2">Principal</span>@endif
                                                            @if($art->modelo_por_definir)<span class="badge bg-warning text-dark ms-2"><i class="fas fa-question-circle me-1"></i>Por definir</span>@endif
                                                        </h6>
                                                        <p class="mb-0 text-muted small">
                                                            <strong>Marca:</strong> {{ $art->marca_nombre }} |
                                                            <strong>Modelo:</strong> {{ $art->modelo_nombre }}
                                                        </p>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-sm {{ $yaAgregado ? 'btn-secondary' : 'btn-primary' }}"
                                                        onclick="agregarArticulo({{ $art->Id_Articulos }})"
                                                        {{ $yaAgregado ? 'disabled' : '' }}>
                                                        <i class="fas {{ $yaAgregado ? 'fa-check' : 'fa-plus' }} me-1"></i>
                                                        {{ $yaAgregado ? 'Agregado' : 'Agregar' }}
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info" id="msgSinArticulos">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Este cliente no tiene artículos.
                                        <button type="button" class="btn btn-sm btn-primary ms-2" onclick="abrirModalCrearArticulo()">
                                            <i class="fas fa-plus me-1"></i>Crear artículo
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Artículos del Levantamiento -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Artículos del Levantamiento</h5>
                        </div>
                        <div class="card-body">
                            <div id="articulos-seleccionados"></div>
                        </div>
                    </div>
                </div>

                <!-- ══ Columna Lateral ══ -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-flag me-2"></i>Estado</h5>
                        </div>
                        <div class="card-body">
                            <div class="por-definir-box">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="modelo_por_definir"
                                        name="modelo_por_definir" onchange="actualizarStatusPreview()"
                                        {{ $levantamiento->modelo_por_definir ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="modelo_por_definir">
                                        <i class="fas fa-question-circle me-1"></i>Modelo por definir
                                    </label>
                                </div>
                                <small class="text-muted d-block">Activo → Pendiente | Inactivo → En Proceso</small>
                            </div>
                            <div class="text-center mt-3">
                                <p class="mb-2">Estado resultante:</p>
                                <span id="statusPreview" class="badge fs-6">{{ $levantamiento->estatus }}</span>
                            </div>
                            <hr>
                            <div class="alert alert-info mb-0">
                                <small><i class="fas fa-info-circle me-1"></i><strong>Actual:</strong> {{ $levantamiento->estatus }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Información</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Creado por:</strong><br>{{ $levantamiento->usuario_nombre }}</p>
                            <p class="mb-0"><strong>Fecha:</strong><br>{{ \Carbon\Carbon::parse($levantamiento->fecha_creacion)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('admin.levantamientos.show', $levantamiento->Id_Levantamiento) }}" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-eye me-2"></i>Ver Detalles
                            </a>
                            <a href="{{ route('admin.levantamientos.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- ══════════════════════════════════════════
         MODAL: Crear / Asociar Artículo
    ══════════════════════════════════════════ -->
    <div class="modal fade" id="modalCrearArticulo" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Agregar Artículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Cliente: <strong>{{ $levantamiento->cliente_nombre }}</strong>
                    </div>
                    <ul class="nav nav-tabs mb-3" id="articuloTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="existentes-tab" data-bs-toggle="tab"
                                data-bs-target="#existentes" type="button">
                                <i class="fas fa-search me-2"></i>Seleccionar Existente
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="nuevo-tab" data-bs-toggle="tab"
                                data-bs-target="#nuevo" type="button">
                                <i class="fas fa-plus me-2"></i>Crear Nuevo
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="articuloTabsContent">

                        <!-- Tab: Existentes -->
                        <div class="tab-pane fade show active" id="existentes" role="tabpanel">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="buscarArticuloExistente"
                                    placeholder="Buscar por nombre, marca o modelo...">
                            </div>
                            <div id="listaArticulosExistentes" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                    <p class="text-muted mt-2">Cargando artículos...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Crear Nuevo -->
                        <div class="tab-pane fade" id="nuevo" role="tabpanel">
                            <form id="formCrearArticulo" onsubmit="guardarNuevoArticulo(event)">
                                <input type="hidden" id="clienteIdArticulo" name="cliente_id" value="{{ $levantamiento->Id_Cliente }}">

                                <div class="mb-3">
                                    <label class="form-label">Nombre del Artículo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre_articulo" name="nombre"
                                        required maxlength="500"
                                        placeholder="Ej: Cámara IP, Router, etc."
                                        oninput="actualizarContadorArt('nombre_articulo','cnt_nombre_art'); verificarDuplicadoArticulo()">
                                    <div class="d-flex justify-content-between mt-1">
                                        <span id="alerta_nombre_art" class="text-danger small" style="display:none">
                                            <i class="fas fa-exclamation-circle me-1"></i>Ya existe un artículo con el mismo nombre, marca y modelo
                                        </span>
                                        <span></span>
                                        <small id="cnt_nombre_art" class="char-counter text-muted">0 / 500</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Marca <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select class="form-select" id="marca_articulo" name="marca_id" required
                                                onchange="verificarDuplicadoArticulo()">
                                                <option value="">Seleccione una marca...</option>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->Id_Marca }}">{{ $marca->Nombre }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-outline-primary"
                                                onclick="abrirModalCrearMarca()"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Modelo</label>
                                        <div class="input-group">
                                            <select class="form-select" id="modelo_articulo" name="modelo_id"
                                                onchange="verificarModeloSeleccionado(); verificarDuplicadoArticulo()">
                                                <option value="">Seleccione un modelo...</option>
                                                @foreach($modelos as $modelo)
                                                    <option value="{{ $modelo->Id_Modelo }}">{{ $modelo->Nombre }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-outline-primary"
                                                onclick="abrirModalCrearModeloRapido()"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="alert alert-warning py-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="modelo_por_definir_articulo" name="modelo_por_definir"
                                                onchange="toggleModeloRequerido(); verificarDuplicadoArticulo()">
                                            <label class="form-check-label fw-semibold" for="modelo_por_definir_articulo">
                                                <i class="fas fa-question-circle me-1"></i>Modelo por definir
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Marca esta opción si aún no conoces el modelo exacto.</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Descripción (opcional)</label>
                                    <textarea class="form-control" id="descripcion_articulo" name="descripcion"
                                        rows="3" maxlength="500"
                                        oninput="actualizarContadorArt('descripcion_articulo','cnt_desc_art')"
                                        placeholder="Especificaciones técnicas..."></textarea>
                                    <div class="d-flex justify-content-end mt-1">
                                        <small id="cnt_desc_art" class="char-counter text-muted">0 / 500</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="es_principal_articulo" name="es_principal">
                                        <label class="form-check-label" for="es_principal_articulo">
                                            <i class="fas fa-star text-warning me-1"></i>Marcar como artículo principal
                                        </label>
                                    </div>
                                </div>

                                <div class="modal-footer px-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Crear Artículo
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         MODAL: Crear Marca
    ══════════════════════════════════════════ -->
    <div class="modal fade" id="modalCrearMarca" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-tag me-2"></i>Crear Nueva Marca</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrearMarca" onsubmit="guardarNuevaMarca(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_marca_rapida" name="nombre"
                                required maxlength="100" placeholder="Ej: Hikvision, TP-Link..."
                                oninput="verificarDuplicadoMarca()">
                            <div class="mt-1">
                                <span id="alerta_marca" class="text-danger small" style="display:none">
                                    <i class="fas fa-exclamation-circle me-1"></i>Ya existe una marca con este nombre
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Crear Marca</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         MODAL: Crear Modelo Rápido (desde crear artículo)
    ══════════════════════════════════════════ -->
    <div class="modal fade" id="modalCrearModeloRapido" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-box me-2"></i>Crear Nuevo Modelo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrearModeloRapido" onsubmit="guardarNuevoModeloRapido(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_modelo_rapido" name="nombre"
                                required maxlength="100" placeholder="Ej: DS-2CD2143G0..."
                                oninput="verificarDuplicadoModeloRapido()">
                            <div class="mt-1">
                                <span id="alerta_modelo_rapido" class="text-danger small" style="display:none">
                                    <i class="fas fa-exclamation-circle me-1"></i>Ya existe un modelo con este nombre
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-2"></i>Crear Modelo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         MODAL: Definir Modelo (artículos "por definir")
    ══════════════════════════════════════════ -->
    <div class="modal fade" id="modalDefinirModelo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Definir Modelo del Artículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="definirArticuloId">
                    <input type="hidden" id="definirArticuloIdReal">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Artículo:</strong> <span id="definirArticuloNombre"></span><br>
                        <strong>Marca:</strong> <span id="definirArticuloMarca"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Modelo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-select" id="definirModeloSelect" required>
                                <option value="">Seleccione un modelo...</option>
                                @foreach($modelos as $modelo)
                                    <option value="{{ $modelo->Id_Modelo }}">{{ $modelo->Nombre }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary"
                                onclick="abrirModalCrearModeloDesdeDefinir()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <small class="text-muted">O crea un nuevo modelo si no existe</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" onclick="guardarModeloDefinido()">
                        <i class="fas fa-save me-2"></i>Guardar Modelo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         MODAL: Crear Modelo (desde "Definir Modelo")
    ══════════════════════════════════════════ -->
    <div class="modal fade" id="modalCrearModeloDesdeDefinir" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-box me-2"></i>Crear Nuevo Modelo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrearModeloDesdeDefinir" onsubmit="guardarNuevoModeloDesdeDefinir(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_modelo_definir" name="nombre"
                                required placeholder="Ej: DS-2CD2143G0...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcion_modelo_definir" name="descripcion" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white">
                            <i class="fas fa-save me-2"></i>Crear Modelo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken         = document.querySelector('meta[name="csrf-token"]').content;
        const clienteId         = {{ $levantamiento->Id_Cliente }};
        let articulosCliente    = @json($articulosCliente);
        let articulosSeleccionados = [];
        let contadorArticulos   = 0;
        let modelosDisponibles  = @json($modelos);
        let marcasDisponibles   = @json($marcas);
        let todosLosArticulos   = [];

        // ══════════════════════════════════════════
        // Sidebar
        // ══════════════════════════════════════════
        const sidebar     = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        document.getElementById('toggleBtn').addEventListener('click', () => {
            if (window.innerWidth <= 768) sidebar.classList.toggle('mobile-open');
            else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
        });

        // ══════════════════════════════════════════
        // Inicialización
        // ══════════════════════════════════════════
        document.addEventListener('DOMContentLoaded', function () {

            // Cargar artículos existentes del levantamiento
            @foreach($articulosLevantamiento as $art)
                contadorArticulos++;
                articulosSeleccionados.push({
                    id:                 contadorArticulos,
                    id_articulo:        {{ $art->Id_Articulo }},
                    nombre:             @json($art->articulo_nombre),
                    marca_nombre:       @json($art->marca_nombre),
                    modelo_nombre:      @json($art->modelo_nombre),
                    modelo_por_definir: {{ $art->modelo_por_definir ?? 0 }},
                    cantidad:           {{ $art->Cantidad }},
                    precio_unitario:    {{ $art->Precio_Unitario }},
                    notas:              @json($art->Notas ?? '')
                });
            @endforeach

            renderizarArticulosSeleccionados();
            actualizarStatusPreview();

            // Validar al enviar
            document.getElementById('formEditarLevantamiento').addEventListener('submit', function (e) {
                if (articulosSeleccionados.length === 0) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Atención', text: 'Debe tener al menos un artículo' });
                }
            });
        });

        // ══════════════════════════════════════════
        // Normalización para comparar
        // ══════════════════════════════════════════
        function normalizar(str) {
            if (!str) return '';
            return String(str).trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        // ══════════════════════════════════════════
        // Contadores de caracteres
        // ══════════════════════════════════════════
        function actualizarContadorArt(inputId, contadorId) {
            const input = document.getElementById(inputId);
            const cnt   = document.getElementById(contadorId);
            if (!input || !cnt) return;
            const max = parseInt(input.getAttribute('maxlength'));
            const len = input.value.length;
            cnt.textContent = `${len} / ${max}`;
            cnt.className   = 'char-counter ' + (len >= max ? 'error' : len > max * 0.9 ? 'warn' : 'text-muted');
        }

        // ══════════════════════════════════════════
        // Verificación de duplicados
        // ══════════════════════════════════════════
        function verificarDuplicadoArticulo() {
            const inputNombre = document.getElementById('nombre_articulo');
            const alerta      = document.getElementById('alerta_nombre_art');
            const valNombre   = normalizar(inputNombre.value);
            const marcaId     = document.getElementById('marca_articulo').value;
            const modeloId    = document.getElementById('modelo_articulo').value;
            const porDefinir  = document.getElementById('modelo_por_definir_articulo').checked;

            if (valNombre.length === 0) {
                alerta.style.display = 'none';
                inputNombre.classList.remove('input-duplicado');
                return false;
            }

            const existe = todosLosArticulos.some(a => {
                if (normalizar(a.Nombre) !== valNombre) return false;
                if (marcaId !== '' && String(a.Id_Marca) !== String(marcaId)) return false;
                if (porDefinir) return (a.modelo_por_definir == 1 || !a.Id_Modelo);
                if (modeloId !== '') return String(a.Id_Modelo) === String(modeloId);
                return false;
            });

            alerta.style.display = existe ? 'inline' : 'none';
            inputNombre.classList.toggle('input-duplicado', existe);
            return existe;
        }

        function verificarDuplicadoMarca() {
            const input  = document.getElementById('nombre_marca_rapida');
            const alerta = document.getElementById('alerta_marca');
            const val    = normalizar(input.value);
            const existe = val.length > 0 && marcasDisponibles.some(m => normalizar(m.Nombre) === val);
            alerta.style.display = existe ? 'inline' : 'none';
            input.classList.toggle('input-duplicado', existe);
            return existe;
        }

        function verificarDuplicadoModeloRapido() {
            const input  = document.getElementById('nombre_modelo_rapido');
            const alerta = document.getElementById('alerta_modelo_rapido');
            const val    = normalizar(input.value);
            const existe = val.length > 0 && modelosDisponibles.some(m => normalizar(m.Nombre) === val);
            alerta.style.display = existe ? 'inline' : 'none';
            input.classList.toggle('input-duplicado', existe);
            return existe;
        }

        // ══════════════════════════════════════════
        // Agregar artículo desde la lista del cliente
        // ══════════════════════════════════════════
        function agregarArticulo(articuloId) {
            const articulo = articulosCliente.find(a => a.Id_Articulos == articuloId);
            if (!articulo) return;
            if (articulosSeleccionados.find(a => a.id_articulo == articuloId)) {
                Swal.fire({ icon: 'warning', title: 'Ya agregado', timer: 2000, showConfirmButton: false });
                return;
            }
            contadorArticulos++;
            articulosSeleccionados.push({
                id:                 contadorArticulos,
                id_articulo:        articuloId,
                nombre:             articulo.Nombre,
                marca_nombre:       articulo.marca_nombre,
                modelo_nombre:      articulo.modelo_nombre,
                modelo_por_definir: articulo.modelo_por_definir || 0,
                cantidad:           1,
                precio_unitario:    0,
                notas:              ''
            });
            renderizarArticulosSeleccionados();
            actualizarListaDisponibles();
            Swal.fire({ icon: 'success', title: 'Agregado', timer: 1500, showConfirmButton: false });
        }

        // ══════════════════════════════════════════
        // Renderizar tabla de artículos seleccionados
        // ══════════════════════════════════════════
        function renderizarArticulosSeleccionados() {
            const container = document.getElementById('articulos-seleccionados');
            if (!articulosSeleccionados.length) {
                container.innerHTML = '<div class="alert alert-secondary text-center mb-0"><i class="fas fa-inbox me-2"></i>Sin artículos</div>';
                return;
            }
            let html = `<div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Artículo</th><th>Marca</th><th>Modelo</th>
                        <th width="100">Cantidad</th><th width="150">Precio Unit.</th>
                        <th width="200">Notas</th><th width="100">Subtotal</th><th width="80"></th>
                    </tr>
                </thead><tbody>`;

            articulosSeleccionados.forEach(art => {
                const subtotal    = (art.cantidad * art.precio_unitario).toFixed(2);
                const esPorDefinir = art.modelo_por_definir == 1;
                html += `<tr>
                    <td>
                        <strong>${art.nombre}</strong>
                        ${esPorDefinir ? '<span class="badge bg-warning text-dark ms-2 small">Por definir</span>' : ''}
                        <input type="hidden" name="articulos[${art.id}][id_articulo]" value="${art.id_articulo}">
                        <input type="hidden" name="articulos[${art.id}][modelo_por_definir]" value="${art.modelo_por_definir}">
                        ${esPorDefinir
                            ? `<br><button type="button" class="btn btn-sm btn-outline-warning mt-1"
                                    onclick="abrirModalDefinirModelo(${art.id}, ${art.id_articulo}, '${art.nombre.replace(/'/g,"\\'")}', '${art.marca_nombre.replace(/'/g,"\\'")}')">
                                    <i class="fas fa-edit me-1"></i>Definir modelo</button>`
                            : ''}
                    </td>
                    <td>${art.marca_nombre}</td>
                    <td>${art.modelo_nombre}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm" min="1" value="${art.cantidad}"
                            name="articulos[${art.id}][cantidad]"
                            onchange="actualizarCantidad(${art.id}, this.value)">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" min="0" step="0.01" value="${art.precio_unitario}"
                                name="articulos[${art.id}][precio_unitario]"
                                onchange="actualizarPrecio(${art.id}, this.value)">
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" value="${art.notas}"
                            name="articulos[${art.id}][notas]"
                            onchange="actualizarNotas(${art.id}, this.value)">
                    </td>
                    <td class="text-end"><strong>$${subtotal}</strong></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarArticulo(${art.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });

            const total = articulosSeleccionados.reduce((sum, a) => sum + a.cantidad * a.precio_unitario, 0).toFixed(2);
            html += `</tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="6" class="text-end">TOTAL:</th>
                        <th class="text-end"><strong>$${total}</strong></th>
                        <th></th>
                    </tr>
                </tfoot>
                </table></div>`;
            container.innerHTML = html;
        }

        function actualizarCantidad(id, v) {
            const a = articulosSeleccionados.find(x => x.id == id);
            if (a) { a.cantidad = parseInt(v) || 1; renderizarArticulosSeleccionados(); }
        }
        function actualizarPrecio(id, v) {
            const a = articulosSeleccionados.find(x => x.id == id);
            if (a) { a.precio_unitario = parseFloat(v) || 0; renderizarArticulosSeleccionados(); }
        }
        function actualizarNotas(id, v) {
            const a = articulosSeleccionados.find(x => x.id == id);
            if (a) a.notas = v;
        }
        function eliminarArticulo(id) {
            Swal.fire({
                title: '¿Eliminar?', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Sí'
            }).then(r => {
                if (r.isConfirmed) {
                    articulosSeleccionados = articulosSeleccionados.filter(a => a.id != id);
                    renderizarArticulosSeleccionados();
                    actualizarListaDisponibles();
                }
            });
        }

        // ══════════════════════════════════════════
        // Actualizar estado visual de lista disponible
        // ══════════════════════════════════════════
        function actualizarListaDisponibles() {
            const items = document.querySelectorAll('#articulos-disponibles .list-group-item');
            items.forEach(item => {
                const btn = item.querySelector('button');
                if (!btn) return;
                const match = btn.getAttribute('onclick').match(/\d+/);
                if (!match) return;
                const articuloId  = parseInt(match[0]);
                const yaAgregado  = articulosSeleccionados.find(a => a.id_articulo == articuloId);
                if (yaAgregado) {
                    item.classList.add('disabled');
                    btn.disabled     = true;
                    btn.className    = 'btn btn-sm btn-secondary';
                    btn.innerHTML    = '<i class="fas fa-check me-1"></i>Agregado';
                } else {
                    item.classList.remove('disabled');
                    btn.disabled     = false;
                    btn.className    = 'btn btn-sm btn-primary';
                    btn.innerHTML    = '<i class="fas fa-plus me-1"></i>Agregar';
                }
            });
        }

        // ══════════════════════════════════════════
        // Estado del levantamiento
        // ══════════════════════════════════════════
        function actualizarStatusPreview() {
            const cb    = document.getElementById('modelo_por_definir');
            const badge = document.getElementById('statusPreview');
            if (cb.checked) {
                badge.textContent = 'Pendiente';
                badge.className   = 'badge bg-warning text-dark fs-6';
            } else {
                badge.textContent = 'En Proceso';
                badge.className   = 'badge bg-info text-white fs-6';
            }
        }

        // ══════════════════════════════════════════
        // MODAL: Crear / Asociar Artículo
        // ══════════════════════════════════════════
        function abrirModalCrearArticulo() {
            // Limpiar form
            document.getElementById('formCrearArticulo').reset();
            document.getElementById('clienteIdArticulo').value = clienteId;
            document.getElementById('modelo_por_definir_articulo').checked = false;
            document.getElementById('modelo_articulo').required = false;
            document.getElementById('modelo_articulo').disabled = false;
            document.getElementById('alerta_nombre_art').style.display = 'none';
            document.getElementById('nombre_articulo').classList.remove('input-duplicado');
            document.getElementById('cnt_nombre_art').textContent = '0 / 500';
            document.getElementById('cnt_desc_art').textContent   = '0 / 500';

            // Cargar artículos existentes para la pestaña de búsqueda
            cargarArticulosExistentes();

            new bootstrap.Modal(document.getElementById('modalCrearArticulo')).show();
        }

        function cargarArticulosExistentes() {
            const container = document.getElementById('listaArticulosExistentes');
            container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="text-muted mt-2">Cargando...</p></div>';
            fetch('/admin/articulos/listar-todos', {
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    todosLosArticulos = data.articulos;
                    mostrarArticulosExistentes(data.articulos);
                } else {
                    container.innerHTML = '<div class="alert alert-danger">Error al cargar artículos</div>';
                }
            })
            .catch(() => container.innerHTML = '<div class="alert alert-danger">Error al cargar artículos</div>');
        }

        function mostrarArticulosExistentes(articulos) {
            const container = document.getElementById('listaArticulosExistentes');
            if (!articulos.length) {
                container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay artículos. Crea uno en "Crear Nuevo".</div>';
                return;
            }
            let html = '<div class="list-group">';
            articulos.forEach(art => {
                const porDefinir = art.modelo_por_definir == 1;
                html += `<div class="list-group-item list-group-item-action articulo-existente-item"
                            data-nombre="${art.Nombre}" data-marca="${art.marca_nombre}" data-modelo="${art.modelo_nombre}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${art.Nombre}
                                ${porDefinir ? '<span class="badge bg-warning text-dark ms-2 small"><i class="fas fa-question-circle me-1"></i>Por definir</span>' : ''}
                            </h6>
                            <p class="mb-1 text-muted small"><strong>Marca:</strong> ${art.marca_nombre} | <strong>Modelo:</strong> ${art.modelo_nombre}</p>
                            ${art.Descripcion ? `<p class="mb-0 small text-secondary">${art.Descripcion}</p>` : ''}
                        </div>
                        <button type="button" class="btn btn-sm btn-success" onclick="asociarArticuloExistente(${art.Id_Articulos})">
                            <i class="fas fa-check me-1"></i>Seleccionar
                        </button>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.innerHTML = html;

            // Buscador en tiempo real
            document.getElementById('buscarArticuloExistente').oninput = function () {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.articulo-existente-item').forEach(item => {
                    const match = [item.dataset.nombre, item.dataset.marca, item.dataset.modelo]
                        .some(v => v.toLowerCase().includes(q));
                    item.style.display = match ? '' : 'none';
                });
            };
        }

        function asociarArticuloExistente(articuloId) {
            Swal.fire({ title: 'Asociando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch('/admin/articulos/asociar-a-cliente', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body:    JSON.stringify({ articulo_id: articuloId, cliente_id: clienteId })
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearArticulo')).hide();
                    Swal.fire({ icon: 'success', title: '¡Artículo asociado!', timer: 2000, showConfirmButton: false });
                    recargarArticulosCliente();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al asociar' });
                }
            })
            .catch(() => { Swal.close(); Swal.fire('Error', 'Error al asociar el artículo', 'error'); });
        }

        // ══════════════════════════════════════════
        // Guardar nuevo artículo
        // ══════════════════════════════════════════
        function guardarNuevoArticulo(event) {
            event.preventDefault();
            const nombreVal = document.getElementById('nombre_articulo').value;
            const descVal   = document.getElementById('descripcion_articulo').value;

            if (nombreVal.length > 500) {
                Swal.fire({ icon: 'error', title: 'Nombre muy largo', text: 'Máximo 500 caracteres.' });
                return;
            }
            if (descVal.length > 500) {
                Swal.fire({ icon: 'error', title: 'Descripción muy larga', text: 'Máximo 500 caracteres.' });
                return;
            }
            if (verificarDuplicadoArticulo()) {
                Swal.fire({
                    icon: 'warning', title: 'Artículo duplicado',
                    html: `Ya existe un artículo con el mismo <strong>nombre</strong>, <strong>marca</strong> y <strong>modelo</strong>.<br><br>
                           Puedes seleccionarlo en la pestaña <em>"Seleccionar Existente"</em>, o cambiar la marca o el modelo.`,
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            const modeloPorDefinir = document.getElementById('modelo_por_definir_articulo').checked;
            const modeloId         = document.getElementById('modelo_articulo').value;
            if (!modeloPorDefinir && !modeloId) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Debe seleccionar un modelo o marcar "Modelo por definir"' });
                return;
            }

            Swal.fire({ title: 'Creando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch('/admin/articulos/crear-desde-levantamiento', {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body:    new FormData(event.target)
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    if (data.articulo) todosLosArticulos.push(data.articulo);
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearArticulo')).hide();
                    Swal.fire({ icon: 'success', title: '¡Creado!', text: 'Artículo creado y asociado correctamente', timer: 2000, showConfirmButton: false });
                    recargarArticulosCliente();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            })
            .catch(err => { Swal.close(); Swal.fire('Error', 'Error al crear: ' + err, 'error'); });
        }

        // ══════════════════════════════════════════
        // Recargar lista de artículos del cliente (vía AJAX)
        // ══════════════════════════════════════════
        function recargarArticulosCliente() {
            fetch(`/admin/levantamientos/cliente/${clienteId}/articulos`)
                .then(r => r.json())
                .then(data => {
                    articulosCliente = data;
                    renderizarListaDisponibleCompleta(data);
                })
                .catch(() => {});
        }

        function renderizarListaDisponibleCompleta(articulos) {
            const container = document.getElementById('articulos-disponibles');
            if (!articulos.length) {
                container.innerHTML = `<div class="alert alert-info" id="msgSinArticulos">
                    <i class="fas fa-info-circle me-2"></i>Este cliente no tiene artículos.
                    <button type="button" class="btn btn-sm btn-primary ms-2" onclick="abrirModalCrearArticulo()">
                        <i class="fas fa-plus me-1"></i>Crear artículo
                    </button>
                </div>`;
                return;
            }
            let html = '<div class="list-group">';
            articulos.forEach(art => {
                const yaAgregado  = articulosSeleccionados.find(a => a.id_articulo == art.Id_Articulos);
                const porDefinir  = art.modelo_por_definir == 1;
                html += `<div class="list-group-item ${yaAgregado ? 'disabled' : ''}" id="item-disponible-${art.Id_Articulos}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${art.Nombre}
                                ${art.Es_Principal ? '<span class="badge bg-primary ms-2">Principal</span>' : ''}
                                ${porDefinir ? '<span class="badge bg-warning text-dark ms-2"><i class="fas fa-question-circle me-1"></i>Por definir</span>' : ''}
                            </h6>
                            <p class="mb-0 text-muted small">
                                <strong>Marca:</strong> ${art.marca_nombre} | <strong>Modelo:</strong> ${art.modelo_nombre}
                            </p>
                        </div>
                        <button type="button" class="btn btn-sm ${yaAgregado ? 'btn-secondary' : 'btn-primary'}"
                            onclick="agregarArticulo(${art.Id_Articulos})" ${yaAgregado ? 'disabled' : ''}>
                            <i class="fas ${yaAgregado ? 'fa-check' : 'fa-plus'} me-1"></i>
                            ${yaAgregado ? 'Agregado' : 'Agregar'}
                        </button>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        }

        // ══════════════════════════════════════════
        // MODAL: Crear Marca rápida
        // ══════════════════════════════════════════
        function abrirModalCrearMarca() {
            document.getElementById('formCrearMarca').reset();
            document.getElementById('alerta_marca').style.display = 'none';
            document.getElementById('nombre_marca_rapida').classList.remove('input-duplicado');
            new bootstrap.Modal(document.getElementById('modalCrearMarca')).show();
        }

        function guardarNuevaMarca(event) {
            event.preventDefault();
            if (verificarDuplicadoMarca()) {
                Swal.fire({ icon: 'warning', title: 'Marca duplicada', text: 'Ya existe una marca con ese nombre.', confirmButtonText: 'Entendido' });
                return;
            }
            Swal.fire({ title: 'Creando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch('/admin/articulos/crear-marca-rapida', {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body:    new FormData(event.target)
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearMarca')).hide();
                    const o = document.createElement('option');
                    o.value = data.marca.Id_Marca;
                    o.textContent = data.marca.Nombre;
                    o.selected = true;
                    document.getElementById('marca_articulo').appendChild(o);
                    marcasDisponibles.push(data.marca);
                    verificarDuplicadoArticulo();
                    Swal.fire({ icon: 'success', title: '¡Marca creada!', timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            });
        }

        // ══════════════════════════════════════════
        // MODAL: Crear Modelo rápido (desde crear artículo)
        // ══════════════════════════════════════════
        function abrirModalCrearModeloRapido() {
            document.getElementById('formCrearModeloRapido').reset();
            document.getElementById('alerta_modelo_rapido').style.display = 'none';
            document.getElementById('nombre_modelo_rapido').classList.remove('input-duplicado');
            new bootstrap.Modal(document.getElementById('modalCrearModeloRapido')).show();
        }

        function guardarNuevoModeloRapido(event) {
            event.preventDefault();
            if (verificarDuplicadoModeloRapido()) {
                Swal.fire({ icon: 'warning', title: 'Modelo duplicado', text: 'Ya existe un modelo con ese nombre.', confirmButtonText: 'Entendido' });
                return;
            }
            Swal.fire({ title: 'Creando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch('/admin/articulos/crear-modelo-rapido', {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body:    new FormData(event.target)
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearModeloRapido')).hide();
                    const o = document.createElement('option');
                    o.value = data.modelo.Id_Modelo;
                    o.textContent = data.modelo.Nombre;
                    o.selected = true;
                    document.getElementById('modelo_articulo').appendChild(o);
                    const cb = document.getElementById('modelo_por_definir_articulo');
                    if (cb) { cb.checked = false; cb.disabled = true; }
                    modelosDisponibles.push(data.modelo);
                    // Agregar también al select del modal de definir modelo
                    const selDefinir = document.getElementById('definirModeloSelect');
                    if (selDefinir) {
                        const od = document.createElement('option');
                        od.value = data.modelo.Id_Modelo;
                        od.textContent = data.modelo.Nombre;
                        selDefinir.appendChild(od);
                    }
                    verificarDuplicadoArticulo();
                    Swal.fire({ icon: 'success', title: '¡Modelo creado!', timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            });
        }

        function toggleModeloRequerido() {
            const cb  = document.getElementById('modelo_por_definir_articulo');
            const sel = document.getElementById('modelo_articulo');
            if (cb.checked) { sel.required = false; sel.disabled = true; sel.value = ''; }
            else            { sel.required = false; sel.disabled = false; }
        }

        function verificarModeloSeleccionado() {
            const sel = document.getElementById('modelo_articulo');
            const cb  = document.getElementById('modelo_por_definir_articulo');
            if (sel.value) { cb.checked = false; cb.disabled = true; }
            else           { cb.disabled = false; }
        }

        // ══════════════════════════════════════════
        // MODAL: Definir Modelo (artículos por definir)
        // ══════════════════════════════════════════
        function abrirModalDefinirModelo(id, idArticuloReal, nombre, marca) {
            document.getElementById('definirArticuloId').value       = id;
            document.getElementById('definirArticuloIdReal').value   = idArticuloReal;
            document.getElementById('definirArticuloNombre').textContent = nombre;
            document.getElementById('definirArticuloMarca').textContent  = marca;
            document.getElementById('definirModeloSelect').value     = '';
            new bootstrap.Modal(document.getElementById('modalDefinirModelo')).show();
        }

        function guardarModeloDefinido() {
            const id           = parseInt(document.getElementById('definirArticuloId').value);
            const idArticuloReal = parseInt(document.getElementById('definirArticuloIdReal').value);
            const modeloId     = document.getElementById('definirModeloSelect').value;

            if (!modeloId) { Swal.fire('Atención', 'Debe seleccionar un modelo', 'warning'); return; }

            const articulo = articulosSeleccionados.find(a => a.id == id);
            if (!articulo) return;

            articulo.modelo_por_definir = 0;
            const modelo = modelosDisponibles.find(m => m.Id_Modelo == modeloId);
            if (modelo) articulo.modelo_nombre = modelo.Nombre;

            Swal.fire({ title: 'Guardando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch('/admin/articulos/actualizar-modelo', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body:    JSON.stringify({ articulo_id: idArticuloReal, modelo_id: modeloId })
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalDefinirModelo')).hide();
                    renderizarArticulosSeleccionados();

                    const hayPorDefinir = articulosSeleccionados.some(a => a.modelo_por_definir == 1);
                    if (!hayPorDefinir) {
                        const cb = document.getElementById('modelo_por_definir');
                        if (cb.checked) { cb.checked = false; actualizarStatusPreview(); }
                        Swal.fire({
                            icon: 'success', title: '¡Modelo definido!',
                            html: 'Todos los modelos han sido definidos.',
                            timer: 3000, showConfirmButton: false
                        });
                    } else {
                        Swal.fire({ icon: 'success', title: '¡Modelo definido!', timer: 2000, showConfirmButton: false });
                    }
                } else {
                    Swal.fire('Error', data.message || 'Error al actualizar', 'error');
                }
            })
            .catch(() => { Swal.close(); Swal.fire('Error', 'Error al actualizar el modelo', 'error'); });
        }

        // ══════════════════════════════════════════
        // MODAL: Crear Modelo desde "Definir Modelo"
        // ══════════════════════════════════════════
        function abrirModalCrearModeloDesdeDefinir() {
            document.getElementById('formCrearModeloDesdeDefinir').reset();
            new bootstrap.Modal(document.getElementById('modalCrearModeloDesdeDefinir')).show();
        }

        function guardarNuevoModeloDesdeDefinir(event) {
            event.preventDefault();
            Swal.fire({ title: 'Creando modelo...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch('/admin/articulos/crear-modelo-rapido', {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body:    new FormData(event.target)
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearModeloDesdeDefinir')).hide();

                    // Agregar al select del modal Definir
                    const selDefinir = document.getElementById('definirModeloSelect');
                    const o = document.createElement('option');
                    o.value = data.modelo.Id_Modelo;
                    o.textContent = data.modelo.Nombre;
                    o.selected = true;
                    selDefinir.appendChild(o);

                    // Agregar al select del modal Crear Artículo también
                    const selCrear = document.getElementById('modelo_articulo');
                    if (selCrear) {
                        const o2 = document.createElement('option');
                        o2.value = data.modelo.Id_Modelo;
                        o2.textContent = data.modelo.Nombre;
                        selCrear.appendChild(o2);
                    }

                    modelosDisponibles.push(data.modelo);
                    Swal.fire({ icon: 'success', title: '¡Modelo creado!', text: 'Ya puedes seleccionarlo', timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            })
            .catch(() => { Swal.close(); Swal.fire('Error', 'Error al crear el modelo', 'error'); });
        }
    </script>
</body>
</html>