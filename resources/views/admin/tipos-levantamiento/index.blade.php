<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tipos de Levantamiento - Admin</title>
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

        /* Sidebar Styles */
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

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h4 {
            margin: 0;
            font-size: 18px;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-header h4,
        .sidebar.collapsed .menu-text {
            opacity: 0;
            width: 0;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .menu-item {
            margin: 5px 0;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }

        .menu-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .menu-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            border-left: 4px solid white;
        }

        .menu-icon {
            width: 30px;
            text-align: center;
            font-size: 20px;
        }

        .menu-text {
            margin-left: 15px;
            white-space: nowrap;
            transition: opacity 0.3s;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            padding: 20px;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Bar */
        .top-bar {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toggle-btn {
            background: var(--admin-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .toggle-btn:hover {
            background: var(--admin-dark);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--admin-color), var(--admin-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .admin-badge {
            background: var(--admin-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        /* Cards */
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-box {
            border: 1px solid #e3e6f0;
        }

        .stat-value {
            font-weight: 600;
            color: #5a5c69;
        }

        .row.g-4 {
            row-gap: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            .mobile-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }

            .mobile-overlay.active {
                display: block;
            }

            .user-name {
                display: none;
            }
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
            <li class="menu-item">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
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
                <a href="{{ route('admin.tipos-levantamiento.index') }}" class="menu-link active">
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
        <!-- Top Bar -->
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn">
                <i class="fas fa-bars"></i>
            </button>

            <div class="user-info">
                <span class="user-name">{{ $usuario->Nombres }} {{ $usuario->ApellidosPat }}</span>
                <div class="user-avatar">
                    {{ strtoupper(substr($usuario->Nombres, 0, 1)) }}
                </div>
                <span class="admin-badge">ADMIN</span>
            </div>
        </div>

        <div class="container-fluid px-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-cogs me-2"></i>Tipos de Levantamiento
                    </h1>
                    <p class="text-muted mb-0">Administra los diferentes tipos de levantamiento disponibles</p>
                </div>
                <a href="{{ route('admin.tipos-levantamiento.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Nuevo Tipo
                </a>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Tipos de Levantamiento Grid -->
            <div class="row g-4">
                @forelse($tipos as $tipo)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm hover-shadow">
                            <div class="card-body">
                                <!-- Header del Card -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                                            <i class="fas {{ $tipo->Icono }} fa-2x"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title mb-1">{{ $tipo->Nombre }}</h5>
                                            <span class="badge {{ $tipo->Activo ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $tipo->Activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Descripción -->
                                @if($tipo->Descripcion)
                                    <p class="text-muted small mb-3">{{ $tipo->Descripcion }}</p>
                                @endif

                                <!-- Estadísticas -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="stat-box bg-light rounded p-2 text-center">
                                            <div class="stat-value h6 mb-0">{{ $tipo->total_campos ?? 0 }}</div>
                                            <div class="stat-label small text-muted">Campos</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-box bg-light rounded p-2 text-center">
                                            <div class="stat-value h6 mb-0">{{ $tipo->levantamientos_count ?? 0 }}</div>
                                            <div class="stat-label small text-muted">Levantamientos</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.tipos-levantamiento.show', $tipo->Id_Tipo_Levantamiento) }}" 
                                       class="btn btn-sm btn-outline-primary flex-fill">
                                        <i class="fas fa-eye me-1"></i>Ver
                                    </a>
                                    <a href="{{ route('admin.tipos-levantamiento.campos', $tipo->Id_Tipo_Levantamiento) }}" 
                                       class="btn btn-sm btn-outline-secondary flex-fill">
                                        <i class="fas fa-list me-1"></i>Campos
                                    </a>
                                    <div class="btn-group flex-fill">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="{{ route('admin.tipos-levantamiento.edit', $tipo->Id_Tipo_Levantamiento) }}">
                                                    <i class="fas fa-edit me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="#" 
                                                   onclick="toggleEstatus({{ $tipo->Id_Tipo_Levantamiento }})">
                                                    <i class="fas fa-{{ $tipo->Activo ? 'ban' : 'check' }} me-2"></i>
                                                    {{ $tipo->Activo ? 'Desactivar' : 'Activar' }}
                                                </a>
                                            </li>
                                            @if($tipo->levantamientos_count == 0)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" 
                                                       onclick="eliminarTipo({{ $tipo->Id_Tipo_Levantamiento }})">
                                                        <i class="fas fa-trash me-2"></i>Eliminar
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Footer con fecha -->
                            <div class="card-footer bg-transparent border-top-0">
                                <small class="text-muted">
                                    <i class="far fa-calendar me-1"></i>
                                    Creado: {{ \Carbon\Carbon::parse($tipo->Fecha_Creacion)->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-cogs fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay tipos de levantamiento registrados</h5>
                                <p class="text-muted mb-3">Comienza creando tu primer tipo de levantamiento</p>
                                <a href="{{ route('admin.tipos-levantamiento.create') }}" class="btn btn-danger">
                                    <i class="fas fa-plus me-2"></i>Crear Tipo de Levantamiento
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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

        // Toggle estatus
        function toggleEstatus(tipoId) {
            Swal.fire({
                title: '¿Cambiar estatus?',
                text: 'Esto activará o desactivará el tipo de levantamiento',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/tipos-levantamiento/${tipoId}/toggle-estatus`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Ocurrió un error', 'error');
                    });
                }
            });
        }

        // Eliminar tipo
        function eliminarTipo(tipoId) {
            Swal.fire({
                title: '¿Eliminar tipo?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/tipos-levantamiento/${tipoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Ocurrió un error', 'error');
                    });
                }
            });
        }

        // Mensajes de sesión
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}'
            });
        @endif
    </script>
</body>
</html>