<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Cliente - {{ $cliente->Nombre }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root { --sidebar-width:250px; --sidebar-collapsed-width:70px; --user-color:#0627bb; --user-dark:#1066d8; }
        body { background:#f5f7fa; overflow-x:hidden; }

        .sidebar { position:fixed; top:0; left:0; height:100vh; width:var(--sidebar-width); background:linear-gradient(135deg,var(--user-color) 0%,var(--user-dark) 100%); transition:all .3s ease; z-index:1000; box-shadow:2px 0 10px rgba(0,0,0,.1); }
        .sidebar.collapsed { width:var(--sidebar-collapsed-width); }
        .sidebar-header { padding:20px; text-align:center; color:white; border-bottom:1px solid rgba(255,255,255,.1); }
        .sidebar-header h4 { margin:0; font-size:18px; white-space:nowrap; overflow:hidden; }
        .sidebar.collapsed .sidebar-header h4, .sidebar.collapsed .menu-text { opacity:0; width:0; }
        .sidebar-menu { list-style:none; padding:0; margin:20px 0; }
        .menu-item { margin:5px 0; }
        .menu-link { display:flex; align-items:center; padding:15px 20px; color:rgba(255,255,255,.8); text-decoration:none; transition:all .3s; position:relative; }
        .menu-link:hover { background:rgba(255,255,255,.1); color:white; }
        .menu-link.active { background:rgba(255,255,255,.2); color:white; border-left:4px solid white; }
        .menu-link.locked { opacity:.5; cursor:not-allowed; }
        .menu-link.locked:hover { background:transparent; }
        .lock-icon { position:absolute; right:15px; font-size:14px; color:rgba(255,255,255,.6); }
        .menu-icon { width:30px; text-align:center; font-size:20px; }
        .menu-text { margin-left:15px; white-space:nowrap; transition:opacity .3s; }
        .sidebar-footer { position:absolute; bottom:0; width:100%; padding:20px; border-top:1px solid rgba(255,255,255,.1); }

        .main-content { margin-left:var(--sidebar-width); transition:margin-left .3s ease; padding:20px; }
        .main-content.expanded { margin-left:var(--sidebar-collapsed-width); }
        .top-bar { background:white; padding:15px 20px; border-radius:10px; margin-bottom:30px; box-shadow:0 2px 4px rgba(0,0,0,.1); display:flex; justify-content:space-between; align-items:center; }
        .toggle-btn { background:var(--user-color); color:white; border:none; width:40px; height:40px; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .3s; }
        .toggle-btn:hover { background:var(--user-dark); }
        .user-info { display:flex; align-items:center; gap:15px; }
        .user-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,var(--user-color),var(--user-dark)); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; }

        .card-custom { background:white; border-radius:15px; box-shadow:0 2px 12px rgba(0,0,0,.08); overflow:hidden; margin-bottom:30px; }
        .card-header-custom { background:linear-gradient(135deg,var(--user-color),var(--user-dark)); color:white; padding:25px 30px; }
        .card-header-custom h4 { margin:0; display:flex; align-items:center; gap:10px; }
        .card-body-custom { padding:30px; }
        .section-title { font-size:18px; font-weight:600; color:#2c3e50; margin-bottom:20px; padding-bottom:10px; border-bottom:2px solid #f0f0f0; display:flex; align-items:center; gap:10px; }
        .form-label { font-weight:600; color:#495057; margin-bottom:8px; }
        .required::after { content:" *"; color:#dc3545; }
        .form-control, .form-select { border:1px solid #e0e0e0; border-radius:8px; padding:10px 15px; transition:all .3s; }
        .form-control:focus, .form-select:focus { border-color:var(--user-color); box-shadow:0 0 0 .2rem rgba(6,39,187,.15); }

        .articulos-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:15px; margin-top:15px; }
        .articulo-item { border:2px solid #e0e0e0; border-radius:10px; padding:15px; transition:all .3s; cursor:pointer; position:relative; }
        .articulo-item:hover { border-color:var(--user-color); background:#f8f9fa; }
        .articulo-item.selected { border-color:var(--user-color); background:linear-gradient(135deg,rgba(6,39,187,.05),rgba(16,102,216,.05)); }
        .articulo-item.principal { border-color:#28a745; background:linear-gradient(135deg,rgba(40,167,69,.05),rgba(40,167,69,.1)); }
        .check-icon { position:absolute; top:10px; right:10px; width:24px; height:24px; border-radius:50%; background:white; border:2px solid #e0e0e0; display:flex; align-items:center; justify-content:center; transition:all .3s; }
        .articulo-item.selected .check-icon { background:var(--user-color); border-color:var(--user-color); color:white; }
        .articulo-item.principal .check-icon { background:#28a745; border-color:#28a745; color:white; }
        .articulo-nombre { font-weight:600; color:#2c3e50; margin-bottom:5px; }
        .articulo-detalles { font-size:13px; color:#6c757d; }
        .principal-badge { position:absolute; top:10px; left:10px; background:#28a745; color:white; padding:3px 10px; border-radius:15px; font-size:11px; font-weight:600; }
        .alert-info-custom { background:linear-gradient(135deg,rgba(6,39,187,.05),rgba(16,102,216,.05)); border-left:4px solid var(--user-color); border-radius:8px; padding:15px; margin-bottom:20px; }
        .counter-badge { background:var(--user-color); color:white; padding:5px 12px; border-radius:20px; font-size:13px; font-weight:600; }
        .btn-primary-custom { background:linear-gradient(135deg,var(--user-color),var(--user-dark)); border:none; padding:12px 30px; border-radius:8px; font-weight:600; transition:all .3s; }
        .btn-primary-custom:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(6,39,187,.3); }
        .btn-secondary-custom { background:#6c757d; border:none; padding:12px 30px; border-radius:8px; font-weight:600; color:white; }
        .btn-secondary-custom:hover { background:#5a6268; }

        .mobile-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:999; }
        .mobile-overlay.active { display:block; }
        @media (max-width:768px) {
            .sidebar { transform:translateX(-100%); }
            .sidebar.mobile-open { transform:translateX(0); }
            .main-content { margin-left:0 !important; }
        }
    </style>
</head>
<body>
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
                    <i class="fas fa-home menu-icon"></i><span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('usuario.levantamientos.index') }}" class="menu-link">
                    <i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Mis Levantamientos</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('usuario.clientesU') }}" class="menu-link active">
                    <i class="fas fa-users menu-icon"></i><span class="menu-text">Clientes</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('usuario.tipos-levantamiento.index') }}" class="menu-link {{ $usuario->Permisos !== 'si' ? 'locked' : '' }}"
                   @if($usuario->Permisos !== 'si') onclick="return false;" @endif>
                    <i class="fas fa-cogs menu-icon"></i><span class="menu-text">Tipos de Levantamiento</span>
                    @if($usuario->Permisos !== 'si')<i class="fas fa-lock lock-icon"></i>@endif
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-link w-100 border-0 bg-transparent">
                    <i class="fas fa-sign-out-alt menu-icon"></i><span class="menu-text">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <main class="main-content" id="mainContent">
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
            <div class="user-info">
                <span>{{ $usuario->Nombres }} {{ $usuario->ApellidosPat }}</span>
                <div class="user-avatar">{{ strtoupper(substr($usuario->Nombres, 0, 1)) }}</div>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header-custom">
                <h4><i class="fas fa-edit"></i> Editar Cliente: {{ $cliente->Nombre }}</h4>
            </div>
            <div class="card-body-custom">
                <form id="clienteForm" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- INFORMACIÓN GENERAL --}}
                    <div class="section-title"><i class="fas fa-info-circle"></i>Información General</div>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Nombre del Cliente</label>
                            <input type="text" class="form-control" name="nombre" value="{{ $cliente->Nombre }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="{{ $cliente->Telefono }}" maxlength="25" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo" value="{{ $cliente->Correo }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    {{-- DIRECCIÓN --}}
                    <div class="section-title"><i class="fas fa-map-marker-alt"></i>Dirección</div>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">País</label>
                            <input type="text" class="form-control" name="pais" value="{{ $cliente->Pais }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Estado</label>
                            <input type="text" class="form-control" name="estado" value="{{ $cliente->Estado }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Ciudad</label>
                            <input type="text" class="form-control" name="municipio" value="{{ $cliente->Municipio }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Código Postal</label>
                            <input type="number" class="form-control" name="codigo_postal" value="{{ $cliente->Codigo_Postal }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Colonia</label>
                            <input type="text" class="form-control" name="colonia" value="{{ $cliente->Colonia }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Calle</label>
                            <input type="text" class="form-control" name="calle" value="{{ $cliente->calle }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Exterior</label>
                            <input type="number" class="form-control" name="no_externo" value="{{ $cliente->No_Ex }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Interior</label>
                            <input type="number" class="form-control" name="no_interno" value="{{ $cliente->No_In }}">
                        </div>
                    </div>

                    {{-- ARTÍCULOS --}}
                    <div class="section-title">
                        <i class="fas fa-box"></i>Artículos del Cliente
                        <span class="ms-auto counter-badge" id="articulosCounter">0/10 seleccionados</span>
                    </div>
                    <div class="alert-info-custom">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instrucciones:</strong> Haz clic en un artículo para marcarlo como principal (opcional).
                        Doble clic para cambiar el principal. Puedes seleccionar hasta 9 artículos adicionales.
                    </div>
                    <input type="hidden" name="articulo_principal" id="articulo_principal">
                    <div class="articulos-grid">
                        @foreach($articulos as $articulo)
                        <div class="articulo-item"
                             data-articulo-id="{{ $articulo->Id_Articulos }}"
                             @if($articuloPrincipal == $articulo->Id_Articulos) data-es-principal="true" @endif
                             @if(in_array($articulo->Id_Articulos, $clienteArticulos)) data-seleccionado="true" @endif>
                            <div class="check-icon"><i class="fas fa-check"></i></div>
                            <div class="articulo-nombre">{{ $articulo->Nombre }}</div>
                            <div class="articulo-detalles">
                                <i class="fas fa-copyright me-1"></i>{{ $articulo->Marca }}<br>
                                <i class="fas fa-tag me-1"></i>{{ $articulo->Modelo ?? 'Sin modelo' }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-4 border-top">
                        <a href="{{ route('usuario.clientesU') }}" class="btn btn-secondary-custom">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-save me-2"></i>Actualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // ── Sidebar ──────────────────────────────────────────────────────────
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

        // ── Artículos con valores previos ────────────────────────────────────
        let articuloPrincipal = {{ $articuloPrincipal ?? 'null' }};
        let articulosAdicionales = @json(array_values(array_diff($clienteArticulos, [$articuloPrincipal ?? 0])));
        const MAX_ARTICULOS = 10;

        $(document).ready(function () {
            $('.articulo-item').each(function () {
                const id = $(this).data('articulo-id');
                if ($(this).data('es-principal')) {
                    $(this).addClass('principal').prepend('<span class="principal-badge">Principal</span>');
                    $('#articulo_principal').val(id);
                } else if ($(this).data('seleccionado')) {
                    $(this).addClass('selected');
                }
            });
            actualizarContador();
        });

        $('.articulo-item').click(function () {
            const id = $(this).data('articulo-id');
            if (articuloPrincipal === id) return;
            if (articuloPrincipal === null) { establecerPrincipal(id); return; }
            if (articulosAdicionales.includes(id)) {
                articulosAdicionales = articulosAdicionales.filter(x => x !== id);
                $(this).removeClass('selected');
            } else {
                if ((articulosAdicionales.length + 1) >= MAX_ARTICULOS) {
                    Swal.fire({ icon:'warning', title:'Límite alcanzado', text:'Solo puedes seleccionar hasta 10 artículos en total', confirmButtonColor:'#0627bb' });
                    return;
                }
                articulosAdicionales.push(id);
                $(this).addClass('selected');
            }
            actualizarContador();
        });

        $('.articulo-item').dblclick(function () { establecerPrincipal($(this).data('articulo-id')); });

        function establecerPrincipal(id) {
            if (articuloPrincipal !== null) {
                $(`.articulo-item[data-articulo-id="${articuloPrincipal}"]`).removeClass('principal');
                $(`.articulo-item[data-articulo-id="${articuloPrincipal}"] .principal-badge`).remove();
                if (!articulosAdicionales.includes(articuloPrincipal) && articulosAdicionales.length < MAX_ARTICULOS - 1) {
                    articulosAdicionales.push(articuloPrincipal);
                    $(`.articulo-item[data-articulo-id="${articuloPrincipal}"]`).addClass('selected');
                }
            }
            articulosAdicionales = articulosAdicionales.filter(x => x !== id);
            articuloPrincipal = id;
            $(`.articulo-item[data-articulo-id="${id}"]`)
                .removeClass('selected')
                .addClass('principal')
                .prepend('<span class="principal-badge">Principal</span>');
            $('#articulo_principal').val(id);
            actualizarContador();
        }

        function actualizarContador() {
            $('#articulosCounter').text(`${(articuloPrincipal ? 1 : 0) + articulosAdicionales.length}/${MAX_ARTICULOS} seleccionados`);
        }

        // ── Mapa campo → selector input ──────────────────────────────────────
        const mapaInputs = {
            'nombre':   '[name="nombre"]',
            'teléfono': '[name="telefono"]',
            'correo':   '[name="correo"]'
        };

        // ── Manejo errores duplicados ─────────────────────────────────────────
        function manejarErrorAjax(xhr) {
            Swal.close();
            if (xhr.status === 422) {
                const resp = xhr.responseJSON;

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                if (resp.campos_duplicados && resp.campos_duplicados.length > 0) {
                    resp.campos_duplicados.forEach(campo => {
                        const selector = mapaInputs[campo];
                        if (selector) {
                            $(selector).addClass('is-invalid');
                            $(selector).siblings('.invalid-feedback').text(`Este ${campo} ya está registrado.`);
                        }
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Cliente duplicado',
                        html: `<p>${resp.message}</p><p class="text-muted small">Verifica los campos marcados en rojo.</p>`,
                        confirmButtonColor: '#dc3545'
                    });
                } else if (resp.errors) {
                    Object.keys(resp.errors).forEach(f => {
                        $(`[name="${f}"]`).addClass('is-invalid')
                                          .siblings('.invalid-feedback')
                                          .text(resp.errors[f][0]);
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de validación',
                        text: 'Por favor corrige los errores en el formulario.',
                        confirmButtonColor: '#dc3545'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: resp.message || 'Error de validación.', confirmButtonColor: '#dc3545' });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Ocurrió un error al actualizar el cliente.',
                    confirmButtonColor: '#dc3545'
                });
            }
        }

        // ── Submit ───────────────────────────────────────────────────────────
        $('#clienteForm').submit(function (e) {
            e.preventDefault();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            let hayErrores = false;
            [
                { name: 'nombre',        label: 'El nombre es obligatorio' },
                { name: 'telefono',      label: 'El teléfono es obligatorio' },
                { name: 'pais',          label: 'El país es obligatorio' },
                { name: 'estado',        label: 'El estado es obligatorio' },
                { name: 'municipio',     label: 'La ciudad es obligatoria' },
                { name: 'codigo_postal', label: 'El código postal es obligatorio' }
            ].forEach(c => {
                const $i = $(`[name="${c.name}"]`);
                if (!$i.val() || !$i.val().trim()) {
                    $i.addClass('is-invalid');
                    $i.siblings('.invalid-feedback').text(c.label);
                    hayErrores = true;
                }
            });

            if (hayErrores) {
                Swal.fire({ icon:'warning', title:'Campos obligatorios', text:'Por favor completa todos los campos marcados con *', confirmButtonColor:'#0627bb' });
                return;
            }

            const formData = new FormData(this);
            articulosAdicionales.forEach(id => formData.append('articulos_adicionales[]', id));

            Swal.fire({ title:'Actualizando cliente...', allowOutsideClick:false, didOpen:() => Swal.showLoading() });

            $.ajax({
                url: '{{ route("usuario.clientes.update", $cliente->Id_Cliente) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: r => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cliente Actualizado!',
                        text: r.message,
                        confirmButtonColor: '#0627bb'
                    }).then(() => window.location.href = '{{ route("usuario.clientesU") }}');
                },
                error: xhr => manejarErrorAjax(xhr)
            });
        });
    </script>
</body>
</html>