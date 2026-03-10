<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nuevo Producto - Sistema de Levantamientos</title>
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

        <!-- Breadcrumb -->
        <div class="welcome-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-plus-circle me-2"></i>Nuevo Producto</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.productos.index') }}" class="text-white">Productos</a></li>
                            <li class="breadcrumb-item active text-white-50">Nuevo</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Datos del Producto</h5>
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

                        {{-- Alerta de duplicado producto (se muestra en tiempo real) --}}
                        <div id="alertaDuplicado" class="alert alert-warning d-none" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Producto duplicado:</strong> Ya existe un producto con ese mismo
                            <strong>Nombre</strong>, <strong>Marca</strong> y <strong>Modelo</strong>.
                            Cambia al menos uno de los tres campos.
                        </div>

                        <form action="{{ route('admin.productos.store') }}" method="POST" id="formProducto">
                            @csrf

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
                                       value="{{ old('nombre') }}"
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
                                          placeholder="Especificaciones técnicas, características del producto...">{{ old('descripcion') }}</textarea>
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
                                                    {{ old('marca_id') == $marca->Id_Marca ? 'selected' : '' }}>
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
                                                    {{ old('modelo_id') == $modelo->Id_Modelo ? 'selected' : '' }}>
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

                            <!-- Botones -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-danger" id="btnGuardar">
                                    <i class="fas fa-save me-2"></i>Guardar Producto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ══════════════════════════════════════════
         MODAL NUEVA MARCA
    ══════════════════════════════════════════ -->
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
                            <input type="text" class="form-control" id="nombre_marca"
                                   required maxlength="100" placeholder="Ej. Hikvision">
                            <div id="errorMarcaDuplicado" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white" id="btnGuardarMarca">
                            <i class="fas fa-save me-2"></i>Guardar Marca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         MODAL NUEVO MODELO
    ══════════════════════════════════════════ -->
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
                            <input type="text" class="form-control" id="nombre_modelo"
                                   required maxlength="100" placeholder="Ej. DS-2CE16K0T-LTS">
                            <div id="errorModeloDuplicado" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarModelo">
                            <i class="fas fa-save me-2"></i>Guardar Modelo
                        </button>
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

        // ══════════════════════════════════════════
        // Sidebar
        // ══════════════════════════════════════════
        const sidebar       = document.getElementById('sidebar');
        const mainContent   = document.getElementById('mainContent');
        const toggleBtn     = document.getElementById('toggleBtn');
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

        // ══════════════════════════════════════════
        // Contadores de caracteres
        // ══════════════════════════════════════════
        function actualizarContador(inputId, contadorId) {
            const input    = document.getElementById(inputId);
            const contador = document.getElementById(contadorId);
            const max      = parseInt(input.getAttribute('maxlength'));
            function update() {
                const len = input.value.length;
                contador.textContent = `${len} / ${max}`;
                contador.className   = len > max * 0.9 ? 'text-warning fw-bold' : 'text-muted';
                if (len >= max) contador.className = 'text-danger fw-bold';
            }
            input.addEventListener('input', update);
            update();
        }
        actualizarContador('nombre',      'contadorNombre');
        actualizarContador('descripcion', 'contadorDescripcion');

        // ══════════════════════════════════════════
        // Verificación de duplicado de PRODUCTO en tiempo real
        // ══════════════════════════════════════════
        const URL_CHECK_DUP = '{{ route('admin.productos.check-duplicado') }}';
        let checkDupTimeout = null;

        function verificarDuplicado() {
            const nombre   = document.getElementById('nombre').value.trim();
            const marcaId  = document.getElementById('marca_id').value;
            const modeloId = document.getElementById('modelo_id').value;

            if (!nombre || !marcaId || !modeloId) {
                ocultarAlertaDuplicado();
                return;
            }

            clearTimeout(checkDupTimeout);
            checkDupTimeout = setTimeout(function () {
                $.get(URL_CHECK_DUP, { nombre, marca_id: marcaId, modelo_id: modeloId })
                    .done(function (data) {
                        data.duplicado ? mostrarAlertaDuplicado() : ocultarAlertaDuplicado();
                    })
                    .fail(function () {
                        ocultarAlertaDuplicado();
                    });
            }, 500);
        }

        function mostrarAlertaDuplicado() {
            document.getElementById('alertaDuplicado').classList.remove('d-none');
            document.getElementById('btnGuardar').disabled = true;
            document.getElementById('nombre').classList.add('is-invalid');
        }

        function ocultarAlertaDuplicado() {
            document.getElementById('alertaDuplicado').classList.add('d-none');
            document.getElementById('btnGuardar').disabled = false;
            document.getElementById('nombre').classList.remove('is-invalid');
        }

        document.getElementById('nombre').addEventListener('input',    verificarDuplicado);
        document.getElementById('marca_id').addEventListener('change',  verificarDuplicado);
        document.getElementById('modelo_id').addEventListener('change', verificarDuplicado);

        // ══════════════════════════════════════════
        // Validación y envío del formulario principal
        // ══════════════════════════════════════════
        document.getElementById('formProducto').addEventListener('submit', async function (e) {
            e.preventDefault();

            const nombre      = document.getElementById('nombre').value;
            const descripcion = document.getElementById('descripcion').value;

            if (nombre.length > 500) {
                Swal.fire('Error', 'El nombre no puede superar los 500 caracteres.', 'error');
                return;
            }
            if (descripcion.length > 500) {
                Swal.fire('Error', 'La descripción no puede superar los 500 caracteres.', 'error');
                return;
            }

            const marcaId  = document.getElementById('marca_id').value;
            const modeloId = document.getElementById('modelo_id').value;

            if (nombre && marcaId && modeloId) {
                try {
                    const resp = await $.get(URL_CHECK_DUP, { nombre, marca_id: marcaId, modelo_id: modeloId });
                    if (resp.duplicado) {
                        mostrarAlertaDuplicado();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Producto duplicado',
                            text: 'Ya existe un producto con ese mismo Nombre, Marca y Modelo.',
                            confirmButtonColor: '#dc3545'
                        });
                        return;
                    }
                } catch (err) {
                    // Si falla el check AJAX, el servidor lo validará igual
                }
            }

            document.getElementById('btnGuardar').disabled = true;
            document.getElementById('btnGuardar').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
            this.submit();
        });

        // ══════════════════════════════════════════
        // Helpers para errores en modales
        // IMPORTANTE: limpiarEstilosError NUNCA toca input.value
        // ══════════════════════════════════════════
        function limpiarEstilosError(inputId, errorDivId) {
            const input = document.getElementById(inputId);
            if (input) input.classList.remove('is-invalid');
            const errorDiv = document.getElementById(errorDivId);
            if (errorDiv) errorDiv.textContent = '';
        }

        function mostrarErrorModal(inputId, errorDivId, mensaje) {
            const input = document.getElementById(inputId);
            if (input) input.classList.add('is-invalid');
            const errorDiv = document.getElementById(errorDivId);
            if (errorDiv) errorDiv.textContent = mensaje;
        }

        // ══════════════════════════════════════════
        // MODAL MARCA
        // ══════════════════════════════════════════

        // Al ABRIR el modal: limpiar valor y estilos
        document.getElementById('modalCrearMarca').addEventListener('show.bs.modal', function () {
            document.getElementById('nombre_marca').value = '';
            limpiarEstilosError('nombre_marca', 'errorMarcaDuplicado');
        });

        // Al ENVIAR el form de marca
        document.getElementById('formMarca').addEventListener('submit', function (e) {
            e.preventDefault();

            // ✅ Paso 1: leer el valor PRIMERO, antes de cualquier limpieza
            const nombreValor = document.getElementById('nombre_marca').value.trim();

            // ✅ Paso 2: limpiar solo estilos (NO el valor)
            limpiarEstilosError('nombre_marca', 'errorMarcaDuplicado');

            // Validación mínima client-side
            if (!nombreValor) {
                mostrarErrorModal('nombre_marca', 'errorMarcaDuplicado', 'El nombre de la marca es obligatorio.');
                return;
            }

            const btnGuardarMarca = document.getElementById('btnGuardarMarca');
            btnGuardarMarca.disabled = true;
            btnGuardarMarca.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

            $.ajax({
                url:  '{{ route('admin.marcas.store') }}',
                type: 'POST',
                data: { nombre_marca: nombreValor },  // ✅ usar la variable ya leída
                success(data) {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalCrearMarca')).hide();
                        const select = document.getElementById('marca_id');
                        const option = new Option(data.marca.Nombre, data.marca.Id_Marca, true, true);
                        select.appendChild(option);
                        Swal.fire({
                            icon: 'success',
                            title: '¡Marca creada!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        verificarDuplicado();
                    }
                },
                error(xhr) {
                    const msg = xhr.responseJSON?.message || 'Error al guardar la marca';
                    if (xhr.status === 422) {
                        // Mostrar error inline debajo del input
                        mostrarErrorModal('nombre_marca', 'errorMarcaDuplicado', msg);
                    } else {
                        Swal.fire('Error', msg, 'error');
                    }
                },
                complete() {
                    btnGuardarMarca.disabled = false;
                    btnGuardarMarca.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Marca';
                }
            });
        });

        // ══════════════════════════════════════════
        // MODAL MODELO
        // ══════════════════════════════════════════

        // Al ABRIR el modal: limpiar valor y estilos
        document.getElementById('modalCrearModelo').addEventListener('show.bs.modal', function () {
            document.getElementById('nombre_modelo').value = '';
            limpiarEstilosError('nombre_modelo', 'errorModeloDuplicado');
        });

        // Al ENVIAR el form de modelo
        document.getElementById('formModelo').addEventListener('submit', function (e) {
            e.preventDefault();

            // ✅ Paso 1: leer el valor PRIMERO, antes de cualquier limpieza
            const nombreValor = document.getElementById('nombre_modelo').value.trim();

            // ✅ Paso 2: limpiar solo estilos (NO el valor)
            limpiarEstilosError('nombre_modelo', 'errorModeloDuplicado');

            // Validación mínima client-side
            if (!nombreValor) {
                mostrarErrorModal('nombre_modelo', 'errorModeloDuplicado', 'El nombre del modelo es obligatorio.');
                return;
            }

            const btnGuardarModelo = document.getElementById('btnGuardarModelo');
            btnGuardarModelo.disabled = true;
            btnGuardarModelo.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

            $.ajax({
                url:  '{{ route('admin.modelos.store') }}',
                type: 'POST',
                data: { nombre_modelo: nombreValor },  // ✅ usar la variable ya leída
                success(data) {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalCrearModelo')).hide();
                        const select = document.getElementById('modelo_id');
                        const option = new Option(data.modelo.Nombre, data.modelo.Id_Modelo, true, true);
                        select.appendChild(option);
                        Swal.fire({
                            icon: 'success',
                            title: '¡Modelo creado!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        verificarDuplicado();
                    }
                },
                error(xhr) {
                    const msg = xhr.responseJSON?.message || 'Error al guardar el modelo';
                    if (xhr.status === 422) {
                        // Mostrar error inline debajo del input
                        mostrarErrorModal('nombre_modelo', 'errorModeloDuplicado', msg);
                    } else {
                        Swal.fire('Error', msg, 'error');
                    }
                },
                complete() {
                    btnGuardarModelo.disabled = false;
                    btnGuardarModelo.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Modelo';
                }
            });
        });
    </script>
</body>
</html>