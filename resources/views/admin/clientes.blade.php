<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Clientes - Sistema de Levantamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #1D67A8;
            --sidebar-width: 280px;
            --sidebar-collapsed: 70px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }

        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #1D67A8 0%, #1D67A8 100%);
            transition: all 0.3s ease; z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed); }
        .sidebar-header {
            padding: 20px; text-align: center; color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header h4 { margin: 0; font-size: 18px; white-space: nowrap; overflow: hidden; }
        .sidebar.collapsed .sidebar-header h4,
        .sidebar.collapsed .menu-text { opacity: 0; width: 0; }
        .sidebar-menu { list-style: none; padding: 0; margin: 20px 0; }
        .menu-item { margin: 5px 0; }
        .menu-link {
            display: flex; align-items: center; padding: 15px 20px;
            color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s;
        }
        .menu-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .menu-link.active { background: rgba(255,255,255,0.2); color: white; border-left: 4px solid white; }
        .menu-icon { width: 30px; text-align: center; font-size: 20px; }
        .menu-text { margin-left: 15px; white-space: nowrap; transition: opacity 0.3s; }
        .sidebar-footer {
            position: absolute; bottom: 0; width: 100%; padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .main-content { margin-left: var(--sidebar-width); transition: margin-left 0.3s ease; padding: 20px; }
        .main-content.expanded { margin-left: var(--sidebar-collapsed); }

        .top-bar {
            background: white; padding: 15px 25px; border-radius: 10px; margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;
        }
        .toggle-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #333; }

        .welcome-card {
            background: white; padding: 30px; border-radius: 10px; margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .welcome-card h1 { color: #2c3e50; margin-bottom: 10px; font-size: 28px; font-weight: 600; }

        .cliente-card {
            margin-bottom: 15px; transition: all 0.3s;
            border-left: 4px solid var(--primary-color);
        }
        .cliente-card:hover { box-shadow: 0 5px 20px rgba(0,0,0,0.1); transform: translateY(-2px); }

        .articulo-badge {
            display: inline-block; padding: 4px 12px; background: #e9ecef;
            border-radius: 15px; font-size: 0.85em; margin-right: 5px; margin-bottom: 5px;
        }
        .articulo-badge.principal { background: #85bce9; color: #000; font-weight: 600; }
        .action-buttons .btn { margin-left: 5px; }

        .search-box { position: relative; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #6c757d; }
        .search-box input { padding-left: 45px; }

        .form-section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .form-section h6 { color: var(--primary-color); font-weight: 600; margin-bottom: 15px; }

        .articulos-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px; margin-bottom: 10px;
        }
        .articulo-checkbox {
            border: 2px solid #e0e0e0; border-radius: 8px; padding: 12px;
            transition: all 0.3s; cursor: pointer;
        }
        .articulo-checkbox:hover { border-color: var(--primary-color); background: #fff5f5; }
        .articulo-checkbox input[type="checkbox"] { margin-right: 10px; }
        .articulo-checkbox input[type="radio"]    { margin-left: 5px; }
        .articulo-checkbox label { cursor: pointer; margin: 0; width: 100%; }

        /* ── Validación visual ── */
        .field-duplicado { border-color: #dc3545 !important; box-shadow: 0 0 0 .2rem rgba(220,53,69,.2) !important; }
        .field-warn      { border-color: #ffc107 !important; box-shadow: 0 0 0 .2rem rgba(255,193,7,.2)  !important; }
        .alerta-campo    { font-size: .8rem; margin-top: 4px; display: none; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .articulos-grid { grid-template-columns: 1fr; }
        }
          .btn-danger {
    background-color: #1D67A8;
    border-color: #1D67A8;
}

.btn-danger:hover {
    background-color: #407fb6;
    border-color: #1D67A8;
}



.bg-danger{
    background-color: #1D67A8 !important;
}

    </style>
</head>
<body>

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
        <h4>Sistema</h4>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="fas fa-home menu-icon"></i><span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin.usuarios') }}" class="menu-link">
                <i class="fas fa-users menu-icon"></i><span class="menu-text">Usuarios</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin.levantamientos.index') }}" class="menu-link">
                <i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Levantamientos</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin.clientes.index') }}" class="menu-link active">
                <i class="fas fa-building menu-icon"></i><span class="menu-text">Clientes</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin.productos.index') }}" class="menu-link">
                <i class="fas fa-box menu-icon"></i><span class="menu-text">Productos</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin.tipos-levantamiento.index') }}" class="menu-link">
                <i class="fa-solid fa-gear menu-icon"></i><span class="menu-text">Tipos de Levantamientos</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="menu-link w-100 border-0 bg-transparent">
                <i class="fas fa-sign-out-alt menu-icon"></i>
                <span class="menu-text">Cerrar Sesión</span>
            </button>
        </form>
    </div>
</aside>

<!-- ═══════════════ MAIN CONTENT ═══════════════ -->
<main class="main-content" id="mainContent">

    <div class="top-bar">
        <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->Nombres }} {{ Auth::user()->ApellidosPat }}</span>
            <span class="admin-badge ms-2 badge bg-danger">ADMIN</span>
        </div>
    </div>

    <div class="welcome-card">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-building me-2"></i>Gestión de Clientes</h1>
                <p class="mb-0">Administra y consulta la información de tus clientes</p>
            </div>
            <button class="btn btn-danger btn-lg" onclick="abrirModalCrear()">
                <i class="fas fa-plus me-2"></i>Nuevo Cliente
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#activos" type="button">
                <i class="fas fa-check-circle me-2"></i>Clientes Activos
                <span class="badge bg-success ms-2">{{ $clientesActivos->count() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inactivos" type="button">
                <i class="fas fa-history me-2"></i>Historial de Inactivos
                <span class="badge bg-secondary ms-2">{{ $clientesInactivos->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Clientes Activos -->
        <div class="tab-pane fade show active" id="activos">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" class="form-control" id="searchActivos" placeholder="Buscar por nombre, correo o teléfono...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="text-muted">Total: <strong>{{ $clientesActivos->count() }}</strong> clientes activos</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="clientesActivosContainer">
                        @forelse($clientesActivos as $cliente)
                        <div class="cliente-card card cliente-item"
                             data-nombre="{{ strtolower($cliente->Nombre) }}"
                             data-correo="{{ strtolower($cliente->Correo ?? '') }}"
                             data-telefono="{{ $cliente->Telefono ?? '' }}">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-lg-4">
                                        <h5 class="mb-1">
                                            <i class="fas fa-building text-primary me-2"></i>{{ $cliente->Nombre }}
                                        </h5>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            Registro: {{ \Carbon\Carbon::parse($cliente->fecha_registro)->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-1">
                                            <i class="fas fa-envelope text-muted me-2"></i>
                                            <small>{{ $cliente->Correo ?? '—' }}</small>
                                        </div>
                                        <div>
                                            <i class="fas fa-phone text-muted me-2"></i>
                                            <small>{{ $cliente->Telefono ?? '—' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-1"><small class="text-muted">Artículos asociados:</small></div>
                                        @if($cliente->articulos->count() > 0)
                                            @foreach($cliente->articulos->take(3) as $art)
                                            <span class="articulo-badge {{ $art->pivot->Es_Principal ? 'principal' : '' }}">
                                                {{ $art->Nombre }}
                                                @if($art->pivot->Es_Principal)<i class="fas fa-star ms-1"></i>@endif
                                            </span>
                                            @endforeach
                                            @if($cliente->articulos->count() > 3)
                                            <span class="articulo-badge">+{{ $cliente->articulos->count() - 3 }} más</span>
                                            @endif
                                        @else
                                            <span class="text-muted"><small>Sin artículos</small></span>
                                        @endif
                                    </div>
                                    <div class="col-lg-2 text-end">
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-info" onclick="verDetalles({{ $cliente->Id_Cliente }})" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editarCliente({{ $cliente->Id_Cliente }})" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="confirmarInactivar({{ $cliente->Id_Cliente }}, '{{ $cliente->Nombre }}')" title="Inactivar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay clientes activos registrados</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes Inactivos -->
        <div class="tab-pane fade" id="inactivos">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" class="form-control" id="searchInactivos" placeholder="Buscar por nombre, correo o teléfono...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="text-muted">Total: <strong>{{ $clientesInactivos->count() }}</strong> clientes inactivos</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="clientesInactivosContainer">
                        @forelse($clientesInactivos as $cliente)
                        <div class="cliente-card card cliente-item"
                             data-nombre="{{ strtolower($cliente->Nombre) }}"
                             data-correo="{{ strtolower($cliente->Correo ?? '') }}"
                             data-telefono="{{ $cliente->Telefono ?? '' }}"
                             style="border-left-color: #6c757d; opacity: 0.8;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-lg-4">
                                        <h5 class="mb-1">
                                            <i class="fas fa-building text-muted me-2"></i>{{ $cliente->Nombre }}
                                        </h5>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            Registro: {{ \Carbon\Carbon::parse($cliente->fecha_registro)->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-1">
                                            <i class="fas fa-envelope text-muted me-2"></i>
                                            <small>{{ $cliente->Correo ?? '—' }}</small>
                                        </div>
                                        <div>
                                            <i class="fas fa-phone text-muted me-2"></i>
                                            <small>{{ $cliente->Telefono ?? '—' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <span class="badge bg-secondary">INACTIVO</span>
                                    </div>
                                    <div class="col-lg-2 text-end">
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-info" onclick="verDetalles({{ $cliente->Id_Cliente }})" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success" onclick="confirmarReactivar({{ $cliente->Id_Cliente }}, '{{ $cliente->Nombre }}')" title="Reactivar">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay clientes inactivos</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL: Crear / Editar Cliente                          -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-building me-2"></i>
                    <span id="modalTitle">Nuevo Cliente</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formCliente">
                <input type="hidden" id="id_cliente"  name="id_cliente">
                <input type="hidden" id="form_mode"   value="create">

                <div class="modal-body">

                    <!-- Información General -->
                    <div class="form-section">
                        <h6><i class="fas fa-info-circle me-2"></i>Información General</h6>
                        <div class="row">

                            <!-- NOMBRE -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nombre del Cliente <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre" id="nombre" required
                                       oninput="validarNombre()">
                                <div class="alerta-campo text-warning" id="alerta_nombre">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Ya existe un cliente con este nombre: <strong id="alerta_nombre_detalle"></strong>.
                                    Puedes continuar si es un cliente diferente.
                                </div>
                            </div>

                            <!-- CORREO -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" name="correo" id="correo"
                                       oninput="validarCorreo()">
                                <div class="alerta-campo text-danger" id="alerta_correo">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    Este correo ya está registrado por: <strong id="alerta_correo_detalle"></strong>.
                                </div>
                            </div>

                            <!-- TELÉFONO -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" id="telefono"
                                       oninput="validarTelefono()">
                                <div class="alerta-campo text-warning" id="alerta_telefono">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Este teléfono ya está registrado por: <strong id="alerta_telefono_detalle"></strong>.
                                    Puedes continuar si es correcto.
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-section">
                        <h6><i class="fas fa-map-marker-alt me-2"></i>Dirección</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">País <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pais" id="pais" value="México" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="estado" id="estado" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" class="form-control" name="ciudad" id="ciudad" placeholder="Ej. Toluca, CDMX, Monterrey...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Municipio</label>
                                <input type="text" class="form-control" name="municipio" id="municipio">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Colonia</label>
                                <input type="text" class="form-control" name="colonia" id="colonia">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Calle</label>
                                <input type="text" class="form-control" name="calle" id="calle">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código Postal</label>
                                <input type="number" class="form-control" name="codigo_postal" id="codigo_postal">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">No. Exterior</label>
                                <input type="number" class="form-control" name="no_ex" id="no_ex">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">No. Interior</label>
                                <input type="number" class="form-control" name="no_in" id="no_in">
                            </div>
                        </div>
                    </div>

                    <!-- Artículos Asociados -->
                    <div class="form-section">
                        <h6>
                            <i class="fas fa-box me-2"></i>Artículos Asociados
                            <small class="text-muted">(Máximo 10 - Marca el principal con la estrella)</small>
                        </h6>
                        <div class="articulos-grid" id="articulosGrid">
                            @foreach($articulos as $articulo)
                            <div class="articulo-checkbox">
                                <input type="checkbox"
                                       name="articulos[]"
                                       value="{{ $articulo->Id_Articulos }}"
                                       id="art_{{ $articulo->Id_Articulos }}"
                                       onchange="limitarArticulos(this)">
                                <label for="art_{{ $articulo->Id_Articulos }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span>{{ $articulo->Nombre }}</span>
                                        <input type="radio"
                                               name="articulo_principal"
                                               value="{{ $articulo->Id_Articulos }}"
                                               title="Marcar como principal"
                                               onclick="marcarPrincipal({{ $articulo->Id_Articulos }})">
                                    </div>
                                    <small class="text-muted d-block mt-1">{{ $articulo->marca->Nombre ?? '' }}</small>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Seleccionados: <span id="countArticulos">0</span>/10
                        </small>
                    </div>

                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger" id="btnGuardarCliente">
                        <i class="fas fa-save me-2"></i>Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL: Detalles Cliente                                -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detalles del Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detallesContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-danger"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ════════════════════════════════════════════════════════════
    // DATOS DE CLIENTES EXISTENTES (para validación en frontend)
    // ════════════════════════════════════════════════════════════
    /**
     * Cargamos todos los clientes (activos e inactivos) para poder
     * validar duplicados sin hacer una petición al servidor por cada
     * tecla que el usuario presione.
     *
     * Estructura de cada elemento:
     *  { id, nombre, correo, telefono }
     */
    const todosLosClientes = [
        @foreach(array_merge($clientesActivos->toArray(), $clientesInactivos->toArray()) as $c)
        {
            id:       {{ $c->Id_Cliente }},
            nombre:   "{{ addslashes($c->Nombre) }}",
            correo:   "{{ addslashes($c->Correo ?? '') }}",
            telefono: "{{ addslashes($c->Telefono ?? '') }}"
        },
        @endforeach
    ];

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ID del cliente que se está editando actualmente (null = crear nuevo)
    let clienteEnEdicionId = null;

    // ════════════════════════════════════════════════════════════
    // SIDEBAR
    // ════════════════════════════════════════════════════════════
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    document.getElementById('toggleBtn').addEventListener('click', () => {
        if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); }
        else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
    });

    // ════════════════════════════════════════════════════════════
    // BÚSQUEDA
    // ════════════════════════════════════════════════════════════
    document.getElementById('searchActivos')?.addEventListener('input', e => buscarClientes(e.target.value, 'clientesActivosContainer'));
    document.getElementById('searchInactivos')?.addEventListener('input', e => buscarClientes(e.target.value, 'clientesInactivosContainer'));

    function buscarClientes(termino, contenedorId) {
        termino = termino.toLowerCase();
        document.querySelectorAll(`#${contenedorId} .cliente-item`).forEach(item => {
            const coincide = [item.dataset.nombre, item.dataset.correo, item.dataset.telefono]
                .some(v => v.includes(termino));
            item.style.display = coincide ? '' : 'none';
        });
    }

    // ════════════════════════════════════════════════════════════
    // ARTÍCULOS
    // ════════════════════════════════════════════════════════════
    function limitarArticulos(checkbox) {
        const count = document.querySelectorAll('input[name="articulos[]"]:checked').length;
        document.getElementById('countArticulos').textContent = count;
        if (count > 10) {
            checkbox.checked = false;
            Swal.fire({ icon: 'warning', title: 'Límite alcanzado', text: 'Solo puedes seleccionar máximo 10 artículos' });
            return;
        }
        if (!checkbox.checked) {
            const radio = document.querySelector(`input[name="articulo_principal"][value="${checkbox.value}"]`);
            if (radio && radio.checked) radio.checked = false;
        }
    }

    function marcarPrincipal(articuloId) {
        const checkbox = document.getElementById(`art_${articuloId}`);
        if (!checkbox.checked) { checkbox.checked = true; limitarArticulos(checkbox); }
    }

    // ════════════════════════════════════════════════════════════
    // HELPERS DE NORMALIZACIÓN
    // ════════════════════════════════════════════════════════════
    function norm(str) {
        if (!str) return '';
        return String(str).trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function limpiarTel(str) {
        // Comparar solo dígitos para teléfonos
        return String(str || '').replace(/\D/g, '');
    }

    // ════════════════════════════════════════════════════════════
    // VALIDACIONES EN TIEMPO REAL
    // ════════════════════════════════════════════════════════════

    /**
     * NOMBRE — Advertencia (no bloquea)
     * Muestra aviso si ya existe un cliente con el mismo nombre,
     * excluyendo al cliente que se está editando.
     */
    function validarNombre() {
        const input   = document.getElementById('nombre');
        const alerta  = document.getElementById('alerta_nombre');
        const detalle = document.getElementById('alerta_nombre_detalle');
        const val     = norm(input.value);

        if (!val) {
            input.classList.remove('field-warn');
            alerta.style.display = 'none';
            return false;
        }

        const encontrado = todosLosClientes.find(c =>
            norm(c.nombre) === val && c.id !== clienteEnEdicionId
        );

        if (encontrado) {
            input.classList.add('field-warn');
            input.classList.remove('field-duplicado');
            detalle.textContent = encontrado.nombre;
            alerta.style.display = 'block';
        } else {
            input.classList.remove('field-warn', 'field-duplicado');
            alerta.style.display = 'none';
        }

        return !!encontrado;
    }

    /**
     * CORREO — Bloquea si está duplicado (no se puede guardar)
     * El correo es único: no se permite duplicar.
     */
    function validarCorreo() {
        const input   = document.getElementById('correo');
        const alerta  = document.getElementById('alerta_correo');
        const detalle = document.getElementById('alerta_correo_detalle');
        const val     = norm(input.value);

        if (!val) {
            input.classList.remove('field-duplicado', 'field-warn');
            alerta.style.display = 'none';
            return false;
        }

        const encontrado = todosLosClientes.find(c =>
            c.correo && norm(c.correo) === val && c.id !== clienteEnEdicionId
        );

        if (encontrado) {
            input.classList.add('field-duplicado');
            input.classList.remove('field-warn');
            detalle.textContent = encontrado.nombre;
            alerta.style.display = 'block';
        } else {
            input.classList.remove('field-duplicado', 'field-warn');
            alerta.style.display = 'none';
        }

        return !!encontrado;
    }

    /**
     * TELÉFONO — Advertencia (no bloquea)
     * Avisa si el teléfono ya está en uso, pero deja continuar.
     */
    function validarTelefono() {
        const input   = document.getElementById('telefono');
        const alerta  = document.getElementById('alerta_telefono');
        const detalle = document.getElementById('alerta_telefono_detalle');
        const val     = limpiarTel(input.value);

        if (!val) {
            input.classList.remove('field-warn', 'field-duplicado');
            alerta.style.display = 'none';
            return false;
        }

        const encontrado = todosLosClientes.find(c =>
            limpiarTel(c.telefono) === val && limpiarTel(c.telefono) !== '' && c.id !== clienteEnEdicionId
        );

        if (encontrado) {
            input.classList.add('field-warn');
            input.classList.remove('field-duplicado');
            detalle.textContent = encontrado.nombre;
            alerta.style.display = 'block';
        } else {
            input.classList.remove('field-warn', 'field-duplicado');
            alerta.style.display = 'none';
        }

        return !!encontrado;
    }

    /**
     * Limpia todas las alertas de validación del formulario
     */
    function limpiarAlertas() {
        ['nombre','correo','telefono'].forEach(campo => {
            document.getElementById(campo).classList.remove('field-duplicado','field-warn');
            document.getElementById(`alerta_${campo}`).style.display = 'none';
        });
    }

    // ════════════════════════════════════════════════════════════
    // ABRIR MODAL CREAR
    // ════════════════════════════════════════════════════════════
    function abrirModalCrear() {
        document.getElementById('formCliente').reset();
        document.getElementById('id_cliente').value  = '';
        document.getElementById('form_mode').value   = 'create';
        document.getElementById('modalTitle').textContent = 'Nuevo Cliente';
        document.getElementById('pais').value  = 'México';
        document.getElementById('countArticulos').textContent = '0';
        clienteEnEdicionId = null;
        limpiarAlertas();

        document.querySelectorAll('input[name="articulos[]"]').forEach(cb => cb.checked = false);
        document.querySelectorAll('input[name="articulo_principal"]').forEach(r  => r.checked  = false);

        new bootstrap.Modal(document.getElementById('modalCliente')).show();
    }

    // ════════════════════════════════════════════════════════════
    // EDITAR CLIENTE
    // ════════════════════════════════════════════════════════════
    function editarCliente(id) {
        Swal.fire({ title: 'Cargando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch(`/admin/clientes/${id}/edit`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            Swal.close();
            if (!data.success) {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo cargar el cliente' });
                return;
            }
            const cliente = data.cliente;

            // Establecer el ID en edición para excluirlo de las validaciones
            clienteEnEdicionId = cliente.Id_Cliente;
            limpiarAlertas();

            document.getElementById('id_cliente').value      = cliente.Id_Cliente;
            document.getElementById('form_mode').value       = 'edit';
            document.getElementById('modalTitle').textContent = 'Editar Cliente';
            document.getElementById('nombre').value           = cliente.Nombre;
            document.getElementById('correo').value           = cliente.Correo    || '';
            document.getElementById('telefono').value         = cliente.Telefono  || '';
            document.getElementById('pais').value             = cliente.direccion.Pais;
            document.getElementById('estado').value           = cliente.direccion.Estado;
            document.getElementById('ciudad').value           = cliente.direccion.Ciudad    || '';
            document.getElementById('municipio').value        = cliente.direccion.Municipio || '';
            document.getElementById('colonia').value          = cliente.direccion.Colonia   || '';
            document.getElementById('calle').value            = cliente.direccion.calle     || '';
            document.getElementById('codigo_postal').value    = cliente.direccion.Codigo_Postal || '';
            document.getElementById('no_ex').value            = cliente.direccion.No_Ex || '';
            document.getElementById('no_in').value            = cliente.direccion.No_In || '';

            // Resetear y marcar artículos
            document.querySelectorAll('input[name="articulos[]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name="articulo_principal"]').forEach(r  => r.checked  = false);

            cliente.articulos.forEach(art => {
                const checkbox = document.getElementById(`art_${art.Id_Articulos}`);
                if (checkbox) {
                    checkbox.checked = true;
                    if (art.pivot.Es_Principal) {
                        const radio = document.querySelector(`input[name="articulo_principal"][value="${art.Id_Articulos}"]`);
                        if (radio) radio.checked = true;
                    }
                }
            });
            document.getElementById('countArticulos').textContent = cliente.articulos.length;

            new bootstrap.Modal(document.getElementById('modalCliente')).show();
        })
        .catch(() => {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al obtener el cliente' });
        });
    }

    // ════════════════════════════════════════════════════════════
    // GUARDAR CLIENTE (submit)
    // ════════════════════════════════════════════════════════════
    document.getElementById('formCliente').addEventListener('submit', function(e) {
        e.preventDefault();

        // 1) CORREO duplicado → BLOQUEAR siempre
        if (validarCorreo()) {
            Swal.fire({
                icon: 'error',
                title: 'Correo duplicado',
                text: 'Ya existe un cliente registrado con ese correo electrónico. Por favor usa uno diferente.'
            });
            return;
        }

        // 2) NOMBRE duplicado → pedir confirmación
        const nombreDuplicado   = validarNombre();
        // 3) TELÉFONO duplicado → pedir confirmación
        const telefonoDuplicado = validarTelefono();

        if (nombreDuplicado || telefonoDuplicado) {
            let mensajes = [];
            if (nombreDuplicado)   mensajes.push('el <strong>nombre</strong>');
            if (telefonoDuplicado) mensajes.push('el <strong>teléfono</strong>');

            Swal.fire({
                icon:              'warning',
                title:             'Posible duplicado',
                html:              `Detectamos que ${mensajes.join(' y ')} ya están registrados en otro cliente.<br><br>¿Deseas continuar de todas formas?`,
                showCancelButton:  true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText:  'Cancelar',
                confirmButtonColor:'#f39c12',
            }).then(result => {
                if (result.isConfirmed) enviarFormulario();
            });
        } else {
            enviarFormulario();
        }
    });

    function enviarFormulario() {
        const formData = new FormData(document.getElementById('formCliente'));
        const mode     = document.getElementById('form_mode').value;
        const id       = document.getElementById('id_cliente').value;

        let url = '/admin/clientes';
        if (mode === 'edit' && id) {
            url = `/admin/clientes/${id}`;
            formData.append('_method', 'PUT');
        }

        Swal.fire({ title: 'Guardando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch(url, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body:    formData
        })
        .then(r => r.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: data.message || 'Cliente guardado correctamente', timer: 2000, showConfirmButton: false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al guardar el cliente' });
            }
        })
        .catch(() => {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al procesar la solicitud' });
        });
    }

    // ════════════════════════════════════════════════════════════
    // VER DETALLES
    // ════════════════════════════════════════════════════════════
    function verDetalles(id) {
        new bootstrap.Modal(document.getElementById('modalDetalles')).show();

        fetch(`/admin/clientes/${id}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('detallesContent').innerHTML = '<p class="text-danger">Error al cargar los detalles</p>';
                return;
            }
            const cliente = data.cliente;
            const ciudadLine = cliente.direccion.Ciudad
                ? `<strong>Ciudad:</strong> ${cliente.direccion.Ciudad}<br>` : '';

            let html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Nombre</h6><p>${cliente.Nombre}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Correo</h6><p>${cliente.Correo || '—'}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Teléfono</h6><p>${cliente.Telefono || '—'}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Estatus</h6>
                        <p><span class="badge bg-${cliente.Estatus === 'Activo' ? 'success' : 'secondary'}">${cliente.Estatus}</span></p>
                    </div>
                </div>
                <hr>
                <h6 class="text-danger mb-3"><i class="fas fa-map-marker-alt me-2"></i>Dirección</h6>
                <p>
                    ${cliente.direccion.calle || ''} ${cliente.direccion.No_Ex ? '#' + cliente.direccion.No_Ex : ''}<br>
                    ${cliente.direccion.Colonia ? cliente.direccion.Colonia + ', ' : ''}${cliente.direccion.Municipio || ''}<br>
                    ${ciudadLine}
                    ${cliente.direccion.Estado}, ${cliente.direccion.Pais}
                    ${cliente.direccion.Codigo_Postal ? ' - CP ' + cliente.direccion.Codigo_Postal : ''}
                </p>
                <hr>
                <h6 class="text-danger mb-3"><i class="fas fa-box me-2"></i>Artículos</h6>
            `;

            if (cliente.articulos && cliente.articulos.length > 0) {
                cliente.articulos.forEach(art => {
                    html += `<span class="articulo-badge ${art.pivot.Es_Principal ? 'principal' : ''}">
                        ${art.Nombre} ${art.pivot.Es_Principal ? '<i class="fas fa-star ms-1"></i>' : ''}
                    </span>`;
                });
            } else {
                html += '<p class="text-muted">Sin artículos asociados</p>';
            }

            document.getElementById('detallesContent').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('detallesContent').innerHTML = '<p class="text-danger">Error al cargar los detalles</p>';
        });
    }

    // ════════════════════════════════════════════════════════════
    // INACTIVAR / REACTIVAR
    // ════════════════════════════════════════════════════════════
    function confirmarInactivar(id, nombre) {
        Swal.fire({
            title: '¿Inactivar cliente?', text: `El cliente "${nombre}" será inactivado`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, inactivar', cancelButtonText: 'Cancelar'
        }).then(r => { if (r.isConfirmed) cambiarEstatus(id, 'inactivar'); });
    }

    function confirmarReactivar(id, nombre) {
        Swal.fire({
            title: '¿Reactivar cliente?', text: `El cliente "${nombre}" será reactivado`,
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#28a745', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, reactivar', cancelButtonText: 'Cancelar'
        }).then(r => { if (r.isConfirmed) cambiarEstatus(id, 'reactivar'); });
    }

    function cambiarEstatus(id, accion) {
        Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`/admin/clientes/${id}/${accion}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: data.message, timer: 2000, showConfirmButton: false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al cambiar el estatus' });
            }
        })
        .catch(() => { Swal.close(); Swal.fire({ icon: 'error', title: 'Error', text: 'Error al procesar la solicitud' }); });
    }
</script>
</body>
</html>