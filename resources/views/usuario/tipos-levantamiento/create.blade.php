<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Tipo de Levantamiento - Sistema de Levantamientos</title>
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
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-check:checked + .btn-outline-primary {
            background-color: var(--bs-primary);
            color: white;
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
                        <i class="fas fa-plus-circle me-2"></i>Crear Tipo de Levantamiento
                    </h1>
                    <p class="text-muted mb-0">Complete el formulario para crear un nuevo tipo</p>
                </div>
                <a href="{{ route('usuario.tipos-levantamiento.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>

            <!-- Formulario -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form id="formTipo">
                                @csrf

                                <!-- Nombre -->
                                <div class="mb-4">
                                    <label for="nombre" class="form-label fw-semibold">
                                        Nombre del Tipo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre" 
                                           name="nombre" 
                                           placeholder="Ej: Cámaras de Seguridad, Cableado Estructurado"
                                           required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Descripción -->
                                <div class="mb-4">
                                    <label for="descripcion" class="form-label fw-semibold">
                                        Descripción
                                    </label>
                                    <textarea class="form-control" 
                                              id="descripcion" 
                                              name="descripcion" 
                                              rows="3"
                                              placeholder="Breve descripción del tipo de levantamiento"></textarea>
                                    <small class="text-muted">Máximo 255 caracteres</small>
                                </div>

                                <!-- Icono -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        Icono <span class="text-danger">*</span>
                                    </label>
                                    <div class="row g-2">
                                        @foreach($iconos as $value => $label)
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <input type="radio" 
                                                       class="btn-check" 
                                                       name="icono" 
                                                       id="icono-{{ $loop->index }}" 
                                                       value="{{ $value }}"
                                                       {{ $loop->first ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3" 
                                                       for="icono-{{ $loop->index }}">
                                                    <i class="fas {{ $value }} fa-2x mb-2"></i>
                                                    <small>{{ $label }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Vista Previa -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Vista Previa</label>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                                                    <i id="preview-icon" class="fas fa-video fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h5 id="preview-nombre" class="mb-1">Nombre del tipo</h5>
                                                    <p id="preview-descripcion" class="text-muted mb-0 small">
                                                        Descripción del tipo
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('usuario.tipos-levantamiento.index') }}" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                                        <i class="fas fa-save me-2"></i>Guardar Tipo
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Nota informativa -->
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Después de crear el tipo, podrás agregar los campos personalizados del formulario.
                    </div>
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

        // Preview en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formTipo');
            const nombreInput = document.getElementById('nombre');
            const descripcionInput = document.getElementById('descripcion');
            const iconoInputs = document.querySelectorAll('input[name="icono"]');
            
            nombreInput.addEventListener('input', function() {
                document.getElementById('preview-nombre').textContent = 
                    this.value || 'Nombre del tipo';
            });
            
            descripcionInput.addEventListener('input', function() {
                document.getElementById('preview-descripcion').textContent = 
                    this.value || 'Descripción del tipo';
            });
            
            iconoInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.checked) {
                        document.getElementById('preview-icon').className = 
                            'fas ' + this.value + ' fa-2x';
                    }
                });
            });

            // Submit del formulario
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const btnSubmit = document.getElementById('btnSubmit');
                const originalText = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

                const formData = new FormData(form);

                fetch('{{ route("usuario.tipos-levantamiento.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
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
                            window.location.href = 
                                `/usuario/tipos-levantamiento/${data.tipo_id}/campos`;
                        });
                    } else {
                        throw new Error(data.message || 'Error al guardar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Error al procesar la solicitud'
                    });
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalText;
                });
            });
        });
    </script>
</body>
</html>