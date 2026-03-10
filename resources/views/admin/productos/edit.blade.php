<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Producto - Sistema de Levantamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboardAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/productos_admin.css') }}">
</head>
<body>
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
            <li class="menu-item"><a href="{{ route('admin.levantamientos.index') }}" class="menu-link"><i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Levantamientos</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.clientes.index') }}" class="menu-link"><i class="fas fa-building menu-icon"></i><span class="menu-text">Clientes</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.productos.index') }}" class="menu-link active"><i class="fas fa-box menu-icon"></i><span class="menu-text">Productos</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.tipos-levantamiento.index') }}" class="menu-link"><i class="fa-solid fa-gear menu-icon"></i><span class="menu-text">Tipos de Levantamientos</span></a></li>
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
            <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->nombre_completo }}</span>
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->Nombres, 0, 1)) }}</div>
                <span class="admin-badge">ADMIN</span>
            </div>
        </div>

        <!-- Header -->
        <div class="welcome-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-edit me-2"></i>Editar Producto</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.productos.index') }}" class="text-white">Productos</a></li>
                            <li class="breadcrumb-item active text-white-50">Editar #{{ $producto->Id_Articulos }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.productos.show', $producto->Id_Articulos) }}" class="btn btn-outline-light">
                        <i class="fas fa-eye me-2"></i>Ver Detalles
                    </a>
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Editando: <strong>{{ Str::limit($producto->Nombre, 60) }}</strong>
                            <span class="badge bg-dark ms-2">#{{ $producto->Id_Articulos }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Errores de validación:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.productos.update', $producto->Id_Articulos) }}" method="POST" id="formProducto">
                            @csrf
                            @method('PUT')

                            <!-- Nombre -->
                            <div class="mb-4">
                                <label for="nombre" class="form-label fw-semibold">
                                    Nombre del Artículo <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-lg @error('nombre') is-invalid @enderror"
                                       id="nombre"
                                       name="nombre"
                                       maxlength="500"
                                       value="{{ old('nombre', $producto->Nombre) }}"
                                       placeholder="Ej. Cámara Bala TURBOHD 3K con Audio Bidireccional"
                                       required>
                                <div class="d-flex justify-content-between mt-1">
                                    <div>
                                        @error('nombre')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted" id="contadorNombre">0 / 500</small>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-4">
                                <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                          id="descripcion"
                                          name="descripcion"
                                          rows="4"
                                          maxlength="500"
                                          placeholder="Especificaciones técnicas, características del producto...">{{ old('descripcion', $producto->Descripcion) }}</textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <div>
                                        @error('descripcion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted" id="contadorDescripcion">0 / 500</small>
                                </div>
                            </div>

                            <!-- Marca y Modelo -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="marca_id" class="form-label fw-semibold">
                                        Marca <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select class="form-select @error('marca_id') is-invalid @enderror"
                                                id="marca_id" name="marca_id" required>
                                            <option value="">— Seleccione una marca —</option>
                                            @foreach($marcas as $marca)
                                                <option value="{{ $marca->Id_Marca }}"
                                                    {{ old('marca_id', $producto->Id_Marca) == $marca->Id_Marca ? 'selected' : '' }}>
                                                    {{ $marca->Nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button"
                                                data-bs-toggle="modal" data-bs-target="#modalCrearMarca"
                                                title="Nueva marca">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        @error('marca_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="modelo_id" class="form-label fw-semibold">
                                        Modelo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select class="form-select @error('modelo_id') is-invalid @enderror"
                                                id="modelo_id" name="modelo_id" required>
                                            <option value="">— Seleccione un modelo —</option>
                                            @foreach($modelos as $modelo)
                                                <option value="{{ $modelo->Id_Modelo }}"
                                                    {{ old('modelo_id', $producto->Id_Modelo) == $modelo->Id_Modelo ? 'selected' : '' }}>
                                                    {{ $modelo->Nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button"
                                                data-bs-toggle="modal" data-bs-target="#modalCrearModelo"
                                                title="Nuevo modelo">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        @error('modelo_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Info adicional -->
                            <div class="alert alert-light border mb-4">
                                <div class="row text-muted small">
                                    <div class="col-sm-6">
                                        <i class="fas fa-calendar me-1"></i>
                                        Creado: {{ \Carbon\Carbon::parse($producto->fecha_creacion)->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="col-sm-6">
                                        <i class="fas fa-shopping-cart me-1"></i>
                                        Veces solicitado: <strong>{{ $producto->veces_solicitado }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-warning" id="btnGuardar">
                                    <i class="fas fa-save me-2"></i>Actualizar Producto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL NUEVA MARCA -->
    <div class="modal fade" id="modalCrearMarca" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-tag me-2"></i>Nueva Marca</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formMarca">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Marca <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_marca" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_marca" rows="2" maxlength="250"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-2"></i>Guardar Marca</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL NUEVO MODELO -->
    <div class="modal fade" id="modalCrearModelo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Nuevo Modelo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formModelo">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_modelo" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_modelo" rows="2" maxlength="250"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Modelo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleBtn');
        const mobileOverlay = document.getElementById('mobileOverlay');
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); mobileOverlay.classList.toggle('active'); }
            else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
        });
        mobileOverlay.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); mobileOverlay.classList.remove('active'); });

        // Contadores
        function actualizarContador(inputId, contadorId) {
            const input = document.getElementById(inputId);
            const contador = document.getElementById(contadorId);
            const max = parseInt(input.getAttribute('maxlength'));
            function update() {
                const len = input.value.length;
                contador.textContent = `${len} / ${max}`;
                contador.className = len >= max ? 'text-danger fw-bold' : len > max * 0.9 ? 'text-warning fw-bold' : 'text-muted';
            }
            input.addEventListener('input', update);
            update();
        }
        actualizarContador('nombre', 'contadorNombre');
        actualizarContador('descripcion', 'contadorDescripcion');

        // Validación
        document.getElementById('formProducto').addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre').value;
            const descripcion = document.getElementById('descripcion').value;
            if (nombre.length > 500) { e.preventDefault(); Swal.fire('Error', 'El nombre no puede superar los 500 caracteres.', 'error'); return; }
            if (descripcion.length > 500) { e.preventDefault(); Swal.fire('Error', 'La descripción no puede superar los 500 caracteres.', 'error'); return; }
            document.getElementById('btnGuardar').disabled = true;
            document.getElementById('btnGuardar').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...';
        });

        // Crear marca AJAX
        document.getElementById('formMarca').addEventListener('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: `{{ route('admin.marcas.store') }}`, type: 'POST',
                data: { nombre_marca: document.getElementById('nombre_marca').value, descripcion_marca: document.getElementById('descripcion_marca').value },
                success(data) {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalCrearMarca')).hide();
                        const select = document.getElementById('marca_id');
                        const option = new Option(data.marca.Nombre, data.marca.Id_Marca, true, true);
                        select.appendChild(option);
                        Swal.fire({ icon: 'success', title: '¡Éxito!', text: data.message, timer: 1500, showConfirmButton: false });
                    }
                },
                error(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Error al guardar la marca', 'error'); }
            });
        });

        // Crear modelo AJAX
        document.getElementById('formModelo').addEventListener('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: `{{ route('admin.modelos.store') }}`, type: 'POST',
                data: { nombre_modelo: document.getElementById('nombre_modelo').value, descripcion_modelo: document.getElementById('descripcion_modelo').value },
                success(data) {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalCrearModelo')).hide();
                        const select = document.getElementById('modelo_id');
                        const option = new Option(data.modelo.Nombre, data.modelo.Id_Modelo, true, true);
                        select.appendChild(option);
                        Swal.fire({ icon: 'success', title: '¡Éxito!', text: data.message, timer: 1500, showConfirmButton: false });
                    }
                },
                error(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Error al guardar el modelo', 'error'); }
            });
        });

        @if(session('success'))
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: '{{ session("success") }}', confirmButtonColor: '#ffc107' });
        @endif
    </script>
</body>
</html>