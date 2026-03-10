<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Clientes - Sistema de Levantamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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

        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }

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
        .sidebar.collapsed .menu-text { opacity: 0; width: 0; }

        .sidebar-menu { list-style: none; padding: 0; margin: 20px 0; }

        .menu-item { margin: 5px 0; }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }

        .menu-link:hover { background: rgba(255,255,255,0.1); color: white; }

        .menu-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            border-left: 4px solid white;
        }

        .menu-link.locked { opacity: 0.5; cursor: not-allowed; }
        .menu-link.locked:hover { background: transparent; }

        .lock-icon {
            position: absolute;
            right: 15px;
            font-size: 14px;
            color: rgba(255,255,255,0.6);
        }

        .menu-icon { width: 30px; text-align: center; font-size: 20px; }

        .menu-text { margin-left: 15px; white-space: nowrap; transition: opacity 0.3s; }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            padding: 20px;
        }

        .main-content.expanded { margin-left: var(--sidebar-collapsed-width); }

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

        .toggle-btn:hover { background: var(--user-dark); }

        .user-info { display: flex; align-items: center; gap: 15px; }

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

        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .content-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--user-color), var(--user-dark));
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(6, 39, 187, 0.3);
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 8px 15px;
            margin-left: 10px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 5px 10px;
            margin: 0 10px;
        }

        table.dataTable thead th {
            background: linear-gradient(135deg, var(--user-color), var(--user-dark));
            color: white;
            font-weight: 600;
            border: none;
        }

        table.dataTable tbody tr { transition: all 0.3s; }
        table.dataTable tbody tr:hover { background-color: #f8f9fa; }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-activo { background: #d4edda; color: #155724; }
        .status-inactivo { background: #f8d7da; color: #721c24; }

        .stats-mini { display: flex; gap: 10px; align-items: center; }

        .stat-mini-item {
            background: #f8f9fa;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-mini-item i { font-size: 14px; }
        .stat-completado { color: #28a745; }
        .stat-proceso { color: #ffc107; }
        .stat-pendiente { color: #17a2b8; }

        .action-buttons { display: flex; gap: 8px; }

        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-view { background: #17a2b8; color: white; }
        .btn-view:hover { background: #138496; transform: translateY(-2px); }

        .btn-edit { background: #ffc107; color: white; }
        .btn-edit:hover { background: #e0a800; transform: translateY(-2px); }

        .btn-toggle { background: #dc3545; color: white; }
        .btn-toggle:hover { background: #c82333; transform: translateY(-2px); }
        .btn-toggle.activo { background: #28a745; }
        .btn-toggle.activo:hover { background: #218838; }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 80px; color: #e0e0e0; margin-bottom: 20px; }
        .empty-state h3 { color: #6c757d; margin-bottom: 10px; }
        .empty-state p { color: #adb5bd; }

        /* Badge "Nuevo" para clientes recientes (últimas 24h) */
        .badge-nuevo {
            background: #28a745;
            color: white;
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: 600;
            margin-left: 6px;
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
            .mobile-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .mobile-overlay.active { display: block; }
            .content-header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .action-buttons { flex-direction: column; }
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.9);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.active { display: flex; }
        .spinner-border { width: 3rem; height: 3rem; }
        .btn-primary{
            background: #1D67A8;
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

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
                <a href="{{ route('usuario.clientesU') }}" class="menu-link active {{ !$tienePermisosEspeciales ? 'locked' : '' }}"
                   data-permiso-requerido="clientes"
                   onclick="{{ !$tienePermisosEspeciales ? 'verificarPermiso(event, \"clientes\"); return false;' : '' }}">
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
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-info">
                <span class="user-name">{{ $usuario->Nombres }} {{ $usuario->ApellidosPat }}</span>
                <div class="user-avatar">
                    {{ strtoupper(substr($usuario->Nombres, 0, 1)) }}
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-header">
                <h2 class="content-title">
                    <i class="fas fa-users"></i>
                    Gestión de Clientes
                </h2>
                @if($tienePermisosEspeciales)
                    <a href="{{ route('usuario.clientes.create') }}" class="btn btn-primary-custom">
                        <i class="fas fa-plus me-2"></i>Nuevo Cliente
                    </a>
                @else
                    <button class="btn btn-primary-custom" disabled>
                        <i class="fas fa-lock me-2"></i>Nuevo Cliente
                    </button>
                @endif
            </div>

            @if($clientes->count() > 0)
                <div class="table-responsive">
                    <table id="clientesTable" class="table table-hover">
                        <thead>
                            <tr>
                                {{-- col 0: fecha ISO oculta para ordenar --}}
                                <th class="d-none">FechaOrden</th>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Ubicación</th>
                                <th>Artículo Principal</th>
                                <th>Levantamientos</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientes as $cliente)
                            @php
                                $esNuevo = \Carbon\Carbon::parse($cliente->fecha_registro)->diffInHours(now()) < 24;
                            @endphp
                            <tr>
                                {{-- col 0: fecha ISO (oculta), usada solo para el sort --}}
                                <td class="d-none">{{ $cliente->fecha_registro }}</td>

                                {{-- col 1: Cliente --}}
                                <td>
                                    <strong>
                                        {{ $cliente->Nombre }}
                                        @if($esNuevo)
                                            <span class="badge-nuevo">Nuevo</span>
                                        @endif
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="far fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($cliente->fecha_registro)->format('d/m/Y') }}
                                    </small>
                                </td>

                                {{-- col 2: Contacto --}}
                                <td>
                                    @if($cliente->Correo)
                                        <div><i class="far fa-envelope"></i> {{ $cliente->Correo }}</div>
                                    @endif
                                    @if($cliente->Telefono)
                                        <div><i class="fas fa-phone"></i> {{ $cliente->Telefono }}</div>
                                    @endif
                                </td>

                                {{-- col 3: Ubicación --}}
                                <td>
                                    {{ $cliente->Municipio }}, {{ $cliente->Estado }}
                                    @if($cliente->Colonia)
                                        <br><small class="text-muted">{{ $cliente->Colonia }}</small>
                                    @endif
                                </td>

                                {{-- col 4: Artículo Principal --}}
                                <td>
                                    <span class="badge bg-info text-white">
                                        {{ $cliente->ArticuloPrincipal ?? 'No asignado' }}
                                    </span>
                                </td>

                                {{-- col 5: Levantamientos --}}
                                <td>
                                    <div class="stats-mini">
                                        <div class="stat-mini-item stat-completado">
                                            <i class="fas fa-check-circle"></i>
                                            {{ $cliente->levantamientos->completados }}
                                        </div>
                                        <div class="stat-mini-item stat-proceso">
                                            <i class="fas fa-spinner"></i>
                                            {{ $cliente->levantamientos->en_proceso }}
                                        </div>
                                        <div class="stat-mini-item stat-pendiente">
                                            <i class="fas fa-clock"></i>
                                            {{ $cliente->levantamientos->pendientes }}
                                        </div>
                                    </div>
                                </td>

                                {{-- col 6: Estatus --}}
                                <td>
                                    <span class="status-badge status-{{ strtolower($cliente->Estatus) }}">
                                        {{ $cliente->Estatus }}
                                    </span>
                                </td>

                                {{-- col 7: Acciones --}}
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('usuario.clientes.show', $cliente->Id_Cliente) }}"
                                           class="btn-action btn-view" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($tienePermisosEspeciales)
                                            <a href="{{ route('usuario.clientes.edit', $cliente->Id_Cliente) }}"
                                               class="btn-action btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn-action btn-toggle {{ $cliente->Estatus === 'Activo' ? 'activo' : '' }}"
                                                    onclick="toggleEstatus({{ $cliente->Id_Cliente }}, '{{ $cliente->Estatus }}')"
                                                    title="{{ $cliente->Estatus === 'Activo' ? 'Desactivar' : 'Activar' }}">
                                                <i class="fas fa-{{ $cliente->Estatus === 'Activo' ? 'check' : 'times' }}"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <h3>No hay clientes registrados</h3>
                    <p>Comienza creando tu primer cliente</p>
                    @if($tienePermisosEspeciales)
                        <a href="{{ route('usuario.clientes.create') }}" class="btn btn-primary-custom mt-3">
                            <i class="fas fa-plus me-2"></i>Crear Cliente
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Sidebar
        const sidebar      = document.getElementById('sidebar');
        const mainContent  = document.getElementById('mainContent');
        const toggleBtn    = document.getElementById('toggleBtn');
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

        // DataTable — col 0 oculta (fecha ISO) ordena DESC, resto visible desde col 1
        $(document).ready(function () {
            $('#clientesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
                },
                pageLength: 10,
                // ordenar por col 0 (fecha ISO) descendente → más reciente arriba
                order: [[0, 'desc']],
                columnDefs: [
                    // col 0: oculta visualmente pero activa para ordenar
                    { targets: 0, visible: false, searchable: false }
                ],
                responsive: true
            });
        });

        // Toggle Estatus
        function toggleEstatus(clienteId, estatusActual) {
            const accion = estatusActual === 'Activo' ? 'desactivar' : 'activar';

            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas ${accion} este cliente?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0627bb',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loadingOverlay').addClass('active');

                    $.ajax({
                        url: `/usuario/clientes/${clienteId}/toggle-estatus`,
                        type: 'POST',
                        success: function (response) {
                            $('#loadingOverlay').removeClass('active');
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: response.message,
                                    confirmButtonColor: '#0627bb'
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: response.message, confirmButtonColor: '#dc3545' });
                            }
                        },
                        error: function (xhr) {
                            $('#loadingOverlay').removeClass('active');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Ocurrió un error al procesar la solicitud',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        }

        function verificarPermiso(event, accion) {
            event.preventDefault();
            event.stopPropagation();
            Swal.fire({
                icon: 'warning',
                title: 'Acceso Restringido',
                html: `<p>No tienes permisos para acceder a <strong>${getNombreAccion(accion)}</strong>.</p>
                       <p class="text-muted small">Contacta al administrador para solicitar acceso.</p>`,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0627bb'
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

        @if(session('success'))
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: '{{ session("success") }}', confirmButtonColor: '#0627bb' });
        @endif

        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Error', text: '{{ session("error") }}', confirmButtonColor: '#dc3545' });
        @endif
    </script>
</body>
</html>