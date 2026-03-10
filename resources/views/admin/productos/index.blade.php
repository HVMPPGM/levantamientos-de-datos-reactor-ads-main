<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Productos - Sistema de Levantamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboardAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/productos_admin.css') }}">
    <style>
        .stat-card {
            cursor: pointer;
            transition: transform .15s, box-shadow .15s, border-color .15s;
            border: 2px solid transparent;
            border-radius: .75rem;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.12); }
        .stat-card-productos.active-stat { border-color: #0d6efd; }
        .stat-card-marcas.active-stat    { border-color: #0dcaf0; }
        .stat-card-modelos.active-stat   { border-color: #0d6efd; }
        .stat-card-top.active-stat       { border-color: #198754; }
        .seccion { display: none; }
        .seccion.visible { display: block; }
        .section-header-badge { font-size: .78rem; padding: .3rem .65rem; }
    </style>
</head>
<body>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
            <h4>Sistema</h4>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-item"><a href="{{ route('admin.dashboard') }}" class="menu-link"><i class="fas fa-home menu-icon"></i><span class="menu-text">Dashboard</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.usuarios') }}" class="menu-link"><i class="fas fa-users menu-icon"></i><span class="menu-text">Usuarios</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.levantamientos.index') }}" class="menu-link"><i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Levantamientos</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.clientes.index') }}" class="menu-link"><i class="fas fa-building menu-icon"></i><span class="menu-text">Clientes</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.productos.index') }}" class="menu-link active"><i class="fas fa-box menu-icon"></i><span class="menu-text">Productos</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.tipos-levantamiento.index') }}" class="menu-link"><i class="fa-solid fa-gear menu-icon"></i><span class="menu-text">Tipos de Levantamientos</span></a></li>
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

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->nombre_completo }}</span>
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->Nombres, 0, 1)) }}</div>
                <span class="admin-badge">ADMIN</span>
            </div>
        </div>

        <!-- Header -->
        <div class="welcome-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-box me-2"></i>Gestión de Productos</h1>
                    <p class="mb-0">Haz clic en una tarjeta para ver su sección</p>
                </div>
                <a href="{{ route('admin.productos.create') }}" class="btn btn-danger">
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
                </a>
            </div>
        </div>

        <!-- ── Stats clicables ── -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card stat-card-productos active-stat" onclick="mostrarSeccion('productos')" id="stat-productos">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Productos</h6>
                            <h3 class="mb-0">{{ $totalProductos ?? 0 }}</h3>
                            <small class="text-primary"><i class="fas fa-box"></i> Ver catálogo</small>
                        </div>
                        <div class="text-warning"><i class="fas fa-box fa-3x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card stat-card-marcas" onclick="mostrarSeccion('marcas')" id="stat-marcas">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Marcas</h6>
                            <h3 class="mb-0">{{ $totalMarcas ?? 0 }}</h3>
                            <small class="text-info"><i class="fas fa-tag"></i> Administrar</small>
                        </div>
                        <div class="text-info"><i class="fas fa-tag fa-3x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card stat-card-modelos" onclick="mostrarSeccion('modelos')" id="stat-modelos">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Modelos</h6>
                            <h3 class="mb-0">{{ $totalModelos ?? 0 }}</h3>
                            <small class="text-primary"><i class="fas fa-cog"></i> Administrar</small>
                        </div>
                        <div class="text-primary"><i class="fas fa-cog fa-3x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card stat-card-top" onclick="mostrarSeccion('top')" id="stat-top">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Más Solicitado</h6>
                            <h3 class="mb-0">{{ $masVendido->veces_solicitado ?? 0 }}</h3>
                            <small class="text-success"><i class="fas fa-star"></i> {{ Str::limit($masVendido->Nombre ?? 'N/A', 18) }}</small>
                        </div>
                        <div class="text-success"><i class="fas fa-chart-line fa-3x"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECCIÓN PRODUCTOS ══ -->
        <div id="seccion-productos" class="seccion visible">
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-6">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar nombre, marca o modelo...">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <select id="filterMarca" class="form-select">
                                <option value="">Todas las marcas</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->Nombre }}">{{ $marca->Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <select id="filterModelo" class="form-select">
                                <option value="">Todos los modelos</option>
                                @foreach($modelos as $modelo)
                                    <option value="{{ $modelo->Nombre }}">{{ $modelo->Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Catálogo de Productos</h5>
                    <span class="badge bg-light text-primary section-header-badge">{{ $totalProductos }} registros</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tablaProductos">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Veces Solicitado</th>
                                    <th>Fecha Creación</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productos as $producto)
                                    <tr data-producto-id="{{ $producto->Id_Articulos }}"
                                        data-marca="{{ $producto->marca->Nombre }}"
                                        data-modelo="{{ $producto->modelo->Nombre }}">
                                        <td><strong>{{ $producto->Nombre }}</strong></td>
                                        <td><small class="text-muted">{{ $producto->Descripcion ? Str::limit($producto->Descripcion, 60) : 'Sin descripción' }}</small></td>
                                        <td><span class="badge bg-info">{{ $producto->marca->Nombre ?? 'N/A' }}</span></td>
                                        <td><span class="badge bg-primary">{{ $producto->modelo->Nombre ?? 'N/A' }}</span></td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-shopping-cart me-1"></i>{{ $producto->veces_solicitado }}
                                            </span>
                                        </td>
                                        <td><small class="text-muted">{{ \Carbon\Carbon::parse($producto->fecha_creacion)->format('d/m/Y') }}</small></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="{{ route('admin.productos.show', $producto->Id_Articulos) }}"
                                                   class="btn btn-sm btn-info btn-action" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.productos.edit', $producto->Id_Articulos) }}"
                                                   class="btn btn-sm btn-warning btn-action" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger btn-action"
                                                        onclick="eliminarProducto({{ $producto->Id_Articulos }}, '{{ addslashes($producto->Nombre) }}')"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted">No hay productos registrados</p>
                                            <a href="{{ route('admin.productos.create') }}" class="btn btn-danger btn-sm">
                                                <i class="fas fa-plus me-1"></i>Crear primer producto
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECCIÓN MARCAS ══ -->
        <div id="seccion-marcas" class="seccion">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Catálogo de Marcas</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-info section-header-badge">{{ $totalMarcas }} registros</span>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearMarca" onclick="resetModalMarca()">
                            <i class="fas fa-plus me-1"></i>Nueva Marca
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th><th>Nombre</th><th>Descripción</th><th>Productos asociados</th><th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($marcas as $marca)
                                    <tr>
                                        <td><small class="text-muted">{{ $marca->Id_Marca }}</small></td>
                                        <td><strong>{{ $marca->Nombre }}</strong></td>
                                        <td><small class="text-muted">{{ $marca->Descripcion ?? 'Sin descripción' }}</small></td>
                                        <td><span class="badge bg-secondary">{{ $productos->where('marca_nombre', $marca->Nombre)->count() }} productos</span></td>
                                        <td>
                                            <div class="table-actions justify-content-center">
                                                <button class="btn btn-sm btn-warning btn-action"
                                                        onclick="editarMarca({{ $marca->Id_Marca }}, '{{ addslashes($marca->Nombre) }}', '{{ addslashes($marca->Descripcion ?? '') }}')"
                                                        title="Editar"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-danger btn-action"
                                                        onclick="eliminarMarca({{ $marca->Id_Marca }}, '{{ addslashes($marca->Nombre) }}')"
                                                        title="Eliminar"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-5">
                                        <i class="fas fa-tag fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No hay marcas registradas</p>
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECCIÓN MODELOS ══ -->
        <div id="seccion-modelos" class="seccion">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Catálogo de Modelos</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-primary section-header-badge">{{ $totalModelos }} registros</span>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearModelo" onclick="resetModalModelo()">
                            <i class="fas fa-plus me-1"></i>Nuevo Modelo
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th><th>Nombre</th><th>Descripción</th><th>Productos asociados</th><th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($modelos as $modelo)
                                    <tr>
                                        <td><small class="text-muted">{{ $modelo->Id_Modelo }}</small></td>
                                        <td><strong>{{ $modelo->Nombre }}</strong></td>
                                        <td><small class="text-muted">{{ $modelo->Descripcion ?? 'Sin descripción' }}</small></td>
                                        <td><span class="badge bg-secondary">{{ $productos->where('modelo_nombre', $modelo->Nombre)->count() }} productos</span></td>
                                        <td>
                                            <div class="table-actions justify-content-center">
                                                <button class="btn btn-sm btn-warning btn-action"
                                                        onclick="editarModelo({{ $modelo->Id_Modelo }}, '{{ addslashes($modelo->Nombre) }}', '{{ addslashes($modelo->Descripcion ?? '') }}')"
                                                        title="Editar"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-danger btn-action"
                                                        onclick="eliminarModelo({{ $modelo->Id_Modelo }}, '{{ addslashes($modelo->Nombre) }}')"
                                                        title="Eliminar"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-5">
                                        <i class="fas fa-cog fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No hay modelos registrados</p>
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECCIÓN TOP ══ -->
        <div id="seccion-top" class="seccion">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Productos Más Solicitados</h5>
                    <span class="badge bg-light text-success section-header-badge">Ranking</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Producto</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th class="text-center">Veces Solicitado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $ranking = $productos->sortByDesc('veces_solicitado')->values(); @endphp
                                @forelse($ranking as $i => $producto)
                                    <tr>
                                        <td class="text-center fw-bold">
                                            @if($i === 0) <span class="text-warning"><i class="fas fa-trophy"></i></span>
                                            @elseif($i === 1) <span class="text-secondary"><i class="fas fa-medal"></i></span>
                                            @elseif($i === 2) <span class="text-danger"><i class="fas fa-award"></i></span>
                                            @else {{ $i + 1 }}
                                            @endif
                                        </td>
                                        <td><strong>{{ $producto->Nombre }}</strong></td>
                                        <td><span class="badge bg-info">{{ $producto->marca->Nombre ?? 'N/A' }}</span></td>
                                        <td><span class="badge bg-primary">{{ $producto->modelo->Nombre ?? 'N/A' }}</span></td>
                                        <td class="text-center">
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-shopping-cart me-1"></i>{{ $producto->veces_solicitado }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.productos.show', $producto->Id_Articulos) }}"
                                               class="btn btn-sm btn-info btn-action" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-5">
                                        <i class="fas fa-chart-bar fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No hay datos de solicitudes aún</p>
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- MODAL MARCA -->
    <div class="modal fade" id="modalCrearMarca" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-tag me-2"></i><span id="tituloModalMarca">Nueva Marca</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formMarca">
                    @csrf
                    <input type="hidden" id="marcaId" value="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Marca <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre_marca" id="nombre_marca" required maxlength="100">
                        </div>
                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white">
                            <i class="fas fa-save me-2"></i><span id="btnTextMarca">Guardar Marca</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL MODELO -->
    <div class="modal fade" id="modalCrearModelo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-cog me-2"></i><span id="tituloModalModelo">Nuevo Modelo</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formModelo">
                    @csrf
                    <input type="hidden" id="modeloId" value="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre_modelo" id="nombre_modelo" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion_modelo" id="descripcion_modelo" rows="2" maxlength="250"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><span id="btnTextModelo">Guardar Modelo</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // ── Sidebar ──
        const sidebar       = document.getElementById('sidebar');
        const mainContent   = document.getElementById('mainContent');
        const toggleBtn     = document.getElementById('toggleBtn');
        const mobileOverlay = document.getElementById('mobileOverlay');
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); mobileOverlay.classList.toggle('active'); }
            else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
        });
        mobileOverlay.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); mobileOverlay.classList.remove('active'); });

        // ── Navegación por stats ──
        const secciones = ['productos', 'marcas', 'modelos', 'top'];

        function mostrarSeccion(nombre) {
            secciones.forEach(s => {
                document.getElementById('seccion-' + s).classList.remove('visible');
                document.getElementById('stat-' + s).classList.remove('active-stat');
            });
            document.getElementById('seccion-' + nombre).classList.add('visible');
            document.getElementById('stat-' + nombre).classList.add('active-stat');
            document.getElementById('seccion-' + nombre).scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // ── Filtros ──
        function filtrarTabla() {
            const search       = document.getElementById('searchInput').value.toLowerCase();
            const marcaFiltro  = document.getElementById('filterMarca').value;
            const modeloFiltro = document.getElementById('filterModelo').value;
            document.querySelectorAll('#tablaProductos tbody tr').forEach(row => {
                const matchSearch  = row.textContent.toLowerCase().includes(search);
                const matchMarca   = !marcaFiltro  || (row.dataset.marca  || '') === marcaFiltro;
                const matchModelo  = !modeloFiltro || (row.dataset.modelo || '') === modeloFiltro;
                row.style.display  = (matchSearch && matchMarca && matchModelo) ? '' : 'none';
            });
        }
        document.getElementById('searchInput').addEventListener('keyup', filtrarTabla);
        document.getElementById('filterMarca').addEventListener('change', filtrarTabla);
        document.getElementById('filterModelo').addEventListener('change', filtrarTabla);

        // ── Eliminar producto ──
        function eliminarProducto(id, nombre) {
            Swal.fire({
                title: '¿Eliminar producto?',
                html: `Se eliminará <strong>${nombre}</strong> permanentemente.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/productos/${id}`, type: 'DELETE',
                        success(data) {
                            if (data.success) Swal.fire('Eliminado', data.message, 'success').then(() => location.reload());
                            else Swal.fire('Error', data.message, 'error');
                        },
                        error(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar', 'error'); }
                    });
                }
            });
        }

        // ── MARCAS ──
        function resetModalMarca() {
            document.getElementById('tituloModalMarca').textContent = 'Nueva Marca';
            document.getElementById('btnTextMarca').textContent = 'Guardar Marca';
            document.getElementById('marcaId').value = '';
            document.getElementById('formMarca').reset();
        }
        function editarMarca(id, nombre, descripcion) {
            document.getElementById('tituloModalMarca').textContent = 'Editar Marca';
            document.getElementById('btnTextMarca').textContent = 'Actualizar Marca';
            document.getElementById('marcaId').value = id;
            document.getElementById('nombre_marca').value = nombre;
            document.getElementById('descripcion_marca').value = descripcion;
            new bootstrap.Modal(document.getElementById('modalCrearMarca')).show();
        }
        function eliminarMarca(id, nombre) {
            Swal.fire({
                title: '¿Eliminar marca?',
                html: `Se eliminará <strong>${nombre}</strong>.<br><small class="text-danger">Los productos asociados perderán su marca.</small>`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/marcas/${id}`, type: 'DELETE',
                        success(data) {
                            if (data.success) Swal.fire('Eliminada', data.message, 'success').then(() => location.reload());
                            else Swal.fire('Error', data.message, 'error');
                        },
                        error(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar la marca', 'error'); }
                    });
                }
            });
        }
        document.getElementById('formMarca').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('marcaId').value;
            const isEdit = id !== '';
            $.ajax({
                url: isEdit ? `/admin/marcas/${id}` : `{{ route('admin.marcas.store') }}`,
                type: isEdit ? 'PUT' : 'POST',
                data: { nombre_marca: document.getElementById('nombre_marca').value, descripcion_marca: document.getElementById('descripcion_marca').value },
                success(data) {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalCrearMarca')).hide();
                        Swal.fire('¡Éxito!', data.message, 'success').then(() => location.reload());
                    }
                },
                error(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Error al guardar la marca', 'error'); }
            });
        });

        // ── MODELOS ──
        function resetModalModelo() {
            document.getElementById('tituloModalModelo').textContent = 'Nuevo Modelo';
            document.getElementById('btnTextModelo').textContent = 'Guardar Modelo';
            document.getElementById('modeloId').value = '';
            document.getElementById('formModelo').reset();
        }
        function editarModelo(id, nombre, descripcion) {
            document.getElementById('tituloModalModelo').textContent = 'Editar Modelo';
            document.getElementById('btnTextModelo').textContent = 'Actualizar Modelo';
            document.getElementById('modeloId').value = id;
            document.getElementById('nombre_modelo').value = nombre;
            document.getElementById('descripcion_modelo').value = descripcion;
            new bootstrap.Modal(document.getElementById('modalCrearModelo')).show();
        }
        function eliminarModelo(id, nombre) {
            Swal.fire({
                title: '¿Eliminar modelo?',
                html: `Se eliminará <strong>${nombre}</strong>.<br><small class="text-danger">Los productos asociados perderán su modelo.</small>`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/modelos/${id}`, type: 'DELETE',
                        success(data) {
                            if (data.success) Swal.fire('Eliminado', data.message, 'success').then(() => location.reload());
                            else Swal.fire('Error', data.message, 'error');
                        },
                        error(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar el modelo', 'error'); }
                    });
                }
            });
        }
        document.getElementById('formModelo').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('modeloId').value;
            const isEdit = id !== '';
            $.ajax({
                url: isEdit ? `/admin/modelos/${id}` : `{{ route('admin.modelos.store') }}`,
                type: isEdit ? 'PUT' : 'POST',
                data: { nombre_modelo: document.getElementById('nombre_modelo').value, descripcion_modelo: document.getElementById('descripcion_modelo').value },
                success(data) {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalCrearModelo')).hide();
                        Swal.fire('¡Éxito!', data.message, 'success').then(() => location.reload());
                    }
                },
                error(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Error al guardar el modelo', 'error'); }
            });
        });

        // ── Flash messages ──
        @if(session('success'))
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: '{{ session("success") }}', confirmButtonColor: '#dc3545' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Error', text: '{{ session("error") }}', confirmButtonColor: '#dc3545' });
        @endif
    </script>
</body>
</html>