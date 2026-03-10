<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestionar Campos - {{ $tipo->Nombre }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed: 70px;
            --admin-color: #1D67A8;
            --admin-dark: #1D67A8;
        }

        body { background: #f5f7fa; overflow-x: hidden; }

        /* Sidebar */
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: var(--sidebar-width); background: linear-gradient(135deg, #1D67A8 0%, #1D67A8 100%); color: white; transition: all 0.3s ease; z-index: 1000; overflow-y: auto; }
        .sidebar.collapsed { width: var(--sidebar-collapsed); }
        .sidebar-header { padding: 25px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar.collapsed .sidebar-header h4, .sidebar.collapsed .menu-text { display: none; }
        .sidebar-menu { list-style: none; padding: 15px 0; margin: 0; }
        .menu-item { margin: 5px 15px; }
        .menu-link { display: flex; align-items: center; padding: 12px 15px; color: rgba(255,255,255,0.8); text-decoration: none; border-radius: 8px; transition: all 0.3s; }
        .menu-link:hover, .menu-link.active { background: rgba(255,255,255,0.1); color: white; }
        .menu-icon { width: 20px; margin-right: 15px; text-align: center; }
        .sidebar.collapsed .menu-link { justify-content: center; }
        .sidebar.collapsed .menu-icon { margin-right: 0; }
        .sidebar-footer { position: absolute; bottom: 0; width: 100%; padding: 15px; border-top: 1px solid rgba(255,255,255,0.1); }

        /* Main Content */
        .main-content { margin-left: var(--sidebar-width); transition: margin-left 0.3s ease; padding: 20px; }
        .main-content.expanded { margin-left: var(--sidebar-collapsed); }

        /* Top Bar */
        .top-bar { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .toggle-btn { background: var(--admin-color); color: white; border: none; width: 40px; height: 40px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
        .toggle-btn:hover { background: var(--admin-dark); }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--admin-color), var(--admin-dark)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        .admin-badge { background: var(--admin-color); color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }

        /* Header Card */
        .header-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .tipo-icon-header { width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; }

        /* Campos List */
        .campos-container { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .campo-item { background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 10px; padding: 20px; margin-bottom: 15px; transition: all 0.3s; cursor: move; }
        .campo-item:hover { border-color: var(--admin-color); box-shadow: 0 3px 10px rgba(220, 53, 69, 0.1); }
        .campo-item.sortable-ghost { opacity: 0.4; }
        .campo-item.sortable-drag { box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
        .campo-header { display: flex; justify-content: between; align-items: start; margin-bottom: 10px; }
        .campo-badge { display: inline-block; padding: 4px 10px; border-radius: 15px; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-left: 8px; }
        .badge-requerido { background: #fff3cd; color: #997404; }
        .badge-opcional { background: #d1ecf1; color: #0c5460; }
        .badge-inactivo { background: #f8d7da; color: #58151c; }
        .drag-handle { color: #6c757d; font-size: 20px; cursor: move; margin-right: 15px; }
        .drag-handle:hover { color: var(--admin-color); }

        /* Botones */
        .btn-icon { width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
        .btn-crear-campo { position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: var(--admin-color); color: white; border: none; font-size: 24px; box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4); cursor: pointer; transition: all 0.3s; z-index: 999; }
        .btn-crear-campo:hover { transform: scale(1.1); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.6); }

        /* Empty State */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 64px; color: #bdc3c7; margin-bottom: 20px; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0; }
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

        <!-- Header Card -->
        <div class="header-card">
            <div class="d-flex align-items-center gap-3 mb-3">
                <a href="{{ route('admin.tipos-levantamiento.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="tipo-icon-header">
                    <i class="fas {{ $tipo->Icono }}"></i>
                </div>
                <div class="flex-grow-1">
                    <h2 class="mb-0">{{ $tipo->Nombre }}</h2>
                    <p class="text-muted mb-0">Gestión de Campos del Formulario</p>
                </div>
                <button class="btn btn-danger" onclick="abrirModalCrearCampo()">
                    <i class="fas fa-plus me-2"></i>Agregar Campo
                </button>
            </div>

            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Puedes arrastrar y soltar los campos para reordenarlos
            </div>
        </div>

        <!-- Campos Container -->
        <div class="campos-container">
            <h4 class="mb-4">
                <i class="fas fa-list me-2 text-primary"></i>
                Campos del Formulario ({{ $campos->count() }})
            </h4>

            @if($campos->count() > 0)
                <div id="camposList">
                    @foreach($campos as $campo)
                        <div class="campo-item" data-id="{{ $campo->Id_Campo }}">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-grip-vertical drag-handle"></i>
                                
                                <div class="flex-grow-1">
                                    <div class="campo-header">
                                        <div>
                                            <h5 class="mb-1">
                                                {{ $campo->Etiqueta }}
                                                @if($campo->Es_Requerido)
                                                    <span class="campo-badge badge-requerido">Requerido</span>
                                                @else
                                                    <span class="campo-badge badge-opcional">Opcional</span>
                                                @endif
                                                @if(!$campo->Activo)
                                                    <span class="campo-badge badge-inactivo">Inactivo</span>
                                                @endif
                                            </h5>
                                            <div class="text-muted small">
                                                <strong>Nombre:</strong> {{ $campo->Nombre_Campo }} | 
                                                <strong>Tipo:</strong> {{ $tiposInput[$campo->Tipo_Input] ?? $campo->Tipo_Input }} | 
                                                <strong>Orden:</strong> {{ $campo->Orden }}
                                            </div>
                                            @if($campo->Placeholder)
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-quote-left me-1"></i>{{ $campo->Placeholder }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-warning btn-icon" 
                                            onclick="editarCampo({{ $campo->Id_Campo }})"
                                            title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm {{ $campo->Activo ? 'btn-secondary' : 'btn-success' }} btn-icon" 
                                            onclick="toggleCampoEstatus({{ $campo->Id_Campo }})"
                                            title="{{ $campo->Activo ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas {{ $campo->Activo ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-icon" 
                                            onclick="eliminarCampo({{ $campo->Id_Campo }})"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>No hay campos configurados</h4>
                    <p class="text-muted">Agrega el primer campo para este tipo de levantamiento</p>
                    <button class="btn btn-danger" onclick="abrirModalCrearCampo()">
                        <i class="fas fa-plus me-2"></i>Crear Primer Campo
                    </button>
                </div>
            @endif
        </div>
    </main>

    <!-- Botón Flotante -->
    @if($campos->count() > 0)
        <button class="btn-crear-campo" onclick="abrirModalCrearCampo()" title="Agregar Campo">
            <i class="fas fa-plus"></i>
        </button>
    @endif

    <!-- Modal Crear/Editar Campo -->
    <div class="modal fade" id="modalCampo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalCampoTitle">
                        <i class="fas fa-plus-circle me-2"></i>Nuevo Campo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCampo">
                    <div class="modal-body">
                        <input type="hidden" id="campoId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre del Campo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_campo" required 
                                       placeholder="Ej: descripcion_trabajo">
                                <small class="text-muted">Sin espacios, usa guiones bajos</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Etiqueta <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="etiqueta" required 
                                       placeholder="Ej: Descripción del Trabajo">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tipo de Input <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_input" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($tiposInput as $valor => $nombre)
                                        <option value="{{ $valor }}">{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Orden <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="orden" required min="0" value="{{ $campos->count() + 1 }}">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Placeholder</label>
                                <input type="text" class="form-control" id="placeholder" 
                                       placeholder="Texto de ayuda para el usuario">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Valor por Defecto</label>
                                <input type="text" class="form-control" id="valor_default" 
                                       placeholder="Valor inicial opcional">
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="es_requerido">
                                    <label class="form-check-label" for="es_requerido">
                                        <i class="fas fa-asterisk text-danger me-1"></i>
                                        Campo obligatorio
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger" id="btnGuardarCampo">
                            <i class="fas fa-save me-2"></i>Guardar Campo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const tipoId = {{ $tipo->Id_Tipo_Levantamiento }};
        let modalCampo;
        let sortable;

        // Toggle sidebar
        document.getElementById('toggleBtn').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        });

        // Inicializar SortableJS para drag & drop
        document.addEventListener('DOMContentLoaded', function() {
            const camposList = document.getElementById('camposList');
            if (camposList) {
                sortable = new Sortable(camposList, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        reordenarCampos();
                    }
                });
            }

            modalCampo = new bootstrap.Modal(document.getElementById('modalCampo'));
        });

        // Abrir modal crear campo
        function abrirModalCrearCampo() {
            document.getElementById('modalCampoTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Nuevo Campo';
            document.getElementById('formCampo').reset();
            document.getElementById('campoId').value = '';
            document.getElementById('orden').value = {{ $campos->count() + 1 }};
            modalCampo.show();
        }

        // Editar campo
        function editarCampo(id) {
            // Aquí deberías hacer un fetch para obtener los datos del campo
            Swal.fire('Función de edición', 'Por implementar', 'info');
        }

        // Toggle estatus campo
        function toggleCampoEstatus(campoId) {
            Swal.fire({
                title: '¿Cambiar estatus?',
                text: 'Esto activará o desactivará el campo',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/tipos-levantamiento/${tipoId}/campos/${campoId}/toggle`, {
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

        // Eliminar campo
        function eliminarCampo(campoId) {
            Swal.fire({
                title: '¿Eliminar campo?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/tipos-levantamiento/${tipoId}/campos/${campoId}`, {
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

        // Guardar campo
        document.getElementById('formCampo').addEventListener('submit', function(e) {
            e.preventDefault();

            const campoId = document.getElementById('campoId').value;
            const isEdit = campoId !== '';
            
            const formData = new FormData();
            formData.append('nombre_campo', document.getElementById('nombre_campo').value);
            formData.append('etiqueta', document.getElementById('etiqueta').value);
            formData.append('tipo_input', document.getElementById('tipo_input').value);
            formData.append('orden', document.getElementById('orden').value);
            formData.append('placeholder', document.getElementById('placeholder').value);
            formData.append('valor_default', document.getElementById('valor_default').value);
            if (document.getElementById('es_requerido').checked) {
                formData.append('es_requerido', '1');
            }

            const url = isEdit 
                ? `/admin/tipos-levantamiento/${tipoId}/campos/${campoId}`
                : `/admin/tipos-levantamiento/${tipoId}/campos`;
            
            if (isEdit) {
                formData.append('_method', 'PUT');
            }

            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    modalCampo.hide();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Error al guardar', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Ocurrió un error', 'error');
            });
        });

        // Reordenar campos
        function reordenarCampos() {
            const items = document.querySelectorAll('.campo-item');
            const orden = Array.from(items).map(item => parseInt(item.dataset.id));

            fetch(`/admin/tipos-levantamiento/${tipoId}/campos/reordenar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ orden: orden })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Campos reordenados',
                        timer: 1000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo reordenar', 'error');
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