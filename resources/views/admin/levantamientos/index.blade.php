<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Levantamientos - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #1D67A8;
            --sidebar-width: 280px;
            --sidebar-collapsed: 70px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #1D67A8 0%, #1D67A8 100%);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }
        
        .sidebar-header {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar.collapsed .sidebar-header h4,
        .sidebar.collapsed .menu-text {
            display: none;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 15px 0;
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

        .sidebar.collapsed .menu-link {
            justify-content: center;
        }
        
        .sidebar.collapsed .menu-icon {
            margin-right: 0;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
            padding: 20px;
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed);
        }
        
        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .toggle-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #1D67A8;
        }
        
        /* Filtros */
        .filter-tabs {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 25px;
            margin-right: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .filter-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .filter-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .filter-badge {
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            margin-left: 5px;
        }
        
        .filter-btn.active .filter-badge {
            background: rgba(255,255,255,0.3);
        }
        
        /* Cards de Levantamientos */
        .levantamiento-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-left: 4px solid #e0e0e0;
        }
        
        .levantamiento-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .levantamiento-card.pendiente {
            border-left-color: #1D67A8;
        }
        
        .levantamiento-card.enproceso {
            border-left-color: #1D67A8;
        }
        
        .levantamiento-card.completado {
            border-left-color: #1D67A8;
        }
        
        .levantamiento-card.cancelado {
            border-left-color: #1D67A8;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .status-Pendiente {
            background: #1D67A8;
            color: #1D67A8;
        }
        
        .status-EnProceso {
            background: #1D67A8;
            color: white;
        }
        
        .status-Completado {
            background: #d1e7dd;
            color: #0a3622;
        }
        
        .status-Cancelado {
            background: #f8d7da;
            color: #58151c;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        .btn-primary {
    background-color: #1D67A8;
    border-color: #1D67A8;
}

.btn-primary:hover {
    background-color: #175d96;
    border-color: #175d96;
}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
            <h4>Sistema</h4>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('admin.dashboard') }}" class="menu-link ">
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
                <a href="{{ route('admin.levantamientos.index') }}" class="menu-link active">
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
                <a href="{{ route('admin.tipos-levantamiento.index') }}" class="menu-link ">
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
            <button class="toggle-btn" id="toggleBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h2 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Levantamientos</h2>
            </div>
            <div>
                <a href="{{ route('admin.levantamientos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Levantamiento
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-tabs">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Filtrar por estado</h5>
            </div>
            <div>
                <button class="filter-btn {{ $estatus == 'all' ? 'active' : '' }}" onclick="filtrarPorEstatus('all')">
                    Todos <span class="filter-badge">{{ $contadores['todos'] }}</span>
                </button>
                <button class="filter-btn {{ $estatus == 'Pendiente' ? 'active' : '' }}" onclick="filtrarPorEstatus('Pendiente')">
                    Pendientes <span class="filter-badge">{{ $contadores['pendiente'] }}</span>
                </button>
                <button class="filter-btn {{ $estatus == 'En Proceso' ? 'active' : '' }}" onclick="filtrarPorEstatus('En Proceso')">
                    En Proceso <span class="filter-badge">{{ $contadores['proceso'] }}</span>
                </button>
                <button class="filter-btn {{ $estatus == 'Completado' ? 'active' : '' }}" onclick="filtrarPorEstatus('Completado')">
                    Completados <span class="filter-badge">{{ $contadores['completado'] }}</span>
                </button>
                <button class="filter-btn {{ $estatus == 'Cancelado' ? 'active' : '' }}" onclick="filtrarPorEstatus('Cancelado')">
                    Cancelados <span class="filter-badge">{{ $contadores['cancelado'] }}</span>
                </button>
            </div>
        </div>

        <!-- Lista de Levantamientos -->
        <div class="levantamientos-container">
            @forelse($levantamientos as $lev)
                @php
                    $cssClass = strtolower(str_replace(' ', '', $lev->estatus));
                    $badgeClass = 'status-' . str_replace(' ', '', $lev->estatus);
                @endphp
                <div class="levantamiento-card {{ $cssClass }}">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2">
                                <i class="fas fa-file-alt me-2"></i>
                                LEV-{{ str_pad($lev->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}
                                @if(isset($lev->modelo_por_definir) && $lev->modelo_por_definir)
                                    <span class="badge bg-warning text-dark ms-2" style="font-size:0.7rem;">
                                        <i class="fas fa-question-circle me-1"></i>Modelo por definir
                                    </span>
                                @endif
                            </h5>
                            <p class="text-muted mb-2">
                                <i class="fas fa-building me-2"></i><strong>Cliente:</strong> {{ $lev->cliente_nombre }}
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-tag me-2"></i><strong>Tipo:</strong> {{ $lev->tipo_nombre ?? 'Sin tipo' }}
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-user me-2"></i><strong>Creador:</strong> {{ $lev->usuario_nombre }} {{ $lev->usuario_apellido }}
                                <span class="ms-3">
                                    <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($lev->fecha_creacion)->format('d/m/Y H:i') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="status-badge {{ $badgeClass }}">{{ $lev->estatus }}</span>
                            <div class="mt-3 d-flex gap-2 justify-content-end flex-wrap">
                                <a href="{{ route('admin.levantamientos.show', $lev->Id_Levantamiento) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="{{ route('admin.levantamientos.edit', $lev->Id_Levantamiento) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                @if($lev->estatus === 'En Proceso')
                                    <button class="btn btn-sm btn-success" onclick="completarLevantamiento({{ $lev->Id_Levantamiento }})">
                                        <i class="fas fa-check-circle me-1"></i>Completar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay levantamientos {{ $estatus != 'all' ? 'con este estado' : '' }}</h5>
                </div>
            @endforelse
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        document.getElementById('toggleBtn').addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        // Filtrar por estatus
        function filtrarPorEstatus(estatus) {
            window.location.href = '{{ route("admin.levantamientos.index") }}?estatus=' + estatus;
        }

        // Completar levantamiento
        function completarLevantamiento(id) {
            Swal.fire({
                title: '¿Completar levantamiento?',
                html: 'Se marcará como <strong>Completado</strong>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check-circle me-1"></i>Completar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (!result.isConfirmed) return;
                
                Swal.fire({
                    title: 'Procesando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                fetch(`/admin/levantamientos/${id}/estatus`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ estatus: 'Completado' })
                })
                .then(r => r.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Completado!',
                            text: 'El levantamiento ha sido completado',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.close();
                    Swal.fire('Error', 'Error al procesar', 'error');
                });
            });
        }
    </script>
</body>
</html>