<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Sistema de Levantamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --user-color: #1D67A8;
            --user-dark: #1D67A8;
        }

        body { background: #f5f7fa; overflow-x: hidden; }

        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--user-color) 0%, var(--user-dark) 100%);
            transition: all 0.3s ease; z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }
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
            color: rgba(255,255,255,0.8); text-decoration: none;
            transition: all 0.3s; position: relative;
        }
        .menu-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .menu-link.active { background: rgba(255,255,255,0.2); color: white; border-left: 4px solid white; }
        .menu-link.locked { opacity: 0.5; cursor: not-allowed; }
        .menu-link.locked:hover { background: transparent; }
        .lock-icon { position: absolute; right: 15px; font-size: 14px; color: rgba(255,255,255,0.6); }
        .menu-icon { width: 30px; text-align: center; font-size: 20px; }
        .menu-text { margin-left: 15px; white-space: nowrap; transition: opacity 0.3s; }
        .sidebar-footer {
            position: absolute; bottom: 0; width: 100%; padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .main-content { margin-left: var(--sidebar-width); transition: margin-left 0.3s ease; padding: 20px; }
        .main-content.expanded { margin-left: var(--sidebar-collapsed-width); }

        .top-bar {
            background: white; padding: 15px 20px; border-radius: 10px;
            margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex; justify-content: space-between; align-items: center;
        }
        .toggle-btn {
            background: var(--user-color); color: white; border: none;
            width: 40px; height: 40px; border-radius: 8px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: all 0.3s;
        }
        .toggle-btn:hover { background: var(--user-dark); }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--user-color), var(--user-dark));
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: bold;
        }
        .user-badge { background: var(--user-color); color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .user-badge.sin-permisos { background: #dc3545; }
        .user-badge.con-permisos { background: #28a745; }

        .welcome-card {
            background: linear-gradient(135deg, var(--user-color) 0%, var(--user-dark) 100%);
            color: white; border-radius: 15px; padding: 30px; margin-bottom: 30px;
        }
        .permissions-info { background: rgba(255,255,255,0.2); padding: 15px; border-radius: 10px; margin-top: 15px; }
        .permissions-info i { margin-right: 10px; }

        .stat-card {
            background: white; border-radius: 10px; padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.3s; height: 100%;
        }
        .stat-card:hover { transform: translateY(-5px); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
            .mobile-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; }
            .mobile-overlay.active { display: block; }
            .user-name { display: none; }
        }
        .bg-primary { background: var(--user-color) !important; }
        .btn-primary{
            background: #1D67A8;
        }
    </style>
</head>
<body>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- ═══════════════ SIDEBAR ═══════════════ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
            <h4>Sistema Levantamientos</h4>
        </div>

        <ul class="sidebar-menu">

            {{-- Dashboard --}}
            <li class="menu-item">
                <a href="{{ route('usuario.dashboard') }}" class="menu-link active">
                    <i class="fas fa-home menu-icon"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            {{-- Mis Levantamientos (siempre visible) --}}
            <li class="menu-item">
                <a href="{{ route('usuario.levantamientos.index') }}" class="menu-link">
                    <i class="fas fa-clipboard-list menu-icon"></i>
                    <span class="menu-text">Mis Levantamientos</span>
                </a>
            </li>

            {{-- Clientes (con permiso) --}}
            <li class="menu-item">
               <a href="{{ $tienePermisosEspeciales ? route('usuario.clientesU') : '#' }}"
                   class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}"
                   @if(!$tienePermisosEspeciales)
                       onclick="verificarPermiso(event, 'clientes'); return false;"
                   @endif>
                    <i class="fas fa-users menu-icon"></i>
                    <span class="menu-text">Clientes</span>
                    @if(!$tienePermisosEspeciales)
                        <i class="fas fa-lock lock-icon"></i>
                    @endif
                </a>
            </li>

           {{-- Productos (con permiso) --}}
<li class="menu-item">
    <a href="{{ $tienePermisosEspeciales ? route('usuario.productos.index') : '#' }}"
       class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}"
       @if(!$tienePermisosEspeciales)
           onclick="verificarPermiso(event, 'productos'); return false;"
       @endif>
        <i class="fas fa-box menu-icon"></i>
        <span class="menu-text">Productos</span>
        @if(!$tienePermisosEspeciales)
            <i class="fas fa-lock lock-icon"></i>
        @endif
    </a>
</li>

            {{-- Tipos de Levantamiento (con permiso) --}}
            <li class="menu-item">
                <a href="{{ $tienePermisosEspeciales ? route('usuario.tipos-levantamiento.index') : '#' }}"
                   class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}"
                   @if(!$tienePermisosEspeciales)
                       onclick="verificarPermiso(event, 'tipos_levantamiento'); return false;"
                   @endif>
                    <i class="fas fa-cogs menu-icon"></i>
                    <span class="menu-text">Tipos de Levantamiento</span>
                    @if(!$tienePermisosEspeciales)
                        <i class="fas fa-lock lock-icon"></i>
                    @endif
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
                <span class="user-name">{{ $usuario->Nombres }} {{ $usuario->ApellidosPat }}</span>
                <div class="user-avatar">{{ strtoupper(substr($usuario->Nombres, 0, 1)) }}</div>
                <span class="user-badge {{ $tienePermisosEspeciales ? 'con-permisos' : 'sin-permisos' }}">
                    {{ $usuario->Rol }}
                </span>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="welcome-card">
            <h1><i class="fas fa-hand-wave me-2"></i>¡Bienvenido, {{ $usuario->Nombres }}!</h1>
            <p class="mb-0">Rol: {{ $usuario->Rol }}</p>
            <small>Última actividad: {{ \Carbon\Carbon::parse($usuario->ultima_actividad)->format('d/m/Y H:i') }}</small>
            <div class="permissions-info mt-3">
                @if($tienePermisosEspeciales)
                    <i class="fas fa-check-circle"></i>
                    <strong>Permisos Especiales Activos</strong>
                    <p class="mb-0 mt-2 small">Puedes crear y gestionar clientes, productos y tipos de levantamiento.</p>
                @else
                    <i class="fas fa-lock"></i>
                    <strong>Permisos Limitados</strong>
                    <p class="mb-0 mt-2 small">No tienes permisos para gestionar clientes, productos o tipos de levantamiento. Contacta al administrador si necesitas acceso.</p>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Mis Levantamientos</h6>
                            <h3 class="mb-0">{{ $estadisticas['levantamientos']->total ?? 0 }}</h3>
                            <small class="text-info">
                                <i class="fas fa-clock"></i> {{ $estadisticas['levantamientos']->pendientes ?? 0 }} pendientes
                            </small>
                        </div>
                        <div class="text-primary"><i class="fas fa-clipboard-check fa-3x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Clientes</h6>
                            <h3 class="mb-0">{{ $estadisticas['total_clientes'] }}</h3>
                            @if(!$tienePermisosEspeciales)
                                <small class="text-danger"><i class="fas fa-lock"></i> Sin permisos de creación</small>
                            @else
                                <small class="text-success"><i class="fas fa-check"></i> Acceso completo</small>
                            @endif
                        </div>
                        <div class="{{ $tienePermisosEspeciales ? 'text-success' : 'text-muted' }}">
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">En Proceso</h6>
                            <h3 class="mb-0">{{ $estadisticas['levantamientos']->en_proceso ?? 0 }}</h3>
                            <small class="text-warning"><i class="fas fa-spinner"></i> Trabajando en ello</small>
                        </div>
                        <div class="text-warning"><i class="fas fa-tasks fa-3x"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--user-color), var(--user-dark)); color: white;">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-3">
                                <a href="{{ route('usuario.levantamientos.create') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Nuevo Levantamiento
                                </a>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                @if($tienePermisosEspeciales)
                                    <a href="{{ route('usuario.clientesU') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-user-plus me-2"></i>Nuevo Cliente
                                    </a>
                                @else
                                    <button class="btn btn-success w-100 disabled" onclick="verificarPermiso(event, 'clientes'); return false;">
                                        <i class="fas fa-lock me-2"></i>Nuevo Cliente
                                    </button>
                                @endif
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                @if($tienePermisosEspeciales)
                                    <a href="{{ route('usuario.productos.index') }}" class="btn btn-primary text-white w-100">
                                        <i class="fas fa-box me-2"></i>Ver Productos
                                    </a>
                                @else
                                    <button class="btn btn-info text-white w-100 disabled" onclick="verificarPermiso(event, 'productos'); return false;">
                                        <i class="fas fa-lock me-2"></i>Ver Productos
                                    </button>
                                @endif
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                @if($tienePermisosEspeciales)
                                    <a href="{{ route('usuario.tipos-levantamiento.index') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-cogs me-2"></i>Tipos de Levant.
                                    </a>
                                @else
                                    <button class="btn btn-secondary w-100 disabled" onclick="verificarPermiso(event, 'tipos_levantamiento'); return false;">
                                        <i class="fas fa-lock me-2"></i>Tipos de Levant.
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ── Sidebar ──
        const sidebar       = document.getElementById('sidebar');
        const mainContent   = document.getElementById('mainContent');
        const mobileOverlay = document.getElementById('mobileOverlay');

        document.getElementById('toggleBtn').addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                mobileOverlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        mobileOverlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('active');
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('mobile-open');
                mobileOverlay.classList.remove('active');
            }
        });

        // ── Permiso denegado ──
        function verificarPermiso(event, accion) {
            event.preventDefault();
            event.stopPropagation();

            Swal.fire({
                icon: 'warning',
                title: 'Acceso Restringido',
                html: `
                    <p>No tienes permisos para acceder a <strong>${getNombreAccion(accion)}</strong>.</p>
                    <p class="text-muted small">Contacta al administrador para solicitar acceso.</p>
                `,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0627bb'
            });
        }

        function getNombreAccion(accion) {
            const nombres = {
                'clientes':            'Clientes',
                'productos':           'Productos',
                'tipos_levantamiento': 'Tipos de Levantamiento'
            };
            return nombres[accion] || accion;
        }
    </script>
</body>
</html>