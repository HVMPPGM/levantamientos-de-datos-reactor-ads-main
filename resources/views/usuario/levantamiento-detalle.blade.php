<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalle Levantamiento - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --user-color: #667eea;
            --user-dark: #764ba2;
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
            min-height: 100vh;
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

        /* Header Card */
        .header-card {
            background: linear-gradient(135deg, var(--user-color) 0%, var(--user-dark) 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9em;
            font-weight: 600;
            display: inline-block;
        }

        .status-pendiente {
            background: #fff3cd;
            color: #997404;
        }

        .status-proceso {
            background: #cff4fc;
            color: #055160;
        }

        .status-completado {
            background: #d1e7dd;
            color: #0a3622;
        }

        .status-cancelado {
            background: #f8d7da;
            color: #58151c;
        }

        /* Info Cards */
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .info-card h5 {
            color: var(--user-color);
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-row {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
        }

        /* Table Styles */
        .table-custom {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .table-custom thead {
            background: linear-gradient(135deg, var(--user-color), var(--user-dark));
            color: white;
        }

        .table-custom thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
        }

        .table-custom tbody td {
            padding: 15px;
            vertical-align: middle;
        }

        .table-custom tbody tr:hover {
            background-color: #f8f9ff;
        }

        /* Action Buttons */
        .btn-back {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
            color: white;
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
                <a href="{{ route('usuario.levantamientos') }}" class="menu-link active">
                    <i class="fas fa-clipboard-list menu-icon"></i>
                    <span class="menu-text">Mis Levantamientos</span>
                </a>
            </li>

            @php
                $usuario = Auth::user();
                $tienePermisosEspeciales = $usuario->Permisos === 'si';
            @endphp

            <!-- Clientes -->
            <li class="menu-item">
                <a href="#" class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}" 
                   data-permiso-requerido="clientes"
                   onclick="{{ !$tienePermisosEspeciales ? 'verificarPermiso(event, \"clientes\"); return false;' : '' }}">
                    <i class="fas fa-users menu-icon"></i>
                    <span class="menu-text">Clientes</span>
                    @if(!$tienePermisosEspeciales)
                        <i class="fas fa-lock lock-icon"></i>
                    @endif
                </a>
            </li>

            <!-- Artículos -->
            <li class="menu-item">
                <a href="#" class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}" 
                   data-permiso-requerido="articulos"
                   onclick="{{ !$tienePermisosEspeciales ? 'verificarPermiso(event, \"articulos\"); return false;' : '' }}">
                    <i class="fas fa-box menu-icon"></i>
                    <span class="menu-text">Artículos</span>
                    @if(!$tienePermisosEspeciales)
                        <i class="fas fa-lock lock-icon"></i>
                    @endif
                </a>
            </li>

            <!-- Tipos de Levantamiento -->
            <li class="menu-item">
                <a href="{{ route('usuario.tipos-levantamiento.index') }}" class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}" 
                   data-permiso-requerido="tipos_levantamiento"
                   onclick="{{ !$tienePermisosEspeciales ? 'verificarPermiso(event, \"tipos_levantamiento\"); return false;' : '' }}">
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
                <span class="user-badge">
                    {{ $usuario->Rol }}
                </span>
            </div>
        </div>

        <!-- Header Card -->
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-3">
                        <i class="fas fa-file-alt me-2"></i>
                        Levantamiento LEV-{{ str_pad($levantamiento->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="mb-2">
                        <i class="fas fa-tag me-2"></i>
                        <strong>Tipo:</strong> {{ $levantamiento->tipo_nombre ?? 'Sin tipo' }}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        <strong>Fecha de Creación:</strong> 
                        {{ \Carbon\Carbon::parse($levantamiento->fecha_creacion)->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div>
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '', $levantamiento->estatus)) }}">
                        <i class="fas fa-circle me-1" style="font-size: 0.6em;"></i>
                        {{ $levantamiento->estatus }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Información del Cliente -->
        <div class="info-card">
            <h5>
                <i class="fas fa-building"></i>
                Información del Cliente
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Nombre del Cliente</div>
                        <div class="info-value">{{ $levantamiento->cliente_nombre }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Correo</div>
                        <div class="info-value">
                            @if($levantamiento->cliente_correo)
                                <a href="mailto:{{ $levantamiento->cliente_correo }}">{{ $levantamiento->cliente_correo }}</a>
                            @else
                                <span class="text-muted">No especificado</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Teléfono</div>
                        <div class="info-value">
                            @if($levantamiento->cliente_telefono)
                                <a href="tel:{{ $levantamiento->cliente_telefono }}">{{ $levantamiento->cliente_telefono }}</a>
                            @else
                                <span class="text-muted">No especificado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artículos del Levantamiento -->
        <div class="info-card">
            <h5>
                <i class="fas fa-box-open"></i>
                Artículos del Levantamiento
            </h5>
            
            @if($articulos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Artículo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unitario</th>
                                <th class="text-end">Subtotal</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($articulos as $art)
                                @php 
                                    $subtotal = $art->Cantidad * $art->Precio_Unitario;
                                    $total += $subtotal;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $art->articulo_nombre }}</strong>
                                        @if($art->articulo_descripcion)
                                            <br><small class="text-muted">{{ $art->articulo_descripcion }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $art->marca_nombre ?? 'N/A' }}</td>
                                    <td>{{ $art->modelo_nombre ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $art->Cantidad }}</span>
                                    </td>
                                    <td class="text-end">${{ number_format($art->Precio_Unitario, 2) }}</td>
                                    <td class="text-end">
                                        <strong>${{ number_format($subtotal, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($art->Notas)
                                            <small class="text-muted">{{ $art->Notas }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #f8f9fa; font-weight: bold;">
                            <tr>
                                <td colspan="5" class="text-end">TOTAL:</td>
                                <td class="text-end" style="color: var(--user-color); font-size: 1.1em;">
                                    ${{ number_format($total, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    No hay artículos registrados en este levantamiento.
                </div>
            @endif
        </div>

        <!-- Información Adicional -->
        @if($valoresDinamicos->count() > 0)
            <div class="info-card">
                <h5>
                    <i class="fas fa-info-circle"></i>
                    Información Adicional
                </h5>
                <div class="row">
                    @foreach($valoresDinamicos as $valor)
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">{{ $valor->Etiqueta }}</div>
                                <div class="info-value">
                                    @if($valor->Tipo_Input === 'textarea')
                                        <div style="white-space: pre-wrap;">{{ $valor->Valor }}</div>
                                    @elseif($valor->Tipo_Input === 'date')
                                        {{ \Carbon\Carbon::parse($valor->Valor)->format('d/m/Y') }}
                                    @elseif($valor->Tipo_Input === 'number')
                                        {{ number_format($valor->Valor, 2) }}
                                    @else
                                        {{ $valor->Valor }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Botón Volver -->
        <div class="mt-4">
            <a href="{{ route('usuario.levantamientos') }}" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>
                Volver a Mis Levantamientos
            </a>
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

        // Verificar permiso
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
    </script>
</body>
</html>