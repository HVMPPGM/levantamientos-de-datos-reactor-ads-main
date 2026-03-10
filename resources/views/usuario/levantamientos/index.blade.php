<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mis Levantamientos - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
       :root { --sidebar-width:250px; --sidebar-collapsed-width:70px; --user-color:#1D67A8; --user-dark:#1D67A8; }
        body { background:#f5f7fa; overflow-x:hidden; }
        .sidebar { position:fixed; top:0; left:0; height:100vh; width:var(--sidebar-width); background:linear-gradient(135deg,var(--user-color) 0%,var(--user-dark) 100%); transition:all .3s ease; z-index:1000; box-shadow:2px 0 10px rgba(0,0,0,.1); }
        .sidebar.collapsed { width:var(--sidebar-collapsed-width); }
        .sidebar-header { padding:20px; text-align:center; color:white; border-bottom:1px solid rgba(255,255,255,.1); }
        .sidebar-header h4 { margin:0; font-size:18px; white-space:nowrap; overflow:hidden; }
        .sidebar.collapsed .sidebar-header h4, .sidebar.collapsed .menu-text { opacity:0; width:0; }
        .sidebar-menu { list-style:none; padding:0; margin:20px 0; }
        .menu-item { margin:5px 0; }
        .menu-link { display:flex; align-items:center; padding:15px 20px; color:rgba(255,255,255,.8); text-decoration:none; transition:all .3s; position:relative; }
        .menu-link:hover { background:rgba(255,255,255,.1); color:white; }
        .menu-link.active { background:rgba(255,255,255,.2); color:white; border-left:4px solid white; }
        .menu-link.locked { opacity:.5; cursor:not-allowed; }
        .menu-link.locked:hover { background:transparent; }
        .lock-icon { position:absolute; right:15px; font-size:14px; color:rgba(255,255,255,.6); }
        .menu-icon { width:30px; text-align:center; font-size:20px; }
        .menu-text { margin-left:15px; white-space:nowrap; transition:opacity .3s; }
        .sidebar-footer { position:absolute; bottom:0; width:100%; padding:20px; border-top:1px solid rgba(255,255,255,.1); }
        .main-content { margin-left:var(--sidebar-width); transition:margin-left .3s ease; padding:20px; }
        .main-content.expanded { margin-left:var(--sidebar-collapsed-width); }
        .top-bar { background:white; padding:15px 20px; border-radius:10px; margin-bottom:30px; box-shadow:0 2px 4px rgba(0,0,0,.1); display:flex; justify-content:space-between; align-items:center; }
        .toggle-btn { background:var(--user-color); color:white; border:none; width:40px; height:40px; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .3s; }
        .toggle-btn:hover { background:var(--user-dark); }
        .user-info { display:flex; align-items:center; gap:15px; }
        .user-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,var(--user-color),var(--user-dark)); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; }
        .user-badge { background:var(--user-color); color:white; padding:5px 15px; border-radius:20px; font-size:12px; font-weight:bold; }
        .filter-tabs { background:white; padding:20px; border-radius:10px; margin-bottom:20px; box-shadow:0 2px 10px rgba(0,0,0,.05); }
        .filter-btn { padding:10px 20px; border:2px solid #e0e0e0; background:white; border-radius:25px; margin-right:10px; margin-bottom:10px; cursor:pointer; transition:all .3s; font-weight:500; }
        .filter-btn:hover { border-color:var(--user-color); color:var(--user-color); }
        .filter-btn.active { background:var(--user-color); color:white; border-color:var(--user-color); }
        .filter-badge { background:#f0f0f0; padding:2px 8px; border-radius:12px; font-size:.85em; margin-left:5px; }
        .filter-btn.active .filter-badge { background:rgba(255,255,255,.3); }
        .levantamiento-card { background:white; border-radius:10px; padding:20px; margin-bottom:15px; box-shadow:0 2px 10px rgba(0,0,0,.05); transition:all .3s; border-left:4px solid #e0e0e0; }
        .levantamiento-card:hover { box-shadow:0 5px 20px rgba(0,0,0,.1); transform:translateY(-2px); }
        .levantamiento-card.pendiente { border-left-color:#ffc107; }
        .levantamiento-card.enproceso { border-left-color:#0dcaf0; }
        .levantamiento-card.completado { border-left-color:#198754; }
        .levantamiento-card.cancelado { border-left-color:#dc3545; }
        .status-badge { padding:5px 15px; border-radius:20px; font-size:.85em; font-weight:600; }
        .status-pendiente { background:#fff3cd; color:#997404; }
        .status-enproceso { background:#cff4fc; color:#055160; }
        .status-completado { background:#d1e7dd; color:#0a3622; }
        .status-cancelado { background:#f8d7da; color:#58151c; }
        .modal-header { background:linear-gradient(135deg,var(--user-color) 0%,var(--user-dark) 100%); color:white; }
        .tipo-card { border:2px solid #e0e0e0; border-radius:10px; padding:20px; margin-bottom:15px; cursor:pointer; transition:all .3s; }
        .tipo-card:hover { border-color:var(--user-color); box-shadow:0 5px 15px rgba(29,103,168,.2); }
        .tipo-card.selected { border-color:var(--user-color); background:#f8f9ff; }
        .tipo-icon { font-size:2.5em; color:var(--user-color); margin-bottom:10px; }
        .btn-primary { background-color: #1D67A8 !important; border-color: #1D67A8 !important; }
        .btn-primary:hover { background-color: #175a94 !important; border-color: #175a94 !important; }
        .btn-primary:focus, .btn-primary:active { background-color: #154f82 !important; border-color: #154f82 !important; box-shadow: 0 0 0 0.25rem rgba(29,103,168,0.4) !important; }
        .btn-nuevo-levantamiento { position:fixed; bottom:30px; right:30px; width:60px; height:60px; border-radius:50%; background:var(--user-color); color:white; border:none; font-size:24px; box-shadow:0 5px 15px rgba(29,103,168,.4); cursor:pointer; transition:all .3s; z-index:999; }
        .btn-nuevo-levantamiento:hover { transform:scale(1.1); box-shadow:0 8px 20px rgba(29,103,168,.6); }
        #articulos-disponibles .list-group-item { transition:all .2s ease; }
        #articulos-disponibles .list-group-item:hover { background-color:#f8f9fa; border-left:3px solid var(--user-color); }
        #articulos-seleccionados table { font-size:.9rem; }
        #articulos-seleccionados thead th { background-color:#e9ecef; font-weight:600; }
        .mobile-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:999; }
        .mobile-overlay.active { display:block; }
        @media (max-width:768px) { .sidebar{transform:translateX(-100%);} .sidebar.mobile-open{transform:translateX(0);} .main-content{margin-left:0;} .user-name{display:none;} }
        .por-definir-badge { background:#fff3cd; color:#856404; padding:2px 8px; border-radius:10px; font-size:.75em; }
        .articulo-tab-btn { border:2px solid #e0e0e0; background:white; border-radius:8px; padding:10px 20px; cursor:pointer; transition:all .2s; font-weight:500; }
        .articulo-tab-btn.active { background:var(--user-color); color:white; border-color:var(--user-color); }
        .articulo-tab-content { display:none; }
        .articulo-tab-content.active { display:block; }
        .articulo-existente-item { border:1px solid #e0e0e0; border-radius:8px; padding:12px; margin-bottom:8px; cursor:pointer; transition:all .2s; }
        .articulo-existente-item:hover { border-color:var(--user-color); background:#f8f9ff; }
        .articulo-existente-item.selected { border-color:var(--user-color); background:#eef0ff; }
        .char-counter { font-size:.78em; transition:color .2s; }
        .char-counter.warning { color:#fd7e14 !important; }
        .char-counter.danger  { color:#dc3545 !important; font-weight:600; }

        /* ── BORRADOR ──────────────────────────────────────────── */
        .draft-indicator {
            position: fixed;
            bottom: 100px;
            right: 30px;
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 10px 16px;
            font-size: .85em;
            font-weight: 600;
            color: #856404;
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
            cursor: pointer;
            z-index: 998;
            display: none;
            animation: pulse-draft 2s infinite;
            transition: all .3s;
        }
        .draft-indicator:hover { transform: scale(1.05); }
        @keyframes pulse-draft {
            0%,100% { box-shadow: 0 4px 12px rgba(255,193,7,.3); }
            50%      { box-shadow: 0 4px 20px rgba(255,193,7,.7); }
        }
        .draft-indicator i { margin-right:6px; }
    </style>
</head>
<body>
<div class="mobile-overlay" id="mobileOverlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
        <h4>Sistema Levantamientos</h4>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="{{ route('usuario.dashboard') }}" class="menu-link">
                <i class="fas fa-home menu-icon"></i><span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('usuario.levantamientos.index') }}" class="menu-link active">
                <i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Mis Levantamientos</span>
            </a>
        </li>
        @php $tienePermisosEspeciales = $usuario->Permisos === 'si'; @endphp
        <li class="menu-item">
            <a href="{{ $tienePermisosEspeciales ? route('usuario.clientesU') : '#' }}"
               class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}"
               @if(!$tienePermisosEspeciales) onclick="verificarPermiso(event,'clientes'); return false;" @endif>
                <i class="fas fa-users menu-icon"></i><span class="menu-text">Clientes</span>
                @if(!$tienePermisosEspeciales)<i class="fas fa-lock lock-icon"></i>@endif
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ $tienePermisosEspeciales ? route('usuario.productos.index') : '#' }}"
               class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}"
               @if(!$tienePermisosEspeciales) onclick="verificarPermiso(event, 'productos'); return false;" @endif>
                <i class="fas fa-box menu-icon"></i>
                <span class="menu-text">Productos</span>
                @if(!$tienePermisosEspeciales)<i class="fas fa-lock lock-icon"></i>@endif
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ $tienePermisosEspeciales ? route('usuario.tipos-levantamiento.index') : '#' }}"
               class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}"
               @if(!$tienePermisosEspeciales) onclick="verificarPermiso(event,'tipos_levantamiento'); return false;" @endif>
                <i class="fas fa-cogs menu-icon"></i><span class="menu-text">Tipos de Levantamiento</span>
                @if(!$tienePermisosEspeciales)<i class="fas fa-lock lock-icon"></i>@endif
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="menu-link w-100 border-0 bg-transparent">
                <i class="fas fa-sign-out-alt menu-icon"></i><span class="menu-text">Cerrar Sesión</span>
            </button>
        </form>
    </div>
</aside>

<main class="main-content" id="mainContent">
    <div class="top-bar">
        <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
        <div class="user-info">
            <span class="user-name">{{ $usuario->Nombres }} {{ $usuario->ApellidosPat }}</span>
            <div class="user-avatar">{{ strtoupper(substr($usuario->Nombres, 0, 1)) }}</div>
            <span class="user-badge">{{ $usuario->Rol }}</span>
        </div>
    </div>

    <div class="filter-tabs">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filtrar por estado</h5>
        </div>
        <div>
            <button class="filter-btn {{ $estatus == 'all' ? 'active' : '' }}" onclick="filtrarPorEstatus('all')">Todos <span class="filter-badge">{{ $contadores['todos'] }}</span></button>
            <button class="filter-btn {{ $estatus == 'Pendiente' ? 'active' : '' }}" onclick="filtrarPorEstatus('Pendiente')">Pendientes <span class="filter-badge">{{ $contadores['pendiente'] }}</span></button>
            <button class="filter-btn {{ $estatus == 'En Proceso' ? 'active' : '' }}" onclick="filtrarPorEstatus('En Proceso')">En Proceso <span class="filter-badge">{{ $contadores['proceso'] }}</span></button>
            <button class="filter-btn {{ $estatus == 'Completado' ? 'active' : '' }}" onclick="filtrarPorEstatus('Completado')">Completados <span class="filter-badge">{{ $contadores['completado'] }}</span></button>
        </div>
    </div>

    <div class="levantamientos-container">
        @forelse($levantamientos as $lev)
        <div class="levantamiento-card {{ strtolower(str_replace(' ', '', $lev->estatus)) }}">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-2"><i class="fas fa-file-alt me-2 text-primary"></i>LEV-{{ str_pad($lev->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</h5>
                    <p class="text-muted mb-2"><i class="fas fa-building me-2"></i><strong>Cliente:</strong> {{ $lev->cliente_nombre }}</p>
                    <p class="text-muted mb-2"><i class="fas fa-tag me-2"></i><strong>Tipo:</strong> {{ $lev->tipo_nombre ?? 'Sin tipo' }}</p>
                    <p class="text-muted mb-0"><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($lev->fecha_creacion)->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '', $lev->estatus)) }}">{{ $lev->estatus }}</span>
                    <div class="mt-3 d-flex gap-2 justify-content-end">
                        <a href="{{ route('usuario.levantamientos.show', $lev->Id_Levantamiento) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Ver</a>
                        @if(!in_array($lev->estatus, ['Cancelado','Completado']))
                        <a href="{{ route('usuario.levantamientos.edit', $lev->Id_Levantamiento) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i> Editar</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No hay levantamientos{{ $estatus != 'all' ? ' con este estado' : '' }}</h5>
            <p class="text-muted">Crea tu primer levantamiento usando el botón <i class="fas fa-plus"></i></p>
        </div>
        @endforelse
    </div>
</main>

<!-- Indicador de borrador flotante -->
<div class="draft-indicator" id="draftIndicator" onclick="restaurarBorrador()">
    <i class="fas fa-pencil-alt"></i> Borrador guardado — <strong>Continuar</strong>
</div>

<button class="btn-nuevo-levantamiento" onclick="abrirModalNuevo()" title="Nuevo Levantamiento"><i class="fas fa-plus"></i></button>

{{-- MODAL: SELECCIONAR TIPO --}}
<div class="modal fade" id="modalNuevoLevantamiento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-clipboard-list me-2"></i>Nuevo Levantamiento - Seleccionar Tipo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="tiposLevantamiento">
                    @foreach($tiposLevantamiento as $tipo)
                    <div class="col-md-6">
                        <div class="tipo-card" onclick="seleccionarTipo({{ $tipo->Id_Tipo_Levantamiento }}, '{{ addslashes($tipo->Nombre) }}')">
                            <div class="text-center">
                                <i class="fas {{ $tipo->Icono ?? 'fa-clipboard-list' }} tipo-icon"></i>
                                <h6 class="mt-2">{{ $tipo->Nombre }}</h6>
                                @if($tipo->Descripcion)<small class="text-muted">{{ $tipo->Descripcion }}</small>@endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: FORMULARIO LEVANTAMIENTO --}}
<div class="modal fade" id="modalFormularioLevantamiento" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloFormulario"><i class="fas fa-clipboard-list me-2"></i>Nuevo Levantamiento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formLevantamiento" onsubmit="guardarLevantamiento(event)">
                <div class="modal-body">
                    <input type="hidden" id="tipoLevantamientoId" name="tipo_levantamiento_id">

                    {{-- Banner de borrador --}}
                    <div id="draftBanner" class="alert alert-warning d-flex align-items-center justify-content-between mb-3" style="display:none!important">
                        <div><i class="fas fa-save me-2"></i><strong>Borrador activo.</strong> Los cambios se guardan automáticamente.</div>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-3" onclick="descartarBorrador()">
                            <i class="fas fa-trash me-1"></i>Descartar borrador
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="form-label"><strong>Cliente <span class="text-danger">*</span></strong></label>
                        <select class="form-select" id="cliente_id" name="cliente_id" required>
                            <option value="">Seleccione un cliente...</option>
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->Id_Cliente }}">{{ $cliente->Nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fas fa-box me-2 text-primary"></i>Artículos del Cliente</h6>
                            <button type="button" class="btn btn-success btn-sm" id="btnCrearArticulo" onclick="abrirModalCrearArticulo()" disabled>
                                <i class="fas fa-plus me-1"></i> Nuevo Artículo
                            </button>
                        </div>
                        <div class="input-group mb-2" id="divBuscadorArticulos" style="display:none!important">
                            <input type="text" class="form-control" id="buscarArticulo" placeholder="Buscar artículo..." oninput="filtrarArticulosDisponibles(this.value)">
                            <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                        </div>
                        <div id="articulos-disponibles" class="border rounded p-3 bg-light" style="max-height:280px;overflow-y:auto">
                            <p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>Seleccione un cliente para ver sus artículos</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="mb-3"><i class="fas fa-shopping-cart me-2 text-success"></i>Artículos del Levantamiento</h6>
                        <div id="articulos-seleccionados">
                            <div class="alert alert-secondary text-center mb-0"><i class="fas fa-inbox me-2"></i>No hay artículos agregados.</div>
                        </div>
                    </div>
                    <hr>
                    <div id="camposDinamicos"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitForm"><i class="fas fa-save me-2"></i>Guardar Levantamiento</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: CREAR / ASOCIAR ARTÍCULO --}}
<div class="modal fade" id="modalCrearArticulo" tabindex="-1" style="z-index:1060">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-box me-2"></i>Agregar Artículo al Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-4">
                    <button type="button" class="articulo-tab-btn active" id="tabBtnExistente" onclick="cambiarTab('existente')">
                        <i class="fas fa-search me-1"></i>Buscar existente
                    </button>
                    <button type="button" class="articulo-tab-btn" id="tabBtnNuevo" onclick="cambiarTab('nuevo')">
                        <i class="fas fa-plus me-1"></i>Crear nuevo
                    </button>
                </div>
                <div class="articulo-tab-content active" id="tabExistente">
                    <div class="alert alert-info py-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Busca un artículo ya registrado y asócialo al cliente: <strong id="nombreClienteArticuloExistente"></strong>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="buscarArticuloExistente" placeholder="Buscar por nombre o marca..." oninput="buscarArticulosExistentes(this.value)">
                        <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                    <div id="listaArticulosExistentes" style="max-height:320px;overflow-y:auto">
                        <p class="text-muted text-center py-3"><i class="fas fa-search me-2"></i>Escribe para buscar artículos</p>
                    </div>
                    <div class="mt-3 text-end" id="btnAsociarContainer" style="display:none">
                        <button type="button" class="btn btn-success" onclick="asociarArticuloExistente()">
                            <i class="fas fa-link me-2"></i>Asociar al cliente y agregar
                        </button>
                    </div>
                </div>
                <div class="articulo-tab-content" id="tabNuevo">
                    <div class="alert alert-info py-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Se creará y asociará al cliente: <strong id="nombreClienteArticulo"></strong>
                    </div>
                    <input type="hidden" id="clienteIdArticulo" name="cliente_id">
                    <form id="formCrearArticulo" onsubmit="guardarNuevoArticulo(event)">
                        <input type="hidden" id="clienteIdArticuloForm" name="cliente_id">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nombre del Artículo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_articulo" name="nombre" required
                                       maxlength="500" placeholder="Ej: Cámara IP, Router, etc."
                                       oninput="actualizarContador(this, 'cntNombre', 500)">
                                <div class="d-flex justify-content-end mt-1">
                                    <small class="char-counter text-muted" id="cntNombre">0 / 500</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Marca <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-select" id="marca_articulo" name="marca_id" required>
                                        <option value="">Seleccione una marca...</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" onclick="abrirModalCrearMarca()" title="Nueva marca"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Modelo</label>
                                <div class="input-group">
                                    <select class="form-select" id="modelo_articulo" name="modelo_id">
                                        <option value="">Seleccione un modelo...</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" onclick="abrirModalCrearModelo()" title="Nuevo modelo"><i class="fas fa-plus"></i></button>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="modelo_por_definir" name="modelo_por_definir" onchange="toggleModeloPorDefinir(this)">
                                    <label class="form-check-label" for="modelo_por_definir">
                                        <i class="fas fa-question-circle text-warning me-1"></i>Modelo por definir
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Descripción (opcional)</label>
                                <textarea class="form-control" id="descripcion_articulo" name="descripcion" rows="2"
                                          maxlength="500" placeholder="Descripción adicional..."
                                          oninput="actualizarContador(this, 'cntDescripcion', 500)"></textarea>
                                <div class="d-flex justify-content-end mt-1">
                                    <small class="char-counter text-muted" id="cntDescripcion">0 / 500</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="es_principal_articulo" name="es_principal">
                                    <label class="form-check-label" for="es_principal_articulo">
                                        <i class="fas fa-star text-warning me-1"></i>Marcar como artículo principal del cliente
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Crear y Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: CREAR MARCA --}}
<div class="modal fade" id="modalCrearMarca" tabindex="-1" style="z-index:1070">
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
                        <input type="text" class="form-control" id="nombre_marca" name="nombre" required placeholder="Ej: Hikvision, TP-Link...">
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

{{-- MODAL: CREAR MODELO --}}
<div class="modal fade" id="modalCrearModelo" tabindex="-1" style="z-index:1070">
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
                        <input type="text" class="form-control" id="nombre_modelo" name="nombre" required placeholder="Ej: DS-2CD2143G0...">
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
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const toggleBtn = document.getElementById('toggleBtn');
const mobileOverlay = document.getElementById('mobileOverlay');
toggleBtn.addEventListener('click', () => {
    if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); mobileOverlay.classList.toggle('active'); }
    else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
});
mobileOverlay.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); mobileOverlay.classList.remove('active'); });

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let articulosCliente = [], articulosSeleccionados = [], contadorArticulos = 0;
let clienteSeleccionadoId = null, clienteSeleccionadoNombre = '';
let marcasDisponibles = [], modelosDisponibles = [];
let articuloExistenteSeleccionado = null;
const DRAFT_KEY = 'levantamiento_draft_v1';

// ══════════════════════════════════════════════════════════════════
// SISTEMA DE BORRADOR
// ══════════════════════════════════════════════════════════════════

function guardarBorrador() {
    if (!clienteSeleccionadoId && !articulosSeleccionados.length) return;

    const tipoId    = document.getElementById('tipoLevantamientoId').value;
    const clienteId = document.getElementById('cliente_id').value;
    const titulo    = document.getElementById('tituloFormulario').innerHTML;

    // Capturar campos dinámicos
    const camposDin = {};
    document.querySelectorAll('#camposDinamicos [name]').forEach(el => {
        camposDin[el.name] = el.value;
    });

    const draft = {
        tipoId,
        clienteId,
        clienteNombre: clienteSeleccionadoNombre,
        titulo,
        articulosSeleccionados: JSON.parse(JSON.stringify(articulosSeleccionados)),
        contadorArticulos,
        camposDin,
        fecha: new Date().toISOString(),
    };

    sessionStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
    actualizarIndicadorBorrador();
}

function cargarBorrador() {
    try {
        const raw = sessionStorage.getItem(DRAFT_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch { return null; }
}

function tieneBorrador() {
    const d = cargarBorrador();
    return d && (d.articulosSeleccionados?.length > 0 || d.clienteId);
}

function actualizarIndicadorBorrador() {
    const ind = document.getElementById('draftIndicator');
    if (tieneBorrador()) {
        const d = cargarBorrador();
        const n = d.articulosSeleccionados?.length || 0;
        ind.innerHTML = `<i class="fas fa-pencil-alt"></i> Borrador guardado (${n} artículo${n!==1?'s':''}) — <strong>Continuar</strong>`;
        ind.style.display = 'block';
    } else {
        ind.style.display = 'none';
    }
}

function restaurarBorrador() {
    const draft = cargarBorrador();
    if (!draft) return;

    // Restaurar estado global
    articulosSeleccionados = draft.articulosSeleccionados || [];
    contadorArticulos      = draft.contadorArticulos || 0;
    clienteSeleccionadoId  = draft.clienteId || null;
    clienteSeleccionadoNombre = draft.clienteNombre || '';

    // Abrir directamente el modal de formulario si ya hay tipo
    if (draft.tipoId) {
        document.getElementById('tipoLevantamientoId').value = draft.tipoId;
        document.getElementById('tituloFormulario').innerHTML = draft.titulo || '<i class="fas fa-clipboard-list me-2"></i>Nuevo Levantamiento';
        document.getElementById('btnSubmitForm').innerHTML = '<i class="fas fa-save me-2"></i>Guardar Levantamiento';
        document.getElementById('cliente_id').disabled = false;

        // Mostrar banner de borrador activo
        document.getElementById('draftBanner').style.removeProperty('display');

        cargarCamposTipo(draft.tipoId, () => {
            // Restaurar cliente
            if (draft.clienteId) {
                const sel = document.getElementById('cliente_id');
                sel.value = draft.clienteId;
                document.getElementById('btnCrearArticulo').disabled = false;

                // Cargar artículos del cliente y luego renderizar seleccionados
                fetch(`/usuario/levantamientos/cliente/${draft.clienteId}/articulos`)
                    .then(r => r.json())
                    .then(data => {
                        articulosCliente = data;
                        mostrarArticulosDisponibles(data);
                        document.getElementById('divBuscadorArticulos').style.setProperty('display', data.length > 5 ? 'flex' : 'none', 'important');
                        renderizarArticulosSeleccionados();
                    }).catch(() => {});

                // Restaurar campos dinámicos
                if (draft.camposDin) {
                    setTimeout(() => {
                        Object.entries(draft.camposDin).forEach(([name, value]) => {
                            const el = document.querySelector(`#camposDinamicos [name="${name}"]`);
                            if (el) el.value = value;
                        });
                    }, 300);
                }
            }

            new bootstrap.Modal(document.getElementById('modalFormularioLevantamiento')).show();
        });
    } else {
        // Sin tipo: abrir modal de selección de tipo
        new bootstrap.Modal(document.getElementById('modalNuevoLevantamiento')).show();
    }
}

function descartarBorrador() {
    Swal.fire({
        title: '¿Descartar borrador?',
        text: 'Se eliminarán todos los datos del borrador guardado.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, descartar',
        cancelButtonText: 'No, conservar',
    }).then(r => {
        if (r.isConfirmed) {
            sessionStorage.removeItem(DRAFT_KEY);
            actualizarIndicadorBorrador();
            document.getElementById('draftBanner').style.setProperty('display', 'none', 'important');
            bootstrap.Modal.getInstance(document.getElementById('modalFormularioLevantamiento'))?.hide();
            limpiarArticulos();
            clienteSeleccionadoId = null; clienteSeleccionadoNombre = '';
            Swal.fire({ icon:'success', title:'Borrador descartado', timer:1200, showConfirmButton:false });
        }
    });
}

// Guardar borrador automáticamente cada vez que cambie algo relevante
function autoGuardar() { guardarBorrador(); }

// ══════════════════════════════════════════════════════════════════

function actualizarContador(input, contadorId, max) {
    const cnt = document.getElementById(contadorId);
    if (!cnt) return;
    const len = input.value.length;
    cnt.textContent = len + ' / ' + max;
    cnt.classList.remove('warning', 'danger', 'text-muted');
    if (len >= max)               cnt.classList.add('danger');
    else if (len >= max * 0.85)   cnt.classList.add('warning');
    else                          cnt.classList.add('text-muted');
}

function verificarPermiso(event, accion) {
    event.preventDefault();
    const nombres = { clientes:'Clientes', tipos_levantamiento:'Tipos de Levantamiento' };
    Swal.fire({ icon:'warning', title:'Acceso Restringido', html:`<p>No tienes permisos para acceder a <strong>${nombres[accion]||accion}</strong>.</p><p class="text-muted small">Contacta al administrador.</p>`, confirmButtonColor:'#1D67A8' });
}

function filtrarPorEstatus(e) { window.location.href = `{{ route('usuario.levantamientos.index') }}?estatus=${e}`; }

function abrirModalNuevo() {
    const draft = cargarBorrador();

    if (draft && (draft.articulosSeleccionados?.length > 0 || draft.clienteId)) {
        // Hay borrador: preguntar
        Swal.fire({
            title: '¡Tienes un borrador guardado!',
            html: `<p>Tienes un levantamiento en progreso con <strong>${draft.articulosSeleccionados?.length || 0} artículo(s)</strong> agregado(s).</p><p>¿Qué deseas hacer?</p>`,
            icon: 'info',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-redo me-1"></i> Continuar borrador',
            denyButtonText: '<i class="fas fa-plus me-1"></i> Nuevo levantamiento',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#1D67A8',
            denyButtonColor: '#6c757d',
        }).then(r => {
            if (r.isConfirmed) {
                restaurarBorrador();
            } else if (r.isDenied) {
                sessionStorage.removeItem(DRAFT_KEY);
                actualizarIndicadorBorrador();
                abrirModalNuevoLimpio();
            }
        });
    } else {
        abrirModalNuevoLimpio();
    }
}

function abrirModalNuevoLimpio() {
    document.getElementById('formLevantamiento').reset();
    delete document.getElementById('formLevantamiento').dataset.editId;
    document.getElementById('tituloFormulario').innerHTML = '<i class="fas fa-clipboard-list me-2"></i>Nuevo Levantamiento';
    document.getElementById('btnSubmitForm').innerHTML = '<i class="fas fa-save me-2"></i>Guardar Levantamiento';
    document.getElementById('cliente_id').disabled = false;
    document.getElementById('btnCrearArticulo').disabled = true;
    document.getElementById('draftBanner').style.setProperty('display', 'none', 'important');
    limpiarArticulos();
    document.getElementById('camposDinamicos').innerHTML = '';
    clienteSeleccionadoId = null; clienteSeleccionadoNombre = '';
    new bootstrap.Modal(document.getElementById('modalNuevoLevantamiento')).show();
}

function seleccionarTipo(tipoId, nombreTipo) {
    document.getElementById('tipoLevantamientoId').value = tipoId;
    document.getElementById('tituloFormulario').innerHTML = `<i class="fas fa-clipboard-list me-2"></i>Nuevo Levantamiento - ${nombreTipo}`;
    bootstrap.Modal.getInstance(document.getElementById('modalNuevoLevantamiento')).hide();
    cargarCamposTipo(tipoId);
    setTimeout(() => {
        document.getElementById('draftBanner').style.removeProperty('display');
        new bootstrap.Modal(document.getElementById('modalFormularioLevantamiento')).show();
        autoGuardar();
    }, 300);
}

function cargarCamposTipo(tipoId, callback) {
    fetch(`/usuario/levantamientos/tipo/${tipoId}/formulario`)
        .then(r => r.json())
        .then(data => {
            marcasDisponibles = data.marcas;
            modelosDisponibles = data.modelos;
            renderizarCampos(data.campos);
            if (callback) callback();
        })
        .catch(() => Swal.fire('Error','Error al cargar el formulario','error'));
}

function renderizarCampos(campos) {
    const container = document.getElementById('camposDinamicos');
    container.innerHTML = '';
    const excluidos = ['articulo','cantidad','marca','modelo','precio_unitario','servicio_profesional'];
    campos.forEach(campo => {
        if (excluidos.includes(campo.Nombre_Campo)) return;
        let html = `<div class="campo-dinamico mb-3"><label class="form-label">${campo.Etiqueta} ${campo.Es_Requerido ? '<span class="text-danger">*</span>' : ''}</label>`;
        switch(campo.Tipo_Input) {
            case 'textarea': html += `<textarea class="form-control" name="${campo.Nombre_Campo}" rows="3" ${campo.Es_Requerido?'required':''} placeholder="${campo.Placeholder||''}" oninput="autoGuardar()"></textarea>`; break;
            case 'number':   html += `<input type="number" class="form-control" name="${campo.Nombre_Campo}" ${campo.Es_Requerido?'required':''} placeholder="${campo.Placeholder||''}" step="0.01" oninput="autoGuardar()">`; break;
            case 'select':   html += `<select class="form-select" name="${campo.Nombre_Campo}" ${campo.Es_Requerido?'required':''} onchange="autoGuardar()"><option value="">${campo.Placeholder||'Seleccione...'}</option></select>`; break;
            default:         html += `<input type="${campo.Tipo_Input}" class="form-control" name="${campo.Nombre_Campo}" ${campo.Es_Requerido?'required':''} placeholder="${campo.Placeholder||''}" oninput="autoGuardar()">`;
        }
        html += '</div>';
        container.innerHTML += html;
    });
}

function cargarArticulosCliente(clienteId, callback) {
    if (!clienteId) { limpiarArticulos(); document.getElementById('btnCrearArticulo').disabled = true; clienteSeleccionadoId = null; clienteSeleccionadoNombre = ''; autoGuardar(); return; }
    const sel = document.getElementById('cliente_id');
    clienteSeleccionadoId = clienteId;
    clienteSeleccionadoNombre = sel.options[sel.selectedIndex]?.text || '';
    document.getElementById('btnCrearArticulo').disabled = false;
    fetch(`/usuario/levantamientos/cliente/${clienteId}/articulos`)
        .then(r => r.json())
        .then(data => {
            articulosCliente = data;
            mostrarArticulosDisponibles(data);
            document.getElementById('divBuscadorArticulos').style.setProperty('display', data.length > 5 ? 'flex' : 'none', 'important');
            autoGuardar();
            if (callback) callback();
        })
        .catch(() => Swal.fire('Error','No se pudieron cargar los artículos','error'));
}

function filtrarArticulosDisponibles(q) {
    const f = q ? articulosCliente.filter(a => a.Nombre.toLowerCase().includes(q.toLowerCase()) || (a.marca_nombre||'').toLowerCase().includes(q.toLowerCase())) : articulosCliente;
    mostrarArticulosDisponibles(f);
}

function mostrarArticulosDisponibles(articulos) {
    const c = document.getElementById('articulos-disponibles');
    if (!articulos.length) { c.innerHTML = `<div class="alert alert-info mb-0"><i class="fas fa-info-circle me-2"></i>Este cliente no tiene artículos. Usa "Nuevo Artículo".</div>`; return; }
    let html = '<div class="list-group">';
    articulos.forEach(art => {
        const yaAgregado = articulosSeleccionados.find(a => a.id_articulo == art.Id_Articulos);
        html += `<div class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${art.Nombre} ${art.Es_Principal ? '<span class="badge bg-primary ms-1">Principal</span>' : ''} ${art.modelo_por_definir ? '<span class="por-definir-badge">Modelo por definir</span>' : ''}</h6>
                    <small class="text-muted"><i class="fas fa-tag me-1"></i>${art.marca_nombre||'Sin marca'} | ${art.modelo_nombre||'Sin modelo'}</small>
                    ${art.Descripcion ? `<br><small class="text-secondary">${art.Descripcion}</small>` : ''}
                </div>
                <button type="button" class="btn btn-sm ${yaAgregado ? 'btn-secondary disabled' : 'btn-primary'}" onclick="agregarArticulo(${art.Id_Articulos})" ${yaAgregado ? 'disabled' : ''}>
                    <i class="fas fa-plus me-1"></i>${yaAgregado ? 'Agregado' : 'Agregar'}
                </button>
            </div>
        </div>`;
    });
    html += '</div>';
    c.innerHTML = html;
}

function agregarArticulo(articuloId) {
    const art = articulosCliente.find(a => a.Id_Articulos == articuloId);
    if (!art) return;
    if (articulosSeleccionados.find(a => a.id_articulo == articuloId)) { Swal.fire({ icon:'warning', title:'Ya agregado', timer:2000, showConfirmButton:false }); return; }
    contadorArticulos++;
    articulosSeleccionados.push({ id:contadorArticulos, id_articulo:articuloId, nombre:art.Nombre, marca_nombre:art.marca_nombre||'Sin marca', modelo_nombre:art.modelo_nombre||(art.modelo_por_definir ? '⚠ Por definir' : 'Sin modelo'), modelo_por_definir:art.modelo_por_definir||0, cantidad:1, precio_unitario:0, notas:'' });
    renderizarArticulosSeleccionados();
    mostrarArticulosDisponibles(articulosCliente);
    autoGuardar();
    Swal.fire({ icon:'success', title:'Agregado', text:`${art.Nombre} agregado`, timer:1200, showConfirmButton:false });
}

function renderizarArticulosSeleccionados() {
    const c = document.getElementById('articulos-seleccionados');
    if (!articulosSeleccionados.length) { c.innerHTML = `<div class="alert alert-secondary text-center mb-0"><i class="fas fa-inbox me-2"></i>No hay artículos agregados.</div>`; return; }
    let html = `<div class="table-responsive"><table class="table table-bordered table-hover mb-0">
        <thead><tr><th>Artículo</th><th>Marca/Modelo</th><th width="90">Cant.</th><th width="140">Precio Unit.</th><th width="180">Notas</th><th width="90">Subtotal</th><th width="50"></th></tr></thead><tbody>`;
    articulosSeleccionados.forEach(art => {
        const subtotal = (art.cantidad * art.precio_unitario).toFixed(2);
        html += `<tr>
            <td><strong>${art.nombre}</strong>${art.modelo_por_definir ? ' <span class="por-definir-badge">Por definir</span>' : ''}
                <input type="hidden" name="articulos[${art.id}][id_articulo]" value="${art.id_articulo}">
                <input type="hidden" name="articulos[${art.id}][modelo_por_definir]" value="${art.modelo_por_definir}">
            </td>
            <td><small>${art.marca_nombre}<br>${art.modelo_nombre}</small></td>
            <td><input type="number" class="form-control form-control-sm" min="1" value="${art.cantidad}" name="articulos[${art.id}][cantidad]" onchange="actualizarCantidad(${art.id},this.value)"></td>
            <td><div class="input-group input-group-sm"><span class="input-group-text">$</span><input type="number" class="form-control" min="0" step="0.01" value="${art.precio_unitario}" name="articulos[${art.id}][precio_unitario]" onchange="actualizarPrecio(${art.id},this.value)"></div></td>
            <td><input type="text" class="form-control form-control-sm" value="${art.notas}" name="articulos[${art.id}][notas]" onchange="actualizarNotas(${art.id},this.value)" placeholder="Notas..."></td>
            <td class="text-end fw-bold">$${subtotal}</td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="eliminarArticulo(${art.id})"><i class="fas fa-trash"></i></button></td>
        </tr>`;
    });
    const total = articulosSeleccionados.reduce((s,a) => s + a.cantidad*a.precio_unitario, 0).toFixed(2);
    html += `</tbody><tfoot class="table-light"><tr><th colspan="5" class="text-end">TOTAL:</th><th class="text-end">$${total}</th><th></th></tr></tfoot></table></div>`;
    c.innerHTML = html;
}

function actualizarCantidad(id,v){const a=articulosSeleccionados.find(x=>x.id==id);if(a){a.cantidad=parseInt(v)||1;renderizarArticulosSeleccionados();autoGuardar();}}
function actualizarPrecio(id,v){const a=articulosSeleccionados.find(x=>x.id==id);if(a){a.precio_unitario=parseFloat(v)||0;renderizarArticulosSeleccionados();autoGuardar();}}
function actualizarNotas(id,v){const a=articulosSeleccionados.find(x=>x.id==id);if(a){a.notas=v;autoGuardar();}}
function eliminarArticulo(id){
    Swal.fire({title:'¿Eliminar artículo?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545',cancelButtonColor:'#6c757d',confirmButtonText:'Sí, quitar',cancelButtonText:'Cancelar'})
    .then(r=>{if(r.isConfirmed){articulosSeleccionados=articulosSeleccionados.filter(a=>a.id!=id);renderizarArticulosSeleccionados();mostrarArticulosDisponibles(articulosCliente);autoGuardar();}});
}

function limpiarArticulos(){
    articulosSeleccionados=[];articulosCliente=[];contadorArticulos=0;
    const d=document.getElementById('articulos-disponibles');
    const s=document.getElementById('articulos-seleccionados');
    if(d)d.innerHTML=`<p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>Seleccione un cliente para ver sus artículos</p>`;
    if(s)s.innerHTML=`<div class="alert alert-secondary text-center mb-0"><i class="fas fa-inbox me-2"></i>No hay artículos agregados.</div>`;
}

function guardarLevantamiento(event){
    event.preventDefault();
    if(!articulosSeleccionados.length){Swal.fire({icon:'warning',title:'Atención',text:'Debe agregar al menos un artículo'});return;}
    const formData=new FormData(event.target);
    const editId=event.target.dataset.editId;
    const url=editId?`/usuario/levantamientos/${editId}`:'/usuario/levantamientos';
    if(editId)formData.append('_method','PUT');
    Swal.fire({title:editId?'Actualizando...':'Guardando...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch(url,{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:formData})
    .then(r=>r.json()).then(data=>{
        Swal.close();
        if(data.success){
            // Limpiar borrador al guardar exitosamente
            sessionStorage.removeItem(DRAFT_KEY);
            actualizarIndicadorBorrador();
            Swal.fire({icon:'success',title:'¡Éxito!',text:editId?'Levantamiento actualizado':'Levantamiento creado exitosamente'}).then(()=>location.reload());
        } else {
            Swal.fire({icon:'error',title:'Error',text:data.message||'Error al guardar'});
        }
    }).catch(()=>{Swal.close();Swal.fire({icon:'error',title:'Error',text:'Error de conexión'});});
}

// ── Tabs del modal artículo ──────────────────────────────────────
function cambiarTab(tab) {
    document.getElementById('tabBtnExistente').classList.toggle('active', tab === 'existente');
    document.getElementById('tabBtnNuevo').classList.toggle('active', tab === 'nuevo');
    document.getElementById('tabExistente').classList.toggle('active', tab === 'existente');
    document.getElementById('tabNuevo').classList.toggle('active', tab === 'nuevo');
}

function abrirModalCrearArticulo() {
    if (!clienteSeleccionadoId) { Swal.fire({icon:'warning',title:'Atención',text:'Primero selecciona un cliente'}); return; }
    document.getElementById('clienteIdArticulo').value = clienteSeleccionadoId;
    document.getElementById('clienteIdArticuloForm').value = clienteSeleccionadoId;
    document.getElementById('nombreClienteArticulo').textContent = clienteSeleccionadoNombre;
    document.getElementById('nombreClienteArticuloExistente').textContent = clienteSeleccionadoNombre;
    document.getElementById('formCrearArticulo').reset();
    document.getElementById('clienteIdArticuloForm').value = clienteSeleccionadoId;
    document.getElementById('cntNombre').textContent = '0 / 500';
    document.getElementById('cntNombre').className = 'char-counter text-muted';
    document.getElementById('cntDescripcion').textContent = '0 / 500';
    document.getElementById('cntDescripcion').className = 'char-counter text-muted';
    document.getElementById('listaArticulosExistentes').innerHTML = '<p class="text-muted text-center py-3"><i class="fas fa-search me-2"></i>Escribe para buscar artículos</p>';
    document.getElementById('buscarArticuloExistente').value = '';
    document.getElementById('btnAsociarContainer').style.display = 'none';
    articuloExistenteSeleccionado = null;
    cambiarTab('existente');
    cargarMarcasEnSelect(); cargarModelosEnSelect();
    new bootstrap.Modal(document.getElementById('modalCrearArticulo')).show();
}

let buscarTimeout = null;
function buscarArticulosExistentes(q) {
    clearTimeout(buscarTimeout);
    if (q.length < 2) {
        document.getElementById('listaArticulosExistentes').innerHTML = '<p class="text-muted text-center py-3"><i class="fas fa-search me-2"></i>Escribe al menos 2 caracteres</p>';
        document.getElementById('btnAsociarContainer').style.display = 'none';
        return;
    }
    buscarTimeout = setTimeout(() => {
        fetch(`/usuario/articulos/buscar?q=${encodeURIComponent(q)}&cliente_id=${clienteSeleccionadoId}`)
        .then(r => r.json()).then(data => {
            const c = document.getElementById('listaArticulosExistentes');
            if (!data.length) { c.innerHTML = '<p class="text-muted text-center py-3">No se encontraron artículos</p>'; return; }
            let html = '';
            data.forEach(art => {
                const yaEnLev = articulosSeleccionados.find(a => a.id_articulo == art.Id_Articulos);
                html += `<div class="articulo-existente-item ${yaEnLev ? 'opacity-50' : ''}" id="artExistente_${art.Id_Articulos}" onclick="${yaEnLev ? '' : `seleccionarArticuloExistente(${art.Id_Articulos})`}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${art.Nombre}</strong>
                            ${art.ya_en_cliente ? '<span class="badge bg-success ms-2">Ya en cliente</span>' : ''}
                            ${yaEnLev ? '<span class="badge bg-secondary ms-2">Ya en levantamiento</span>' : ''}
                            ${art.modelo_por_definir ? '<span class="por-definir-badge ms-2">Modelo por definir</span>' : ''}
                            <br><small class="text-muted">${art.marca_nombre||'Sin marca'} | ${art.modelo_nombre||'Sin modelo'}</small>
                        </div>
                        <i class="fas fa-circle-check text-success mt-1" id="checkExistente_${art.Id_Articulos}" style="display:none"></i>
                    </div>
                </div>`;
            });
            c.innerHTML = html;
        }).catch(()=>{});
    }, 350);
}

function seleccionarArticuloExistente(articuloId) {
    document.querySelectorAll('.articulo-existente-item').forEach(el => el.classList.remove('selected'));
    document.querySelectorAll('[id^="checkExistente_"]').forEach(el => el.style.display = 'none');
    const el = document.getElementById(`artExistente_${articuloId}`);
    const check = document.getElementById(`checkExistente_${articuloId}`);
    if (el) el.classList.add('selected');
    if (check) check.style.display = 'inline';
    articuloExistenteSeleccionado = articuloId;
    document.getElementById('btnAsociarContainer').style.display = 'block';
}

function asociarArticuloExistente() {
    if (!articuloExistenteSeleccionado) return;
    Swal.fire({title:'Asociando artículo...', allowOutsideClick:false, didOpen:()=>Swal.showLoading()});
    fetch('/usuario/articulos/asociar-a-cliente', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({ cliente_id: clienteSeleccionadoId, articulo_id: articuloExistenteSeleccionado })
    })
    .then(r=>r.json()).then(data=>{
        Swal.close();
        if (data.success || data.ya_asociado) {
            bootstrap.Modal.getInstance(document.getElementById('modalCrearArticulo')).hide();
            Swal.fire({icon:'success',title:'Listo',timer:1800,showConfirmButton:false});
            fetch(`/usuario/levantamientos/cliente/${clienteSeleccionadoId}/articulos`)
            .then(r=>r.json()).then(d=>{
                articulosCliente = d;
                const art = d.find(a => a.Id_Articulos == articuloExistenteSeleccionado);
                if (art) agregarArticuloSilencioso(art);
                mostrarArticulosDisponibles(d);
            });
        } else {
            Swal.fire({icon:'error',title:'Error',text:data.message||'No se pudo asociar'});
        }
    }).catch(()=>{Swal.close();Swal.fire({icon:'error',title:'Error',text:'Error de conexión'});});
}

function agregarArticuloSilencioso(art) {
    if (articulosSeleccionados.find(a => a.id_articulo == art.Id_Articulos)) return;
    contadorArticulos++;
    articulosSeleccionados.push({ id:contadorArticulos, id_articulo:art.Id_Articulos, nombre:art.Nombre, marca_nombre:art.marca_nombre||'Sin marca', modelo_nombre:art.modelo_nombre||(art.modelo_por_definir?'⚠ Por definir':'Sin modelo'), modelo_por_definir:art.modelo_por_definir||0, cantidad:1, precio_unitario:0, notas:'' });
    renderizarArticulosSeleccionados();
    autoGuardar();
}

function toggleModeloPorDefinir(cb){const s=document.getElementById('modelo_articulo');s.disabled=cb.checked;if(cb.checked){s.value='';s.required=false;}else{s.required=false;}}
function cargarMarcasEnSelect(){const s=document.getElementById('marca_articulo');s.innerHTML='<option value="">Seleccione una marca...</option>';marcasDisponibles.forEach(m=>{const o=document.createElement('option');o.value=m.Id_Marca;o.textContent=m.Nombre;s.appendChild(o);});}
function cargarModelosEnSelect(){const s=document.getElementById('modelo_articulo');s.innerHTML='<option value="">Seleccione un modelo...</option>';modelosDisponibles.forEach(m=>{const o=document.createElement('option');o.value=m.Id_Modelo;o.textContent=m.Nombre;s.appendChild(o);});}

function guardarNuevoArticulo(event) {
    event.preventDefault();
    const nombre = document.getElementById('nombre_articulo').value;
    const desc   = document.getElementById('descripcion_articulo').value;
    if (nombre.length > 500) { Swal.fire({ icon:'warning', title:'Nombre muy largo', text:'El nombre no puede superar los 500 caracteres.', confirmButtonColor:'#1D67A8' }); return; }
    if (desc.length > 500) { Swal.fire({ icon:'warning', title:'Descripción muy larga', text:'La descripción no puede superar los 500 caracteres.', confirmButtonColor:'#1D67A8' }); return; }
    Swal.fire({title:'Creando artículo...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/usuario/articulos/crear-desde-levantamiento',{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:new FormData(event.target)})
    .then(r=>r.json()).then(data=>{
        Swal.close();
        if(data.success){
            bootstrap.Modal.getInstance(document.getElementById('modalCrearArticulo')).hide();
            Swal.fire({icon:'success',title:'¡Artículo creado!',timer:1800,showConfirmButton:false});
            fetch(`/usuario/levantamientos/cliente/${clienteSeleccionadoId}/articulos`)
            .then(r=>r.json()).then(d=>{ articulosCliente=d; mostrarArticulosDisponibles(d); });
        }else{
            Swal.fire({icon:'error',title:'Error al crear artículo',text:data.message||'Error desconocido.',confirmButtonColor:'#dc3545'});
        }
    }).catch(()=>{Swal.close();Swal.fire({icon:'error',title:'Error',text:'Error de conexión'});});
}

function abrirModalCrearMarca(){document.getElementById('formCrearMarca').reset();new bootstrap.Modal(document.getElementById('modalCrearMarca')).show();}
function guardarNuevaMarca(event){
    event.preventDefault();
    Swal.fire({title:'Creando marca...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/usuario/articulos/crear-marca-rapida',{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:new FormData(event.target)})
    .then(r=>r.json()).then(data=>{
        Swal.close();
        if(data.success){
            bootstrap.Modal.getInstance(document.getElementById('modalCrearMarca')).hide();
            const s=document.getElementById('marca_articulo');
            const o=document.createElement('option');o.value=data.marca.Id_Marca;o.textContent=data.marca.Nombre;o.selected=true;
            s.appendChild(o);marcasDisponibles.push(data.marca);
            Swal.fire({icon:'success',title:'¡Marca creada!',text:data.marca.Nombre,timer:1500,showConfirmButton:false});
        } else if(data.duplicado){
            Swal.fire({
                icon:'warning', title:'Marca ya existente',
                html:`${data.message}<br><br>¿Deseas seleccionar la marca existente <strong>${data.marca.Nombre}</strong>?`,
                showCancelButton:true, confirmButtonText:'Sí, usar esta', cancelButtonText:'Cancelar',
                confirmButtonColor:'#1D67A8', cancelButtonColor:'#6c757d'
            }).then(res=>{
                if(res.isConfirmed){
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearMarca')).hide();
                    const s=document.getElementById('marca_articulo');
                    let opt = s.querySelector(`option[value="${data.marca.Id_Marca}"]`);
                    if(!opt){ opt=document.createElement('option');opt.value=data.marca.Id_Marca;opt.textContent=data.marca.Nombre;s.appendChild(opt); }
                    s.value=data.marca.Id_Marca;
                }
            });
        }else{
            Swal.fire({icon:'error',title:'Error',text:data.message||'No se pudo crear la marca.',confirmButtonColor:'#dc3545'});
        }
    }).catch(()=>{Swal.close();Swal.fire({icon:'error',title:'Error',text:'Error de conexión'});});
}

function abrirModalCrearModelo(){document.getElementById('formCrearModelo').reset();new bootstrap.Modal(document.getElementById('modalCrearModelo')).show();}
function guardarNuevoModelo(event){
    event.preventDefault();
    Swal.fire({title:'Creando modelo...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/usuario/articulos/crear-modelo-rapido',{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:new FormData(event.target)})
    .then(r=>r.json()).then(data=>{
        Swal.close();
        if(data.success){
            bootstrap.Modal.getInstance(document.getElementById('modalCrearModelo')).hide();
            const s=document.getElementById('modelo_articulo');
            const o=document.createElement('option');o.value=data.modelo.Id_Modelo;o.textContent=data.modelo.Nombre;o.selected=true;
            s.appendChild(o);modelosDisponibles.push(data.modelo);
            Swal.fire({icon:'success',title:'¡Modelo creado!',text:data.modelo.Nombre,timer:1500,showConfirmButton:false});
        } else if(data.duplicado){
            Swal.fire({
                icon:'warning', title:'Modelo ya existente',
                html:`${data.message}<br><br>¿Deseas seleccionar el modelo existente <strong>${data.modelo.Nombre}</strong>?`,
                showCancelButton:true, confirmButtonText:'Sí, usar este', cancelButtonText:'Cancelar',
                confirmButtonColor:'#1D67A8', cancelButtonColor:'#6c757d'
            }).then(res=>{
                if(res.isConfirmed){
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearModelo')).hide();
                    const s=document.getElementById('modelo_articulo');
                    let opt = s.querySelector(`option[value="${data.modelo.Id_Modelo}"]`);
                    if(!opt){ opt=document.createElement('option');opt.value=data.modelo.Id_Modelo;opt.textContent=data.modelo.Nombre;s.appendChild(opt); }
                    s.value=data.modelo.Id_Modelo;
                }
            });
        }else{
            Swal.fire({icon:'error',title:'Error',text:data.message||'No se pudo crear el modelo.',confirmButtonColor:'#dc3545'});
        }
    }).catch(()=>{Swal.close();Swal.fire({icon:'error',title:'Error',text:'Error de conexión'});});
}

document.addEventListener('DOMContentLoaded',()=>{
    // Mostrar indicador si hay borrador al cargar la página
    actualizarIndicadorBorrador();

    document.getElementById('cliente_id').addEventListener('change', function(){
        if(this.value) cargarArticulosCliente(this.value);
        else limpiarArticulos();
        autoGuardar();
    });

    // Al cerrar el modal del formulario: NO limpiar si hay artículos (es borrador)
    document.getElementById('modalFormularioLevantamiento').addEventListener('hide.bs.modal', () => {
        // Guardar borrador antes de cerrar
        if (articulosSeleccionados.length > 0 || clienteSeleccionadoId) {
            autoGuardar();
            // Mostrar toast de aviso
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'info',
                title: 'Borrador guardado automáticamente',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
        }
    });

    // Limpiar estado interno del modal solo cuando el modal termina de cerrarse
    // pero solo si el borrador fue descartado (sessionStorage vacío)
    document.getElementById('modalFormularioLevantamiento').addEventListener('hidden.bs.modal', () => {
        if (!tieneBorrador()) {
            const form = document.getElementById('formLevantamiento');
            form.reset();
            delete form.dataset.editId;
            document.getElementById('tituloFormulario').innerHTML = '<i class="fas fa-clipboard-list me-2"></i>Nuevo Levantamiento';
            document.getElementById('btnSubmitForm').innerHTML = '<i class="fas fa-save me-2"></i>Guardar Levantamiento';
            document.getElementById('cliente_id').disabled = false;
            document.getElementById('btnCrearArticulo').disabled = true;
            limpiarArticulos();
            clienteSeleccionadoId = null; clienteSeleccionadoNombre = '';
            document.getElementById('camposDinamicos').innerHTML = '';
            document.getElementById('draftBanner').style.setProperty('display','none','important');
        }
        actualizarIndicadorBorrador();
    });
});
</script>
</body>
</html>