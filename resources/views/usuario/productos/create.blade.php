<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nuevo Artículo - Sistema de Levantamientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{--sidebar-width:250px;--sidebar-collapsed-width:70px;--uc:#1D67A8;--ud:#1D67A8;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f7fa;}
        .sidebar{position:fixed;top:0;left:0;height:100vh;width:var(--sidebar-width);background:linear-gradient(135deg,var(--uc) 0%,var(--ud) 100%);transition:all .3s ease;z-index:1000;box-shadow:2px 0 10px rgba(0,0,0,.1);}
        .sidebar.collapsed{width:var(--sidebar-collapsed-width);}
        .sidebar-header{padding:20px;text-align:center;color:white;border-bottom:1px solid rgba(255,255,255,.1);}
        .sidebar-header h4{margin:0;font-size:18px;white-space:nowrap;overflow:hidden;}
        .sidebar.collapsed .sidebar-header h4,.sidebar.collapsed .menu-text{opacity:0;width:0;}
        .sidebar-menu{list-style:none;padding:0;margin:20px 0;}
        .menu-item{margin:5px 0;}
        .menu-link{display:flex;align-items:center;padding:15px 20px;color:rgba(255,255,255,.8);text-decoration:none;transition:all .3s;}
        .menu-link:hover{background:rgba(255,255,255,.1);color:white;}
        .menu-link.active{background:rgba(255,255,255,.2);color:white;border-left:4px solid white;}
        .menu-icon{width:30px;text-align:center;font-size:20px;}
        .menu-text{margin-left:15px;white-space:nowrap;transition:opacity .3s;}
        .sidebar-footer{position:absolute;bottom:0;width:100%;padding:20px;border-top:1px solid rgba(255,255,255,.1);}
        .main-content{margin-left:var(--sidebar-width);transition:margin-left .3s ease;padding:20px;}
        .main-content.expanded{margin-left:var(--sidebar-collapsed-width);}
        .top-bar{background:white;padding:15px 20px;border-radius:10px;margin-bottom:25px;box-shadow:0 2px 4px rgba(0,0,0,.1);display:flex;justify-content:space-between;align-items:center;}
        .toggle-btn{background:var(--uc);color:white;border:none;width:40px;height:40px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
        .page-header{background:linear-gradient(135deg,var(--uc),var(--ud));color:white;border-radius:12px;padding:25px 30px;margin-bottom:25px;}
        .form-card{background:white;border-radius:12px;padding:28px;box-shadow:0 2px 10px rgba(0,0,0,.07);margin-bottom:20px;}
        .form-card h6{color:var(--uc);font-weight:700;font-size:.95rem;margin-bottom:18px;padding-bottom:8px;border-bottom:2px solid #eef0f8;}
        .char-counter{font-size:.75rem;color:#aaa;text-align:right;margin-top:3px;}
        .char-counter.near{color:#f39c12;} .char-counter.full{color:#dc3545;}
        .alerta-dup{font-size:.8rem;margin-top:5px;display:none;padding:7px 11px;border-radius:6px;}
        .alerta-dup.warn{background:#fff8e1;color:#795548;border:1px solid #ffe082;}
        .alerta-dup.block{background:#fdecea;color:#c62828;border:1px solid #ffcdd2;}
        .field-warn{border-color:#ffc107!important;box-shadow:0 0 0 .2rem rgba(255,193,7,.2)!important;}
        @media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.mobile-open{transform:translateX(0);}.main-content{margin-left:0!important;}}
        .bg-primary{background-color:var(--uc) !important;}
    </style>
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header"><i class="fas fa-clipboard-list fa-2x mb-2"></i><h4>Sistema Levantamientos</h4></div>
    <ul class="sidebar-menu">
        <li class="menu-item"><a href="{{ route('usuario.dashboard') }}" class="menu-link"><i class="fas fa-home menu-icon"></i><span class="menu-text">Dashboard</span></a></li>
        <li class="menu-item"><a href="{{ route('usuario.levantamientos.index') }}" class="menu-link"><i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Mis Levantamientos</span></a></li>
        <li class="menu-item"><a href="{{ route('usuario.clientesU') }}" class="menu-link"><i class="fas fa-users menu-icon"></i><span class="menu-text">Clientes</span></a></li>
        <li class="menu-item"><a href="{{ route('usuario.productos.index') }}" class="menu-link active"><i class="fas fa-box menu-icon"></i><span class="menu-text">Productos</span></a></li>
        <li class="menu-item"><a href="{{ route('usuario.tipos-levantamiento.index') }}" class="menu-link"><i class="fas fa-cogs menu-icon"></i><span class="menu-text">Tipos de Levantamiento</span></a></li>
    </ul>
    <div class="sidebar-footer"><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="menu-link w-100 border-0 bg-transparent"><i class="fas fa-sign-out-alt menu-icon"></i><span class="menu-text">Cerrar Sesión</span></button></form></div>
</aside>

<main class="main-content" id="mainContent">
    <div class="top-bar">
        <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('usuario.productos.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Volver</a>
            <span class="fw-semibold">{{ Auth::user()->Nombres }}</span>
        </div>
    </div>

    <div class="page-header">
        <h1 style="font-size:24px;font-weight:600;"><i class="fas fa-plus-circle me-2"></i>Nuevo Artículo</h1>
        <p class="mb-0 opacity-75">Registra un nuevo artículo en el catálogo del sistema</p>
    </div>

    <form id="formProducto">
        @csrf
        <!-- Info principal -->
        <div class="form-card">
            <h6><i class="fas fa-info-circle me-2"></i>Información del Artículo</h6>
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nombre" name="nombre" maxlength="500" required
                           oninput="contarChars(this,'cnt_nombre',500); schedVerificar();">
                    <div class="char-counter" id="cnt_nombre">0 / 500</div>
                    <div class="alerta-dup warn" id="alerta_dup">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Ya existe un artículo con el mismo nombre, marca y modelo. Verifica antes de guardar.
                    </div>
                </div>
                <div class="col-12 mb-1">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea class="form-control" name="descripcion" rows="3" maxlength="500"
                              oninput="contarChars(this,'cnt_desc',500)"
                              placeholder="Características técnicas, especificaciones..."></textarea>
                    <div class="char-counter" id="cnt_desc">0 / 500</div>
                </div>
            </div>
        </div>

        <!-- Marca y Modelo -->
        <div class="form-card">
            <h6><i class="fas fa-tag me-2"></i>Marca y Modelo</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Marca <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select class="form-select" name="id_marca" id="id_marca" required onchange="schedVerificar()">
                            <option value="">Selecciona una marca</option>
                            @foreach($marcas as $m)
                            <option value="{{ $m->Id_Marca }}">{{ $m->Nombre }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" onclick="abrirModalMarca()" title="Nueva marca">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Modelo</label>
                    <div class="input-group">
                        <select class="form-select" name="id_modelo" id="id_modelo" onchange="schedVerificar()">
                            <option value="">Sin modelo específico</option>
                            @foreach($modelos as $m)
                            <option value="{{ $m->Id_Modelo }}">{{ $m->Nombre }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" onclick="abrirModalModelo()" title="Nuevo modelo">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" name="modelo_por_definir" id="mpd" value="1" onchange="toggleMPD(); schedVerificar();">
                        <label class="form-check-label" for="mpd"><i class="fas fa-clock text-warning me-1"></i>Modelo por definir</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 justify-content-end">
            <a href="{{ route('usuario.productos.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancelar</a>
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Guardar Artículo</button>
        </div>
    </form>
</main>

<!-- Modal Marca -->
<div class="modal fade" id="modalMarca" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title"><i class="fas fa-tag me-2"></i>Nueva Marca</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nm_nombre" placeholder="Ej. Hikvision, Dahua...">
                <div class="alerta-dup block" id="alerta_marca" style="display:none;">
                    <i class="fas fa-exclamation-circle me-1"></i><span id="alerta_marca_txt"></span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción (opcional)</label>
                <input type="text" class="form-control" id="nm_desc">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="guardarMarca()"><i class="fas fa-save me-1"></i>Guardar</button>
        </div>
    </div></div>
</div>

<!-- Modal Modelo -->
<div class="modal fade" id="modalModelo" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title"><i class="fas fa-microchip me-2"></i>Nuevo Modelo</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nmo_nombre" placeholder="Ej. DS-2CE16K0T-LTS...">
                <div class="alerta-dup block" id="alerta_modelo" style="display:none;">
                    <i class="fas fa-exclamation-circle me-1"></i><span id="alerta_modelo_txt"></span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción (opcional)</label>
                <input type="text" class="form-control" id="nmo_desc">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="guardarModelo()"><i class="fas fa-save me-1"></i>Guardar</button>
        </div>
    </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

// Sidebar
document.getElementById('toggleBtn').addEventListener('click', () => {
    const s = document.getElementById('sidebar'), m = document.getElementById('mainContent');
    if (window.innerWidth <= 768) s.classList.toggle('mobile-open');
    else { s.classList.toggle('collapsed'); m.classList.toggle('expanded'); }
});

// Contadores
function contarChars(el, cid, max) {
    const len = el.value.length;
    const c = document.getElementById(cid);
    c.textContent = `${len} / ${max}`;
    c.className = 'char-counter' + (len >= max ? ' full' : len >= max*.85 ? ' near' : '');
}

// Modelo por definir
function toggleMPD() {
    const s = document.getElementById('id_modelo');
    s.disabled = document.getElementById('mpd').checked;
    if (s.disabled) s.value = '';
}

// Verificar duplicado con debounce
let timer;
function schedVerificar() { clearTimeout(timer); timer = setTimeout(verificarDuplicado, 450); }

function verificarDuplicado() {
    const nombre  = document.getElementById('nombre').value.trim();
    const marcaId = document.getElementById('id_marca').value;
    const modId   = document.getElementById('id_modelo').value;
    const porDef  = document.getElementById('mpd').checked;
    const alerta  = document.getElementById('alerta_dup');

    if (!nombre || !marcaId || (!modId && !porDef)) { alerta.style.display = 'none'; return; }

    fetch(`/usuario/productos/check-dup?nombre=${encodeURIComponent(nombre)}&marca=${marcaId}&modelo=${modId}&por_definir=${porDef?1:0}`, {
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(d => { alerta.style.display = d.existe ? 'block' : 'none'; })
    .catch(() => { alerta.style.display = 'none'; });
}

// Modal Marca
function abrirModalMarca() {
    document.getElementById('nm_nombre').value = '';
    document.getElementById('nm_desc').value = '';
    document.getElementById('alerta_marca').style.display = 'none';
    new bootstrap.Modal(document.getElementById('modalMarca')).show();
}
function guardarMarca() {
    const n = document.getElementById('nm_nombre').value.trim();
    if (!n) { Swal.fire({ icon:'warning', title:'Nombre requerido' }); return; }
    Swal.fire({ title:'Guardando...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
    fetch('{{ route("usuario.productos.marcas.store") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
        body: JSON.stringify({ nombre:n, descripcion: document.getElementById('nm_desc').value })
    })
    .then(r=>r.json()).then(d => {
        Swal.close();
        if (d.success) {
            const s = document.getElementById('id_marca');
            s.appendChild(new Option(d.marca.Nombre, d.marca.Id_Marca, true, true));
            bootstrap.Modal.getInstance(document.getElementById('modalMarca')).hide();
            schedVerificar();
            Swal.fire({ icon:'success', title:'Marca creada', timer:1500, showConfirmButton:false });
        } else {
            document.getElementById('alerta_marca_txt').textContent = d.message;
            document.getElementById('alerta_marca').style.display = 'block';
        }
    });
}

// Modal Modelo
function abrirModalModelo() {
    document.getElementById('nmo_nombre').value = '';
    document.getElementById('nmo_desc').value = '';
    document.getElementById('alerta_modelo').style.display = 'none';
    new bootstrap.Modal(document.getElementById('modalModelo')).show();
}
function guardarModelo() {
    const n = document.getElementById('nmo_nombre').value.trim();
    if (!n) { Swal.fire({ icon:'warning', title:'Nombre requerido' }); return; }
    Swal.fire({ title:'Guardando...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
    fetch('{{ route("usuario.productos.modelos.store") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
        body: JSON.stringify({ nombre:n, descripcion: document.getElementById('nmo_desc').value })
    })
    .then(r=>r.json()).then(d => {
        Swal.close();
        if (d.success) {
            const s = document.getElementById('id_modelo');
            s.disabled = false;
            document.getElementById('mpd').checked = false;
            s.appendChild(new Option(d.modelo.Nombre, d.modelo.Id_Modelo, true, true));
            bootstrap.Modal.getInstance(document.getElementById('modalModelo')).hide();
            schedVerificar();
            Swal.fire({ icon:'success', title:'Modelo creado', timer:1500, showConfirmButton:false });
        } else {
            document.getElementById('alerta_modelo_txt').textContent = d.message;
            document.getElementById('alerta_modelo').style.display = 'block';
        }
    });
}

// Submit
document.getElementById('formProducto').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({ title:'Guardando...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
    fetch('{{ route("usuario.productos.store") }}', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},
        body: new FormData(this)
    })
    .then(r=>r.json()).then(d => {
        Swal.close();
        if (d.success) {
            Swal.fire({ icon:'success', title:'¡Artículo guardado!', timer:2000, showConfirmButton:false })
                .then(() => window.location.href = '{{ route("usuario.productos.index") }}');
        } else {
            Swal.fire({ icon:'error', title:'Error', text: d.message || 'No se pudo guardar el artículo.' });
        }
    })
    .catch(() => { Swal.close(); Swal.fire({ icon:'error', title:'Error de conexión' }); });
});
</script>
</body>
</html>