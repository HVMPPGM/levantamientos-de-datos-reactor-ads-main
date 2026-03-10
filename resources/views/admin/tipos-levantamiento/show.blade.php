<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ver Tipo - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --admin-color: #1D67A8;
            --admin-dark: #1D67A8;
        }

        body {
            background: #f5f7fa;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #1D67A8 0%, #1D67A8 100%);
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        .sidebar-header { padding: 20px; text-align: center; color: white; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h4 { margin: 0; font-size: 18px; white-space: nowrap; overflow: hidden; }
        .sidebar.collapsed .sidebar-header h4, .sidebar.collapsed .menu-text { opacity: 0; width: 0; }
        .sidebar-menu { list-style: none; padding: 0; margin: 20px 0; }
        .menu-item { margin: 5px 0; }
        .menu-link { display: flex; align-items: center; padding: 15px 20px; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s; }
        .menu-link:hover, .menu-link.active { background: rgba(255,255,255,0.1); color: white; }
        .menu-link.active { border-left: 4px solid white; }
        .menu-icon { width: 30px; text-align: center; font-size: 20px; }
        .menu-text { margin-left: 15px; white-space: nowrap; }
        .sidebar-footer { position: absolute; bottom: 0; width: 100%; padding: 20px; border-top: 1px solid rgba(255,255,255,0.1); }

        /* Main Content */
        .main-content { margin-left: var(--sidebar-width); transition: margin-left 0.3s ease; padding: 20px; }
        .main-content.expanded { margin-left: var(--sidebar-collapsed-width); }

        /* Top Bar */
        .top-bar { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .toggle-btn { background: var(--admin-color); color: white; border: none; width: 40px; height: 40px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
        .toggle-btn:hover { background: var(--admin-dark); }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--admin-color), var(--admin-dark)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        .admin-badge { background: var(--admin-color); color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }

        /* Header Hero */
        .hero-header {
            background: linear-gradient(135deg, #1754d8 0%, #2174f0 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .tipo-icon-hero {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            backdrop-filter: blur(10px);
        }

        /* Info Cards */
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .info-card-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .info-card-header h4 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .info-row {
            padding: 12px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #5a5c69;
            display: inline-block;
            min-width: 150px;
        }

        .info-value {
            color: #858796;
        }

        /* Campo Items */
        .campo-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #2445d4;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .campo-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .campo-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .campo-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .campo-meta-item {
            background: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            color: #5a5c69;
            border: 1px solid #dee2e6;
        }

        .badge-custom {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-requerido {
            background: #fff3cd;
            color: #997404;
            border: 1px solid #ffc107;
        }

        .badge-opcional {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #17a2b8;
        }

        .badge-inactivo {
            background: #f8d7da;
            color: #2a55e4;
            border: 1px solid #356adc;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .stat-icon.primary { background: linear-gradient(135deg, #667eea 0%, #3751e9 100%); color: white; }
        .stat-icon.success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
        .stat-icon.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #858796;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            border: 2px dashed #dee2e6;
        }

        .empty-state i {
            font-size: 64px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
            .mobile-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; }
            .mobile-overlay.active { display: block; }
            .user-name { display: none; }
        }
    </style>
</head>
<body>
    <!-- Mobile Overlay -->
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
            <li class="menu-item">
                <a href="{{ route('admin.levantamientos.index') }}" class="menu-link">
                    <i class="fas fa-clipboard-list menu-icon"></i>
                    <span class="menu-text">Levantamientos</span>
                </a>
            </li>
            <li class="menu-item"><a href="{{ route('admin.clientes.index') }}" class="menu-link"><i class="fas fa-building menu-icon"></i><span class="menu-text">Clientes</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.productos.index') }}" class="menu-link"><i class="fas fa-box menu-icon"></i><span class="menu-text">Productos</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.tipos-levantamiento.index') }}" class="menu-link active"><i class="fa-solid fa-gear menu-icon"></i><span class="menu-text">Tipos de Levantamientos</span></a></li>
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
        <!-- Top Bar -->
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-info">
                <span class="user-name">{{ $usuario->Nombres }} {{ $usuario->ApellidosPat }}</span>
                <div class="user-avatar">{{ strtoupper(substr($usuario->Nombres, 0, 1)) }}</div>
                <span class="admin-badge">ADMIN</span>
            </div>
        </div>

        <div class="container-fluid px-4">
            <!-- Breadcrumb -->
           

            <!-- Hero Header -->
            <div class="hero-header">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="tipo-icon-hero">
                            <i class="fas {{ $tipo->Icono }}"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h1 class="mb-2">{{ $tipo->Nombre }}</h1>
                        <p class="mb-3 opacity-75">{{ $tipo->Descripcion ?? 'Sin descripción' }}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-{{ $tipo->Activo ? 'check-circle' : 'times-circle' }} me-1"></i>
                                {{ $tipo->Activo ? 'Activo' : 'Inactivo' }}
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-clipboard-list me-1"></i>
                                {{ $levantamientosCount }} Levantamiento(s)
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-calendar me-1"></i>
                                Creado: {{ \Carbon\Carbon::parse($tipo->Fecha_Creacion)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="action-buttons">
                            <a href="{{ route('admin.tipos-levantamiento.edit', $tipo->Id_Tipo_Levantamiento) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a>
                            <a href="{{ route('admin.tipos-levantamiento.campos', $tipo->Id_Tipo_Levantamiento) }}" class="btn btn-light">
                                <i class="fas fa-list me-2"></i>Gestionar Campos
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-icon primary">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="stat-value">{{ $campos->count() }}</div>
                    <div class="stat-label">Total Campos</div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">{{ $campos->where('Activo', 1)->count() }}</div>
                    <div class="stat-label">Campos Activos</div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon warning">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="stat-value">{{ $levantamientosCount }}</div>
                    <div class="stat-label">Levantamientos</div>
                </div>
            </div>

            <!-- Información General -->
            <div class="info-card">
                <div class="info-card-header">
                    <h4>
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Información General
                    </h4>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">ID del Tipo:</span>
                            <span class="info-value">#{{ $tipo->Id_Tipo_Levantamiento }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Nombre:</span>
                            <span class="info-value">{{ $tipo->Nombre }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Icono:</span>
                            <span class="info-value">
                                <i class="fas {{ $tipo->Icono }} fa-2x text-primary"></i>
                                <code class="ms-2">{{ $tipo->Icono }}</code>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Estado:</span>
                            <span class="info-value">
                                <span class="badge {{ $tipo->Activo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $tipo->Activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Fecha de Creación:</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($tipo->Fecha_Creacion)->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Levantamientos:</span>
                            <span class="info-value">{{ $levantamientosCount }} asociado(s)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campos del Formulario -->
            <div class="info-card">
                <div class="info-card-header d-flex justify-content-between align-items-center">
                    <h4>
                        <i class="fas fa-tasks me-2 text-success"></i>
                        Campos del Formulario
                        <span class="badge bg-primary ms-2">{{ $campos->count() }}</span>
                    </h4>
                    <a href="{{ route('admin.tipos-levantamiento.campos', $tipo->Id_Tipo_Levantamiento) }}" class="btn btn-sm btn-danger">
                        <i class="fas fa-plus me-2"></i>Gestionar Campos
                    </a>
                </div>

                @if($campos->count() > 0)
                    @foreach($campos as $campo)
                        <div class="campo-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="campo-title">
                                        {{ $campo->Etiqueta }}
                                        @if($campo->Es_Requerido)
                                            <span class="badge-custom badge-requerido">Requerido</span>
                                        @else
                                            <span class="badge-custom badge-opcional">Opcional</span>
                                        @endif
                                        @if(!$campo->Activo)
                                            <span class="badge-custom badge-inactivo">Inactivo</span>
                                        @endif
                                    </div>
                                    
                                    <div class="campo-meta">
                                        <span class="campo-meta-item">
                                            <i class="fas fa-code me-1"></i>
                                            <strong>Campo:</strong> {{ $campo->Nombre_Campo }}
                                        </span>
                                        <span class="campo-meta-item">
                                            <i class="fas fa-keyboard me-1"></i>
                                            <strong>Tipo:</strong> {{ ucfirst($campo->Tipo_Input) }}
                                        </span>
                                        <span class="campo-meta-item">
                                            <i class="fas fa-sort-numeric-up me-1"></i>
                                            <strong>Orden:</strong> {{ $campo->Orden }}
                                        </span>
                                    </div>

                                    @if($campo->Placeholder)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-quote-left me-1"></i>
                                                <em>{{ $campo->Placeholder }}</em>
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5 class="text-muted mb-2">No hay campos configurados</h5>
                        <p class="text-muted mb-3">Este tipo de levantamiento aún no tiene campos en el formulario</p>
                        <a href="{{ route('admin.tipos-levantamiento.campos', $tipo->Id_Tipo_Levantamiento) }}" class="btn btn-danger">
                            <i class="fas fa-plus me-2"></i>Agregar Primer Campo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleBtn');
        const mobileOverlay = document.getElementById('mobileOverlay');

        toggleBtn.addEventListener('click', () => {
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
    </script>
</body>
</html>