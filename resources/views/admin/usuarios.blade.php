<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Levantamientos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboardAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/usuarios_admin.css') }}">
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-user-shield fa-2x mb-2"></i>
            <h4>Panel Admin</h4>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <i class="fas fa-home menu-icon"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.usuarios') }}" class="menu-link active">
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
        <!-- Top Bar -->
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn">
                <i class="fas fa-bars"></i>
            </button>

            <div class="user-info">
                <span class="user-name">{{ Auth::user()->nombre_completo ?? Auth::user()->Nombres }}</span>
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->Nombres, 0, 1)) }}
                </div>
                <span class="admin-badge">ADMIN</span>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="welcome-card">
            <h1>
                <i class="fas fa-users me-2"></i>
                Gestión de Usuarios
            </h1>
            <p class="mb-0">Administra los usuarios del sistema, sus roles y permisos</p>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Usuarios</h6>
                            <h3 class="mb-0">{{ $usuarios->count() }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Usuarios Activos</h6>
                            <h3 class="mb-0 text-success">{{ $usuarios->where('Estatus', 'Activo')->count() }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-user-check fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Usuarios Inactivos</h6>
                            <h3 class="mb-0 text-secondary">{{ $usuarios->where('Estatus', 'Inactivo')->count() }}</h3>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-user-times fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Administradores</h6>
                            <h3 class="mb-0 text-warning">{{ $usuarios->where('Rol', 'Admin')->count() }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-user-shield fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="users-card">
            <div class="users-card-header">
                <div class="d-flex align-items-center">
                    <h2 class="users-card-title">Lista de Usuarios</h2>
                    <span class="filter-badge active-filter" id="currentFilter">
                        <i class="fas fa-user-check me-1"></i>
                        Mostrando: Activos
                    </span>
                </div>
                <div class="d-flex gap-2 flex-wrap flex-grow-1 justify-content-end">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Buscar usuario...">
                    </div>
                    <button class="btn-history" id="btnToggleHistory">
                        <i class="fas fa-history"></i>
                        <span>Historial</span>
                    </button>
                    <button class="btn-add-user" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="fas fa-plus"></i>
                        <span>Nuevo Usuario</span>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Permisos</th>
                            <th>Registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @forelse($usuarios as $usuario)
                            <tr data-status="{{ $usuario->Estatus }}" class="user-row {{ $usuario->Estatus === 'Activo' ? 'active-user-row' : 'inactive-user-row' }}">
                                <td>
                                    <div class="user-name-cell">
                                        <div class="user-avatar-table">
                                            {{ strtoupper(substr($usuario->Nombres, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $usuario->Nombres }} {{ $usuario->ApellidosPat }} {{ $usuario->ApellidoMat }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $usuario->Correo }}</td>
                                <td>{{ $usuario->Telefono ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $usuario->Rol === 'Admin' ? 'badge-warning' : 'badge-secondary' }}">
                                        {{ $usuario->Rol }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $usuario->Estatus === 'Activo' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $usuario->Estatus }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-permission {{ $usuario->Permisos === 'si' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $usuario->Permisos === 'si' ? 'Con permisos' : 'Sin permisos' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($usuario->fecha_registro)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <button class="btn-action btn-view" onclick="viewUser({{ $usuario->id_usuarios }})" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-edit" onclick="editUser({{ $usuario->id_usuarios }})" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-toggle {{ $usuario->Estatus === 'Activo' ? 'active-user' : '' }}" 
                                            onclick="toggleUserStatus({{ $usuario->id_usuarios }}, '{{ $usuario->Estatus }}')" 
                                            title="{{ $usuario->Estatus === 'Activo' ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $usuario->Estatus === 'Activo' ? 'check' : 'times' }}"></i>
                                    </button>
                                    <button class="btn-action btn-permissions" onclick="managePermissions({{ $usuario->id_usuarios }})" title="Permisos">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay usuarios registrados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Create/Edit User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">
                        <i class="fas fa-user-plus me-2"></i>
                        Crear Nuevo Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="userForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id_usuarios" id="userId">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control" id="nombres" name="Nombres" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellidosPat" class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" id="apellidosPat" name="ApellidosPat">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellidosMat" class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" id="apellidosMat" name="ApellidoMat">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="Telefono">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="correo" class="form-label">Correo Electrónico *</label>
                                <input type="email" class="form-control" id="correo" name="Correo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contrasena" class="form-label">Contraseña <span id="passwordRequired">*</span></label>
                                <input type="password" class="form-control" id="contrasena" name="Contrasena">
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rol" class="form-label">Rol *</label>
                                <select class="form-select" id="rol" name="Rol" required>
                                    <option value="">Seleccionar rol</option>
                                    <option value="Admin">Administrador</option>
                                    <option value="Usuario">Usuario</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estatus" class="form-label">Estado *</label>
                                <select class="form-select" id="estatus" name="Estatus" required>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save me-2"></i>
                            Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View User Details Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>
                        Detalles del Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- Los detalles se cargarán aquí -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Modal -->
    <div class="modal fade" id="permissionsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i>
                        Gestionar Permisos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="permissionsForm" method="POST">
                    @csrf
                    <input type="hidden" name="id_usuarios" id="permissionUserId">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <h6 class="text-muted mb-3">Usuario: <span id="permissionUserName" class="text-dark"></span></h6>
                        </div>
                        
                        <div class="permission-item">
                            <div class="d-flex align-items-center gap-3 flex-grow-1">
                                <div class="permission-icon clients">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <strong>Otorgar Permisos Especiales</strong>
                                    <div class="text-muted small">Permite crear clientes y tipos de levantamiento</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="Permisos" id="permGeneral" value="si" style="width: 50px; height: 25px;">
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Los usuarios con permisos especiales pueden crear y gestionar clientes y tipos de levantamiento.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save me-2"></i>
                            Guardar Permisos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Configurar CSRF token para todas las peticiones AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Variable para controlar el estado del filtro
        let showingInactive = false;

        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        const mobileOverlay = document.getElementById('mobileOverlay');

        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                mobileOverlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });

        mobileOverlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('active');
        });

        // Función para filtrar usuarios por búsqueda
        $('#searchInput').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            const currentStatus = showingInactive ? 'Inactivo' : 'Activo';
            
            $('#usersTableBody tr').each(function() {
                const userStatus = $(this).data('status');
                const rowText = $(this).text().toLowerCase();
                
                // Mostrar solo si coincide con el estado actual Y con el término de búsqueda
                const matchesStatus = userStatus === currentStatus;
                const matchesSearch = rowText.indexOf(searchTerm) > -1;
                
                $(this).toggle(matchesStatus && matchesSearch);
            });
        });

        // Toggle entre usuarios activos e inactivos
        $('#btnToggleHistory').on('click', function() {
            showingInactive = !showingInactive;
            const btnIcon = $(this).find('i');
            const btnText = $(this).find('span');
            const filterBadge = $('#currentFilter');
            
            if (showingInactive) {
                // Mostrar inactivos
                $('.active-user-row').hide();
                $('.inactive-user-row').show();
                btnIcon.removeClass('fa-history').addClass('fa-users');
                btnText.text('Ver Activos');
                filterBadge.removeClass('active-filter').addClass('inactive-filter');
                filterBadge.html('<i class="fas fa-user-times me-1"></i>Mostrando: Inactivos');
            } else {
                // Mostrar activos
                $('.inactive-user-row').hide();
                $('.active-user-row').show();
                btnIcon.removeClass('fa-users').addClass('fa-history');
                btnText.text('Historial');
                filterBadge.removeClass('inactive-filter').addClass('active-filter');
                filterBadge.html('<i class="fas fa-user-check me-1"></i>Mostrando: Activos');
            }
            
            // Limpiar búsqueda al cambiar de vista
            $('#searchInput').val('');
        });

        // Inicializar: mostrar solo usuarios activos
        $(document).ready(function() {
            $('.inactive-user-row').hide();
            $('.active-user-row').show();
        });

        // Crear/Editar usuario
        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            
            const userId = $('#userId').val();
            const url = userId ? `/admin/usuarios/${userId}` : '/admin/usuarios';
            const method = userId ? 'PUT' : 'POST';
            
            const formData = {
                Nombres: $('#nombres').val(),
                ApellidosPat: $('#apellidosPat').val(),
                ApellidoMat: $('#apellidosMat').val(),
                Telefono: $('#telefono').val(),
                Correo: $('#correo').val(),
                Rol: $('#rol').val(),
                Estatus: $('#estatus').val(),
                Permisos: 'no'
            };

            // Solo incluir contraseña si se está creando o si se modificó
            if (!userId || $('#contrasena').val()) {
                const password = $('#contrasena').val();
                if (password.length < 6) {
                    alert('La contraseña debe tener al menos 6 caracteres');
                    return;
                }
                formData.Contrasena = password;
            }

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    alert(response.message || 'Usuario guardado correctamente');
                    $('#createUserModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error al guardar el usuario');
                }
            });
        });

        // Ver detalles de usuario
        function viewUser(id) {
            $.ajax({
                url: `/admin/usuarios/${id}`,
                method: 'GET',
                success: function(user) {
                    const fullName = `${user.Nombres} ${user.ApellidosPat || ''} ${user.ApellidoMat || ''}`.trim();
                    
                    const detailsHtml = `
                        <div class="detail-row">
                            <div class="detail-label">Nombre completo:</div>
                            <div class="detail-value"><strong>${fullName}</strong></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Correo:</div>
                            <div class="detail-value">${user.Correo}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Teléfono:</div>
                            <div class="detail-value">${user.Telefono || 'No registrado'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Rol:</div>
                            <div class="detail-value">
                                <span class="badge ${user.Rol === 'Admin' ? 'badge-warning' : 'badge-secondary'}">
                                    ${user.Rol}
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Estado:</div>
                            <div class="detail-value">
                                <span class="badge ${user.Estatus === 'Activo' ? 'badge-success' : 'badge-secondary'}">
                                    ${user.Estatus}
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Permisos:</div>
                            <div class="detail-value">
                                <span class="badge ${user.Permisos === 'si' ? 'badge-success' : 'badge-secondary'}">
                                    ${user.Permisos === 'si' ? 'Con permisos especiales' : 'Sin permisos especiales'}
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Fecha de registro:</div>
                            <div class="detail-value">${new Date(user.fecha_registro).toLocaleDateString('es-MX')}</div>
                        </div>
                    `;
                    
                    $('#userDetailsContent').html(detailsHtml);
                    $('#viewUserModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar los detalles del usuario');
                }
            });
        }

        // Editar usuario
        function editUser(id) {
            $.ajax({
                url: `/admin/usuarios/${id}`,
                method: 'GET',
                success: function(user) {
                    $('#userModalTitle').html('<i class="fas fa-edit me-2"></i>Editar Usuario');
                    $('#userId').val(user.id_usuarios);
                    $('#nombres').val(user.Nombres);
                    $('#apellidosPat').val(user.ApellidosPat || '');
                    $('#apellidosMat').val(user.ApellidoMat || '');
                    $('#telefono').val(user.Telefono || '');
                    $('#correo').val(user.Correo);
                    $('#contrasena').val('');
                    $('#contrasena').removeAttr('required');
                    $('#passwordRequired').hide();
                    $('#rol').val(user.Rol);
                    $('#estatus').val(user.Estatus);
                    $('#formMethod').val('PUT');
                    
                    $('#createUserModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del usuario');
                }
            });
        }

        // Alternar estado del usuario
        function toggleUserStatus(id, currentStatus) {
            const newStatus = currentStatus === 'Activo' ? 'Inactivo' : 'Activo';
            const action = newStatus === 'Activo' ? 'activar' : 'desactivar';
            
            if (confirm(`¿Está seguro de ${action} este usuario?`)) {
                $.ajax({
                    url: `/admin/usuarios/${id}/toggle-status`,
                    method: 'POST',
                    data: { Estatus: newStatus },
                    success: function(response) {
                        alert(response.message || 'Estado actualizado correctamente');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al actualizar el estado');
                    }
                });
            }
        }

        // Gestionar permisos
        function managePermissions(id) {
            $.ajax({
                url: `/admin/usuarios/${id}`,
                method: 'GET',
                success: function(user) {
                    const fullName = `${user.Nombres} ${user.ApellidosPat || ''} ${user.ApellidoMat || ''}`.trim();
                    
                    $('#permissionUserId').val(user.id_usuarios);
                    $('#permissionUserName').text(fullName);
                    $('#permGeneral').prop('checked', user.Permisos === 'si');
                    
                    $('#permissionsModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del usuario');
                }
            });
        }

        // Guardar permisos
        $('#permissionsForm').on('submit', function(e) {
            e.preventDefault();
            
            const userId = $('#permissionUserId').val();
            const hasPermissions = $('#permGeneral').is(':checked');
            
            $.ajax({
                url: `/admin/usuarios/${userId}/permissions`,
                method: 'POST',
                data: {
                    Permisos: hasPermissions ? 'si' : 'no'
                },
                success: function(response) {
                    alert(response.message || 'Permisos actualizados correctamente');
                    $('#permissionsModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error al actualizar los permisos');
                }
            });
        });

        // Resetear formulario al cerrar modal
        $('#createUserModal').on('hidden.bs.modal', function() {
            $('#userForm')[0].reset();
            $('#userId').val('');
            $('#userModalTitle').html('<i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario');
            $('#contrasena').attr('required', 'required');
            $('#passwordRequired').show();
            $('#formMethod').val('POST');
        });
    </script>
</body>
</html>