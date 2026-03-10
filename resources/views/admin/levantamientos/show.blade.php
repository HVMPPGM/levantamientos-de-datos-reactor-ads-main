<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalle Levantamiento - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <style>
        :root { --primary-color: #358bdc; --sidebar-width: 280px; --sidebar-collapsed: 70px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .sidebar { position: fixed; left: 0; top: 0; height: 100vh; width: var(--sidebar-width); background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; transition: all 0.3s ease; z-index: 1000; overflow-y: auto; }
        .sidebar.collapsed { width: var(--sidebar-collapsed); }
        .sidebar-header { padding: 25px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar.collapsed .sidebar-header h4, .sidebar.collapsed .menu-text { display: none; }
        .sidebar-menu { list-style: none; padding: 15px 0; }
        .menu-item { margin: 5px 15px; }
        .menu-link { display: flex; align-items: center; padding: 12px 15px; color: rgba(255,255,255,0.8); text-decoration: none; border-radius: 8px; transition: all 0.3s; }
        .menu-link:hover, .menu-link.active { background: rgba(255,255,255,0.1); color: white; }
        .menu-icon { width: 20px; margin-right: 15px; text-align: center; }
        .sidebar.collapsed .menu-link { justify-content: center; }
        .sidebar.collapsed .menu-icon { margin-right: 0; }
        .main-content { margin-left: var(--sidebar-width); transition: all 0.3s ease; min-height: 100vh; padding: 20px; }
        .main-content.expanded { margin-left: var(--sidebar-collapsed); }
        .top-bar { background: white; padding: 15px 25px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .toggle-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #333; }
        .timeline { position: relative; padding-left: 30px; }
        .timeline::before { content: ''; position: absolute; left: 9px; top: 0; bottom: 0; width: 2px; background: #e9ecef; }
        .timeline-item { position: relative; padding-bottom: 20px; }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-marker { position: absolute; left: -26px; width: 20px; height: 20px; border-radius: 50%; border: 3px solid #fff; }
        .timeline-content h6 { font-size: 0.9rem; font-weight: 600; }
        @media print { .btn, .card-header, .sidebar, .top-bar, .timeline { display: none !important; } }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
        .btn-primary {
    background-color: #1D67A8;
    border-color: #1D67A8;
}

.btn-primary:hover {
    background-color: #175d96;
    border-color: #175d96;
}
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
            <h4>Sistema</h4>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-item"><a href="{{ route('admin.dashboard') }}" class="menu-link"><i class="fas fa-home menu-icon"></i><span class="menu-text">Dashboard</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.usuarios') }}" class="menu-link"><i class="fas fa-users menu-icon"></i><span class="menu-text">Usuarios</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.levantamientos.index') }}" class="menu-link active"><i class="fas fa-clipboard-list menu-icon"></i><span class="menu-text">Levantamientos</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.clientes.index') }}" class="menu-link"><i class="fas fa-building menu-icon"></i><span class="menu-text">Clientes</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.productos.index') }}" class="menu-link"><i class="fas fa-box menu-icon"></i><span class="menu-text">Productos</span></a></li>
            <li class="menu-item"><a href="{{ route('admin.tipos-levantamiento.index') }}" class="menu-link"><i class="fa-solid fa-gear menu-icon"></i><span class="menu-text">Tipos</span></a></li>
        </ul>
        <div class="sidebar-footer p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-link w-100 border-0 bg-transparent">
                    <i class="fas fa-sign-out-alt menu-icon"></i>
                    <span class="menu-text">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content" id="mainContent">
        <div class="top-bar">
            <button class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></button>
            <div>
                <h2 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    LEV-{{ str_pad($levantamiento->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}
                    <span class="badge bg-{{ 
                        $levantamiento->estatus == 'Completado' ? 'success' : 
                        ($levantamiento->estatus == 'En Proceso' ? 'info' : 
                        ($levantamiento->estatus == 'Pendiente' ? 'warning' : 'danger'))
                    }}">{{ $levantamiento->estatus }}</span>
                    @if($levantamiento->modelo_por_definir)
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-question-circle me-1"></i>Modelo por definir
                        </span>
                    @endif
                </h2>
            </div>
            <div>
                <a href="{{ route('admin.levantamientos.edit', $levantamiento->Id_Levantamiento) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
                <a href="{{ route('admin.levantamientos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Folio</label>
                                <p class="mb-0 fw-bold">LEV-{{ str_pad($levantamiento->Id_Levantamiento, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Estado</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ 
                                        $levantamiento->estatus == 'Completado' ? 'success' : 
                                        ($levantamiento->estatus == 'En Proceso' ? 'info' : 
                                        ($levantamiento->estatus == 'Pendiente' ? 'warning' : 'danger'))
                                    }}">{{ $levantamiento->estatus }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Cliente</label>
                                <p class="mb-0 fw-bold">{{ $levantamiento->cliente_nombre }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Tipo</label>
                                <p class="mb-0">{{ $levantamiento->tipo_nombre ?? 'Sin tipo' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Creado por</label>
                                <p class="mb-0">{{ $levantamiento->usuario_nombre }} {{ $levantamiento->usuario_apellido }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Fecha</label>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($levantamiento->fecha_creacion)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Artículos</h5>
                    </div>
                    <div class="card-body">
                        @if($articulos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th><th>Artículo</th><th>Marca</th><th>Modelo</th>
                                            <th class="text-center">Cant.</th><th class="text-end">P. Unit.</th>
                                            <th class="text-end">Subtotal</th><th>Notas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach($articulos as $index => $art)
                                            @php $subtotal = $art->Cantidad * $art->Precio_Unitario; $total += $subtotal; @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $art->articulo_nombre }}</strong>
                                                    @if($art->modelo_por_definir)
                                                        <br><span class="badge bg-warning text-dark small mt-1"><i class="fas fa-question-circle me-1"></i>Por definir</span>
                                                    @endif
                                                </td>
                                                <td>{{ $art->marca_nombre }}</td>
                                                <td>{{ $art->modelo_nombre }}</td>
                                                <td class="text-center"><span class="badge bg-secondary">{{ $art->Cantidad }}</span></td>
                                                <td class="text-end">${{ number_format($art->Precio_Unitario, 2) }}</td>
                                                <td class="text-end"><strong>${{ number_format($subtotal, 2) }}</strong></td>
                                                <td>{{ $art->Notas ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="6" class="text-end">TOTAL:</th>
                                            <th class="text-end"><strong>${{ number_format($total, 2) }}</strong></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0"><i class="fas fa-info-circle me-2"></i>Sin artículos</div>
                        @endif
                    </div>
                </div>

                @if($valores->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i>Información Adicional</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($valores as $valor)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">{{ $valor->Etiqueta }}</label>
                                        <p class="mb-0">{{ $valor->Valor ?: 'No especificado' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Acciones</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.levantamientos.edit', $levantamiento->Id_Levantamiento) }}" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a>
                        @if($levantamiento->estatus === 'Pendiente' && !$levantamiento->modelo_por_definir)
                            <button class="btn btn-success w-100 mb-2" onclick="marcarRevisado({{ $levantamiento->Id_Levantamiento }})">
                                <i class="fas fa-check-circle me-2"></i>Revisado
                            </button>
                        @endif
                        @if($levantamiento->estatus !== 'Completado')
                            <button class="btn btn-primary w-100 mb-2" onclick="descargarPDF({{ $levantamiento->Id_Levantamiento }})">
                                <i class="fas fa-file-pdf me-2"></i>Descargar PDF
                            </button>
                            <button class="btn btn-danger w-100 mb-2" onclick="enviarGmail({{ $levantamiento->Id_Levantamiento }})">
                                <i class="fab fa-google me-2"></i>Enviar por Gmail
                            </button>
                        @else
                            <button class="btn btn-primary w-100 mb-2" onclick="descargarPDFSinCambiarEstado({{ $levantamiento->Id_Levantamiento }})">
                                <i class="fas fa-file-pdf me-2"></i>Descargar PDF
                            </button>
                            <button class="btn btn-danger w-100 mb-2" onclick="enviarGmailSinCambiarEstado({{ $levantamiento->Id_Levantamiento }})">
                                <i class="fab fa-google me-2"></i>Enviar por Gmail
                            </button>
                        @endif
                        <button class="btn btn-outline-secondary w-100" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Historial</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Creado</h6>
                                    <p class="text-muted small mb-0">{{ \Carbon\Carbon::parse($levantamiento->fecha_creacion)->format('d/m/Y H:i') }}</p>
                                    <p class="text-muted small mb-0">Por: {{ $levantamiento->usuario_nombre }} {{ $levantamiento->usuario_apellido }}</p>
                                </div>
                            </div>
                            @if($levantamiento->estatus === 'Completado')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Completado</h6>
                                        <p class="text-muted small mb-0">Levantamiento completado</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken   = document.querySelector('meta[name="csrf-token"]').content;
        const sidebar     = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        document.getElementById('toggleBtn').addEventListener('click', () => {
            if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); }
            else { sidebar.classList.toggle('collapsed'); mainContent.classList.toggle('expanded'); }
        });

        // ════════════════════════════════════════════════════════════════════
        // PALETA DE COLORES
        // ════════════════════════════════════════════════════════════════════
        const COLOR = {
            azulOscuro   : [30,  60,  114],
            azulMedio    : [42,  82,  152],
            azulNota     : [25,  118, 210],
            azulNotaFondo: [232, 240, 254],
            grisClaro    : [245, 247, 250],
            grisLinea    : [210, 214, 220],
            blanco       : [255, 255, 255],
            negro        : [33,  37,   41],
            grisEncSinP  : [108, 117, 125],
        };

        // ════════════════════════════════════════════════════════════════════
        // buildRows — genera body para autoTable.
        // Por cada artículo con nota inserta una fila extra inmediatamente
        // debajo, que autoTable dimensiona naturalmente.
        // ════════════════════════════════════════════════════════════════════
        function buildRows(lista, tieneColumnasPrecio) {
            const body    = [];
            const notaMap = new Map(); // rowIndex en body → índice en lista
            const numCols = tieneColumnasPrecio ? 7 : 5;

            lista.forEach((art, artIdx) => {
                // Fila normal del artículo
                if (tieneColumnasPrecio) {
                    body.push([
                        artIdx + 1, art.nombre, art.marca, art.modelo, art.cantidad,
                        '$' + parseFloat(art.precio).toFixed(2),
                        '$' + parseFloat(art.subtotal).toFixed(2)
                    ]);
                } else {
                    body.push([artIdx + 1, art.nombre, art.marca, art.modelo, art.cantidad]);
                }

                // Fila extra solo si hay nota
                if (art.notas && art.notas.trim() !== '') {
                    notaMap.set(body.length, artIdx);   // guarda índice ANTES de push
                    const fila = Array(numCols).fill('');
                    fila[1] = '__NOTA__';
                    body.push(fila);
                }
            });

            return { body, notaMap };
        }

        // ════════════════════════════════════════════════════════════════════
        // dibujarBloqueNota — pinta el rectángulo azul con el texto
        // ════════════════════════════════════════════════════════════════════
        function dibujarBloqueNota(doc, hookData, textoNota) {
            const PAD     = 2;
            const RADIO   = 1.5;
            const FS      = 7.5;
            const LINE_H  = 4.2;
            const LABEL_W = 10;
            const ACENTO  = 2;

            const x = hookData.cell.x + PAD;
            const y = hookData.cell.y + PAD;
            const w = hookData.cell.width  - PAD * 2;
            const h = hookData.cell.height - PAD * 2;

            // Fondo azul suave con borde
            doc.setFillColor(...COLOR.azulNotaFondo);
            doc.setDrawColor(...COLOR.azulNota);
            doc.setLineWidth(0.3);
            doc.roundedRect(x, y, w, h, RADIO, RADIO, 'FD');

            // Barra de acento izquierda
            doc.setFillColor(...COLOR.azulNota);
            doc.roundedRect(x, y, ACENTO, h, RADIO, RADIO, 'F');

            const tx = x + ACENTO + PAD;
            const ty = y + 2.5;

            // Etiqueta "Nota:"
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(FS);
            doc.setTextColor(...COLOR.azulNota);
            doc.text('Nota:', tx, ty + FS * 0.35);

            // Texto de la nota
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(35, 65, 135);
            const anchoTexto = w - ACENTO - PAD - LABEL_W - 1;
            const lineas = doc.splitTextToSize(textoNota.trim(), anchoTexto);
            lineas.forEach((l, i) => doc.text(l, tx + LABEL_W, ty + FS * 0.35 + i * LINE_H));

            // Restaurar
            doc.setTextColor(...COLOR.negro);
            doc.setFontSize(8);
            doc.setFont('helvetica', 'normal');
            doc.setLineWidth(0.1);
        }

        // ════════════════════════════════════════════════════════════════════
        // Hooks reutilizables
        // ════════════════════════════════════════════════════════════════════
        function makeDidParseCell(notaMap, lista, anchoCol1, doc) {
            return function(hookData) {
                if (hookData.section !== 'body') return;
                const ri = hookData.row.index;
                if (!notaMap.has(ri)) return;

                if (hookData.column.index !== 1) {
                    // Celdas laterales de la fila-nota: invisibles
                    hookData.cell.styles.fillColor     = COLOR.blanco;
                    hookData.cell.styles.lineWidth     = 0;
                    hookData.cell.styles.minCellHeight = 0;
                } else {
                    // Celda del artículo: reservar altura para el bloque
                    const art    = lista[notaMap.get(ri)];
                    doc.setFontSize(7.5);
                    const lineas = doc.splitTextToSize(art.notas.trim(), anchoCol1 - 14);
                    hookData.cell.styles.minCellHeight = 4 + lineas.length * 4.2 + 8;
                    hookData.cell.styles.fillColor     = COLOR.blanco;
                    hookData.cell.text = [''];
                }
            };
        }

        function makeDidDrawCell(notaMap, lista, doc) {
            return function(hookData) {
                if (hookData.section !== 'body') return;
                const ri = hookData.row.index;
                if (notaMap.has(ri) && hookData.column.index === 1) {
                    dibujarBloqueNota(doc, hookData, lista[notaMap.get(ri)].notas);
                }
            };
        }

        // ════════════════════════════════════════════════════════════════════
        // GENERAR PDF
        //
        // Orden en el documento:
        //   1. Información General
        //   2. Título grande "Artículos del Levantamiento"
        //   3. Subtítulo "Sin Precio Asignado"  → tabla sin precio  (PRIMERO)
        //   4. Subtítulo "Con Precio Asignado"  → tabla con precio  (AL FINAL)
        //   5. Información Adicional
        // ════════════════════════════════════════════════════════════════════
        function generarPDF(datos) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let y     = 20;
            const mL  = 15;
            const mR  = 195;

            // Separar artículos — SIN precio primero, CON precio al final
            const articulosSinPrecio = (datos.articulos || []).filter(a => parseFloat(a.precio) <= 0);
            const articulosConPrecio = (datos.articulos || []).filter(a => parseFloat(a.precio)  > 0);

            // ── HEADER ────────────────────────────────────────────────────
            doc.setFillColor(...COLOR.azulOscuro);
            doc.rect(0, 0, 210, 42, 'F');
            doc.setFillColor(...COLOR.azulMedio);
            doc.rect(0, 37, 210, 5, 'F');

            doc.setTextColor(...COLOR.blanco);
            doc.setFontSize(17); doc.setFont('helvetica', 'bold');
            doc.text('SISTEMA DE LEVANTAMIENTOS', mL, 14);
            doc.setFontSize(9.5); doc.setFont('helvetica', 'normal');
            doc.text('Reactor ADS', mL, 21);
            doc.setFont('helvetica', 'bold'); doc.setFontSize(15);
            doc.text(datos.folio, mR, 14, { align: 'right' });
            doc.setFontSize(9); doc.setFont('helvetica', 'normal');
            doc.text(datos.fecha, mR, 21, { align: 'right' });

            y = 54;
            doc.setTextColor(...COLOR.negro);

            // ── INFORMACIÓN GENERAL ───────────────────────────────────────
            doc.setFontSize(11); doc.setFont('helvetica', 'bold');
            doc.setTextColor(...COLOR.azulOscuro);
            doc.text('Información General', mL, y);
            doc.setDrawColor(...COLOR.azulOscuro); doc.setLineWidth(0.5);
            doc.line(mL, y + 2, mR, y + 2); doc.setLineWidth(0.1);
            y += 8;

            const infoRows = [
                ['Folio:',       datos.folio],
                ['Cliente:',     datos.cliente_nombre],
                ['Correo:',      datos.cliente_correo   || 'No registrado'],
                ['Teléfono:',    datos.cliente_telefono || 'No registrado'],
                ['Tipo:',        datos.tipo_nombre      || 'Sin tipo'],
                ['Creado por:',  datos.usuario_nombre],
                ['Fecha:',       datos.fecha],
            ];

            doc.setFillColor(...COLOR.grisClaro); doc.setDrawColor(...COLOR.grisLinea);
            doc.roundedRect(mL, y - 3, mR - mL, infoRows.length * 7 + 4, 2, 2, 'FD');
            doc.setFontSize(9.5); doc.setTextColor(...COLOR.negro);
            infoRows.forEach(([label, val]) => {
                doc.setFont('helvetica', 'bold'); doc.setTextColor(...COLOR.azulMedio);
                doc.text(label, mL + 3, y);
                doc.setFont('helvetica', 'normal'); doc.setTextColor(...COLOR.negro);
                doc.text(String(val || ''), mL + 38, y);
                y += 7;
            });
            y += 8;

            // ════════════════════════════════════════════════════════════════
            // TÍTULO GENERAL "Artículos del Levantamiento"
            // ════════════════════════════════════════════════════════════════
            if (articulosSinPrecio.length > 0 || articulosConPrecio.length > 0) {
                if (y > 230) { doc.addPage(); y = 20; }

                doc.setFontSize(12); doc.setFont('helvetica', 'bold');
                doc.setTextColor(...COLOR.azulOscuro);
                doc.text('Artículos del Levantamiento', mL, y);
                doc.setDrawColor(...COLOR.azulOscuro); doc.setLineWidth(0.8);
                doc.line(mL, y + 2, mR, y + 2); doc.setLineWidth(0.1);
                y += 10;
            }

            // ════════════════════════════════════════════════════════════════
            // 1° ARTÍCULOS SIN PRECIO ASIGNADO
            // ════════════════════════════════════════════════════════════════
            if (articulosSinPrecio.length > 0) {
                if (y > 230) { doc.addPage(); y = 20; }

                // Subtítulo
                doc.setFontSize(10); doc.setFont('helvetica', 'bold');
                doc.setTextColor(...COLOR.grisEncSinP);
                doc.text('Sin Precio Asignado', mL, y);
                doc.setDrawColor(...COLOR.grisEncSinP); doc.setLineWidth(0.4);
                doc.line(mL, y + 2, mR, y + 2); doc.setLineWidth(0.1);
                y += 6;

                const { body: bodySP, notaMap: notaMapSP } = buildRows(articulosSinPrecio, false);

                doc.autoTable({
                    startY: y,
                    head: [['#', 'Artículo', 'Marca', 'Modelo', 'Cant.']],
                    body: bodySP,
                    theme: 'grid',
                    headStyles: {
                        fillColor: COLOR.grisEncSinP, textColor: COLOR.blanco,
                        fontSize: 8.5, fontStyle: 'bold',
                        halign: 'center', valign: 'middle', cellPadding: 3,
                    },
                    bodyStyles: {
                        fontSize: 8, textColor: COLOR.negro,
                        cellPadding: { top: 2.5, right: 2, bottom: 2.5, left: 2 },
                        valign: 'top',
                    },
                    alternateRowStyles: { fillColor: [249, 250, 252] },
                    columnStyles: {
                        0: { cellWidth: 8,  halign: 'center', fontStyle: 'bold' },
                        1: { cellWidth: 84 },
                        2: { cellWidth: 34 },
                        3: { cellWidth: 34 },
                        4: { cellWidth: 12, halign: 'center' },
                    },
                    margin: { left: mL, right: 15 },
                    didParseCell: makeDidParseCell(notaMapSP, articulosSinPrecio, 84, doc),
                    didDrawCell:  makeDidDrawCell(notaMapSP,  articulosSinPrecio, doc),
                });
                y = doc.lastAutoTable.finalY + 10;
            }

            // ════════════════════════════════════════════════════════════════
            // 2° ARTÍCULOS CON PRECIO ASIGNADO (al final)
            // ════════════════════════════════════════════════════════════════
            if (articulosConPrecio.length > 0) {
                if (y > 230) { doc.addPage(); y = 20; }

                // Subtítulo
                doc.setFontSize(10); doc.setFont('helvetica', 'bold');
                doc.setTextColor(...COLOR.azulMedio);
                doc.text('Con Precio Asignado', mL, y);
                doc.setDrawColor(...COLOR.azulMedio); doc.setLineWidth(0.4);
                doc.line(mL, y + 2, mR, y + 2); doc.setLineWidth(0.1);
                y += 6;

                const { body: bodyCP, notaMap: notaMapCP } = buildRows(articulosConPrecio, true);
                const total = articulosConPrecio.reduce((s, a) => s + parseFloat(a.subtotal), 0);

                doc.autoTable({
                    startY: y,
                    head: [['#', 'Artículo', 'Marca', 'Modelo', 'Cant.', 'P.Unit.', 'Subtotal']],
                    body: bodyCP,
                    foot: [['', '', '', '', '', 'TOTAL:', '$' + total.toFixed(2)]],
                    theme: 'grid',
                    headStyles: {
                        fillColor: COLOR.azulOscuro, textColor: COLOR.blanco,
                        fontSize: 8.5, fontStyle: 'bold',
                        halign: 'center', valign: 'middle', cellPadding: 3,
                    },
                    footStyles: {
                        fillColor: COLOR.grisClaro, textColor: COLOR.azulOscuro,
                        fontSize: 9.5, fontStyle: 'bold',
                    },
                    bodyStyles: {
                        fontSize: 8, textColor: COLOR.negro,
                        cellPadding: { top: 2.5, right: 2, bottom: 2.5, left: 2 },
                        valign: 'top',
                    },
                    alternateRowStyles: { fillColor: [249, 250, 252] },
                    columnStyles: {
                        0: { cellWidth: 8,  halign: 'center', fontStyle: 'bold' },
                        1: { cellWidth: 52 },
                        2: { cellWidth: 28 },
                        3: { cellWidth: 28 },
                        4: { cellWidth: 12, halign: 'center' },
                        5: { cellWidth: 22, halign: 'right' },
                        6: { cellWidth: 22, halign: 'right', fontStyle: 'bold' },
                    },
                    margin: { left: mL, right: 15 },
                    didParseCell: makeDidParseCell(notaMapCP, articulosConPrecio, 52, doc),
                    didDrawCell:  makeDidDrawCell(notaMapCP,  articulosConPrecio, doc),
                });
                y = doc.lastAutoTable.finalY + 10;
            }

            // ── INFORMACIÓN ADICIONAL ─────────────────────────────────────
            if (datos.valores && datos.valores.length > 0) {
                if (y > 240) { doc.addPage(); y = 20; }
                doc.setFontSize(11); doc.setFont('helvetica', 'bold');
                doc.setTextColor(...COLOR.azulOscuro);
                doc.text('Información Adicional', mL, y);
                doc.setDrawColor(...COLOR.azulOscuro); doc.setLineWidth(0.5);
                doc.line(mL, y + 2, mR, y + 2); doc.setLineWidth(0.1);
                y += 8;

                doc.setFillColor(...COLOR.grisClaro); doc.setDrawColor(...COLOR.grisLinea);
                doc.roundedRect(mL, y - 3, mR - mL, datos.valores.length * 9 + 4, 2, 2, 'FD');
                doc.setFontSize(9.5); doc.setTextColor(...COLOR.negro);
                datos.valores.forEach(v => {
                    if (y > 270) { doc.addPage(); y = 20; }
                    doc.setFont('helvetica', 'bold'); doc.setTextColor(...COLOR.azulMedio);
                    doc.text(v.etiqueta + ':', mL + 3, y);
                    doc.setFont('helvetica', 'normal'); doc.setTextColor(...COLOR.negro);
                    const lines = doc.splitTextToSize(String(v.valor || 'No especificado'), 120);
                    doc.text(lines, mL + 52, y);
                    y += 9 * lines.length;
                });
            }

            // ── FOOTER EN TODAS LAS PÁGINAS ───────────────────────────────
            const totalPages = doc.internal.getNumberOfPages();
            const hoy = new Date().toLocaleDateString('es-MX', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
            for (let i = 1; i <= totalPages; i++) {
                doc.setPage(i);
                doc.setDrawColor(...COLOR.azulOscuro); doc.setLineWidth(0.4);
                doc.line(mL, 280, mR, 280);
                doc.setFillColor(...COLOR.azulOscuro);
                doc.rect(0, 283, 210, 14, 'F');
                doc.setFontSize(7.5); doc.setTextColor(...COLOR.blanco); doc.setFont('helvetica', 'normal');
                doc.text('Generado: ' + hoy, mL, 289);
                doc.text('Sistema de Levantamientos — Reactor ADS', 105, 289, { align: 'center' });
                doc.text(`Pág. ${i} / ${totalPages}`, mR, 289, { align: 'right' });
            }

            return doc;
        }

        // ════════════════════════════════════════════════════════════════════
        // HELPERS GMAIL / BLOB
        // ════════════════════════════════════════════════════════════════════
        function descargarBlob(doc, fileName) {
            const url  = URL.createObjectURL(doc.output('blob'));
            const link = Object.assign(document.createElement('a'), { href: url, download: fileName });
            link.click();
            URL.revokeObjectURL(url);
        }

        function abrirGmail(datos) {
            const { cliente_nombre: cli, folio, fecha, tipo_nombre: tipo } = datos;
            const subject = encodeURIComponent(`Levantamiento ${folio} - ${cli}`);
            const body    = encodeURIComponent(
                `Estimado/a,\n\nAdjunto encontrará el PDF del levantamiento ${folio}.\n\n`+
                `Cliente: ${cli}\nTipo: ${tipo || 'Sin tipo'}\nFecha: ${fecha}\n\n`+
                `NOTA: El PDF se descargó automáticamente. Por favor adjúntelo antes de enviar.\n\n`+
                `Saludos,\nSistema de Levantamientos - Reactor ADS`
            );
            setTimeout(() => window.open(`https://mail.google.com/mail/?view=cm&fs=1&su=${subject}&body=${body}`, '_blank'), 500);
        }

        // ════════════════════════════════════════════════════════════════════
        // CAMBIAR ESTADO A COMPLETADO
        // ════════════════════════════════════════════════════════════════════
        function cambiarEstatusACompletado(id) {
            return fetch(`/admin/levantamientos/${id}/estatus`, {
                method : 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body   : JSON.stringify({ estatus: 'Completado' })
            }).then(r => r.json()).catch(err => console.error('Error al actualizar estado:', err));
        }

        function hayModeloSinDefinir(articulos) {
            return (articulos || []).some(a => a.modelo_por_definir === true);
        }

        // ════════════════════════════════════════════════════════════════════
        // MARCAR COMO REVISADO
        // ════════════════════════════════════════════════════════════════════
        function marcarRevisado(id) {
            Swal.fire({
                title: '¿Marcar como Revisado?', text: 'El levantamiento pasará al estado "En Proceso".',
                icon: 'question', showCancelButton: true,
                confirmButtonColor: '#28a745', cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Sí, marcar', cancelButtonText: 'Cancelar'
            }).then(async result => {
                if (!result.isConfirmed) return;
                try {
                    const res  = await fetch(`/admin/levantamientos/${id}/estatus`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ estatus: 'En Proceso' })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '¡Revisado!', text: 'El levantamiento ahora está En Proceso.', timer: 2200, showConfirmButton: false })
                            .then(() => location.reload());
                    } else { Swal.fire('Error', data.message || 'No se pudo actualizar el estatus', 'error'); }
                } catch { Swal.fire('Error', 'Error de conexión al servidor', 'error'); }
            });
        }

        // ════════════════════════════════════════════════════════════════════
        // DESCARGAR PDF
        // ════════════════════════════════════════════════════════════════════
        function descargarPDF(id) {
            Swal.fire({ title: 'Generando PDF...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch(`/admin/levantamientos/${id}/datos-pdf`).then(r => r.json()).then(async data => {
                Swal.close();
                if (!data.success) { Swal.fire('Error', 'No se pudieron obtener los datos', 'error'); return; }
                generarPDF(data.datos).save(`${data.datos.folio}.pdf`);
                if (hayModeloSinDefinir(data.datos.articulos)) {
                    Swal.fire({ icon: 'warning', title: 'PDF generado',
                        html: 'El PDF se descargó pero el levantamiento <strong>NO se marcó como Completado</strong> porque hay artículos con <strong>modelo por definir</strong>.',
                        confirmButtonText: 'Entendido' });
                } else {
                    await cambiarEstatusACompletado(id);
                    Swal.fire({ icon: 'success', title: 'PDF generado', text: 'El levantamiento se marcó como Completado.', timer: 2500, showConfirmButton: false })
                        .then(() => location.reload());
                }
            }).catch(() => { Swal.close(); Swal.fire('Error', 'Error al generar el PDF', 'error'); });
        }

        function descargarPDFSinCambiarEstado(id) {
            Swal.fire({ title: 'Generando PDF...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch(`/admin/levantamientos/${id}/datos-pdf`).then(r => r.json()).then(data => {
                Swal.close();
                if (!data.success) { Swal.fire('Error', 'No se pudieron obtener los datos', 'error'); return; }
                generarPDF(data.datos).save(`${data.datos.folio}.pdf`);
                Swal.fire({ icon: 'success', title: 'PDF generado', timer: 2000, showConfirmButton: false });
            }).catch(() => { Swal.close(); Swal.fire('Error', 'Error al generar el PDF', 'error'); });
        }

        // ════════════════════════════════════════════════════════════════════
        // ENVIAR POR GMAIL
        // ════════════════════════════════════════════════════════════════════
        function enviarGmail(id) {
            Swal.fire({ title: 'Preparando correo...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch(`/admin/levantamientos/${id}/datos-pdf`).then(r => r.json()).then(async data => {
                Swal.close();
                if (!data.success) { Swal.fire('Error', 'No se pudieron obtener los datos', 'error'); return; }
                const doc = generarPDF(data.datos);
                const fn  = `${data.datos.folio}.pdf`;
                descargarBlob(doc, fn);
                abrirGmail(data.datos);
                if (hayModeloSinDefinir(data.datos.articulos)) {
                    Swal.fire({ icon: 'warning', title: 'Correo preparado',
                        html: `El PDF <strong>${fn}</strong> se descargó y se abrió Gmail.<br>
                               <small class="text-muted">Adjunta el PDF al correo antes de enviarlo.</small><br><br>
                               <span class="text-warning fw-bold"><i class="fas fa-exclamation-triangle"></i>
                               El levantamiento <strong>NO se marcó como Completado</strong> porque hay artículos con <strong>modelo por definir</strong>.</span>`,
                        confirmButtonText: 'Entendido' });
                } else {
                    await cambiarEstatusACompletado(id);
                    Swal.fire({ icon: 'success', title: 'Listo',
                        html: `El PDF <strong>${fn}</strong> se descargó y se abrió Gmail.<br><small class="text-muted">Adjunta el PDF al correo antes de enviarlo.</small>`,
                        confirmButtonText: 'Entendido' }).then(() => location.reload());
                }
            }).catch(() => { Swal.close(); Swal.fire('Error', 'Error al preparar el correo', 'error'); });
        }

        function enviarGmailSinCambiarEstado(id) {
            Swal.fire({ title: 'Preparando correo...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch(`/admin/levantamientos/${id}/datos-pdf`).then(r => r.json()).then(data => {
                Swal.close();
                if (!data.success) { Swal.fire('Error', 'No se pudieron obtener los datos', 'error'); return; }
                const doc = generarPDF(data.datos);
                const fn  = `${data.datos.folio}.pdf`;
                descargarBlob(doc, fn);
                abrirGmail(data.datos);
                Swal.fire({ icon: 'success', title: 'Listo',
                    html: `El PDF <strong>${fn}</strong> se descargó y se abrió Gmail.<br><small class="text-muted">Adjunta el PDF al correo antes de enviarlo.</small>`,
                    confirmButtonText: 'Entendido' });
            }).catch(() => { Swal.close(); Swal.fire('Error', 'Error al preparar el correo', 'error'); });
        }
    </script>
</body>
</html>