<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Productos - Sistema de Levantamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --sidebar-width:250px; --sidebar-collapsed-width:70px; --uc:#1D67A8; --ud:#1D67A8; }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f7fa;}
        .sidebar{position:fixed;top:0;left:0;height:100vh;width:var(--sidebar-width);background:linear-gradient(135deg,var(--uc) 0%,var(--ud) 100%);transition:all .3s ease;z-index:1000;box-shadow:2px 0 10px rgba(0,0,0,.1);}
        .sidebar.collapsed{width:var(--sidebar-collapsed-width);}
        .sidebar-header{padding:20px;text-align:center;color:white;border-bottom:1px solid rgba(255,255,255,.1);}
        .sidebar-header h4{margin:0;font-size:18px;white-space:nowrap;overflow:hidden;}
        .sidebar.collapsed .sidebar-header h4,.sidebar.collapsed .menu-text{opacity:0;width:0;}
        .sidebar-menu{list-style:none;padding:0;margin:20px 0;}
        .menu-item{margin:5px 0;}
        .menu-link{display:flex;align-items:center;padding:15px 20px;color:rgba(255,255,255,.8);text-decoration:none;transition:all .3s;position:relative;}
        .menu-link:hover{background:rgba(255,255,255,.1);color:white;}
        .menu-link.active{background:rgba(255,255,255,.2);color:white;border-left:4px solid white;}
        .menu-icon{width:30px;text-align:center;font-size:20px;}
        .menu-text{margin-left:15px;white-space:nowrap;transition:opacity .3s;}
        .sidebar-footer{position:absolute;bottom:0;width:100%;padding:20px;border-top:1px solid rgba(255,255,255,.1);}
        .main-content{margin-left:var(--sidebar-width);transition:margin-left .3s ease;padding:20px;}
        .main-content.expanded{margin-left:var(--sidebar-collapsed-width);}
        .top-bar{background:white;padding:15px 20px;border-radius:10px;margin-bottom:25px;box-shadow:0 2px 4px rgba(0,0,0,.1);display:flex;justify-content:space-between;align-items:center;}
        .toggle-btn{background:var(--uc);color:white;border:none;width:40px;height:40px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
        .toggle-btn:hover{background:var(--ud);}
        .page-header{background:linear-gradient(135deg,var(--uc),var(--ud));color:white;border-radius:12px;padding:25px 30px;margin-bottom:25px;}
        .page-header h1{font-size:26px;font-weight:600;margin-bottom:5px;}
        .filter-card{background:white;border-radius:10px;padding:20px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.06);}
        .producto-card{background:white;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.06);transition:all .3s;border-top:3px solid var(--uc);overflow:hidden;height:100%;display:flex;flex-direction:column;}
        .producto-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(6,39,187,.15);}
        .producto-card-body{padding:18px;flex-grow:1;display:flex;flex-direction:column;}
        .producto-nombre{font-weight:600;font-size:.95rem;color:#1a1a2e;line-height:1.4;margin-bottom:10px;}
        .producto-meta span{font-size:.8rem;display:flex;align-items:center;gap:5px;color:#555;margin-bottom:4px;}
        .producto-footer{padding:12px 18px;background:#f8f9ff;border-top:1px solid #eef0f8;display:flex;gap:6px;align-items:center;}
        .badge-definir{background:#fff3cd;color:#856404;font-size:.72rem;padding:3px 8px;border-radius:20px;font-weight:600;}
        .badge-usos{background:#e8f4fd;color:#0d6efd;font-size:.72rem;padding:3px 8px;border-radius:20px;}
        .empty-state{text-align:center;padding:60px 20px;color:#aaa;}
        .empty-state i{font-size:60px;margin-bottom:15px;display:block;}
        @media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.mobile-open{transform:translateX(0);}.main-content{margin-left:0!important;}}
        .btn-primary{
            background: var(--uc);
            border-color: var(--uc);
        }
    </style>
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header"><i class="fas fa-clipboard-list fa-2x mb-2"></i><h4>Sistema Levantamientos</h4></div>
    <ul class="sidebar-menu">
        <li class="menu-item"><a href="{{ route('usuario.dashboard') }}" class="menu-link"><i class="fas fa-home menu-icon"></i><span class="menu-text">Dashboard</span></a></li>
        <li class="menu-item"><a href="{{ route('usuario.levantamientos.index') }}" class="menu-link"><i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Mis Levantamientos</span></a></li>
        <li class="menu-item"><a href="{{ route('usuario.clientesU') }}" class="menu-link"><i class="fas fa-users menu-icon"></i><span class="menu-text">Clientes</span></a></li>
        <li class="menu-item"><a href="{{ route('usuario.productos.index') }}" class="menu-link active"><i class="fas fa-box menu-icon"></i><span class="menu-text">Productos</span></a></li>
        <li class="menu-item"><a href="{{ route('usuario.tipos-levantamiento.index') }}" class="menu-link"><i class="fas fa-cogs menu-icon"></i><span class="menu-text">Tipos de Levantamiento</span></a></li>
    </ul>
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" class="menu-link w-100 border-0 bg-transparent"><i class="fas fa-sign-out-alt menu-icon"></i><span class="menu-text">Cerrar Sesión</span></button>
        </form>
    </div>
</aside>

<main class="main-content" id="mainContent">
    <div class="top-bar">
        <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold">{{ Auth::user()->Nombres }} {{ Auth::user()->ApellidosPat }}</span>
        </div>
    </div>

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1><i class="fas fa-box me-2"></i>Catálogo de Productos</h1>
                <p class="mb-0 opacity-75">Consulta y administra los artículos del sistema</p>
            </div>
            <a href="{{ route('usuario.productos.create') }}" class="btn btn-light fw-semibold">
                <i class="fas fa-plus me-2"></i>Nuevo Artículo
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Filtros -->
    <div class="filter-card">
        <form method="GET" action="{{ route('usuario.productos.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Buscar artículo</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control" name="busqueda" value="{{ $busqueda }}" placeholder="Nombre, marca o modelo...">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Marca</label>
                <select class="form-select" name="marca">
                    <option value="">Todas las marcas</option>
                    @foreach($marcas as $m)
                    <option value="{{ $m->Id_Marca }}" {{ $marcaFiltro == $m->Id_Marca ? 'selected' : '' }}>{{ $m->Nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrar</button>
                @if($busqueda || $marcaFiltro)
                <a href="{{ route('usuario.productos.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="fas fa-times"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <small class="text-muted">
            Mostrando {{ $articulos->firstItem() ?? 0 }}–{{ $articulos->lastItem() ?? 0 }} de {{ $articulos->total() }} artículos
        </small>
    </div>

    @if($articulos->count() > 0)
    <div class="row g-3">
        @foreach($articulos as $art)
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="producto-card">
                <div class="producto-card-body">
                    <p class="producto-nombre">{{ $art->Nombre }}</p>
                    <div class="producto-meta mt-auto">
                        <span><i class="fas fa-tag text-primary"></i> {{ $art->marca_nombre ?? '—' }}</span>
                        <span>
                            <i class="fas fa-microchip text-secondary"></i>
                            @if($art->modelo_por_definir)
                                <span class="badge-definir">Por definir</span>
                            @else
                                {{ $art->modelo_nombre ?? '—' }}
                            @endif
                        </span>
                        @if($art->Descripcion)
                        <span class="text-muted mt-1" style="font-size:.76rem;line-height:1.3;display:block;">
                            {{ Str::limit($art->Descripcion, 65) }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="producto-footer">
                    <span class="badge-usos me-auto"><i class="fas fa-redo-alt"></i> {{ $art->veces_solicitado }} usos</span>
                    <a href="{{ route('usuario.productos.show', $art->Id_Articulos) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('usuario.productos.edit', $art->Id_Articulos) }}" class="btn btn-sm btn-outline-warning" title="Editar"><i class="fas fa-edit"></i></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $articulos->links() }}</div>

    @else
    <div class="empty-state">
        <i class="fas fa-box-open text-muted"></i>
        <h5 class="text-muted">No se encontraron artículos</h5>
        @if($busqueda || $marcaFiltro)
        <p class="text-muted">Intenta con otros filtros</p>
        <a href="{{ route('usuario.productos.index') }}" class="btn btn-outline-primary mt-2">Limpiar filtros</a>
        @else
        <p class="text-muted">Aún no hay artículos registrados</p>
        <a href="{{ route('usuario.productos.create') }}" class="btn btn-primary mt-2"><i class="fas fa-plus me-2"></i>Crear primer artículo</a>
        @endif
    </div>
    @endif
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    document.getElementById('toggleBtn').addEventListener('click', () => {
        if (window.innerWidth <= 768) sidebar.classList.toggle('mobile-open');
        else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
    });
</script>
</body>
</html>