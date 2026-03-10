<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cliente->Nombre }} - Detalles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-width:250px; --sidebar-collapsed-width:70px; --user-color:#0627bb; --user-dark:#1066d8; }
        body { background:#f5f7fa; overflow-x:hidden; }

        /* Sidebar */
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

        /* Layout */
        .main-content { margin-left:var(--sidebar-width); transition:margin-left .3s ease; padding:20px; }
        .main-content.expanded { margin-left:var(--sidebar-collapsed-width); }
        .top-bar { background:white; padding:15px 20px; border-radius:10px; margin-bottom:30px; box-shadow:0 2px 4px rgba(0,0,0,.1); display:flex; justify-content:space-between; align-items:center; }
        .toggle-btn { background:var(--user-color); color:white; border:none; width:40px; height:40px; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .3s; }
        .toggle-btn:hover { background:var(--user-dark); }
        .user-info { display:flex; align-items:center; gap:15px; }
        .user-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,var(--user-color),var(--user-dark)); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; }

        /* Content */
        .card-custom { background:white; border-radius:15px; box-shadow:0 2px 12px rgba(0,0,0,.08); margin-bottom:30px; }
        .card-header-custom { background:linear-gradient(135deg,var(--user-color),var(--user-dark)); color:white; padding:25px 30px; border-radius:15px 15px 0 0; }
        .client-header { display:flex; justify-content:space-between; align-items:center; }
        .client-info h2 { margin:0; font-size:28px; }
        .status-badge { padding:8px 20px; border-radius:20px; font-weight:600; }
        .status-activo { background:#28a745; color:white; }
        .status-inactivo { background:#dc3545; color:white; }
        .info-section { padding:25px 30px; }
        .info-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; margin-bottom:20px; }
        .info-item { padding:15px; background:#f8f9fa; border-radius:10px; border-left:4px solid var(--user-color); }
        .info-label { font-size:13px; color:#6c757d; margin-bottom:5px; font-weight:600; }
        .info-value { font-size:16px; color:#2c3e50; font-weight:500; }
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin:20px 0; }
        .stat-card { background:white; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,.05); border-left:4px solid; transition:transform .3s; }
        .stat-card:hover { transform:translateY(-5px); }
        .stat-card.completado { border-color:#28a745; } .stat-card.proceso { border-color:#ffc107; } .stat-card.pendiente { border-color:#17a2b8; } .stat-card.cancelado { border-color:#dc3545; }
        .stat-icon { font-size:32px; margin-bottom:10px; }
        .stat-card.completado .stat-icon { color:#28a745; } .stat-card.proceso .stat-icon { color:#ffc107; } .stat-card.pendiente .stat-icon { color:#17a2b8; } .stat-card.cancelado .stat-icon { color:#dc3545; }
        .stat-number { font-size:36px; font-weight:bold; margin:10px 0; }
        .stat-label { color:#6c757d; font-size:14px; }
        .section-title { font-size:22px; font-weight:600; color:#2c3e50; margin-bottom:20px; padding-bottom:10px; border-bottom:2px solid #f0f0f0; display:flex; align-items:center; gap:10px; }
        .articulos-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:15px; }
        .articulo-card { background:#f8f9fa; border-radius:10px; padding:15px; border-left:4px solid var(--user-color); }
        .articulo-card.principal { border-left-color:#28a745; background:linear-gradient(135deg,rgba(40,167,69,.05),rgba(40,167,69,.1)); }
        .articulo-badge { background:#28a745; color:white; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:600; display:inline-block; margin-bottom:8px; }
        .articulo-nombre { font-weight:600; color:#2c3e50; margin-bottom:8px; }
        .articulo-detalles { font-size:13px; color:#6c757d; }
        .nav-tabs-custom { border-bottom:2px solid #e0e0e0; }
        .nav-tabs-custom .nav-link { border:none; color:#6c757d; font-weight:600; padding:12px 20px; transition:all .3s; }
        .nav-tabs-custom .nav-link:hover { color:var(--user-color); }
        .nav-tabs-custom .nav-link.active { color:var(--user-color); border-bottom:3px solid var(--user-color); background:transparent; }
        .levantamiento-card { background:white; border-radius:10px; padding:20px; margin-bottom:15px; box-shadow:0 2px 6px rgba(0,0,0,.05); border-left:4px solid; transition:all .3s; }
        .levantamiento-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.1); }
        .levantamiento-card.completado { border-left-color:#28a745; } .levantamiento-card.proceso { border-left-color:#ffc107; } .levantamiento-card.pendiente { border-left-color:#17a2b8; } .levantamiento-card.cancelado { border-left-color:#dc3545; }
        .levantamiento-header { display:flex; justify-content:space-between; align-items:start; margin-bottom:15px; }
        .levantamiento-folio { font-size:18px; font-weight:700; color:#2c3e50; }
        .levantamiento-estatus { padding:5px 15px; border-radius:15px; font-size:12px; font-weight:600; }
        .estatus-completado { background:#d4edda; color:#155724; } .estatus-proceso { background:#fff3cd; color:#856404; } .estatus-pendiente { background:#d1ecf1; color:#0c5460; } .estatus-cancelado { background:#f8d7da; color:#721c24; }
        .levantamiento-info { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:15px; margin-top:15px; }
        .lev-info-item { display:flex; align-items:center; gap:10px; }
        .lev-info-item i { color:var(--user-color); width:20px; }
        .empty-state { text-align:center; padding:60px 20px; }
        .empty-state i { font-size:60px; color:#e0e0e0; margin-bottom:15px; }
        .empty-state h4 { color:#6c757d; }
        .empty-state p { color:#adb5bd; }
        .btn-primary-custom { background:linear-gradient(135deg,var(--user-color),var(--user-dark)); border:none; padding:10px 20px; border-radius:8px; font-weight:600; color:white; }
        .btn-primary-custom:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(6,39,187,.3); }

        /* Mobile */
        .mobile-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:999; }
        .mobile-overlay.active { display:block; }
        @media (max-width:768px) { .sidebar { transform:translateX(-100%); } .sidebar.mobile-open { transform:translateX(0); } .main-content { margin-left:0 !important; } }
    </style>
</head>
<body>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Sidebar -->
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
                <a href="{{ route('usuario.levantamientos.index') }}" class="menu-link">
                    <i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Mis Levantamientos</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('usuario.clientesU') }}" class="menu-link active">
                    <i class="fas fa-users menu-icon"></i><span class="menu-text">Clientes</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('usuario.tipos-levantamiento.index') }}" class="menu-link {{ $usuario->Permisos !== 'si' ? 'locked' : '' }}"
                   @if($usuario->Permisos !== 'si') onclick="return false;" @endif>
                    <i class="fas fa-cogs menu-icon"></i><span class="menu-text">Tipos de Levantamiento</span>
                    @if($usuario->Permisos !== 'si')<i class="fas fa-lock lock-icon"></i>@endif
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

    <!-- Main -->
    <main class="main-content" id="mainContent">
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
            <div class="user-info">
                <span>{{ $usuario->Nombres }} {{ $usuario->ApellidosPat }}</span>
                <div class="user-avatar">{{ strtoupper(substr($usuario->Nombres, 0, 1)) }}</div>
            </div>
        </div>

        <!-- Header Card -->
        <div class="card-custom">
            <div class="card-header-custom">
                <div class="client-header">
                    <div class="client-info">
                        <h2><i class="fas fa-building me-2"></i>{{ $cliente->Nombre }}</h2>
                        <small>Cliente desde {{ \Carbon\Carbon::parse($cliente->fecha_registro)->format('d/m/Y') }}</small>
                    </div>
                    <div>
                        <span class="status-badge status-{{ strtolower($cliente->Estatus) }}">{{ $cliente->Estatus }}</span>
                    </div>
                </div>
            </div>
            <div class="info-section">
                <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Estadísticas de Levantamientos</h5>
                <div class="stats-grid">
                    <div class="stat-card completado"><div class="stat-icon"><i class="fas fa-check-circle"></i></div><div class="stat-number">{{ $estadisticas['completados'] }}</div><div class="stat-label">Completados</div></div>
                    <div class="stat-card proceso"><div class="stat-icon"><i class="fas fa-spinner"></i></div><div class="stat-number">{{ $estadisticas['en_proceso'] }}</div><div class="stat-label">En Proceso</div></div>
                    <div class="stat-card pendiente"><div class="stat-icon"><i class="fas fa-clock"></i></div><div class="stat-number">{{ $estadisticas['pendientes'] }}</div><div class="stat-label">Pendientes</div></div>
                    <div class="stat-card cancelado"><div class="stat-icon"><i class="fas fa-times-circle"></i></div><div class="stat-number">{{ $estadisticas['cancelados'] }}</div><div class="stat-label">Cancelados</div></div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card-custom">
            <div class="info-section">
                <div class="section-title"><i class="fas fa-info-circle"></i>Información del Cliente</div>
                <div class="info-row">
                    @if($cliente->Correo)
                    <div class="info-item">
                        <div class="info-label"><i class="far fa-envelope me-2"></i>Correo</div>
                        <div class="info-value">{{ $cliente->Correo }}</div>
                    </div>
                    @endif
                    @if($cliente->Telefono)
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-phone me-2"></i>Teléfono</div>
                        <div class="info-value">{{ $cliente->Telefono }}</div>
                    </div>
                    @endif
                </div>
                <div class="section-title mt-4"><i class="fas fa-map-marker-alt"></i>Dirección</div>
                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Dirección Completa</div>
                        <div class="info-value">{{ $cliente->calle }} @if($cliente->No_Ex) #{{ $cliente->No_Ex }} @endif @if($cliente->No_In), Int. {{ $cliente->No_In }} @endif</div>
                    </div>
                    <div class="info-item"><div class="info-label">Colonia</div><div class="info-value">{{ $cliente->Colonia ?? 'N/A' }}</div></div>
                    <div class="info-item"><div class="info-label">Municipio</div><div class="info-value">{{ $cliente->Municipio }}</div></div>
                    <div class="info-item"><div class="info-label">Estado</div><div class="info-value">{{ $cliente->Estado }}</div></div>
                    <div class="info-item"><div class="info-label">Código Postal</div><div class="info-value">{{ $cliente->Codigo_Postal }}</div></div>
                    <div class="info-item"><div class="info-label">País</div><div class="info-value">{{ $cliente->Pais }}</div></div>
                </div>
            </div>
        </div>

        <!-- Artículos Card -->
        <div class="card-custom">
            <div class="info-section">
                <div class="section-title">
                    <i class="fas fa-box"></i>Artículos Asociados
                    <span class="ms-auto badge bg-primary">{{ $articulos->count() }} artículos</span>
                </div>
                <div class="articulos-grid">
                    @foreach($articulos as $articulo)
                    <div class="articulo-card {{ $articulo->Es_Principal ? 'principal' : '' }}">
                        @if($articulo->Es_Principal)<span class="articulo-badge">Principal</span>@endif
                        <div class="articulo-nombre">{{ $articulo->Nombre }}</div>
                        <div class="articulo-detalles">
                            <i class="fas fa-copyright me-1"></i>{{ $articulo->Marca }}<br>
                            <i class="fas fa-tag me-1"></i>{{ $articulo->Modelo }}
                            @if($articulo->Descripcion)<br><small>{{ $articulo->Descripcion }}</small>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Levantamientos Card -->
        <div class="card-custom">
            <div class="info-section">
                <div class="section-title">
                    <i class="fas fa-history"></i>Historial de Levantamientos
                    <span class="ms-auto badge bg-secondary">{{ $estadisticas['total'] }} total</span>
                </div>

                <ul class="nav nav-tabs-custom mb-4" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#completados">Completados ({{ $estadisticas['completados'] }})</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#proceso">En Proceso ({{ $estadisticas['en_proceso'] }})</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pendientes">Pendientes ({{ $estadisticas['pendientes'] }})</a></li>
                    @if($estadisticas['cancelados'] > 0)
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#cancelados">Cancelados ({{ $estadisticas['cancelados'] }})</a></li>
                    @endif
                </ul>

                <div class="tab-content">
                    <!-- Completados -->
                    <div class="tab-pane fade show active" id="completados">
                        @forelse($levantamientosPorEstatus['Completado'] as $lev)
                        <div class="levantamiento-card completado">
                            <div class="levantamiento-header">
                                <div>
                                    <div class="levantamiento-folio"><i class="{{ $lev->Icono ?? 'fas fa-clipboard-list' }} me-2"></i>LEV-{{ str_pad($lev->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</div>
                                    @if($lev->TipoLevantamiento)<small class="text-muted">{{ $lev->TipoLevantamiento }}</small>@endif
                                </div>
                                <span class="levantamiento-estatus estatus-completado"><i class="fas fa-check-circle me-1"></i>{{ $lev->estatus }}</span>
                            </div>
                            <div class="levantamiento-info">
                                <div class="lev-info-item"><i class="far fa-calendar"></i><span>{{ \Carbon\Carbon::parse($lev->fecha_creacion)->format('d/m/Y') }}</span></div>
                                <div class="lev-info-item"><i class="fas fa-user"></i><span>{{ $lev->NombreUsuario }}</span></div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state"><i class="fas fa-clipboard-check"></i><h4>No hay levantamientos completados</h4></div>
                        @endforelse
                    </div>

                    <!-- En Proceso -->
                    <div class="tab-pane fade" id="proceso">
                        @forelse($levantamientosPorEstatus['En Proceso'] as $lev)
                        <div class="levantamiento-card proceso">
                            <div class="levantamiento-header">
                                <div>
                                    <div class="levantamiento-folio"><i class="{{ $lev->Icono ?? 'fas fa-clipboard-list' }} me-2"></i>LEV-{{ str_pad($lev->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</div>
                                    @if($lev->TipoLevantamiento)<small class="text-muted">{{ $lev->TipoLevantamiento }}</small>@endif
                                </div>
                                <span class="levantamiento-estatus estatus-proceso"><i class="fas fa-spinner me-1"></i>{{ $lev->estatus }}</span>
                            </div>
                            <div class="levantamiento-info">
                                <div class="lev-info-item"><i class="far fa-calendar"></i><span>{{ \Carbon\Carbon::parse($lev->fecha_creacion)->format('d/m/Y') }}</span></div>
                                <div class="lev-info-item"><i class="fas fa-user"></i><span>{{ $lev->NombreUsuario }}</span></div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state"><i class="fas fa-tasks"></i><h4>No hay levantamientos en proceso</h4></div>
                        @endforelse
                    </div>

                    <!-- Pendientes -->
                    <div class="tab-pane fade" id="pendientes">
                        @forelse($levantamientosPorEstatus['Pendiente'] as $lev)
                        <div class="levantamiento-card pendiente">
                            <div class="levantamiento-header">
                                <div>
                                    <div class="levantamiento-folio"><i class="{{ $lev->Icono ?? 'fas fa-clipboard-list' }} me-2"></i>LEV-{{ str_pad($lev->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</div>
                                    @if($lev->TipoLevantamiento)<small class="text-muted">{{ $lev->TipoLevantamiento }}</small>@endif
                                </div>
                                <span class="levantamiento-estatus estatus-pendiente"><i class="fas fa-clock me-1"></i>{{ $lev->estatus }}</span>
                            </div>
                            <div class="levantamiento-info">
                                <div class="lev-info-item"><i class="far fa-calendar"></i><span>{{ \Carbon\Carbon::parse($lev->fecha_creacion)->format('d/m/Y') }}</span></div>
                                <div class="lev-info-item"><i class="fas fa-user"></i><span>{{ $lev->NombreUsuario }}</span></div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state"><i class="fas fa-hourglass-half"></i><h4>No hay levantamientos pendientes</h4></div>
                        @endforelse
                    </div>

                    <!-- Cancelados -->
                    @if($estadisticas['cancelados'] > 0)
                    <div class="tab-pane fade" id="cancelados">
                        @forelse($levantamientosPorEstatus['Cancelado'] as $lev)
                        <div class="levantamiento-card cancelado">
                            <div class="levantamiento-header">
                                <div>
                                    <div class="levantamiento-folio"><i class="{{ $lev->Icono ?? 'fas fa-clipboard-list' }} me-2"></i>LEV-{{ str_pad($lev->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</div>
                                    @if($lev->TipoLevantamiento)<small class="text-muted">{{ $lev->TipoLevantamiento }}</small>@endif
                                </div>
                                <span class="levantamiento-estatus estatus-cancelado"><i class="fas fa-times-circle me-1"></i>{{ $lev->estatus }}</span>
                            </div>
                            <div class="levantamiento-info">
                                <div class="lev-info-item"><i class="far fa-calendar"></i><span>{{ \Carbon\Carbon::parse($lev->fecha_creacion)->format('d/m/Y') }}</span></div>
                                <div class="lev-info-item"><i class="fas fa-user"></i><span>{{ $lev->NombreUsuario }}</span></div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state"><i class="fas fa-ban"></i><h4>No hay levantamientos cancelados</h4></div>
                        @endforelse
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar'), mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleBtn'), mobileOverlay = document.getElementById('mobileOverlay');
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); mobileOverlay.classList.toggle('active'); }
            else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
        });
        mobileOverlay.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); mobileOverlay.classList.remove('active'); });
    </script>
</body>
</html>