<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $articulo->Nombre }} - Sistema de Levantamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--sidebar-width:250px;--sidebar-collapsed-width:70px;--uc:#1D67A8;--ud:#1D67A8;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f7fa;}
        .sidebar{position:fixed;top:0;left:0;height:100vh;width:var(--sidebar-width);background:linear-gradient(135deg,var(--uc) 0%,var(--ud) 100%);transition:all .3s ease;z-index:1000;box-shadow:2px 0 10px rgba(0,0,0,.1);}
        .sidebar.collapsed{width:var(--sidebar-collapsed-width);}
        .sidebar-header{padding:20px;text-align:center;color:white;border-bottom:1px solid rgba(255,255,255,.1);}
        .sidebar-header h4{margin:0;font-size:18px;white-space:nowrap;overflow:hidden;}
        .sidebar.collapsed .sidebar-header h4,.sidebar.collapsed .menu-text{opacity:0;width:0;}
        .sidebar-menu{list-style:none;padding:0;margin:20px 0;}
        .menu-item{margin:5px 0;}
        .menu-link{display:flex;align-items:center;padding:15px 20px;color:rgba(255,255,255,.8);text-decoration:none;transition:all .3s;}
        .menu-link:hover{background:rgba(255,255,255,.1);color:white;}
        .menu-link.active{background:rgba(255,255,255,.2);color:white;border-left:4px solid white;}
        .menu-icon{width:30px;text-align:center;font-size:20px;}
        .menu-text{margin-left:15px;white-space:nowrap;transition:opacity .3s;}
        .sidebar-footer{position:absolute;bottom:0;width:100%;padding:20px;border-top:1px solid rgba(255,255,255,.1);}
        .main-content{margin-left:var(--sidebar-width);transition:margin-left .3s ease;padding:20px;}
        .main-content.expanded{margin-left:var(--sidebar-collapsed-width);}
        .top-bar{background:white;padding:15px 20px;border-radius:10px;margin-bottom:25px;box-shadow:0 2px 4px rgba(0,0,0,.1);display:flex;justify-content:space-between;align-items:center;}
        .toggle-btn{background:var(--uc);color:white;border:none;width:40px;height:40px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
        .page-header{background:linear-gradient(135deg,var(--uc),var(--ud));color:white;border-radius:12px;padding:25px 30px;margin-bottom:25px;}
        .info-card{background:white;border-radius:12px;padding:25px;box-shadow:0 2px 10px rgba(0,0,0,.07);margin-bottom:20px;}
        .info-card h6{color:var(--uc);font-weight:700;font-size:.92rem;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid #eef0f8;}
        .dato-row{display:flex;align-items:flex-start;gap:12px;margin-bottom:14px;}
        .dato-icon{width:34px;height:34px;border-radius:8px;background:#eef2ff;display:flex;align-items:center;justify-content:center;color:var(--uc);font-size:.85rem;flex-shrink:0;}
        .dato-label{font-size:.75rem;color:#888;margin-bottom:2px;}
        .dato-val{font-size:.95rem;color:#1a1a2e;font-weight:500;}
        .badge-definir{background:#fff3cd;color:#856404;font-size:.78rem;padding:4px 10px;border-radius:20px;font-weight:600;}
        .badge-usos{background:#e8f4fd;color:#0d6efd;font-size:.78rem;padding:4px 10px;border-radius:20px;}
        .cliente-pill{display:inline-flex;align-items:center;gap:6px;background:#f0f4ff;color:var(--uc);border-radius:20px;padding:5px 12px;font-size:.83rem;margin:3px;}
        .lev-row{border-bottom:1px solid #f0f0f0;padding:10px 0;display:flex;align-items:center;gap:12px;}
        .lev-row:last-child{border-bottom:none;}
        .stat-mini{background:#f8f9ff;border-radius:10px;padding:14px;text-align:center;}
        .stat-mini .num{font-size:1.8rem;font-weight:700;color:var(--uc);}
        .stat-mini .label{font-size:.75rem;color:#888;}
        @media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.mobile-open{transform:translateX(0);}.main-content{margin-left:0!important;}}
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
    <div class="sidebar-footer"><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="menu-link w-100 border-0 bg-transparent"><i class="fas fa-sign-out-alt menu-icon"></i><span class="menu-text">Cerrar Sesión</span></button></form></div>
</aside>

<main class="main-content" id="mainContent">
    <div class="top-bar">
        <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('usuario.productos.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Catálogo</a>
            <a href="{{ route('usuario.productos.edit', $articulo->Id_Articulos) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
        </div>
    </div>

    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div style="flex:1;">
                <p class="mb-1 opacity-75" style="font-size:.85rem;">Detalle del Artículo</p>
                <h1 style="font-size:22px;font-weight:600;line-height:1.3;">{{ $articulo->Nombre }}</h1>
                <div class="mt-2 d-flex gap-2 flex-wrap">
                    @if($articulo->modelo_por_definir)
                    <span class="badge-definir"><i class="fas fa-clock me-1"></i>Modelo por definir</span>
                    @endif
                    <span class="badge-usos"><i class="fas fa-redo-alt me-1"></i>{{ $articulo->veces_solicitado }} veces solicitado</span>
                </div>
            </div>
            <div class="text-end opacity-75" style="font-size:.8rem;">
                Registrado: {{ \Carbon\Carbon::parse($articulo->fecha_creacion)->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Col izquierda: Info -->
        <div class="col-lg-8">

            <!-- Datos generales -->
            <div class="info-card">
                <h6><i class="fas fa-info-circle me-2"></i>Información General</h6>

                <div class="dato-row">
                    <div class="dato-icon"><i class="fas fa-tag"></i></div>
                    <div>
                        <div class="dato-label">Marca</div>
                        <div class="dato-val">{{ $articulo->marca_nombre ?? '—' }}</div>
                        @if($articulo->marca_descripcion)
                        <small class="text-muted">{{ $articulo->marca_descripcion }}</small>
                        @endif
                    </div>
                </div>

                <div class="dato-row">
                    <div class="dato-icon"><i class="fas fa-microchip"></i></div>
                    <div>
                        <div class="dato-label">Modelo</div>
                        @if($articulo->modelo_por_definir)
                        <span class="badge-definir"><i class="fas fa-clock me-1"></i>Por definir</span>
                        @else
                        <div class="dato-val">{{ $articulo->modelo_nombre ?? '—' }}</div>
                        @if($articulo->modelo_descripcion)
                        <small class="text-muted">{{ $articulo->modelo_descripcion }}</small>
                        @endif
                        @endif
                    </div>
                </div>

                @if($articulo->Descripcion)
                <div class="dato-row">
                    <div class="dato-icon"><i class="fas fa-align-left"></i></div>
                    <div>
                        <div class="dato-label">Descripción</div>
                        <div class="dato-val" style="font-weight:400;font-size:.9rem;line-height:1.5;">{{ $articulo->Descripcion }}</div>
                    </div>
                </div>
                @endif

                <div class="dato-row mb-0">
                    <div class="dato-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div>
                        <div class="dato-label">Fecha de registro</div>
                        <div class="dato-val">{{ \Carbon\Carbon::parse($articulo->fecha_creacion)->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Levantamientos recientes -->
            <div class="info-card">
                <h6><i class="fas fa-clipboard-list me-2"></i>Levantamientos Recientes</h6>
                @forelse($levantamientosRecientes as $lev)
                <div class="lev-row">
                    <div style="width:36px;height:36px;border-radius:8px;background:#eef2ff;display:flex;align-items:center;justify-content:center;color:var(--uc);flex-shrink:0;">
                        <i class="fas fa-clipboard-check" style="font-size:.85rem;"></i>
                    </div>
                    <div style="flex:1;">
                        <div class="fw-semibold" style="font-size:.88rem;">{{ $lev->cliente_nombre }}</div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($lev->fecha_creacion)->format('d/m/Y') }} · Cant: {{ $lev->Cantidad }}</small>
                    </div>
                    <span class="badge bg-{{ $lev->estatus === 'Completado' ? 'success' : ($lev->estatus === 'Pendiente' ? 'warning text-dark' : 'info') }}">
                        {{ $lev->estatus }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center py-3"><i class="fas fa-inbox me-2"></i>Sin levantamientos registrados</p>
                @endforelse
            </div>
        </div>

        <!-- Col derecha: Stats + Clientes -->
        <div class="col-lg-4">
            <!-- Stats -->
            <div class="info-card mb-4">
                <h6><i class="fas fa-chart-bar me-2"></i>Estadísticas</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-mini">
                            <div class="num">{{ $articulo->veces_solicitado }}</div>
                            <div class="label">Veces solicitado</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-mini">
                            <div class="num">{{ $clientesQueUsan->count() }}</div>
                            <div class="label">Clientes activos</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-mini">
                            <div class="num">{{ $levantamientosRecientes->count() }}</div>
                            <div class="label">Levantamientos</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-mini">
                            <div class="num" style="font-size:1.1rem;">
                                @if($articulo->modelo_por_definir)
                                    <span style="color:#f39c12;font-size:.8rem;">Por definir</span>
                                @else
                                    <i class="fas fa-check-circle text-success"></i>
                                @endif
                            </div>
                            <div class="label">Estado del modelo</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clientes que lo usan -->
            <div class="info-card">
                <h6><i class="fas fa-users me-2"></i>Clientes que lo usan</h6>
                @forelse($clientesQueUsan as $cl)
                <span class="cliente-pill">
                    <i class="fas fa-building" style="font-size:.75rem;"></i>
                    {{ $cl->Nombre }}
                    @if($cl->Es_Principal)
                    <i class="fas fa-star text-warning" style="font-size:.7rem;" title="Principal"></i>
                    @endif
                </span>
                @empty
                <p class="text-muted" style="font-size:.85rem;"><i class="fas fa-info-circle me-1"></i>Ningún cliente activo usa este artículo.</p>
                @endforelse
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('toggleBtn').addEventListener('click', () => {
        const s = document.getElementById('sidebar'), m = document.getElementById('mainContent');
        if (window.innerWidth <= 768) s.classList.toggle('mobile-open');
        else { s.classList.toggle('collapsed'); m.classList.toggle('expanded'); }
    });
</script>
</body>
</html>