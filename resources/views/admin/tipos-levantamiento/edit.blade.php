<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Tipo - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed: 70px;
            --admin-color: #1D67A8;
            --admin-dark: #1D67A8;
        }
        body { background: #f5f7fa; overflow-x: hidden; }
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
        .main-content { margin-left: var(--sidebar-width); transition: margin-left 0.3s ease; padding: 20px; }
        .main-content.expanded { margin-left: var(--sidebar-collapsed); }
        .top-bar { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .toggle-btn { background: var(--admin-color); color: white; border: none; width: 40px; height: 40px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
        .toggle-btn:hover { background: var(--admin-dark); }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--admin-color), var(--admin-dark)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        .admin-badge { background: var(--admin-color); color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .form-card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .form-card h2 { margin-bottom: 25px; color: #2c3e50; font-size: 24px; font-weight: 600; }
        .icon-selector { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; max-height: 400px; overflow-y: auto; padding: 15px; border: 1px solid #dee2e6; border-radius: 8px; background: #f8f9fa; }
        .icon-option { padding: 15px; border: 2px solid #dee2e6; border-radius: 8px; text-align: center; cursor: pointer; transition: all 0.3s; background: white; }
        .icon-option:hover { border-color: var(--admin-color); transform: translateY(-2px); }
        .icon-option.selected { border-color: var(--admin-color); background: #fff5f5; }
        .icon-option i { font-size: 28px; color: #495057; margin-bottom: 5px; }
        .icon-option small { display: block; font-size: 10px; color: #6c757d; }
        .form-actions { display: flex; gap: 10px; margin-top: 30px; }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><i class="fas fa-clipboard-list fa-2x mb-2"></i><h4>Sistema</h4></div>
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
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="menu-link w-100 border-0 bg-transparent"><i class="fas fa-sign-out-alt menu-icon"></i><span class="menu-text">Cerrar Sesión</span></button></form>
        </div>
    </aside>

    <main class="main-content" id="mainContent">
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
            <div class="user-info">
                <span class="user-name">{{ $usuario->Nombres }} {{ $usuario->ApellidosPat }}</span>
                <div class="user-avatar">{{ strtoupper(substr($usuario->Nombres, 0, 1)) }}</div>
                <span class="admin-badge">ADMIN</span>
            </div>
        </div>

        <div class="form-card">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.tipos-levantamiento.index') }}" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
                <h2 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i>Editar Tipo de Levantamiento</h2>
            </div>

            <form id="formEditarTipo">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label fw-bold">Nombre del Tipo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $tipo->Nombre }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Descripción (Opcional)</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ $tipo->Descripcion }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Seleccionar Icono <span class="text-danger">*</span></label>
                    <input type="hidden" id="icono" name="icono" value="{{ $tipo->Icono }}" required>
                    <div class="icon-selector" id="iconSelector">
                        @foreach($iconos as $clase => $nombre)
                            <div class="icon-option {{ $clase === $tipo->Icono ? 'selected' : '' }}" data-icon="{{ $clase }}" onclick="seleccionarIcono(this)">
                                <i class="fas {{ $clase }}"></i>
                                <small>{{ $nombre }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-warning flex-grow-1"><i class="fas fa-save me-2"></i>Actualizar Tipo</button>
                    <a href="{{ route('admin.tipos-levantamiento.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const tipoId = {{ $tipo->Id_Tipo_Levantamiento }};

        document.getElementById('toggleBtn').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        });

        function seleccionarIcono(element) {
            document.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('icono').value = element.dataset.icon;
        }

        document.getElementById('formEditarTipo').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            Swal.fire({ title: 'Actualizando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

            fetch(`/admin/tipos-levantamiento/${tipoId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '¡Éxito!', text: data.message }).then(() => {
                        window.location.href = '{{ route('admin.tipos-levantamiento.index') }}';
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al actualizar' });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un error' });
            });
        });
    </script>
</body>
</html>