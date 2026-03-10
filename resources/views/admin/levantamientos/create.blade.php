<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nuevo Levantamiento - Sistema</title>
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
        .char-counter.error { color: #1D67A8; font-weight: 600; }
        .input-duplicado { border-color: #1D67A8 !important; box-shadow: 0 0 0 .2rem rgba(220,53,69,.25) !important; }
        .badge-duplicado { font-size: .7rem; }
        .btn-primary { background-color: #1D67A8; border-color: #1D67A8; }
        .btn-primary:hover { background-color: #175d96; border-color: #175d96; }
        .bg-primary { background-color: #1D67A8 !important; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        /* ── Indicador de borrador ── */
        #draftIndicator {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            background: #1D67A8;
            color: #fff;
            padding: 10px 18px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(29,103,168,0.35);
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            pointer-events: none;
            user-select: none;
        }
        #draftIndicator i { font-size: 0.9rem; }

        /* ── Badge de borrador en top-bar ── */
        #draftBadge {
            display: none;
            font-size: 0.75rem;
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 4px 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        #draftBadge:hover { background: #ffeaa7; }

        /* ── Pulso en botón guardar ── */
        @keyframes savePulse {
            0%   { box-shadow: 0 0 0 0 rgba(29,103,168,0.5); }
            70%  { box-shadow: 0 0 0 8px rgba(29,103,168,0); }
            100% { box-shadow: 0 0 0 0 rgba(29,103,168,0); }
        }
        .draft-pulsing { animation: savePulse 1s ease-out; }
    </style>
</head>
<body>

    {{-- ── Indicador flotante de borrador guardado ── --}}
    <div id="draftIndicator">
        <i class="fas fa-cloud-upload-alt"></i>
        Borrador guardado
    </div>

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
            <div class="d-flex align-items-center gap-3">
                <h2 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nuevo Levantamiento</h2>
                {{-- Badge de borrador activo --}}
                <button type="button" id="draftBadge" onclick="confirmarDescartarBorrador()" title="Hay un borrador guardado. Clic para descartarlo.">
                    <i class="fas fa-save me-1"></i> Borrador activo &nbsp;·&nbsp; <i class="fas fa-times"></i>
                </button>
            </div>
            <div>
                <a href="{{ route('admin.levantamientos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        <form id="formLevantamiento" action="{{ route('admin.levantamientos.store') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Columna Principal -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Levantamiento <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tipo_levantamiento_id" name="tipo_levantamiento_id" required>
                                        <option value="">Seleccione un tipo...</option>
                                        @foreach($tiposLevantamiento as $tipo)
                                            <option value="{{ $tipo->Id_Tipo_Levantamiento }}">{{ $tipo->Nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <select class="form-select" id="cliente_id" name="cliente_id" required>
                                        <option value="">Seleccione un cliente...</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->Id_Cliente }}">{{ $cliente->Nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="camposDinamicos"></div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i>Artículos del Cliente</h5>
                            <button type="button" class="btn btn-light btn-sm" id="btnCrearArticulo" disabled onclick="abrirModalCrearArticulo()">
                                <i class="fas fa-plus me-1"></i>Crear Nuevo Artículo
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="articulos-disponibles">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Seleccione un cliente para ver sus artículos
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Artículos del Levantamiento</h5>
                        </div>
                        <div class="card-body">
                            <div id="articulos-seleccionados">
                                <div class="alert alert-secondary text-center mb-0">
                                    <i class="fas fa-inbox me-2"></i>No hay artículos agregados
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Lateral -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-flag me-2"></i>Estado del Levantamiento</h5>
                        </div>
                        <div class="card-body">
                            <div class="por-definir-box">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="modelo_por_definir" name="modelo_por_definir" onchange="actualizarStatusPreview()">
                                    <label class="form-check-label fw-semibold" for="modelo_por_definir">
                                        <i class="fas fa-question-circle me-1"></i>Modelo / Especificaciones por definir
                                    </label>
                                </div>
                                <small class="text-muted d-block">Activo → <strong>Pendiente</strong> | Inactivo → <strong>En Proceso</strong></small>
                            </div>
                            <div class="text-center mt-3">
                                <p class="mb-2">Estado al guardar:</p>
                                <span id="statusPreview" class="badge bg-info text-white fs-6">En Proceso</span>
                            </div>
                        </div>
                    </div>

                    {{-- ── Card de borrador ── --}}
                    <div class="card mb-4" id="draftCard" style="display:none!important; border-color:#ffc107;">
                        <div class="card-body py-3">
                            <h6 class="mb-1 text-warning-emphasis"><i class="fas fa-save me-1"></i> Borrador guardado</h6>
                            <small id="draftTimestamp" class="text-muted d-block mb-2"></small>
                            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="confirmarDescartarBorrador()">
                                <i class="fas fa-trash me-1"></i> Descartar borrador
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2" id="btnGuardarLev">
                                <i class="fas fa-save me-2"></i>Guardar Levantamiento
                            </button>
                            <a href="{{ route('admin.levantamientos.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- ══ MODAL: Crear / Asociar Artículo ══ -->
    <div class="modal fade" id="modalCrearArticulo" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Agregar Artículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Cliente: <strong id="nombreClienteArticulo"></strong>
                    </div>
                    <ul class="nav nav-tabs mb-3" id="articuloTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="existentes-tab" data-bs-toggle="tab" data-bs-target="#existentes" type="button">
                                <i class="fas fa-search me-2"></i>Seleccionar Existente
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="nuevo-tab" data-bs-toggle="tab" data-bs-target="#nuevo" type="button">
                                <i class="fas fa-plus me-2"></i>Crear Nuevo
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="articuloTabsContent">

                        <!-- Tab: Existentes -->
                        <div class="tab-pane fade show active" id="existentes" role="tabpanel">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="buscarArticuloExistente" placeholder="Buscar por nombre, marca o modelo...">
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
                                <input type="hidden" id="clienteIdArticulo" name="cliente_id">

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
                                            </select>
                                            <button type="button" class="btn btn-outline-primary" onclick="abrirModalCrearMarca()"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Modelo</label>
                                        <div class="input-group">
                                            <select class="form-select" id="modelo_articulo" name="modelo_id"
                                                    onchange="verificarModeloSeleccionado(); verificarDuplicadoArticulo()">
                                                <option value="">Seleccione un modelo...</option>
                                            </select>
                                            <button type="button" class="btn btn-outline-primary" onclick="abrirModalCrearModelo()"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="alert alert-warning py-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="modelo_por_definir_articulo" name="modelo_por_definir"
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
                                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Crear Artículo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: Crear Marca ══ -->
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
                            <input type="text" class="form-control" id="nombre_marca" name="nombre"
                                   required maxlength="100"
                                   placeholder="Ej: Hikvision, TP-Link..."
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

    <!-- ══ MODAL: Crear Modelo ══ -->
    <div class="modal fade" id="modalCrearModelo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-box me-2"></i>Crear Nuevo Modelo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrearModelo" onsubmit="guardarNuevoModelo(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_modelo" name="nombre"
                                   required maxlength="100"
                                   placeholder="Ej: DS-2CD2143G0..."
                                   oninput="verificarDuplicadoModelo()">
                            <div class="mt-1">
                                <span id="alerta_modelo" class="text-danger small" style="display:none">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let articulosCliente       = [];
        let articulosSeleccionados = [];
        let contadorArticulos      = 0;
        let clienteSeleccionadoId  = null;
        let clienteSeleccionadoNombre = '';
        let marcasDisponibles      = [];
        let modelosDisponibles     = [];
        let todosLosArticulos      = [];

        // ══════════════════════════════════════════════════════════════
        //  SISTEMA DE BORRADOR AUTOMÁTICO
        // ══════════════════════════════════════════════════════════════
        const DRAFT_KEY          = 'levantamiento_borrador';
        const DRAFT_ARTICULOS_KEY = 'levantamiento_borrador_articulos';

        /** Persiste el estado actual en localStorage */
        function guardarBorrador() {
            try {
                const data = {
                    tipo_levantamiento_id : document.getElementById('tipo_levantamiento_id')?.value || '',
                    cliente_id            : document.getElementById('cliente_id')?.value || '',
                    modelo_por_definir    : document.getElementById('modelo_por_definir')?.checked || false,
                    camposDinamicos       : {},
                    timestamp             : new Date().toISOString(),
                };

                // Capturar campos dinámicos del tipo
                document.querySelectorAll('#camposDinamicos input, #camposDinamicos select, #camposDinamicos textarea')
                    .forEach(el => {
                        if (el.name) data.camposDinamicos[el.name] = el.type === 'checkbox' ? el.checked : el.value;
                    });

                localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
                localStorage.setItem(DRAFT_ARTICULOS_KEY, JSON.stringify(articulosSeleccionados));

                actualizarUIDraftBadge(data.timestamp);
                mostrarIndicadorGuardado();
            } catch (e) {
                console.warn('No se pudo guardar el borrador:', e);
            }
        }

        /** Muestra / actualiza el badge y la card de borrador */
        function actualizarUIDraftBadge(timestamp) {
            const badge = document.getElementById('draftBadge');
            if (badge) badge.style.display = 'inline-flex';

            // Card lateral
            const card = document.getElementById('draftCard');
            const ts   = document.getElementById('draftTimestamp');
            if (card && ts && timestamp) {
                const d = new Date(timestamp);
                ts.textContent = 'Guardado: ' + d.toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
                card.style.cssText = ''; // quitar !important display:none
                card.style.display = 'block';
            }
        }

        /** Oculta los elementos de borrador */
        function ocultarUIDraft() {
            const badge = document.getElementById('draftBadge');
            if (badge) badge.style.display = 'none';
            const card = document.getElementById('draftCard');
            if (card) card.style.display = 'none';
        }

        /** Tooltip flotante de confirmación de guardado */
        function mostrarIndicadorGuardado() {
            const ind = document.getElementById('draftIndicator');
            if (!ind) return;
            ind.style.opacity = '1';
            ind.style.transform = 'translateY(0)';
            clearTimeout(window._draftHideTimer);
            window._draftHideTimer = setTimeout(() => {
                ind.style.opacity = '0';
                ind.style.transform = 'translateY(4px)';
            }, 2200);

            // Pulso en el botón guardar
            const btn = document.getElementById('btnGuardarLev');
            if (btn) {
                btn.classList.add('draft-pulsing');
                setTimeout(() => btn.classList.remove('draft-pulsing'), 1000);
            }
        }

        /** Pregunta si descartar el borrador activo */
        async function confirmarDescartarBorrador() {
            const result = await Swal.fire({
                title: '¿Descartar borrador?',
                text: 'Se perderá toda la información guardada hasta ahora.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, descartar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#1D67A8',
            });
            if (result.isConfirmed) {
                limpiarBorrador();
                ocultarUIDraft();
                Swal.fire({ icon:'success', title:'Borrador descartado', timer:1500, showConfirmButton:false, toast:true, position:'top-end' });
            }
        }

        /** Elimina las claves del localStorage */
        function limpiarBorrador() {
            localStorage.removeItem(DRAFT_KEY);
            localStorage.removeItem(DRAFT_ARTICULOS_KEY);
        }

        /** Verifica al cargar si hay un borrador y ofrece restaurarlo */
        async function restaurarBorrador() {
            try {
                const raw          = localStorage.getItem(DRAFT_KEY);
                const rawArticulos = localStorage.getItem(DRAFT_ARTICULOS_KEY);
                if (!raw) return;

                const data        = JSON.parse(raw);
                const artGuardados = rawArticulos ? JSON.parse(rawArticulos) : [];

                // Si no hay nada útil guardado, ignorar
                const tieneContenido = data.tipo_levantamiento_id || data.cliente_id
                    || artGuardados.length > 0
                    || Object.keys(data.camposDinamicos || {}).length > 0;
                if (!tieneContenido) return;

                const fecha    = new Date(data.timestamp);
                const fechaStr = fecha.toLocaleDateString('es-MX', {
                    day:'2-digit', month:'2-digit', year:'numeric',
                    hour:'2-digit', minute:'2-digit'
                });

                const result = await Swal.fire({
                    title: '📋 Borrador encontrado',
                    html: `
                        <div style="text-align:left;background:#f8f9fa;border-radius:8px;padding:16px;margin-bottom:12px;">
                            <p class="mb-1"><i class="fas fa-clock me-1 text-muted"></i>
                                <small class="text-muted">Guardado el ${fechaStr}</small>
                            </p>
                            ${data.tipo_levantamiento_id
                                ? `<p class="mb-1"><i class="fas fa-tag me-1" style="color:#1D67A8"></i> Tipo de levantamiento seleccionado</p>`
                                : ''}
                            ${data.cliente_id
                                ? `<p class="mb-1"><i class="fas fa-building me-1" style="color:#1D67A8"></i> Cliente seleccionado</p>`
                                : ''}
                            ${artGuardados.length > 0
                                ? `<p class="mb-1"><i class="fas fa-box me-1" style="color:#1D67A8"></i> <strong>${artGuardados.length}</strong> artículo(s) en el carrito</p>`
                                : ''}
                            ${Object.keys(data.camposDinamicos||{}).length > 0
                                ? `<p class="mb-0"><i class="fas fa-list me-1" style="color:#1D67A8"></i> Campos adicionales con datos</p>`
                                : ''}
                        </div>
                        <p class="text-muted mb-0">¿Deseas continuar desde donde lo dejaste?</p>
                    `,
                    showCancelButton  : true,
                    confirmButtonText : '<i class="fas fa-undo me-1"></i> Restaurar borrador',
                    cancelButtonText  : '<i class="fas fa-trash me-1"></i> Descartar',
                    confirmButtonColor: '#1D67A8',
                    cancelButtonColor : '#6c757d',
                    reverseButtons    : true,
                    allowOutsideClick : false,
                });

                if (result.isConfirmed) {
                    await aplicarBorrador(data, artGuardados);
                    actualizarUIDraftBadge(data.timestamp);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    limpiarBorrador();
                }

            } catch (e) {
                console.warn('Error al restaurar borrador:', e);
            }
        }

        /** Aplica los datos del borrador al formulario paso a paso */
        async function aplicarBorrador(data, artGuardados) {
            const swalProgress = Swal.fire({
                title: 'Restaurando borrador...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // 1. Tipo de levantamiento → genera campos dinámicos
            if (data.tipo_levantamiento_id) {
                document.getElementById('tipo_levantamiento_id').value = data.tipo_levantamiento_id;
                cargarCamposTipo(data.tipo_levantamiento_id);
                await delay(700);
            }

            // 2. Cliente → carga artículos disponibles
            if (data.cliente_id) {
                const selCliente = document.getElementById('cliente_id');
                selCliente.value = data.cliente_id;
                clienteSeleccionadoId     = data.cliente_id;
                clienteSeleccionadoNombre = selCliente.options[selCliente.selectedIndex]?.text || '';
                document.getElementById('btnCrearArticulo').disabled = false;
                cargarArticulosCliente(data.cliente_id);
                await delay(800);
            }

            // 3. Checkbox "por definir"
            const cbPD = document.getElementById('modelo_por_definir');
            if (cbPD) {
                cbPD.checked = data.modelo_por_definir || false;
                actualizarStatusPreview();
            }

            // 4. Campos dinámicos (esperar a que estén en el DOM)
            await delay(200);
            Object.entries(data.camposDinamicos || {}).forEach(([name, value]) => {
                const el = document.querySelector(`#camposDinamicos [name="${name}"]`);
                if (!el) return;
                if (el.type === 'checkbox') el.checked = value;
                else el.value = value;
            });

            // 5. Artículos seleccionados
            if (artGuardados.length > 0) {
                articulosSeleccionados = artGuardados;
                contadorArticulos = Math.max(...artGuardados.map(a => a.id), 0);
                await delay(300);
                renderizarArticulosSeleccionados();
                mostrarArticulosDisponibles(articulosCliente);
            }

            Swal.close();
            Swal.fire({
                icon: 'success',
                title: '¡Borrador restaurado!',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        }

        /** Utilidad para esperar N milisegundos */
        const delay = ms => new Promise(res => setTimeout(res, ms));

        // ── Debounce del autosave (500 ms) ──────────────────────────────
        let _draftDebounce = null;
        function triggerAutosave() {
            clearTimeout(_draftDebounce);
            _draftDebounce = setTimeout(guardarBorrador, 500);
        }

        // ══════════════════════════════
        // SIDEBAR
        // ══════════════════════════════
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        document.getElementById('toggleBtn').addEventListener('click', () => {
            if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); }
            else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
        });

        // ══════════════════════════════
        // CONTADORES DE CARACTERES
        // ══════════════════════════════
        function actualizarContadorArt(inputId, contadorId) {
            const input = document.getElementById(inputId);
            const cnt   = document.getElementById(contadorId);
            if (!input || !cnt) return;
            const max = parseInt(input.getAttribute('maxlength'));
            const len = input.value.length;
            cnt.textContent = `${len} / ${max}`;
            cnt.className = 'char-counter ' + (len >= max ? 'error' : len > max * 0.9 ? 'warn' : 'text-muted');
        }

        // ══════════════════════════════
        // NORMALIZACIÓN
        // ══════════════════════════════
        function normalizar(str) {
            if (!str) return '';
            return String(str).trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        // ══════════════════════════════
        // VALIDACIÓN DUPLICADOS
        // ══════════════════════════════
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
                const mismoNombre = normalizar(a.Nombre) === valNombre;
                if (!mismoNombre) return false;
                const mismaMarca = marcaId !== '' && String(a.Id_Marca) === String(marcaId);
                if (!mismaMarca) return false;
                let mismoModelo = false;
                if (porDefinir) {
                    mismoModelo = (a.modelo_por_definir == 1 || !a.Id_Modelo);
                } else if (modeloId !== '') {
                    mismoModelo = String(a.Id_Modelo) === String(modeloId);
                } else {
                    return false;
                }
                return mismoModelo;
            });

            alerta.style.display = existe ? 'inline' : 'none';
            inputNombre.classList.toggle('input-duplicado', existe);
            return existe;
        }

        function verificarDuplicadoMarca() {
            const input  = document.getElementById('nombre_marca');
            const alerta = document.getElementById('alerta_marca');
            const val    = normalizar(input.value);
            const existe = val.length > 0 && marcasDisponibles.some(m => normalizar(m.Nombre) === val);
            alerta.style.display = existe ? 'inline' : 'none';
            input.classList.toggle('input-duplicado', existe);
            return existe;
        }

        function verificarDuplicadoModelo() {
            const input  = document.getElementById('nombre_modelo');
            const alerta = document.getElementById('alerta_modelo');
            const val    = normalizar(input.value);
            const existe = val.length > 0 && modelosDisponibles.some(m => normalizar(m.Nombre) === val);
            alerta.style.display = existe ? 'inline' : 'none';
            input.classList.toggle('input-duplicado', existe);
            return existe;
        }

        // ══════════════════════════════
        // EVENTOS PRINCIPALES
        // ══════════════════════════════
        document.addEventListener('DOMContentLoaded', function() {

            // ── Autosave: escuchar cambios en el formulario ──────────────
            document.getElementById('formLevantamiento').addEventListener('input',  triggerAutosave);
            document.getElementById('formLevantamiento').addEventListener('change', triggerAutosave);

            // ── Limpiar borrador al guardar correctamente ─────────────────
            document.getElementById('formLevantamiento').addEventListener('submit', function(e) {
                if (articulosSeleccionados.length === 0) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Atención', text: 'Debe agregar al menos un artículo', confirmButtonText: 'Entendido' });
                } else {
                    limpiarBorrador();   // limpiar antes de enviar
                }
            });

            // ── Selector de cliente ──────────────────────────────────────
            document.getElementById('cliente_id').addEventListener('change', function() {
                const clienteId = this.value;
                if (clienteId) {
                    cargarArticulosCliente(clienteId);
                    document.getElementById('btnCrearArticulo').disabled = false;
                    clienteSeleccionadoId     = clienteId;
                    clienteSeleccionadoNombre = this.options[this.selectedIndex].text;
                } else {
                    limpiarArticulos();
                    document.getElementById('btnCrearArticulo').disabled = true;
                }
            });

            // ── Selector de tipo ─────────────────────────────────────────
            document.getElementById('tipo_levantamiento_id').addEventListener('change', function() {
                if (this.value) cargarCamposTipo(this.value);
                else document.getElementById('camposDinamicos').innerHTML = '';
            });

            // ── Verificar borrador al cargar ──────────────────────────────
            restaurarBorrador();

            // ── Si ya hay borrador activo, mostrar badge ──────────────────
            const rawExistente = localStorage.getItem(DRAFT_KEY);
            if (rawExistente) {
                try {
                    const d = JSON.parse(rawExistente);
                    if (d.timestamp) actualizarUIDraftBadge(d.timestamp);
                } catch(_) {}
            }
        });

        // ══════════════════════════════
        // ARTÍCULOS DEL CLIENTE
        // ══════════════════════════════
        function cargarArticulosCliente(clienteId) {
            const container = document.getElementById('articulos-disponibles');
            container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Cargando...</p></div>';
            fetch(`/admin/levantamientos/cliente/${clienteId}/articulos`)
                .then(r => r.json())
                .then(data => { articulosCliente = data; mostrarArticulosDisponibles(data); })
                .catch(() => container.innerHTML = '<div class="alert alert-danger">Error al cargar</div>');
        }

        function mostrarArticulosDisponibles(articulos) {
            const container = document.getElementById('articulos-disponibles');
            if (!articulos.length) {
                container.innerHTML = `<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Sin artículos. <button type="button" class="btn btn-sm btn-primary ms-2" onclick="abrirModalCrearArticulo()">Crear artículo</button></div>`;
                return;
            }
            let html = '<div class="list-group">';
            articulos.forEach(art => {
                const porDefinir = art.modelo_por_definir == 1;
                const yaAgregado = articulosSeleccionados.find(a => a.id_articulo == art.Id_Articulos);
                html += `<div class="list-group-item ${yaAgregado ? 'disabled' : ''}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${art.Nombre}
                                ${art.Es_Principal ? '<span class="badge bg-primary ms-2">Principal</span>' : ''}
                                ${porDefinir ? '<span class="badge bg-warning text-dark ms-2"><i class="fas fa-question-circle me-1"></i>Por definir</span>' : ''}
                            </h6>
                            <p class="mb-1 text-muted small"><strong>Marca:</strong> ${art.marca_nombre} | <strong>Modelo:</strong> ${art.modelo_nombre}</p>
                        </div>
                        <button type="button" class="btn btn-sm ${yaAgregado ? 'btn-secondary' : 'btn-primary'}"
                                onclick="agregarArticulo(${art.Id_Articulos})" ${yaAgregado ? 'disabled' : ''}>
                            <i class="fas ${yaAgregado ? 'fa-check' : 'fa-plus'} me-1"></i>${yaAgregado ? 'Agregado' : 'Agregar'}
                        </button>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        }

        function agregarArticulo(articuloId) {
            const articulo = articulosCliente.find(a => a.Id_Articulos == articuloId);
            if (!articulo) return;
            if (articulosSeleccionados.find(a => a.id_articulo == articuloId)) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'Ya está agregado', timer: 2000, showConfirmButton: false });
                return;
            }
            contadorArticulos++;
            articulosSeleccionados.push({
                id: contadorArticulos, id_articulo: articuloId, nombre: articulo.Nombre,
                marca_nombre: articulo.marca_nombre, modelo_nombre: articulo.modelo_nombre,
                modelo_por_definir: articulo.modelo_por_definir || 0, cantidad: 1, precio_unitario: 0, notas: ''
            });
            if (articulo.modelo_por_definir == 1) {
                document.getElementById('modelo_por_definir').checked = true;
                actualizarStatusPreview();
            }
            renderizarArticulosSeleccionados();
            mostrarArticulosDisponibles(articulosCliente);
            triggerAutosave(); // <-- guardar al agregar artículo
            Swal.fire({ icon: 'success', title: 'Agregado', timer: 1500, showConfirmButton: false });
        }

        function renderizarArticulosSeleccionados() {
            const container = document.getElementById('articulos-seleccionados');
            if (!articulosSeleccionados.length) {
                container.innerHTML = '<div class="alert alert-secondary text-center mb-0"><i class="fas fa-inbox me-2"></i>No hay artículos</div>';
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-bordered table-hover mb-0"><thead class="table-light"><tr><th>Artículo</th><th>Marca</th><th>Modelo</th><th width="100">Cantidad</th><th width="150">Precio Unit.</th><th width="200">Notas</th><th width="100">Subtotal</th><th width="60"></th></tr></thead><tbody>';
            articulosSeleccionados.forEach(art => {
                const subtotal = (art.cantidad * art.precio_unitario).toFixed(2);
                html += `<tr>
                    <td><strong>${art.nombre}</strong>${art.modelo_por_definir ? '<span class="badge bg-warning text-dark ms-2 small">Por definir</span>' : ''}
                        <input type="hidden" name="articulos[${art.id}][id_articulo]" value="${art.id_articulo}">
                        <input type="hidden" name="articulos[${art.id}][modelo_por_definir]" value="${art.modelo_por_definir}">
                    </td>
                    <td>${art.marca_nombre}</td>
                    <td>${art.modelo_nombre}</td>
                    <td><input type="number" class="form-control form-control-sm" min="1" value="${art.cantidad}" name="articulos[${art.id}][cantidad]" onchange="actualizarCantidad(${art.id},this.value)"></td>
                    <td><div class="input-group input-group-sm"><span class="input-group-text">$</span><input type="number" class="form-control" min="0" step="0.01" value="${art.precio_unitario}" name="articulos[${art.id}][precio_unitario]" onchange="actualizarPrecio(${art.id},this.value)"></div></td>
                    <td><input type="text" class="form-control form-control-sm" value="${art.notas}" name="articulos[${art.id}][notas]" onchange="actualizarNotas(${art.id},this.value)" placeholder="Notas..."></td>
                    <td class="text-end"><strong>$${subtotal}</strong></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="eliminarArticulo(${art.id})"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            });
            const total = articulosSeleccionados.reduce((s, a) => s + a.cantidad * a.precio_unitario, 0).toFixed(2);
            html += `</tbody><tfoot class="table-light"><tr><th colspan="6" class="text-end">TOTAL:</th><th class="text-end"><strong>$${total}</strong></th><th></th></tr></tfoot></table></div>`;
            container.innerHTML = html;
        }

        function actualizarCantidad(id, v) { const a = articulosSeleccionados.find(x => x.id==id); if(a){a.cantidad=parseInt(v)||1; renderizarArticulosSeleccionados(); triggerAutosave();} }
        function actualizarPrecio(id, v)   { const a = articulosSeleccionados.find(x => x.id==id); if(a){a.precio_unitario=parseFloat(v)||0; renderizarArticulosSeleccionados(); triggerAutosave();} }
        function actualizarNotas(id, v)    { const a = articulosSeleccionados.find(x => x.id==id); if(a){ a.notas=v; triggerAutosave(); } }
        function eliminarArticulo(id) {
            Swal.fire({ title:'¿Eliminar?', icon:'warning', showCancelButton:true, confirmButtonColor:'#dc3545', confirmButtonText:'Sí, eliminar' })
            .then(r => {
                if(r.isConfirmed){
                    articulosSeleccionados = articulosSeleccionados.filter(a => a.id != id);
                    renderizarArticulosSeleccionados();
                    mostrarArticulosDisponibles(articulosCliente);
                    triggerAutosave(); // guardar tras eliminar
                }
            });
        }

        function actualizarStatusPreview() {
            const cb = document.getElementById('modelo_por_definir');
            const b  = document.getElementById('statusPreview');
            if (cb.checked) { b.textContent='Pendiente'; b.className='badge bg-warning text-dark fs-6'; }
            else            { b.textContent='En Proceso'; b.className='badge bg-info text-white fs-6'; }
        }

        function cargarCamposTipo(tipoId) {
            fetch(`/admin/levantamientos/tipo/${tipoId}/formulario`)
                .then(r => r.json())
                .then(data => {
                    marcasDisponibles  = data.marcas;
                    modelosDisponibles = data.modelos;
                    renderizarCamposDinamicos(data.campos);
                });
        }

        function renderizarCamposDinamicos(campos) {
            const container = document.getElementById('camposDinamicos');
            container.innerHTML = '';
            const excluidos = ['articulo','cantidad','marca','modelo','precio_unitario','servicio_profesional'];
            campos.forEach(campo => {
                if (excluidos.includes(campo.Nombre_Campo)) return;
                let html = `<div class="campo-dinamico mb-3"><label class="form-label">${campo.Etiqueta} ${campo.Es_Requerido ? '<span class="text-danger">*</span>' : ''}</label>`;
                switch(campo.Tipo_Input) {
                    case 'textarea': html += `<textarea class="form-control" name="${campo.Nombre_Campo}" rows="3" ${campo.Es_Requerido?'required':''} placeholder="${campo.Placeholder||''}"></textarea>`; break;
                    case 'number':   html += `<input type="number" class="form-control" name="${campo.Nombre_Campo}" ${campo.Es_Requerido?'required':''} placeholder="${campo.Placeholder||''}" step="0.01">`; break;
                    default:         html += `<input type="${campo.Tipo_Input}" class="form-control" name="${campo.Nombre_Campo}" ${campo.Es_Requerido?'required':''} placeholder="${campo.Placeholder||''}">`;
                }
                html += '</div>';
                container.innerHTML += html;
            });
        }

        function limpiarArticulos() {
            articulosSeleccionados=[]; articulosCliente=[]; contadorArticulos=0;
            document.getElementById('articulos-disponibles').innerHTML = '<div class="alert alert-info mb-0"><i class="fas fa-info-circle me-2"></i>Seleccione un cliente</div>';
            document.getElementById('articulos-seleccionados').innerHTML = '<div class="alert alert-secondary text-center mb-0"><i class="fas fa-inbox me-2"></i>No hay artículos</div>';
        }

        // ══════════════════════════════
        // MODAL CREAR ARTÍCULO
        // ══════════════════════════════
        function abrirModalCrearArticulo() {
            if (!clienteSeleccionadoId) { Swal.fire({ icon:'warning', title:'Atención', text:'Primero selecciona un cliente' }); return; }
            document.getElementById('clienteIdArticulo').value = clienteSeleccionadoId;
            document.getElementById('nombreClienteArticulo').textContent = clienteSeleccionadoNombre;
            document.getElementById('formCrearArticulo').reset();
            document.getElementById('clienteIdArticulo').value = clienteSeleccionadoId;
            document.getElementById('modelo_por_definir_articulo').checked = false;
            document.getElementById('modelo_articulo').required  = false;
            document.getElementById('modelo_articulo').disabled  = false;
            document.getElementById('alerta_nombre_art').style.display = 'none';
            document.getElementById('nombre_articulo').classList.remove('input-duplicado');
            document.getElementById('cnt_nombre_art').textContent = '0 / 500';
            document.getElementById('cnt_desc_art').textContent  = '0 / 500';
            cargarMarcasEnSelect();
            cargarModelosEnSelect();
            cargarArticulosExistentes();
            new bootstrap.Modal(document.getElementById('modalCrearArticulo')).show();
        }

        function cargarArticulosExistentes() {
            const container = document.getElementById('listaArticulosExistentes');
            container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="text-muted mt-2">Cargando...</p></div>';
            fetch('/admin/articulos/listar-todos', { headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'} })
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

            document.getElementById('buscarArticuloExistente').oninput = function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.articulo-existente-item').forEach(item => {
                    const match = [item.dataset.nombre,item.dataset.marca,item.dataset.modelo].some(v => v.toLowerCase().includes(q));
                    item.style.display = match ? '' : 'none';
                });
            };
        }

        function asociarArticuloExistente(articuloId) {
            Swal.fire({ title:'Asociando...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
            fetch('/admin/articulos/asociar-a-cliente', {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
                body:JSON.stringify({articulo_id:articuloId,cliente_id:clienteSeleccionadoId})
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearArticulo')).hide();
                    Swal.fire({ icon:'success', title:'¡Artículo asociado!', timer:2000, showConfirmButton:false });
                    cargarArticulosCliente(clienteSeleccionadoId);
                } else {
                    Swal.fire({ icon:'error', title:'Error', text:data.message||'Error al asociar' });
                }
            })
            .catch(() => { Swal.close(); Swal.fire('Error','Error al asociar el artículo','error'); });
        }

        function cargarMarcasEnSelect() {
            const select = document.getElementById('marca_articulo');
            select.innerHTML = '<option value="">Seleccione una marca...</option>';
            marcasDisponibles.forEach(m => { const o=document.createElement('option'); o.value=m.Id_Marca; o.textContent=m.Nombre; select.appendChild(o); });
        }

        function cargarModelosEnSelect() {
            const select = document.getElementById('modelo_articulo');
            select.innerHTML = '<option value="">Seleccione un modelo...</option>';
            modelosDisponibles.forEach(m => { const o=document.createElement('option'); o.value=m.Id_Modelo; o.textContent=m.Nombre; select.appendChild(o); });
        }

        function toggleModeloRequerido() {
            const cb  = document.getElementById('modelo_por_definir_articulo');
            const sel = document.getElementById('modelo_articulo');
            if (cb.checked) { sel.required=false; sel.disabled=true; sel.value=''; }
            else            { sel.required=false; sel.disabled=false; }
        }

        function verificarModeloSeleccionado() {
            const sel = document.getElementById('modelo_articulo');
            const cb  = document.getElementById('modelo_por_definir_articulo');
            if (sel.value) { cb.checked=false; cb.disabled=true; }
            else           { cb.disabled=false; }
        }

        // ══════════════════════════════
        // GUARDAR NUEVO ARTÍCULO
        // ══════════════════════════════
        function guardarNuevoArticulo(event) {
            event.preventDefault();
            const nombreVal = document.getElementById('nombre_articulo').value;
            const descVal   = document.getElementById('descripcion_articulo').value;
            if (nombreVal.length > 500) { Swal.fire({ icon:'error', title:'Nombre muy largo', text:'El nombre no puede superar los 500 caracteres.' }); return; }
            if (descVal.length > 500)   { Swal.fire({ icon:'error', title:'Descripción muy larga', text:'La descripción no puede superar los 500 caracteres.' }); return; }
            if (verificarDuplicadoArticulo()) {
                Swal.fire({
                    icon: 'warning', title: 'Artículo duplicado',
                    html: `Ya existe un artículo con el mismo <strong>nombre</strong>, <strong>marca</strong> y <strong>modelo</strong>.<br><br>
                           Puedes seleccionarlo en la pestaña <em>"Seleccionar Existente"</em>, o cambiar la marca o el modelo para crear uno diferente.`,
                    confirmButtonText: 'Entendido'
                }); return;
            }
            const modeloPorDefinir = document.getElementById('modelo_por_definir_articulo').checked;
            const modeloId         = document.getElementById('modelo_articulo').value;
            if (!modeloPorDefinir && !modeloId) { Swal.fire({ icon:'error', title:'Error', text:'Debe seleccionar un modelo o marcar "Modelo por definir"' }); return; }

            Swal.fire({ title:'Creando...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
            fetch('/admin/articulos/crear-desde-levantamiento', {
                method:'POST',
                headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
                body: new FormData(event.target)
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    if (data.articulo) todosLosArticulos.push(data.articulo);
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearArticulo')).hide();
                    Swal.fire({ icon:'success', title:'¡Creado!', text:'Artículo creado y asociado correctamente', timer:2000, showConfirmButton:false });
                    cargarArticulosCliente(clienteSeleccionadoId);
                } else {
                    Swal.fire({ icon:'error', title:'Error', text:data.message });
                }
            })
            .catch(err => { Swal.close(); Swal.fire('Error','Error al crear: '+err,'error'); });
        }

        // ══════════════════════════════
        // CREAR MARCA
        // ══════════════════════════════
        function abrirModalCrearMarca() {
            document.getElementById('formCrearMarca').reset();
            document.getElementById('alerta_marca').style.display = 'none';
            document.getElementById('nombre_marca').classList.remove('input-duplicado');
            new bootstrap.Modal(document.getElementById('modalCrearMarca')).show();
        }

        function guardarNuevaMarca(event) {
            event.preventDefault();
            if (verificarDuplicadoMarca()) { Swal.fire({ icon:'warning', title:'Marca duplicada', text:'Ya existe una marca con ese nombre.', confirmButtonText:'Entendido' }); return; }
            Swal.fire({ title:'Creando...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
            fetch('/admin/articulos/crear-marca-rapida', {
                method:'POST',
                headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
                body: new FormData(event.target)
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearMarca')).hide();
                    const o=document.createElement('option'); o.value=data.marca.Id_Marca; o.textContent=data.marca.Nombre; o.selected=true;
                    document.getElementById('marca_articulo').appendChild(o);
                    marcasDisponibles.push(data.marca);
                    verificarDuplicadoArticulo();
                    Swal.fire({ icon:'success', title:'¡Marca creada!', timer:2000, showConfirmButton:false });
                } else {
                    Swal.fire({ icon:'error', title:'Error', text:data.message });
                }
            });
        }

        // ══════════════════════════════
        // CREAR MODELO
        // ══════════════════════════════
        function abrirModalCrearModelo() {
            document.getElementById('formCrearModelo').reset();
            document.getElementById('alerta_modelo').style.display = 'none';
            document.getElementById('nombre_modelo').classList.remove('input-duplicado');
            new bootstrap.Modal(document.getElementById('modalCrearModelo')).show();
        }

        function guardarNuevoModelo(event) {
            event.preventDefault();
            if (verificarDuplicadoModelo()) { Swal.fire({ icon:'warning', title:'Modelo duplicado', text:'Ya existe un modelo con ese nombre.', confirmButtonText:'Entendido' }); return; }
            Swal.fire({ title:'Creando...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
            fetch('/admin/articulos/crear-modelo-rapido', {
                method:'POST',
                headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
                body: new FormData(event.target)
            })
            .then(r => r.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearModelo')).hide();
                    const o=document.createElement('option'); o.value=data.modelo.Id_Modelo; o.textContent=data.modelo.Nombre; o.selected=true;
                    document.getElementById('modelo_articulo').appendChild(o);
                    const cb=document.getElementById('modelo_por_definir_articulo');
                    if(cb){ cb.checked=false; cb.disabled=true; }
                    modelosDisponibles.push(data.modelo);
                    verificarDuplicadoArticulo();
                    Swal.fire({ icon:'success', title:'¡Modelo creado!', timer:2000, showConfirmButton:false });
                } else {
                    Swal.fire({ icon:'error', title:'Error', text:data.message });
                }
            });
        }
    </script>
</body>
</html>