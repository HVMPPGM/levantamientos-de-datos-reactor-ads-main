<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar LEV-{{ str_pad($levantamiento->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
       :root { --sidebar-width:250px; --sidebar-collapsed-width:70px; --user-color:#1D67A8; --user-dark:#1D67A8; }
        body { background:#f5f7fa; overflow-x:hidden; }
        .page-header { background: linear-gradient(135deg, var(--user-color) 0%, var(--user-dark) 100%); color: white; padding: 25px 30px; border-radius: 12px; margin-bottom: 25px; }
        .card-section { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 20px; }
        .section-title { font-size: 1em; font-weight: 700; color: var(--user-dark); border-bottom: 2px solid #e9ecef; padding-bottom: 10px; margin-bottom: 18px; }
        #articulos-disponibles .list-group-item { transition: all 0.2s; }
        #articulos-disponibles .list-group-item:hover { background: #f8f9fa; border-left: 3px solid var(--user-color); }
        #articulos-seleccionados table { font-size: 0.9rem; }
        .por-definir-badge { background: #fff3cd; color: #856404; padding: 2px 8px; border-radius: 10px; font-size: 0.75em; }
        .campo-dinamico { margin-bottom: 15px; }
        .status-badge { padding: 6px 16px; border-radius: 20px; font-size: 0.85em; font-weight: 600; }
        .status-pendiente  { background: #fff3cd; color: #997404; }
        .status-enproceso  { background: #cff4fc; color: #055160; }
        .status-completado { background: #d1e7dd; color: #0a3622; }
        .status-cancelado  { background: #f8d7da; color: #58151c; }
        .btn-definir-modelo { background: #fd7e14; color: white; border: none; border-radius: 6px; padding: 3px 10px; font-size: 0.78em; cursor: pointer; transition: all .2s; }
        .btn-definir-modelo:hover { background: #e96c00; }
       
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

  

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="mb-1"><i class="fas fa-edit me-2"></i>Editar LEV-{{ str_pad($levantamiento->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</h3>
                <p class="mb-0 opacity-75">{{ $levantamiento->tipo_nombre ?? 'Sin tipo' }} &mdash; Cliente: {{ $levantamiento->cliente_nombre }}</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="status-badge status-{{ strtolower(str_replace(' ', '', $levantamiento->estatus)) }}" id="badgeEstatus">{{ $levantamiento->estatus }}</span>
                <a href="{{ route('usuario.levantamientos.show', $levantamiento->Id_Levantamiento) }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Cancelar
                </a>
            </div>
        </div>
    </div>

    <form id="formEditarLevantamiento" onsubmit="actualizarLevantamiento(event)">
        @csrf
        @method('PUT')
        <input type="hidden" name="tipo_levantamiento_id" value="{{ $levantamiento->Id_Tipo_Levantamiento }}">

        <div class="row">
            <div class="col-lg-4">
                <div class="card-section">
                    <p class="section-title"><i class="fas fa-building me-2 text-primary"></i>Cliente</p>
                    <div class="mb-3">
                        <label class="form-label">Cliente asignado</label>
                        <input type="text" class="form-control" value="{{ $levantamiento->cliente_nombre }}" readonly>
                        <input type="hidden" name="cliente_id" value="{{ $levantamiento->Id_Cliente }}">
                        <small class="text-muted">El cliente no se puede cambiar en la edición.</small>
                    </div>
                </div>

                @if($campos->count() > 0)
                <div class="card-section">
                    <p class="section-title"><i class="fas fa-sliders-h me-2 text-warning"></i>Datos del Tipo</p>
                    @foreach($campos as $campo)
                        @php
                            $excluidos = ['articulo','cantidad','marca','modelo','precio_unitario','servicio_profesional'];
                            if (in_array($campo->Nombre_Campo, $excluidos)) continue;
                            $valorActual = $valoresDinamicos[$campo->Id_Campo]->Valor ?? '';
                        @endphp
                        <div class="campo-dinamico">
                            <label class="form-label">{{ $campo->Etiqueta }} @if($campo->Es_Requerido)<span class="text-danger">*</span>@endif</label>
                            @switch($campo->Tipo_Input)
                                @case('textarea')
                                    <textarea class="form-control" name="{{ $campo->Nombre_Campo }}" rows="3" {{ $campo->Es_Requerido ? 'required' : '' }} placeholder="{{ $campo->Placeholder ?? '' }}">{{ $valorActual }}</textarea>
                                    @break
                                @case('number')
                                    <input type="number" class="form-control" name="{{ $campo->Nombre_Campo }}" value="{{ $valorActual }}" step="0.01" {{ $campo->Es_Requerido ? 'required' : '' }} placeholder="{{ $campo->Placeholder ?? '' }}">
                                    @break
                                @case('select')
                                    <select class="form-select" name="{{ $campo->Nombre_Campo }}" {{ $campo->Es_Requerido ? 'required' : '' }}>
                                        <option value="">{{ $campo->Placeholder ?? 'Seleccione...' }}</option>
                                    </select>
                                    @break
                                @default
                                    <input type="{{ $campo->Tipo_Input }}" class="form-control" name="{{ $campo->Nombre_Campo }}" value="{{ $valorActual }}" {{ $campo->Es_Requerido ? 'required' : '' }} placeholder="{{ $campo->Placeholder ?? '' }}">
                            @endswitch
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="col-lg-8">
                <div class="card-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="section-title mb-0"><i class="fas fa-box me-2 text-primary"></i>Artículos del Cliente</p>
                        <button type="button" class="btn btn-success btn-sm" onclick="abrirModalCrearArticulo()">
                            <i class="fas fa-plus me-1"></i>Nuevo Artículo
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="buscarArticulo" placeholder="Buscar artículo..." oninput="filtrarArticulosDisponibles(this.value)">
                        <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                    <div id="articulos-disponibles" class="border rounded p-3 bg-light" style="max-height:280px;overflow-y:auto">
                        <div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Cargando artículos...</div>
                    </div>
                </div>

                <div class="card-section">
                    <p class="section-title"><i class="fas fa-shopping-cart me-2 text-success"></i>Artículos del Levantamiento</p>
                    <div id="articulos-seleccionados">
                        <div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Cargando...</div>
                    </div>
                </div>

                <div class="card-section">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('usuario.levantamientos.show', $levantamiento->Id_Levantamiento) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="btnGuardar">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- MODAL: DEFINIR MODELO --}}
<div class="modal fade" id="modalDefinirModelo" tabindex="-1" style="z-index:1060">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#fd7e14;color:white;">
                <h5 class="modal-title"><i class="fas fa-cogs me-2"></i>Definir Modelo del Artículo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="articuloIdDefinir">
                <input type="hidden" id="levArticuloIdDefinir">
                <div class="alert alert-warning py-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Al definir el modelo, el levantamiento cambiará automáticamente a <strong>En Proceso</strong>.
                </div>
                <p class="fw-bold mb-3" id="nombreArticuloDefinir"></p>
                <div class="mb-3">
                    <label class="form-label">Modelo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select class="form-select" id="modeloDefinir">
                            <option value="">Seleccione el modelo...</option>
                            @foreach($modelos as $m)
                            <option value="{{ $m->Id_Modelo }}">{{ $m->Nombre }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" onclick="abrirModalCrearModeloDefinir()" title="Nuevo modelo"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning text-white" onclick="guardarModeloDefinido()">
                    <i class="fas fa-check me-2"></i>Definir Modelo
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: CREAR ARTÍCULO (en edición) --}}
<div class="modal fade" id="modalCrearArticulo" tabindex="-1" style="z-index:1060">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Artículo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearArticulo" onsubmit="guardarNuevoArticulo(event)">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Se asociará al cliente: <strong>{{ $levantamiento->cliente_nombre }}</strong>
                    </div>
                    <input type="hidden" name="cliente_id" value="{{ $levantamiento->Id_Cliente }}">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nombre del Artículo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" required placeholder="Ej: Cámara IP, Router, etc.">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" id="marca_articulo" name="marca_id" required>
                                    <option value="">Seleccione una marca...</option>
                                    @foreach($marcas as $m)
                                    <option value="{{ $m->Id_Marca }}">{{ $m->Nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" onclick="abrirModalCrearMarca()"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <div class="input-group">
                                <select class="form-select" id="modelo_articulo" name="modelo_id">
                                    <option value="">Seleccione un modelo...</option>
                                    @foreach($modelos as $m)
                                    <option value="{{ $m->Id_Modelo }}">{{ $m->Nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" onclick="abrirModalCrearMarca()"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="modelo_por_definir" name="modelo_por_definir" onchange="toggleModeloPorDefinir(this)">
                                <label class="form-check-label" for="modelo_por_definir">
                                    <i class="fas fa-question-circle text-warning me-1"></i>Modelo por definir
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" name="descripcion" rows="2" placeholder="Descripción adicional..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="es_principal" name="es_principal">
                                <label class="form-check-label" for="es_principal">
                                    <i class="fas fa-star text-warning me-1"></i>Marcar como artículo principal del cliente
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Crear Artículo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: CREAR MARCA --}}
<div class="modal fade" id="modalCrearMarca" tabindex="-1" style="z-index:1070">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-tag me-2"></i>Crear Nueva Marca</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearMarca" onsubmit="guardarNuevaMarca(event)">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" class="form-control" name="nombre" required placeholder="Ej: Hikvision..."></div>
                    <div class="mb-3"><label class="form-label">Descripción (opcional)</label><input type="text" class="form-control" name="descripcion" placeholder="..."></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Crear Marca</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: CREAR MODELO --}}
<div class="modal fade" id="modalCrearModelo" tabindex="-1" style="z-index:1070">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-box me-2"></i>Crear Nuevo Modelo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearModelo" onsubmit="guardarNuevoModelo(event)">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nombre <span class="text-danger">*</span></label><input type="text" class="form-control" name="nombre" required placeholder="Ej: DS-2CD2143G0..."></div>
                    <div class="mb-3"><label class="form-label">Descripción (opcional)</label><textarea class="form-control" name="descripcion" rows="2"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-2"></i>Crear Modelo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const clienteId = {{ $levantamiento->Id_Cliente }};
const levantamientoId = {{ $levantamiento->Id_Levantamiento }};

let articulosCliente = [], articulosSeleccionados = [], contadorArticulos = 0;
let marcasDisponibles = @json($marcas);
let modelosDisponibles = @json($modelos);

document.addEventListener('DOMContentLoaded', () => {
    fetch(`/usuario/levantamientos/cliente/${clienteId}/articulos`)
    .then(r => r.json())
    .then(data => {
        articulosCliente = data;
        const articulosGuardados = @json($articulos);
        articulosGuardados.forEach(art => {
            contadorArticulos++;
            articulosSeleccionados.push({
                id: contadorArticulos,
                id_articulo: art.Id_Articulo,
                nombre: art.articulo_nombre,
                marca_nombre: art.marca_nombre || 'Sin marca',
                modelo_nombre: art.modelo_nombre || 'Sin modelo',
                modelo_por_definir: art.modelo_por_definir || 0,
                cantidad: parseInt(art.Cantidad),
                precio_unitario: parseFloat(art.Precio_Unitario),
                notas: art.Notas || ''
            });
        });
        mostrarArticulosDisponibles(data);
        renderizarArticulosSeleccionados();
    })
    .catch(() => {
        document.getElementById('articulos-disponibles').innerHTML = '<div class="alert alert-danger">Error al cargar artículos.</div>';
        document.getElementById('articulos-seleccionados').innerHTML = '<div class="alert alert-danger">Error al cargar artículos.</div>';
    });
});

function filtrarArticulosDisponibles(q) {
    const f = q ? articulosCliente.filter(a => a.Nombre.toLowerCase().includes(q.toLowerCase()) || (a.marca_nombre||'').toLowerCase().includes(q.toLowerCase())) : articulosCliente;
    mostrarArticulosDisponibles(f);
}

function mostrarArticulosDisponibles(articulos) {
    const c = document.getElementById('articulos-disponibles');
    if (!articulos.length) { c.innerHTML = `<div class="alert alert-info mb-0">No hay artículos. Crea uno con "Nuevo Artículo".</div>`; return; }
    let html = '<div class="list-group">';
    articulos.forEach(art => {
        const yaAgregado = articulosSeleccionados.find(a => a.id_articulo == art.Id_Articulos);
        html += `<div class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${art.Nombre} ${art.Es_Principal ? '<span class="badge bg-primary ms-1">Principal</span>' : ''} ${art.modelo_por_definir ? '<span class="por-definir-badge">Por definir</span>' : ''}</h6>
                    <small class="text-muted">${art.marca_nombre||'Sin marca'} | ${art.modelo_nombre||'Sin modelo'}</small>
                </div>
                <button type="button" class="btn btn-sm ${yaAgregado ? 'btn-secondary' : 'btn-primary'}" onclick="agregarArticulo(${art.Id_Articulos})" ${yaAgregado ? 'disabled' : ''}>
                    <i class="fas fa-${yaAgregado ? 'check' : 'plus'} me-1"></i>${yaAgregado ? 'Agregado' : 'Agregar'}
                </button>
            </div>
        </div>`;
    });
    html += '</div>';
    c.innerHTML = html;
}

function agregarArticulo(articuloId) {
    const art = articulosCliente.find(a => a.Id_Articulos == articuloId);
    if (!art) return;
    if (articulosSeleccionados.find(a => a.id_articulo == articuloId)) {
        Swal.fire({icon:'warning',title:'Ya agregado',timer:1500,showConfirmButton:false}); return;
    }
    contadorArticulos++;
    articulosSeleccionados.push({id:contadorArticulos, id_articulo:articuloId, nombre:art.Nombre, marca_nombre:art.marca_nombre||'Sin marca', modelo_nombre:art.modelo_nombre||'Sin modelo', modelo_por_definir:art.modelo_por_definir||0, cantidad:1, precio_unitario:0, notas:''});
    renderizarArticulosSeleccionados();
    mostrarArticulosDisponibles(articulosCliente);
    Swal.fire({icon:'success',title:'Agregado',text:art.Nombre,timer:1200,showConfirmButton:false});
}

function renderizarArticulosSeleccionados() {
    const c = document.getElementById('articulos-seleccionados');
    if (!articulosSeleccionados.length) { c.innerHTML = `<div class="alert alert-secondary text-center mb-0">No hay artículos agregados.</div>`; return; }
    let html = `<div class="table-responsive"><table class="table table-bordered table-hover mb-0">
        <thead class="table-light"><tr><th>Artículo</th><th>Marca/Modelo</th><th width="90">Cant.</th><th width="140">Precio Unit.</th><th width="180">Notas</th><th width="90">Subtotal</th><th width="60"></th></tr></thead><tbody>`;
    articulosSeleccionados.forEach(art => {
        const subtotal = (art.cantidad * art.precio_unitario).toFixed(2);
        // Botón definir modelo solo si está por definir
        const btnDefinir = art.modelo_por_definir
            ? `<br><button type="button" class="btn-definir-modelo mt-1" onclick="abrirModalDefinirModelo(${art.id_articulo}, ${art.id}, '${art.nombre.replace(/'/g,"\\'")}')"><i class="fas fa-cogs me-1"></i>Definir modelo</button>`
            : '';
        html += `<tr id="filaArticulo_${art.id}">
            <td><strong>${art.nombre}</strong>${art.modelo_por_definir ? ' <span class="por-definir-badge" id="badgePorDefinir_'+art.id+'">Por definir</span>' : ''}${btnDefinir}
                <input type="hidden" name="articulos[${art.id}][id_articulo]" value="${art.id_articulo}">
                <input type="hidden" name="articulos[${art.id}][modelo_por_definir]" id="hiddenPorDefinir_${art.id}" value="${art.modelo_por_definir}">
            </td>
            <td><small id="marcaModelo_${art.id}">${art.marca_nombre}<br>${art.modelo_nombre}</small></td>
            <td><input type="number" class="form-control form-control-sm" min="1" value="${art.cantidad}" name="articulos[${art.id}][cantidad]" onchange="actualizarCantidad(${art.id},this.value)"></td>
            <td><div class="input-group input-group-sm"><span class="input-group-text">$</span><input type="number" class="form-control" min="0" step="0.01" value="${art.precio_unitario}" name="articulos[${art.id}][precio_unitario]" onchange="actualizarPrecio(${art.id},this.value)"></div></td>
            <td><input type="text" class="form-control form-control-sm" value="${art.notas}" name="articulos[${art.id}][notas]" onchange="actualizarNotas(${art.id},this.value)" placeholder="Notas..."></td>
            <td class="text-end fw-bold">$${subtotal}</td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="eliminarArticulo(${art.id})"><i class="fas fa-trash"></i></button></td>
        </tr>`;
    });
    const total = articulosSeleccionados.reduce((s,a) => s + a.cantidad*a.precio_unitario, 0).toFixed(2);
    html += `</tbody><tfoot class="table-light"><tr><th colspan="5" class="text-end">TOTAL:</th><th class="text-end">$${total}</th><th></th></tr></tfoot></table></div>`;
    c.innerHTML = html;
}

function actualizarCantidad(id,v){const a=articulosSeleccionados.find(x=>x.id==id);if(a){a.cantidad=parseInt(v)||1;renderizarArticulosSeleccionados();}}
function actualizarPrecio(id,v){const a=articulosSeleccionados.find(x=>x.id==id);if(a){a.precio_unitario=parseFloat(v)||0;renderizarArticulosSeleccionados();}}
function actualizarNotas(id,v){const a=articulosSeleccionados.find(x=>x.id==id);if(a){a.notas=v;}}
function eliminarArticulo(id){
    Swal.fire({title:'¿Quitar artículo?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545',cancelButtonColor:'#6c757d',confirmButtonText:'Sí, quitar'})
    .then(r=>{if(r.isConfirmed){articulosSeleccionados=articulosSeleccionados.filter(a=>a.id!=id);renderizarArticulosSeleccionados();mostrarArticulosDisponibles(articulosCliente);}});
}

// ── Definir modelo ──
function abrirModalDefinirModelo(articuloId, artId, nombre) {
    document.getElementById('articuloIdDefinir').value = articuloId;
    document.getElementById('levArticuloIdDefinir').value = artId;
    document.getElementById('nombreArticuloDefinir').textContent = nombre;
    document.getElementById('modeloDefinir').value = '';
    new bootstrap.Modal(document.getElementById('modalDefinirModelo')).show();
}

function guardarModeloDefinido() {
    const articuloId = document.getElementById('articuloIdDefinir').value;
    const artId = document.getElementById('levArticuloIdDefinir').value;
    const modeloId = document.getElementById('modeloDefinir').value;
    if (!modeloId) { Swal.fire({icon:'warning',title:'Selecciona un modelo',timer:1800,showConfirmButton:false}); return; }

    Swal.fire({title:'Guardando modelo...', allowOutsideClick:false, didOpen:()=>Swal.showLoading()});

    fetch(`/usuario/levantamientos/${levantamientoId}/definir-modelo`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({ articulo_id: articuloId, modelo_id: modeloId })
    })
    .then(r=>r.json()).then(data=>{
        Swal.close();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalDefinirModelo')).hide();

            // Actualizar UI en la tabla sin recargar
            const art = articulosSeleccionados.find(a => a.id == artId);
            if (art) {
                art.modelo_por_definir = 0;
                art.modelo_nombre = data.modelo_nombre;
            }
            renderizarArticulosSeleccionados();

            // Actualizar badge de estatus en el header
            const badge = document.getElementById('badgeEstatus');
            if (badge) {
                badge.className = 'status-badge status-enproceso';
                badge.textContent = 'En Proceso';
            }

            Swal.fire({icon:'success', title:'¡Modelo definido!', html:`El artículo ahora es <strong>${data.modelo_nombre}</strong> y el levantamiento pasó a <strong>En Proceso</strong>`, confirmButtonColor:'#667eea'});
        } else {
            Swal.fire({icon:'error',title:'Error',text:data.message||'No se pudo guardar'});
        }
    }).catch(()=>{Swal.close();Swal.fire({icon:'error',title:'Error',text:'Error de conexión'});});
}

function abrirModalCrearModeloDefinir() {
    document.getElementById('formCrearModelo').reset();
    new bootstrap.Modal(document.getElementById('modalCrearModelo')).show();
}

// ── Guardar levantamiento ──
function actualizarLevantamiento(event) {
    event.preventDefault();
    if (!articulosSeleccionados.length) { Swal.fire({icon:'warning',title:'Sin artículos',text:'Agrega al menos un artículo'}); return; }
    const formData = new FormData(event.target);
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
    fetch(`/usuario/levantamientos/${levantamientoId}`, {method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:formData})
    .then(r=>r.json()).then(data=>{
        btn.disabled=false; btn.innerHTML='<i class="fas fa-save me-2"></i>Guardar Cambios';
        if(data.success){
            Swal.fire({icon:'success',title:'¡Actualizado!',text:'Levantamiento actualizado exitosamente'})
            .then(()=>window.location.href=`{{ route('usuario.levantamientos.show', $levantamiento->Id_Levantamiento) }}`);
        }else{Swal.fire({icon:'error',title:'Error',text:data.message||'Error al actualizar'});}
    }).catch(()=>{btn.disabled=false;btn.innerHTML='<i class="fas fa-save me-2"></i>Guardar Cambios';Swal.fire({icon:'error',title:'Error',text:'Error de conexión'});});
}

// ── Crear artículo ──
function abrirModalCrearArticulo(){document.getElementById('formCrearArticulo').reset();new bootstrap.Modal(document.getElementById('modalCrearArticulo')).show();}
function toggleModeloPorDefinir(cb){const s=document.getElementById('modelo_articulo');s.disabled=cb.checked;if(cb.checked)s.value='';}

function guardarNuevoArticulo(event){
    event.preventDefault();
    Swal.fire({title:'Creando artículo...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/usuario/articulos/crear-desde-levantamiento',{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:new FormData(event.target)})
    .then(r=>r.json()).then(data=>{
        Swal.close();
        if(data.success){
            bootstrap.Modal.getInstance(document.getElementById('modalCrearArticulo')).hide();
            Swal.fire({icon:'success',title:'¡Artículo creado!',timer:1800,showConfirmButton:false});
            fetch(`/usuario/levantamientos/cliente/${clienteId}/articulos`)
            .then(r=>r.json()).then(d=>{articulosCliente=d;mostrarArticulosDisponibles(d);});
        }else{Swal.fire({icon:'error',title:'Error',text:data.message});}
    }).catch(()=>Swal.close());
}

function abrirModalCrearMarca(){document.getElementById('formCrearMarca').reset();new bootstrap.Modal(document.getElementById('modalCrearMarca')).show();}
function guardarNuevaMarca(event){
    event.preventDefault();
    Swal.fire({title:'Creando...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/usuario/articulos/crear-marca-rapida',{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:new FormData(event.target)})
    .then(r=>r.json()).then(data=>{
        Swal.close();
        if(data.success){
            bootstrap.Modal.getInstance(document.getElementById('modalCrearMarca')).hide();
            const s=document.getElementById('marca_articulo');
            const o=document.createElement('option');o.value=data.marca.Id_Marca;o.textContent=data.marca.Nombre;o.selected=true;
            s.appendChild(o);marcasDisponibles.push(data.marca);
            Swal.fire({icon:'success',title:'Marca creada',timer:1500,showConfirmButton:false});
        }else{Swal.fire({icon:'error',title:'Error',text:data.message});}
    });
}

function abrirModalCrearModelo(){document.getElementById('formCrearModelo').reset();new bootstrap.Modal(document.getElementById('modalCrearModelo')).show();}
function guardarNuevoModelo(event){
    event.preventDefault();
    Swal.fire({title:'Creando...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/usuario/articulos/crear-modelo-rapido',{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:new FormData(event.target)})
    .then(r=>r.json()).then(data=>{
        Swal.close();
        if(data.success){
            bootstrap.Modal.getInstance(document.getElementById('modalCrearModelo')).hide();
            // Agregar al select del modal definir modelo también
            const sDefinir = document.getElementById('modeloDefinir');
            const oD=document.createElement('option');oD.value=data.modelo.Id_Modelo;oD.textContent=data.modelo.Nombre;oD.selected=true;sDefinir.appendChild(oD);
            const s=document.getElementById('modelo_articulo');
            const o=document.createElement('option');o.value=data.modelo.Id_Modelo;o.textContent=data.modelo.Nombre;o.selected=true;
            s.appendChild(o);modelosDisponibles.push(data.modelo);
            Swal.fire({icon:'success',title:'Modelo creado',timer:1500,showConfirmButton:false});
        }else{Swal.fire({icon:'error',title:'Error',text:data.message});}
    });
}
</script>
</body>
</html>