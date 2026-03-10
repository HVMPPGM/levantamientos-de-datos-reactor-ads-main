<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalle Producto - Sistema de Levantamientos</title>
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
                    <h1><i class="fas fa-box me-2"></i>Detalle de Producto</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.productos.index') }}" class="text-white">Productos</a></li>
                            <li class="breadcrumb-item active text-white-50">#{{ $producto->Id_Articulos }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.productos.edit', $producto->Id_Articulos) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                    <button class="btn btn-danger" onclick="eliminarProducto({{ $producto->Id_Articulos }}, '{{ addslashes($producto->Nombre) }}')">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </button>
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <!-- Tarjeta principal -->
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Producto</h5>
                    </div>
                    <div class="card-body">
                        <h4 class="fw-bold mb-3">{{ $producto->Nombre }}</h4>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-hashtag me-1"></i>ID del Producto</small>
                                    <strong>#{{ $producto->Id_Articulos }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-calendar me-1"></i>Fecha de Creación</small>
                                    <strong>{{ \Carbon\Carbon::parse($producto->fecha_creacion)->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-tag me-1"></i>Marca</small>
                                    <span class="badge bg-info fs-6">{{ $producto->marca->Nombre }}</span>
                                    @if($producto->marca->Descripcion)
                                        <p class="text-muted small mt-1 mb-0">{{ $producto->marca->Descripcion }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-cog me-1"></i>Modelo</small>
                                    <span class="badge bg-primary fs-6">{{ $producto->modelo->Nombre }}</span>
                                    @if($producto->modelo->Descripcion)
                                        <p class="text-muted small mt-1 mb-0">{{ $producto->modelo->Descripcion }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-semibold text-muted mb-2"><i class="fas fa-align-left me-1"></i>Descripción</h6>
                            <div class="p-3 bg-light rounded">
                                @if($producto->Descripcion)
                                    <p class="mb-0">{{ $producto->Descripcion }}</p>
                                @else
                                    <p class="text-muted mb-0 fst-italic">Sin descripción registrada.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="py-3">
                            <i class="fas fa-shopping-cart fa-3x text-success mb-2"></i>
                            <h2 class="fw-bold text-success">{{ $producto->veces_solicitado }}</h2>
                            <p class="text-muted mb-0">Veces solicitado en levantamientos</p>
                        </div>
                        <hr>
                        <div>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-clock me-1"></i>
                                Registrado hace {{ \Carbon\Carbon::parse($producto->fecha_creacion)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h6>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <a href="{{ route('admin.productos.edit', $producto->Id_Articulos) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar Producto
                        </a>
                        <a href="{{ route('admin.productos.create') }}" class="btn btn-outline-danger">
                            <i class="fas fa-plus me-2"></i>Nuevo Producto
                        </a>
                        <button class="btn btn-outline-danger"
                                onclick="eliminarProducto({{ $producto->Id_Articulos }}, '{{ addslashes($producto->Nombre) }}')">
                            <i class="fas fa-trash me-2"></i>Eliminar Producto
                        </button>
                    </div>
                </div>
            </div>

            <!-- Levantamientos donde aparece -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Levantamientos Asociados</h5>
                        <span class="badge bg-light text-primary">{{ $levantamientos->count() }} levantamientos</span>
                    </div>
                    <div class="card-body p-0">
                        @if($levantamientos->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">Este producto no ha sido incluido en ningún levantamiento aún.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Levantamiento</th>
                                            <th>Cliente</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Estatus</th>
                                            <th>Fecha</th>
                                            <th class="text-center">Ver</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($levantamientos as $lev)
                                            <tr>
                                                <td>
                                                    <strong>LEV-{{ str_pad($lev->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</strong>
                                                </td>
                                                <td>{{ $lev->cliente_nombre }}</td>
                                                <td><span class="badge bg-secondary">{{ $lev->Cantidad }}</span></td>
                                                <td>
                                                    @if($lev->Precio_Unitario > 0)
                                                        ${{ number_format($lev->Precio_Unitario, 2) }}
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeClass = match($lev->estatus) {
                                                            'Completado' => 'bg-success',
                                                            'En Proceso' => 'bg-warning text-dark',
                                                            'Cancelado'  => 'bg-danger',
                                                            default      => 'bg-secondary',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $lev->estatus }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($lev->fecha_creacion)->format('d/m/Y') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.levantamientos.show', $lev->Id_Levantamiento) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </main>

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

        function eliminarProducto(id, nombre) {
            Swal.fire({
                title: '¿Eliminar producto?',
                html: `Se eliminará <strong>${nombre}</strong> permanentemente.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/productos/${id}`, type: 'DELETE',
                        success(data) {
                            if (data.success) {
                                Swal.fire('Eliminado', data.message, 'success')
                                    .then(() => window.location.href = '{{ route("admin.productos.index") }}');
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        },
                        error(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar', 'error'); }
                    });
                }
            });
        }

        @if(session('success'))
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: '{{ session("success") }}', confirmButtonColor: '#dc3545' });
        @endif
    </script>
</body>
</html>