<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LEV-{{ str_pad($levantamiento->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }} - Detalle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --sidebar-width:250px; --sidebar-collapsed-width:70px; --user-color:#667eea; --user-dark:#0749c5; }
        body { background:#f5f7fa; overflow-x:hidden; }
        .sidebar { position:fixed; top:0; left:0; height:100vh; width:var(--sidebar-width); background:linear-gradient(135deg,var(--user-color) 0%,var(--user-dark) 100%); transition:all .3s ease; z-index:1000; box-shadow:2px 0 10px rgba(0,0,0,.1); }
        .sidebar.collapsed { width:var(--sidebar-collapsed-width); }
        .sidebar-header { padding:20px; text-align:center; color:white; border-bottom:1px solid rgba(255,255,255,.1); }
        .sidebar-header h4 { margin:0; font-size:18px; white-space:nowrap; overflow:hidden; }
        .sidebar.collapsed .sidebar-header h4,.sidebar.collapsed .menu-text { opacity:0; width:0; }
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
        .user-badge { background:var(--user-color); color:white; padding:5px 15px; border-radius:20px; font-size:12px; font-weight:bold; }
        .mobile-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:999; }
        .mobile-overlay.active { display:block; }
        @media (max-width:768px) { .sidebar{transform:translateX(-100%);} .sidebar.mobile-open{transform:translateX(0);} .main-content{margin-left:0!important;} .user-name{display:none;} }
        /* Page styles */
        .page-header { background:linear-gradient(135deg,var(--user-color) 0%,var(--user-dark) 100%); color:white; padding:25px 30px; border-radius:12px; margin-bottom:25px; box-shadow:0 4px 15px rgba(102,126,234,.3); }
        .card-detail { background:white; border-radius:12px; padding:25px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:20px; }
        .status-badge { padding:8px 20px; border-radius:25px; font-size:.9em; font-weight:600; display:inline-block; }
        .status-pendiente  { background:#fff3cd; color:#997404; }
        .status-enproceso  { background:#cff4fc; color:#055160; }
        .status-completado { background:#d1e7dd; color:#0a3622; }
        .status-cancelado  { background:#f8d7da; color:#58151c; }
        .info-label { font-size:.8em; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; font-weight:600; margin-bottom:4px; }
        .info-value { font-size:1em; color:#212529; }
        .section-title { font-size:1em; font-weight:700; color:var(--user-dark); border-bottom:2px solid #e9ecef; padding-bottom:10px; margin-bottom:18px; }
        .articulo-row { background:#f8f9fa; border-radius:8px; padding:15px; margin-bottom:10px; border-left:3px solid var(--user-color); }
        .campo-dinamico-item { border-left:3px solid #e9ecef; padding-left:15px; margin-bottom:12px; }
        .por-definir-badge { background:#fff3cd; color:#856404; padding:2px 8px; border-radius:10px; font-size:.75em; }
    </style>
</head>
<body>
<div class="mobile-overlay" id="mobileOverlay"></div>

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
            <a href="{{ route('usuario.levantamientos.index') }}" class="menu-link active">
                <i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Mis Levantamientos</span>
            </a>
        </li>
        @php $tienePermisosEspeciales = auth()->user()->Permisos === 'si'; @endphp
        <li class="menu-item">
            <a {{ $tienePermisosEspeciales ? 'href='.route('usuario.clientesU') : 'href=#' }}
               class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}"
               @if(!$tienePermisosEspeciales) onclick="verificarPermiso(event,'clientes'); return false;" @endif>
                <i class="fas fa-users menu-icon"></i><span class="menu-text">Clientes</span>
                @if(!$tienePermisosEspeciales)<i class="fas fa-lock lock-icon"></i>@endif
            </a>
        </li>
        <li class="menu-item">
            <a {{ $tienePermisosEspeciales ? 'href='.route('usuario.tipos-levantamiento.index') : 'href=#' }}
               class="menu-link {{ !$tienePermisosEspeciales ? 'locked' : '' }}"
               @if(!$tienePermisosEspeciales) onclick="verificarPermiso(event,'tipos_levantamiento'); return false;" @endif>
                <i class="fas fa-cogs menu-icon"></i><span class="menu-text">Tipos de Levantamiento</span>
                @if(!$tienePermisosEspeciales)<i class="fas fa-lock lock-icon"></i>@endif
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

<main class="main-content" id="mainContent">
    <div class="top-bar">
        <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
        <div class="user-info">
            <span class="user-name">{{ auth()->user()->Nombres }} {{ auth()->user()->ApellidosPat }}</span>
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->Nombres, 0, 1)) }}</div>
            <span class="user-badge">{{ auth()->user()->Rol }}</span>
        </div>
    </div>

   

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="mb-1"><i class="fas fa-file-alt me-2"></i>LEV-{{ str_pad($levantamiento->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</h3>
                <p class="mb-0 opacity-75">{{ $levantamiento->tipo_nombre ?? 'Sin tipo de levantamiento' }}</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <span class="status-badge status-{{ strtolower(str_replace(' ', '', $levantamiento->estatus)) }}">
                    {{ $levantamiento->estatus }}
                </span>
                @if(!in_array($levantamiento->estatus, ['Cancelado','Completado']))
                <a href="{{ route('usuario.levantamientos.edit', $levantamiento->Id_Levantamiento) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
                @endif
                <a href="{{ route('usuario.levantamientos.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Regresar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card-detail">
                <p class="section-title"><i class="fas fa-info-circle me-2 text-primary"></i>Información General</p>
                <div class="mb-3">
                    <p class="info-label">Folio</p>
                    <p class="info-value fw-bold text-primary">LEV-{{ str_pad($levantamiento->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="mb-3">
                    <p class="info-label">Estado</p>
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '', $levantamiento->estatus)) }}">{{ $levantamiento->estatus }}</span>
                </div>
                <div class="mb-3">
                    <p class="info-label">Tipo de Levantamiento</p>
                    <p class="info-value">{{ $levantamiento->tipo_nombre ?? '—' }}</p>
                </div>
                <div class="mb-3">
                    <p class="info-label">Fecha de Creación</p>
                    <p class="info-value"><i class="fas fa-calendar me-1 text-muted"></i>{{ \Carbon\Carbon::parse($levantamiento->fecha_creacion)->format('d/m/Y H:i') }}</p>
                </div>
                <div class="mb-3">
                    <p class="info-label">Creado por</p>
                    <p class="info-value">{{ $levantamiento->usuario_nombre }} {{ $levantamiento->usuario_apellido }}</p>
                </div>
            </div>

            <div class="card-detail">
                <p class="section-title"><i class="fas fa-building me-2 text-success"></i>Cliente</p>
                <div class="mb-3">
                    <p class="info-label">Nombre</p>
                    <p class="info-value fw-bold">{{ $levantamiento->cliente_nombre }}</p>
                </div>
                @if($levantamiento->cliente_correo)
                <div class="mb-3">
                    <p class="info-label">Correo</p>
                    <p class="info-value"><i class="fas fa-envelope me-1 text-muted"></i>{{ $levantamiento->cliente_correo }}</p>
                </div>
                @endif
                @if($levantamiento->cliente_telefono)
                <div class="mb-3">
                    <p class="info-label">Teléfono</p>
                    <p class="info-value"><i class="fas fa-phone me-1 text-muted"></i>{{ $levantamiento->cliente_telefono }}</p>
                </div>
                @endif
            </div>

            @if($valoresDinamicos->count() > 0)
            <div class="card-detail">
                <p class="section-title"><i class="fas fa-sliders-h me-2 text-warning"></i>Información del Tipo</p>
                @foreach($valoresDinamicos as $vd)
                <div class="campo-dinamico-item">
                    <p class="info-label">{{ $vd->Etiqueta }}</p>
                    <p class="info-value">{{ $vd->Valor }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card-detail">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="section-title mb-0"><i class="fas fa-boxes me-2 text-info"></i>Artículos del Levantamiento</p>
                    <span class="badge bg-primary rounded-pill">{{ $articulos->count() }} artículo(s)</span>
                </div>

                @forelse($articulos as $art)
                <div class="articulo-row">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1">
                                <i class="fas fa-cube me-2 text-primary"></i>{{ $art->articulo_nombre }}
                                @if($art->modelo_por_definir ?? false)
                                    <span class="por-definir-badge ms-1">Modelo por definir</span>
                                @endif
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-tag me-1"></i>{{ $art->marca_nombre ?? 'Sin marca' }}
                                &nbsp;|&nbsp;
                                <i class="fas fa-box me-1"></i>{{ $art->modelo_nombre ?? 'Sin modelo' }}
                            </small>
                            @if($art->articulo_descripcion)
                                <p class="text-secondary small mt-1 mb-0">{{ $art->articulo_descripcion }}</p>
                            @endif
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="info-label">Cantidad</div>
                            <div class="fw-bold fs-5">{{ $art->Cantidad }}</div>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="info-label">Precio Unitario</div>
                            <div class="fw-bold text-success">${{ number_format($art->Precio_Unitario, 2) }}</div>
                            <div class="info-label mt-1">Subtotal</div>
                            <div class="fw-bold">${{ number_format($art->Subtotal, 2) }}</div>
                        </div>
                    </div>
                    @if($art->Notas)
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted"><i class="fas fa-sticky-note me-1"></i><strong>Notas:</strong> {{ $art->Notas }}</small>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay artículos registrados en este levantamiento</p>
                </div>
                @endforelse

                @if($articulos->count() > 0)
                <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                    <div class="text-end">
                        <p class="info-label">TOTAL DEL LEVANTAMIENTO</p>
                        <h4 class="text-success fw-bold">${{ number_format($articulos->sum(fn($a) => $a->Subtotal), 2) }}</h4>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const toggleBtn = document.getElementById('toggleBtn');
const mobileOverlay = document.getElementById('mobileOverlay');
toggleBtn.addEventListener('click', () => {
    if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); mobileOverlay.classList.toggle('active'); }
    else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
});
mobileOverlay.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); mobileOverlay.classList.remove('active'); });
function verificarPermiso(event, accion) {
    event.preventDefault();
    const nombres = { clientes:'Clientes', tipos_levantamiento:'Tipos de Levantamiento' };
    Swal.fire({ icon:'warning', title:'Acceso Restringido', html:`<p>No tienes permisos para acceder a <strong>${nombres[accion]||accion}</strong>.</p><p class="text-muted small">Contacta al administrador.</p>`, confirmButtonColor:'#667eea' });
}
</script>
</body>
</html>