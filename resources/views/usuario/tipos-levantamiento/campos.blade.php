<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestionar Campos - {{ $tipo->Nombre }} - Sistema de Levantamientos</title>
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
                    <p class="text-muted mb-0">Gestión de campos del formulario</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('usuario.tipos-levantamiento.show', $tipo->Id_Tipo_Levantamiento) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCampo">
                        <i class="fas fa-plus me-2"></i>Agregar Campo
                    </button>
                </div>
            </div>

            <!-- Alerts -->
            <div id="alertContainer"></div>

            <!-- Lista de Campos -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>Campos Configurados ({{ count($campos) }})
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(count($campos) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="tablaCampos">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">Orden</th>
                                                <th>Campo</th>
                                                <th>Tipo</th>
                                                <th style="width: 100px;">Requerido</th>
                                                <th style="width: 100px;">Estado</th>
                                                <th style="width: 150px;" class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="camposBody">
                                            @foreach($campos as $campo)
                                                <tr data-campo-id="{{ $campo->Id_Campo }}">
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary">{{ $campo->Orden }}</span>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $campo->Etiqueta }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <code>{{ $campo->Nombre_Campo }}</code>
                                                            </small>
                                                            @if($campo->Placeholder)
                                                                <br>
                                                                <small class="text-muted fst-italic">
                                                                    Placeholder: "{{ $campo->Placeholder }}"
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ ucfirst($campo->Tipo_Input) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($campo->Es_Requerido)
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-asterisk fa-xs"></i> Sí
                                                            </span>
                                                        @else
                                                            <span class="badge bg-light text-dark">No</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $campo->Activo ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $campo->Activo ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" 
                                                                    class="btn btn-outline-primary" 
                                                                    onclick="editarCampo({{ $campo->Id_Campo }})"
                                                                    title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-outline-secondary" 
                                                                    onclick="toggleCampoEstatus({{ $campo->Id_Campo }})"
                                                                    title="{{ $campo->Activo ? 'Desactivar' : 'Activar' }}">
                                                                <i class="fas fa-{{ $campo->Activo ? 'ban' : 'check' }}"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-outline-danger" 
                                                                    onclick="eliminarCampo({{ $campo->Id_Campo }})"
                                                                    title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay campos configurados</h5>
                                    <p class="text-muted mb-3">Agrega campos personalizados para este tipo de levantamiento</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCampo">
                                        <i class="fas fa-plus me-2"></i>Agregar Primer Campo
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Información -->
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Los campos configurados aquí aparecerán en el formulario de creación de levantamientos.
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Agregar/Editar Campo -->
    <div class="modal fade" id="modalCampo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        <span id="modalTitle">Agregar Campo</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCampo">
                    <div class="modal-body">
                        <input type="hidden" id="campoId" name="campo_id">
                        
                        <div class="row">
                            <!-- Nombre del Campo -->
                            <div class="col-md-6 mb-3">
                                <label for="nombre_campo" class="form-label">
                                    Nombre del Campo <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre_campo" 
                                       name="nombre_campo"
                                       placeholder="ej: descripcion_trabajo"
                                       required>
                                <small class="text-muted">Sin espacios, usar guiones bajos</small>
                            </div>

                            <!-- Etiqueta -->
                            <div class="col-md-6 mb-3">
                                <label for="etiqueta" class="form-label">
                                    Etiqueta <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="etiqueta" 
                                       name="etiqueta"
                                       placeholder="ej: Descripción del Trabajo"
                                       required>
                                <small class="text-muted">Texto que verá el usuario</small>
                            </div>

                            <!-- Tipo de Input -->
                            <div class="col-md-6 mb-3">
                                <label for="tipo_input" class="form-label">
                                    Tipo de Campo <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="tipo_input" name="tipo_input" required>
                                    @foreach($tiposInput as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Orden -->
                            <div class="col-md-6 mb-3">
                                <label for="orden" class="form-label">
                                    Orden <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="orden" 
                                       name="orden"
                                       value="{{ count($campos) + 1 }}"
                                       min="1"
                                       required>
                                <small class="text-muted">Posición en el formulario</small>
                            </div>

                            <!-- Placeholder -->
                            <div class="col-md-6 mb-3">
                                <label for="placeholder" class="form-label">Placeholder</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="placeholder" 
                                       name="placeholder"
                                       placeholder="Texto de ayuda">
                            </div>

                            <!-- Valor por Defecto -->
                            <div class="col-md-6 mb-3">
                                <label for="valor_default" class="form-label">Valor por Defecto</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="valor_default" 
                                       name="valor_default"
                                       placeholder="Valor predeterminado">
                            </div>

                            <!-- Es Requerido -->
                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="es_requerido" 
                                           name="es_requerido">
                                    <label class="form-check-label" for="es_requerido">
                                        Campo obligatorio
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarCampo">
                            <i class="fas fa-save me-2"></i>Guardar Campo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

        // Variables y datos
        const tipoId = {{ $tipo->Id_Tipo_Levantamiento }};
        let modoEdicion = false;
        let campoEditandoId = null;

        // Datos de campos existentes para edición
        const camposData = @json($campos);

        // Agregar nuevo campo
        document.getElementById('formCampo').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btnGuardar = document.getElementById('btnGuardarCampo');
            const originalText = btnGuardar.innerHTML;
            
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

            const url = modoEdicion 
                ? `/usuario/tipos-levantamiento/${tipoId}/campos/${campoEditandoId}`
                : `/usuario/tipos-levantamiento/${tipoId}/campos`;
            
            const method = modoEdicion ? 'PUT' : 'POST';

            // Convertir FormData a objeto
            const data = {};
            formData.forEach((value, key) => {
                if (key === 'es_requerido') {
                    data[key] = document.getElementById('es_requerido').checked ? '1' : '0';
                } else {
                    data[key] = value;
                }
            });

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-HTTP-Method-Override': method
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    bootstrap.Modal.getInstance(document.getElementById('modalCampo')).hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Error al guardar');
                }
            })
            .catch(error => {
                showAlert('danger', error.message);
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = originalText;
            });
        });

        // Editar campo
        function editarCampo(campoId) {
            const campo = camposData.find(c => c.Id_Campo === campoId);
            if (!campo) return;

            modoEdicion = true;
            campoEditandoId = campoId;

            document.getElementById('modalTitle').textContent = 'Editar Campo';
            document.getElementById('nombre_campo').value = campo.Nombre_Campo;
            document.getElementById('etiqueta').value = campo.Etiqueta;
            document.getElementById('tipo_input').value = campo.Tipo_Input;
            document.getElementById('placeholder').value = campo.Placeholder || '';
            document.getElementById('valor_default').value = campo.Valor_Default || '';
            document.getElementById('orden').value = campo.Orden;
            document.getElementById('es_requerido').checked = campo.Es_Requerido == 1;

            new bootstrap.Modal(document.getElementById('modalCampo')).show();
        }

        // Toggle estatus de campo
        function toggleCampoEstatus(campoId) {
            fetch(`/usuario/tipos-levantamiento/${tipoId}/campos/${campoId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => showAlert('danger', error.message));
        }

        // Eliminar campo
        function eliminarCampo(campoId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/usuario/tipos-levantamiento/${tipoId}/campos/${campoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => showAlert('danger', error.message));
                }
            });
        }

        // Resetear modal al cerrar
        document.getElementById('modalCampo').addEventListener('hidden.bs.modal', function () {
            modoEdicion = false;
            campoEditandoId = null;
            document.getElementById('modalTitle').textContent = 'Agregar Campo';
            document.getElementById('formCampo').reset();
            document.getElementById('orden').value = {{ count($campos) + 1 }};
        });

        // Función para mostrar alertas
        function showAlert(type, message) {
            const alertHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alertHTML;
            
            // Auto-dismiss después de 5 segundos
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    bootstrap.Alert.getInstance(alert)?.close();
                }
            }, 5000);
        }
    </script>
</body>
</html>