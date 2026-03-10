<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistema de Levantamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboardAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ActividadReciente.css') }}">
    <style>
        :root { --primary-color: #1D67A8; --sidebar-width: 280px; --sidebar-collapsed: 70px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: var(--sidebar-width);
            background: linear-gradient(135deg, #1D67A8 0%, #1D67A8 100%);
            transition: all 0.3s ease; z-index: 1000; box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        .sidebar-header { padding: 20px; text-align: center; color: white; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h4 { margin: 0; font-size: 18px; white-space: nowrap; overflow: hidden; }
        .sidebar.collapsed .sidebar-header h4, .sidebar.collapsed .menu-text { opacity: 0; width: 0; }
        .sidebar-menu { list-style: none; padding: 0; margin: 20px 0; }
        .menu-item { margin: 5px 0; }
        .menu-link { display: flex; align-items: center; padding: 15px 20px; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s; position: relative; }
        .menu-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .menu-link.active { background: rgba(255,255,255,0.2); color: white; border-left: 4px solid white; }
        .menu-icon { width: 30px; text-align: center; font-size: 20px; }
        .menu-text { margin-left: 15px; white-space: nowrap; transition: opacity 0.3s; }
        .sidebar-footer { position: absolute; bottom: 0; width: 100%; padding: 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        .main-content { margin-left: var(--sidebar-width); transition: margin-left 0.3s ease; padding: 20px; }
        .main-content.expanded { margin-left: var(--sidebar-collapsed-width); }
        .top-bar { background: white; padding: 15px 25px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .toggle-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #1D67A8; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .sidebar.mobile-open { transform: translateX(0); } .main-content { margin-left: 0; } }
        .btn-primary { background-color: #1D67A8; border-color: #1D67A8; }
        .btn-primary:hover { background-color: #155180; border-color: #155180; }
        .bg-primary { background-color: #1D67A8 !important; }
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
            <li class="menu-item">
                <a href="{{ route('admin.dashboard') }}" class="menu-link active">
                    <i class="fas fa-home menu-icon"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.usuarios') }}" class="menu-link">
                    <i class="fas fa-users menu-icon"></i>
                    <span class="menu-text">Usuarios</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.levantamientos.index') }}" class="menu-link">
                    <i class="fas fa-clipboard-list menu-icon"></i>
                    <span class="menu-text">Levantamientos</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.clientes.index') }}" class="menu-link">
                    <i class="fas fa-building menu-icon"></i>
                    <span class="menu-text">Clientes</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.productos.index') }}" class="menu-link">
                    <i class="fas fa-box menu-icon"></i>
                    <span class="menu-text">Productos</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.tipos-levantamiento.index') }}" class="menu-link">
                    <i class="fa-solid fa-gear menu-icon"></i>
                    <span class="menu-text">Tipos de Levantamientos</span>
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

        <!-- Welcome Card -->
        <div class="welcome-card">
            <h1><i class="fas fa-user-shield me-2"></i>Panel de Administración</h1>
            <p class="mb-0">¡Bienvenido, {{ Auth::user()->Nombres }}!</p>
            <small>Rol: {{ Auth::user()->Rol }} | Última actividad: {{ Auth::user()->ultima_actividad->format('d/m/Y H:i') }}</small>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Usuarios</h6>
                            <h3 class="mb-0">{{ \DB::table('usuarios')->count() }}</h3>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> {{ \DB::table('usuarios')->where('Estatus', 'Activo')->count() }} activos</small>
                        </div>
                        <div class="text-danger"><i class="fas fa-users fa-3x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Levantamientos</h6>
                            <h3 class="mb-0">{{ \DB::table('levantamientos')->count() }}</h3>
                            <small class="text-info"><i class="fas fa-clock"></i> {{ \DB::table('levantamientos')->where('estatus', 'Pendiente')->count() }} pendientes</small>
                        </div>
                        <div class="text-primary"><i class="fas fa-clipboard-check fa-3x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Clientes</h6>
                            <h3 class="mb-0">{{ \DB::table('clientes')->count() }}</h3>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> {{ \DB::table('clientes')->where('Estatus', 'Activo')->count() }} activos</small>
                        </div>
                        <div class="text-success"><i class="fas fa-building fa-3x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Productos</h6>
                            <h3 class="mb-0">{{ \DB::table('articulos')->count() }}</h3>
                            <small class="text-muted"><i class="fas fa-box"></i> En catálogo</small>
                        </div>
                        <div class="text-warning"><i class="fas fa-box fa-3x"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Acciones Rápidas -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <a href="{{ route('admin.usuarios') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-user-plus me-2"></i>Crear Usuario
                                </a>
                            </div>
                            <div class="col-12 col-md-6">
                                <a href="{{ route('admin.levantamientos.create') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Nuevo Levantamiento
                                </a>
                            </div>
                            <div class="col-12 col-md-6">
                                <a href="{{ route('admin.clientes.index') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-building me-2"></i>Nuevo Cliente
                                </a>
                            </div>
                            <div class="col-12 col-md-6">
                                <a href="{{ route('admin.productos.create') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-box me-2"></i>Nuevo Producto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════
                 ACTIVIDAD RECIENTE — con filtros día/mes
            ═══════════════════════════════════════════════════════ -->
            <div class="col-12 col-lg-6">
                @php
                    /* Forzar español en Carbon */
                    \Carbon\Carbon::setLocale('es');
                    /* Traemos las últimas 30 actividades para la tarjeta */
                    $actividades = \DB::table('actividades')
                        ->leftJoin('usuarios', 'actividades.Id_Usuario', '=', 'usuarios.id_usuarios')
                        ->select(
                            'actividades.*',
                            'usuarios.Nombres',
                            'usuarios.ApellidosPat',
                            'usuarios.ApellidoMat'
                        )
                        ->orderBy('actividades.Fecha', 'desc')
                        ->limit(30)
                        ->get();

                    /* Agrupamos por fecha (Y-m-d) */
                    $porDia = $actividades->groupBy(function($a) {
                        return \Carbon\Carbon::parse($a->Fecha)->format('Y-m-d');
                    });

                    /* Agrupamos por mes */
                    $porMes = $actividades->groupBy(function($a) {
                        return \Carbon\Carbon::parse($a->Fecha)->format('Y-m');
                    });

                    /* Conteo de hoy */
                    $hoy = today()->toDateString();
                    $countHoy = \DB::table('actividades')->whereDate('Fecha', $hoy)->count();
                @endphp

                <div class="activity-card">
                    <!-- Header -->
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Actividad Reciente</h5>
                        <span class="badge-new">{{ $countHoy }} hoy</span>
                    </div>

                    <!-- Filtros -->
                    <div class="activity-filters">
                        <button type="button" class="filter-btn active" data-filter="dia" onclick="switchView('dia')">Por Día</button>
                        <button type="button" class="filter-btn" data-filter="mes" onclick="switchView('mes')">Por Mes</button>
                        <button type="button" class="filter-btn" data-filter="todo" onclick="switchView('todo')">Todo</button>
                    </div>

                    <!-- ── Vista POR DÍA ── -->
                    <div class="activity-scroll" id="view-dia" style="display:block">
                        @forelse($porDia as $fecha => $items)
                            @php
                                $carbon = \Carbon\Carbon::parse($fecha);
                                $esHoy     = $carbon->isToday();
                                $esAyer    = $carbon->isYesterday();
                                $label     = $esHoy ? 'Hoy' : ($esAyer ? 'Ayer' : $carbon->translatedFormat('d \d\e F'));
                            @endphp

                            <!-- Separador de día -->
                            <div class="day-separator">
                                <span class="day-separator-label">{{ $label }}</span>
                                <div class="day-separator-line"></div>
                                <span class="day-separator-count">{{ $items->count() }}</span>
                            </div>

                            @foreach($items as $actividad)
                                <div class="activity-item">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="activity-icon {{ $actividad->Tipo }}">
                                            @switch($actividad->Tipo)
                                                @case('usuario')      <i class="fas fa-user"></i> @break
                                                @case('levantamiento')<i class="fas fa-clipboard-list"></i> @break
                                                @case('cliente')      <i class="fas fa-building"></i> @break
                                                @case('producto')     <i class="fas fa-box"></i> @break
                                                @case('cotizacion')   <i class="fas fa-file-invoice-dollar"></i> @break
                                                @default              <i class="fas fa-circle"></i>
                                            @endswitch
                                        </div>
                                        <div class="flex-grow-1" style="min-width:0">
                                            <div class="activity-user">
                                                {{ $actividad->Nombres }} {{ $actividad->ApellidosPat }}
                                            </div>
                                            <div class="activity-description">{{ $actividad->Descripcion }}</div>
                                            <div class="activity-time">
                                                <i class="fas fa-clock"></i>
                                                {{ \Carbon\Carbon::parse($actividad->Fecha)->format('H:i') }}
                                                &nbsp;·&nbsp;
                                                {{ \Carbon\Carbon::parse($actividad->Fecha)->diffForHumans() }}
                                            </div>
                                        </div>
                                        @switch($actividad->Accion)
                                            @case('registro') @case('creado') @case('agregado')
                                                <span class="badge bg-success">Nuevo</span> @break
                                            @case('actualizado')
                                                <span class="badge bg-info">Actualizado</span> @break
                                            @case('eliminado') @case('desactivado')
                                                <span class="badge bg-danger">Eliminado</span> @break
                                            @case('reactivado')
                                                <span class="badge bg-warning">Reactivado</span> @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($actividad->Accion) }}</span>
                                        @endswitch
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No hay actividad reciente</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- ── Vista POR MES ── -->
                    <div class="activity-scroll" id="view-mes" style="display:none">
                        @forelse($porMes as $mesKey => $items)
                            @php
                                $labelMes = \Carbon\Carbon::parse($mesKey . '-01')->translatedFormat('F Y');
                            @endphp
                            <div class="month-separator">
                                <span class="month-separator-label">{{ $labelMes }}</span>
                                <div class="month-separator-line"></div>
                                <span class="day-separator-count">{{ $items->count() }} actividades</span>
                            </div>

                            @foreach($items->take(5) as $actividad)
                                <div class="activity-item">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="activity-icon {{ $actividad->Tipo }}">
                                            @switch($actividad->Tipo)
                                                @case('usuario')      <i class="fas fa-user"></i> @break
                                                @case('levantamiento')<i class="fas fa-clipboard-list"></i> @break
                                                @case('cliente')      <i class="fas fa-building"></i> @break
                                                @case('producto')     <i class="fas fa-box"></i> @break
                                                @case('cotizacion')   <i class="fas fa-file-invoice-dollar"></i> @break
                                                @default              <i class="fas fa-circle"></i>
                                            @endswitch
                                        </div>
                                        <div class="flex-grow-1" style="min-width:0">
                                            <div class="activity-user">
                                                {{ $actividad->Nombres }} {{ $actividad->ApellidosPat }}
                                            </div>
                                            <div class="activity-description">{{ $actividad->Descripcion }}</div>
                                            <div class="activity-time">
                                                <i class="fas fa-calendar-alt"></i>
                                                {{ \Carbon\Carbon::parse($actividad->Fecha)->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                        @switch($actividad->Accion)
                                            @case('registro') @case('creado') @case('agregado')
                                                <span class="badge bg-success">Nuevo</span> @break
                                            @case('actualizado')
                                                <span class="badge bg-info">Actualizado</span> @break
                                            @case('eliminado') @case('desactivado')
                                                <span class="badge bg-danger">Eliminado</span> @break
                                            @case('reactivado')
                                                <span class="badge bg-warning">Reactivado</span> @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($actividad->Accion) }}</span>
                                        @endswitch
                                    </div>
                                </div>
                            @endforeach

                            @if($items->count() > 5)
                                <p class="text-center" style="font-size:12px;color:#94a3b8;padding:6px 0">
                                    +{{ $items->count() - 5 }} más en este mes
                                </p>
                            @endif
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No hay actividad</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- ── Vista TODO ── -->
                    <div class="activity-scroll" id="view-todo" style="display:none">
                        @forelse($actividades as $actividad)
                            <div class="activity-item">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="activity-icon {{ $actividad->Tipo }}">
                                        @switch($actividad->Tipo)
                                            @case('usuario')      <i class="fas fa-user"></i> @break
                                            @case('levantamiento')<i class="fas fa-clipboard-list"></i> @break
                                            @case('cliente')      <i class="fas fa-building"></i> @break
                                            @case('producto')     <i class="fas fa-box"></i> @break
                                            @case('cotizacion')   <i class="fas fa-file-invoice-dollar"></i> @break
                                            @default              <i class="fas fa-circle"></i>
                                        @endswitch
                                    </div>
                                    <div class="flex-grow-1" style="min-width:0">
                                        <div class="activity-user">
                                            {{ $actividad->Nombres }} {{ $actividad->ApellidosPat }}
                                        </div>
                                        <div class="activity-description">{{ $actividad->Descripcion }}</div>
                                        <div class="activity-time">
                                            <i class="fas fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($actividad->Fecha)->diffForHumans() }}
                                        </div>
                                    </div>
                                    @switch($actividad->Accion)
                                        @case('registro') @case('creado') @case('agregado')
                                            <span class="badge bg-success">Nuevo</span> @break
                                        @case('actualizado')
                                            <span class="badge bg-info">Actualizado</span> @break
                                        @case('eliminado') @case('desactivado')
                                            <span class="badge bg-danger">Eliminado</span> @break
                                        @case('reactivado')
                                            <span class="badge bg-warning">Reactivado</span> @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($actividad->Accion) }}</span>
                                    @endswitch
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No hay actividad reciente</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Footer -->
                    <div class="card-footer text-center bg-light">
                        <a href="#" class="btn-ver-todas" data-bs-toggle="modal" data-bs-target="#modalActividades">
                            Ver todas las actividades
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div><!-- /col actividad -->
        </div><!-- /row -->
    </main>

    <!-- ═══════════════════════════════════════════════════════════════════
         MODAL — TODAS LAS ACTIVIDADES
    ════════════════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="modalActividades" tabindex="-1" aria-labelledby="modalActividadesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="modalActividadesLabel">
                        <i class="fas fa-history me-2"></i>
                        Historial Completo de Actividades
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Controles -->
                <div class="modal-controls">
                    <!-- Búsqueda -->
                    <div class="modal-search">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="modalSearchInput" placeholder="Buscar por usuario, descripción…">
                    </div>

                    <!-- Filtro por tipo -->
                    <div class="modal-filter-group" id="modalTipoFilter">
                        <button type="button" class="modal-filter-btn active" data-tipo="todos">Todos</button>
                        <button type="button" class="modal-filter-btn" data-tipo="usuario">
                            <i class="fas fa-user me-1"></i>Usuarios
                        </button>
                        <button type="button" class="modal-filter-btn" data-tipo="levantamiento">
                            <i class="fas fa-clipboard-list me-1"></i>Lev.
                        </button>
                        <button type="button" class="modal-filter-btn" data-tipo="cliente">
                            <i class="fas fa-building me-1"></i>Clientes
                        </button>
                        <button type="button" class="modal-filter-btn" data-tipo="producto">
                            <i class="fas fa-box me-1"></i>Productos
                        </button>
                    </div>

                    <!-- Filtro por período -->
                    <select class="modal-period-select" id="modalPeriodSelect">
                        <option value="todos">Todos los períodos</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Esta semana</option>
                        <option value="mes">Este mes</option>
                        <option value="mes_anterior">Mes anterior</option>
                    </select>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body" id="modalBody">
                    @php
                        /* Cargamos TODAS las actividades para el modal */
                        $todasActividades = \DB::table('actividades')
                            ->leftJoin('usuarios', 'actividades.Id_Usuario', '=', 'usuarios.id_usuarios')
                            ->select(
                                'actividades.*',
                                'usuarios.Nombres',
                                'usuarios.ApellidosPat',
                                'usuarios.ApellidoMat'
                            )
                            ->orderBy('actividades.Fecha', 'desc')
                            ->get();

                        /* Agrupamos por día para el modal */
                        $porDiaModal = $todasActividades->groupBy(function($a) {
                            return \Carbon\Carbon::parse($a->Fecha)->format('Y-m-d');
                        });
                    @endphp

                    @forelse($porDiaModal as $fecha => $items)
                        @php
                            $carbon    = \Carbon\Carbon::parse($fecha);
                            $esHoy     = $carbon->isToday();
                            $esAyer    = $carbon->isYesterday();
                            $labelDia  = $esHoy ? 'Hoy' : ($esAyer ? 'Ayer' : $carbon->translatedFormat('l, d \d\e F \d\e Y'));
                            $labelMes  = $carbon->translatedFormat('F Y');
                        @endphp

                        <div class="timeline-group"
                             data-fecha="{{ $fecha }}"
                             data-mes="{{ $carbon->format('Y-m') }}">

                            <!-- Cabecera de día -->
                            <div class="timeline-group-header">
                                <span class="timeline-date-badge">{{ $labelDia }}</span>
                                <div class="timeline-line"></div>
                                <span class="timeline-count">{{ $items->count() }} actividades</span>
                            </div>

                            @foreach($items as $actividad)
                                @php
                                    $nombreCompleto = trim(($actividad->Nombres ?? '') . ' ' . ($actividad->ApellidosPat ?? '') . ' ' . ($actividad->ApellidoMat ?? ''));
                                    $hora = \Carbon\Carbon::parse($actividad->Fecha)->format('H:i');
                                    $fechaFull = \Carbon\Carbon::parse($actividad->Fecha)->translatedFormat('d/m/Y');
                                    $hace = \Carbon\Carbon::parse($actividad->Fecha)->diffForHumans();
                                @endphp

                                <div class="modal-activity-row"
                                     data-tipo="{{ $actividad->Tipo }}"
                                     data-texto="{{ strtolower($nombreCompleto . ' ' . $actividad->Descripcion) }}"
                                     data-fecha="{{ \Carbon\Carbon::parse($actividad->Fecha)->format('Y-m-d') }}"
                                     data-mes="{{ \Carbon\Carbon::parse($actividad->Fecha)->format('Y-m') }}">

                                    <!-- Ícono -->
                                    <div class="modal-act-icon activity-icon {{ $actividad->Tipo }}">
                                        @switch($actividad->Tipo)
                                            @case('usuario')      <i class="fas fa-user"></i> @break
                                            @case('levantamiento')<i class="fas fa-clipboard-list"></i> @break
                                            @case('cliente')      <i class="fas fa-building"></i> @break
                                            @case('producto')     <i class="fas fa-box"></i> @break
                                            @case('cotizacion')   <i class="fas fa-file-invoice-dollar"></i> @break
                                            @default              <i class="fas fa-circle"></i>
                                        @endswitch
                                    </div>

                                    <!-- Contenido -->
                                    <div class="modal-act-content">
                                        <div class="modal-act-user">{{ $nombreCompleto ?: 'Sistema' }}</div>
                                        <div class="modal-act-desc" title="{{ $actividad->Descripcion }}">
                                            {{ $actividad->Descripcion }}
                                        </div>
                                        <div class="modal-act-meta">
                                            <span class="modal-act-time">
                                                <i class="fas fa-clock"></i> {{ $hora }}
                                            </span>
                                            <span class="modal-act-date">
                                                <i class="fas fa-calendar"></i> {{ $fechaFull }}
                                            </span>
                                            <span class="modal-act-time">· {{ $hace }}</span>
                                        </div>
                                    </div>

                                    <!-- Badge -->
                                    <div class="flex-shrink-0">
                                        @switch($actividad->Accion)
                                            @case('registro') @case('creado') @case('agregado')
                                                <span class="badge bg-success">Nuevo</span> @break
                                            @case('actualizado')
                                                <span class="badge bg-info">Actualizado</span> @break
                                            @case('eliminado') @case('desactivado')
                                                <span class="badge bg-danger">Eliminado</span> @break
                                            @case('reactivado')
                                                <span class="badge bg-warning text-dark">Reactivado</span> @break
                                            @case('login')
                                                <span class="badge bg-primary">Login</span> @break
                                            @case('logout')
                                                <span class="badge bg-secondary">Logout</span> @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($actividad->Accion) }}</span>
                                        @endswitch
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="no-results">
                            <i class="fas fa-inbox d-block"></i>
                            <p>No hay actividades registradas</p>
                        </div>
                    @endforelse

                    <!-- Mensaje sin resultados al filtrar -->
                    <div class="no-results" id="noResultsMsg" style="display:none">
                        <i class="fas fa-search d-block"></i>
                        <p>No se encontraron actividades con ese filtro</p>
                    </div>
                </div>

                <!-- Footer del modal -->
                <div class="modal-footer" style="padding:12px 28px;border-top:1px solid #e2e8f0;justify-content:space-between">
                    <small class="text-muted" id="modalResultCount">
                        {{ $todasActividades->count() }} actividades en total
                    </small>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {

    /* ══ SIDEBAR ══════════════════════════════════════════════ */
    var sidebar       = document.getElementById('sidebar');
    var mainContent   = document.getElementById('mainContent');
    var toggleBtn     = document.getElementById('toggleBtn');
    var mobileOverlay = document.getElementById('mobileOverlay');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                if (mobileOverlay) mobileOverlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });
    }
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('active');
        });
    }

    /* ══ TARJETA — botones Por Día / Por Mes / Todo ═══════════ */
    window.switchView = function (filtro) {
        document.getElementById('view-dia').style.display  = (filtro === 'dia')  ? 'block' : 'none';
        document.getElementById('view-mes').style.display  = (filtro === 'mes')  ? 'block' : 'none';
        document.getElementById('view-todo').style.display = (filtro === 'todo') ? 'block' : 'none';

        document.querySelectorAll('.filter-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-filter') === filtro);
        });
    };

    /* ══ MODAL — función principal de filtrado ════════════════ */
    function applyModalFilters() {
        var activeBtn  = document.querySelector('#modalTipoFilter .modal-filter-btn.active');
        var tipo       = activeBtn ? activeBtn.getAttribute('data-tipo') : 'todos';
        var periodo    = document.getElementById('modalPeriodSelect').value;
        var busqueda   = document.getElementById('modalSearchInput').value.toLowerCase().trim();

        /* Fechas como strings YYYY-MM-DD (sin zona horaria) */
        var hoy    = new Date();
        var pad    = function (n) { return String(n).padStart(2, '0'); };
        var hoyStr = hoy.getFullYear() + '-' + pad(hoy.getMonth() + 1) + '-' + pad(hoy.getDate());
        var mesStr = hoy.getFullYear() + '-' + pad(hoy.getMonth() + 1);

        var hace7    = new Date(hoy);
        hace7.setDate(hoy.getDate() - 6);
        var hace7Str = hace7.getFullYear() + '-' + pad(hace7.getMonth() + 1) + '-' + pad(hace7.getDate());

        var mesAntNum  = hoy.getMonth() === 0 ? 12 : hoy.getMonth();
        var mesAntYear = hoy.getMonth() === 0 ? hoy.getFullYear() - 1 : hoy.getFullYear();
        var mesAntStr  = mesAntYear + '-' + pad(mesAntNum);

        var totalVisibles = 0;

        document.querySelectorAll('.timeline-group').forEach(function (grupo) {
            var grupoVisible = false;

            grupo.querySelectorAll('.modal-activity-row').forEach(function (row) {
                var rowTipo   = row.getAttribute('data-tipo')  || '';
                var rowTexto  = row.getAttribute('data-texto') || '';
                var rowFecha  = row.getAttribute('data-fecha') || '';
                var rowMes    = row.getAttribute('data-mes')   || '';

                var pasaTipo = (tipo === 'todos' || rowTipo === tipo);

                var pasaPeriodo = true;
                if (periodo === 'hoy')          { pasaPeriodo = (rowFecha === hoyStr); }
                else if (periodo === 'semana')  { pasaPeriodo = (rowFecha >= hace7Str && rowFecha <= hoyStr); }
                else if (periodo === 'mes')     { pasaPeriodo = (rowMes === mesStr); }
                else if (periodo === 'mes_anterior') { pasaPeriodo = (rowMes === mesAntStr); }

                var pasaBusqueda = (!busqueda || rowTexto.indexOf(busqueda) !== -1);

                var visible = pasaTipo && pasaPeriodo && pasaBusqueda;
                row.style.display = visible ? '' : 'none';
                if (visible) { totalVisibles++; grupoVisible = true; }
            });

            grupo.style.display = grupoVisible ? '' : 'none';
        });

        var noRes = document.getElementById('noResultsMsg');
        if (noRes) noRes.style.display = (totalVisibles === 0) ? '' : 'none';

        var counter = document.getElementById('modalResultCount');
        if (counter) {
            counter.textContent = totalVisibles === 0
                ? 'Sin resultados'
                : totalVisibles + ' actividad' + (totalVisibles !== 1 ? 'es' : '') + ' encontrada' + (totalVisibles !== 1 ? 's' : '');
        }
    }

    /* Botones de tipo en el modal */
    document.querySelectorAll('#modalTipoFilter .modal-filter-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#modalTipoFilter .modal-filter-btn').forEach(function (b) {
                b.classList.remove('active');
            });
            this.classList.add('active');
            applyModalFilters();
        });
    });

    /* Selector de período */
    var periodSelect = document.getElementById('modalPeriodSelect');
    if (periodSelect) periodSelect.addEventListener('change', applyModalFilters);

    /* Buscador */
    var searchInput = document.getElementById('modalSearchInput');
    if (searchInput) searchInput.addEventListener('input', applyModalFilters);

    /* Limpiar al cerrar el modal */
    var modalEl = document.getElementById('modalActividades');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            if (searchInput) searchInput.value = '';
            if (periodSelect) periodSelect.value = 'todos';
            document.querySelectorAll('#modalTipoFilter .modal-filter-btn').forEach(function (b, i) {
                b.classList.toggle('active', i === 0);
            });
            applyModalFilters();
        });
    }

});
</script>
</body>
</html>