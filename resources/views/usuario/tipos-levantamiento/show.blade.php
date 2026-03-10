<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tipo->Nombre }} - Sistema de Levantamientos</title>
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
            background: linear-gradient(135deg, var(--user-color) 0%, var(--user-dark) 100%);
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

        .menu-link.locked {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .menu-link.locked:hover {
            background: transparent;
        }

        .lock-icon {
            position: absolute;
            right: 15px;
            font-size: 14px;
            color: rgba(255,255,255,0.6);
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
            background: var(--user-color);
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
            background: var(--user-dark);
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
            background: linear-gradient(135deg, var(--user-color), var(--user-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .user-badge {
            background: var(--user-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .icon-box {
            width: auto;
            height: auto;
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
        .btn-primary{
    background-color: #1D67A8;
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
            <h4>Sistema Levantamientos</h4>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('usuario.dashboard') }}" class="menu-link">
                    <i class="fas fa-home menu-icon"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('usuario.levantamientos.index') }}" class="menu-link">
                    <i class="fas fa-clipboard-list menu-icon"></i>
                    <span class="menu-text">Mis Levantamientos</span>
                </a>
            </li>
            <li class="menu-item">
                <a {{ $tienePermisosEspeciales ? 'href=' . route('usuario.clientesU') : 'href=#' }}
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
            <li class="menu-item">
                <a href="{{ route('usuario.tipos-levantamiento.index') }}" class="menu-link active">
                    <i class="fas fa-cogs menu-icon"></i>
                    <span class="menu-text">Tipos de Levantamiento</span>
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
                <span class="user-badge">{{ $usuario->Rol }}</span>
            </div>
        </div>

        <div class="container-fluid px-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas {{ $tipo->Icono }} me-2"></i>{{ $tipo->Nombre }}
                    </h1>
                    <p class="text-muted mb-0">Detalles y campos del tipo de levantamiento</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('usuario.tipos-levantamiento.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    <a href="{{ route('usuario.tipos-levantamiento.edit', $tipo->Id_Tipo_Levantamiento) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Información General -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Información General
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Icono y Nombre -->
                            <div class="text-center mb-4">
                                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-4 d-inline-flex mb-3">
                                    <i class="fas {{ $tipo->Icono }} fa-3x"></i>
                                </div>
                                <h4>{{ $tipo->Nombre }}</h4>
                                <span class="badge {{ $tipo->Activo ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                                    {{ $tipo->Activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>

                            <!-- Descripción -->
                            @if($tipo->Descripcion)
                                <div class="mb-3">
                                    <label class="small text-muted mb-1">Descripción</label>
                                    <p class="mb-0">{{ $tipo->Descripcion }}</p>
                                </div>
                            @endif

                            <hr>

                            <!-- Estadísticas -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h3 class="mb-0 text-primary">{{ count($campos) }}</h3>
                                        <small class="text-muted">Campos</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h3 class="mb-0 text-success">{{ $levantamientosCount }}</h3>
                                        <small class="text-muted">Levantamientos</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Fecha de Creación -->
                            <div class="mb-3">
                                <label class="small text-muted mb-1">Fecha de Creación</label>
                                <p class="mb-0">
                                    <i class="far fa-calendar me-2"></i>
                                    {{ \Carbon\Carbon::parse($tipo->Fecha_Creacion)->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <!-- Acciones -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('usuario.tipos-levantamiento.campos', $tipo->Id_Tipo_Levantamiento) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>Gestionar Campos
                                </a>
                                <button type="button" 
                                        class="btn btn-outline-{{ $tipo->Activo ? 'danger' : 'success' }}"
                                        onclick="toggleEstatus()">
                                    <i class="fas fa-{{ $tipo->Activo ? 'ban' : 'check' }} me-2"></i>
                                    {{ $tipo->Activo ? 'Desactivar' : 'Activar' }} Tipo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Campos -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>Campos del Formulario ({{ count($campos) }})
                            </h6>
                            <a href="{{ route('usuario.tipos-levantamiento.campos', $tipo->Id_Tipo_Levantamiento) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Agregar Campo
                            </a>
                        </div>
                        <div class="card-body">
                            @if(count($campos) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>Campo</th>
                                                <th>Tipo</th>
                                                <th style="width: 100px;">Requerido</th>
                                                <th style="width: 100px;">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($campos as $campo)
                                                <tr>
                                                    <td class="text-muted">{{ $campo->Orden }}</td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $campo->Etiqueta }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $campo->Nombre_Campo }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            {{ ucfirst($campo->Tipo_Input) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($campo->Es_Requerido)
                                                            <span class="badge bg-warning">Sí</span>
                                                        @else
                                                            <span class="badge bg-light text-dark">No</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $campo->Activo ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $campo->Activo ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay campos configurados</h5>
                                    <p class="text-muted mb-3">Agrega campos personalizados para este tipo de levantamiento</p>
                                    <a href="{{ route('usuario.tipos-levantamiento.campos', $tipo->Id_Tipo_Levantamiento) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Agregar Primer Campo
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Información adicional -->
                    @if($levantamientosCount > 0)
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Este tipo está siendo utilizado en <strong>{{ $levantamientosCount }}</strong> 
                            levantamiento{{ $levantamientosCount != 1 ? 's' : '' }}.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Sidebar toggle
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
                confirmButtonColor: '#667eea'
            });

            return false;
        }

        function getNombreAccion(accion) {
            const nombres = {
                'clientes': 'Clientes',
                'articulos': 'Artículos',
                'tipos_levantamiento': 'Tipos de Levantamiento'
            };
            return nombres[accion] || accion;
        }

        function toggleEstatus() {
            const tipoId = {{ $tipo->Id_Tipo_Levantamiento }};
            const activo = {{ $tipo->Activo ? 'true' : 'false' }};
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas ${activo ? 'desactivar' : 'activar'} este tipo de levantamiento?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/usuario/tipos-levantamiento/${tipoId}/toggle-estatus`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Error al cambiar el estatus'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>