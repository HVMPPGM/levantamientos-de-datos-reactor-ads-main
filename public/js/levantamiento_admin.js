 // ============================================================================
        // SISTEMA DE LEVANTAMIENTOS - SCRIPT COMPLETO
        // ============================================================================

        // Toggle sidebar
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleBtn');

        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // ============================================================================
        // VARIABLES GLOBALES
        // ============================================================================
        let articulosCliente = [];
        let articulosSeleccionados = [];
        let contadorArticulos = 0;
        let clienteSeleccionadoId = null;
        let clienteSeleccionadoNombre = '';
        let marcasDisponibles = [];
        let modelosDisponibles = [];

        // ============================================================================
        // FUNCIONES DE NAVEGACIÓN Y FILTROS (mantener igual)
        // ============================================================================

        function filtrarPorEstatus(estatus) {
            window.location.href = `{{ route('admin.levantamientos') }}?estatus=${estatus}`;
        }

        function verDetalle(id) {
            window.location.href = `/admin/levantamientos/${id}`;
        }

        // ============================================================================
        // FUNCIONES PARA CREAR LEVANTAMIENTO (mantener igual)
        // ============================================================================

        function seleccionarTipo(tipoId, nombreTipo) {
            document.getElementById('tipoLevantamientoId').value = tipoId;
            document.getElementById('tituloFormulario').innerHTML = `
                <i class="fas fa-clipboard-list me-2"></i>
                Nuevo Levantamiento - ${nombreTipo}
            `;

            const modalTipo = bootstrap.Modal.getInstance(document.getElementById('modalNuevoLevantamiento'));
            modalTipo.hide();

            cargarCamposTipo(tipoId);

            const modalFormulario = new bootstrap.Modal(document.getElementById('modalFormularioLevantamiento'));
            modalFormulario.show();
        }

        function cargarCamposTipo(tipoId) {
            fetch(`/admin/levantamientos/tipo/${tipoId}/formulario`)
                .then(response => response.json())
                .then(data => {
                    // Guardar marcas y modelos para el formulario de crear artículo
                    marcasDisponibles = data.marcas;
                    modelosDisponibles = data.modelos;
                    
                    renderizarCampos(data.campos, data.marcas, data.modelos, data.serviciosProfesionales);
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Error al cargar el formulario', 'error');
                });
        }

        function renderizarCampos(campos, marcas, modelos, serviciosProfesionales) {
            const container = document.getElementById('camposDinamicos');
            container.innerHTML = '';

            const camposExcluidos = ['articulo', 'cantidad', 'marca', 'modelo', 'precio_unitario', 'servicio_profesional'];

            campos.forEach(campo => {
                if (camposExcluidos.includes(campo.Nombre_Campo)) {
                    return;
                }

                let html = `<div class="campo-dinamico mb-3">`;
                html += `<label class="form-label">${campo.Etiqueta} ${campo.Es_Requerido ? '<span class="text-danger">*</span>' : ''}</label>`;

                switch(campo.Tipo_Input) {
                    case 'select':
                        html += `<select class="form-select" name="${campo.Nombre_Campo}" ${campo.Es_Requerido ? 'required' : ''}>`;
                        html += `<option value="">${campo.Placeholder || 'Seleccione...'}</option>`;
                        html += `</select>`;
                        break;

                    case 'textarea':
                        html += `<textarea class="form-control" name="${campo.Nombre_Campo}" rows="3" ${campo.Es_Requerido ? 'required' : ''} placeholder="${campo.Placeholder || ''}"></textarea>`;
                        break;

                    case 'number':
                        html += `<input type="number" class="form-control" name="${campo.Nombre_Campo}" ${campo.Es_Requerido ? 'required' : ''} placeholder="${campo.Placeholder || ''}" step="0.01">`;
                        break;

                    default:
                        html += `<input type="${campo.Tipo_Input}" class="form-control" name="${campo.Nombre_Campo}" ${campo.Es_Requerido ? 'required' : ''} placeholder="${campo.Placeholder || ''}">`;
                }

                html += `</div>`;
                container.innerHTML += html;
            });
        }

        // ============================================================================
        // SISTEMA DE ARTÍCULOS (mantener igual con modificación para habilitar botón)
        // ============================================================================

        function cargarArticulosCliente(clienteId) {
            if (!clienteId) {
                limpiarArticulos();
                // Deshabilitar botón de crear artículo
                document.getElementById('btnCrearArticulo').disabled = true;
                clienteSeleccionadoId = null;
                clienteSeleccionadoNombre = '';
                return;
            }

            // NUEVO: Guardar datos del cliente y habilitar botón
            const selectCliente = document.getElementById('cliente_id');
            clienteSeleccionadoId = clienteId;
            clienteSeleccionadoNombre = selectCliente.options[selectCliente.selectedIndex].text;
            document.getElementById('btnCrearArticulo').disabled = false;

            fetch(`/admin/levantamientos/cliente/${clienteId}/articulos`)
                .then(response => response.json())
                .then(data => {
                    articulosCliente = data;
                    mostrarArticulosDisponibles(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los artículos del cliente', 'error');
                });
        }

        function mostrarArticulosDisponibles(articulos) {
            const contenedor = document.getElementById('articulos-disponibles');
            if (!contenedor) return;

            if (articulos.length === 0) {
                contenedor.innerHTML = `
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Este cliente no tiene artículos registrados. Puedes crear uno usando el botón "Crear Nuevo Artículo".
                    </div>
                `;
                return;
            }

            let html = '<div class="list-group">';
            
            articulos.forEach(art => {
                html += `
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    ${art.Nombre}
                                    ${art.Es_Principal ? '<span class="badge bg-primary ms-2">Principal</span>' : ''}
                                </h6>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-tag me-1"></i>
                                    <strong>Marca:</strong> ${art.marca_nombre} | 
                                    <strong>Modelo:</strong> ${art.modelo_nombre}
                                </p>
                                ${art.Descripcion ? `<p class="mb-0 small text-secondary">${art.Descripcion}</p>` : ''}
                            </div>
                            <button type="button" 
                                    class="btn btn-sm btn-primary" 
                                    onclick="agregarArticulo(${art.Id_Articulos})">
                                <i class="fas fa-plus me-1"></i> Agregar
                            </button>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            contenedor.innerHTML = html;
        }

        function agregarArticulo(articuloId) {
            const articulo = articulosCliente.find(a => a.Id_Articulos == articuloId);
            if (!articulo) return;

            if (articulosSeleccionados.find(a => a.id_articulo == articuloId)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Este artículo ya está agregado al levantamiento',
                    timer: 2000
                });
                return;
            }

            contadorArticulos++;
            articulosSeleccionados.push({
                id: contadorArticulos,
                id_articulo: articuloId,
                nombre: articulo.Nombre,
                marca_id: articulo.Id_Marca,
                marca_nombre: articulo.marca_nombre,
                modelo_id: articulo.Id_Modelo,
                modelo_nombre: articulo.modelo_nombre,
                cantidad: 1,
                precio_unitario: 0,
                notas: ''
            });

            renderizarArticulosSeleccionados();
            
            Swal.fire({
                icon: 'success',
                title: 'Artículo agregado',
                text: `${articulo.Nombre} agregado correctamente`,
                timer: 1500,
                showConfirmButton: false
            });
        }

        function renderizarArticulosSeleccionados() {
            const contenedor = document.getElementById('articulos-seleccionados');
            if (!contenedor) return;

            if (articulosSeleccionados.length === 0) {
                contenedor.innerHTML = `
                    <div class="alert alert-secondary text-center mb-0">
                        <i class="fas fa-inbox me-2"></i>
                        No hay artículos agregados. Selecciona artículos de la lista anterior.
                    </div>
                `;
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Artículo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th width="100">Cantidad</th>
                                <th width="150">Precio Unit.</th>
                                <th width="200">Notas</th>
                                <th width="100">Subtotal</th>
                                <th width="60"></th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            articulosSeleccionados.forEach(art => {
                const subtotal = (art.cantidad * art.precio_unitario).toFixed(2);
                html += `
                    <tr>
                        <td>
                            <strong>${art.nombre}</strong>
                            <input type="hidden" name="articulos[${art.id}][id_articulo]" value="${art.id_articulo}">
                        </td>
                        <td>${art.marca_nombre}</td>
                        <td>${art.modelo_nombre}</td>
                        <td>
                            <input type="number" 
                                   class="form-control form-control-sm" 
                                   min="1" 
                                   value="${art.cantidad}"
                                   name="articulos[${art.id}][cantidad]"
                                   onchange="actualizarCantidad(${art.id}, this.value)">
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control" 
                                       min="0" 
                                       step="0.01"
                                       value="${art.precio_unitario}"
                                       name="articulos[${art.id}][precio_unitario]"
                                       onchange="actualizarPrecio(${art.id}, this.value)">
                            </div>
                        </td>
                        <td>
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   value="${art.notas}"
                                   name="articulos[${art.id}][notas]"
                                   onchange="actualizarNotas(${art.id}, this.value)"
                                   placeholder="Notas opcionales...">
                        </td>
                        <td class="text-end">
                            <strong>$${subtotal}</strong>
                        </td>
                        <td class="text-center">
                            <button type="button" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="eliminarArticulo(${art.id})"
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            const total = articulosSeleccionados.reduce((sum, art) => 
                sum + (art.cantidad * art.precio_unitario), 0
            ).toFixed(2);
            
            html += `
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="6" class="text-end">TOTAL:</th>
                                <th class="text-end"><strong>$${total}</strong></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;
            
            contenedor.innerHTML = html;
        }

        function actualizarCantidad(articuloId, cantidad) {
            const articulo = articulosSeleccionados.find(a => a.id == articuloId);
            if (articulo) {
                articulo.cantidad = parseInt(cantidad) || 1;
                renderizarArticulosSeleccionados();
            }
        }

        function actualizarPrecio(articuloId, precio) {
            const articulo = articulosSeleccionados.find(a => a.id == articuloId);
            if (articulo) {
                articulo.precio_unitario = parseFloat(precio) || 0;
                renderizarArticulosSeleccionados();
            }
        }

        function actualizarNotas(articuloId, notas) {
            const articulo = articulosSeleccionados.find(a => a.id == articuloId);
            if (articulo) {
                articulo.notas = notas;
            }
        }

        function eliminarArticulo(articuloId) {
            Swal.fire({
                title: '¿Eliminar artículo?',
                text: 'Se quitará este artículo del levantamiento',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    articulosSeleccionados = articulosSeleccionados.filter(a => a.id != articuloId);
                    renderizarArticulosSeleccionados();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'Artículo eliminado correctamente',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        function limpiarArticulos() {
            articulosSeleccionados = [];
            articulosCliente = [];
            contadorArticulos = 0;
            
            const disponibles = document.getElementById('articulos-disponibles');
            const seleccionados = document.getElementById('articulos-seleccionados');
            
            if (disponibles) {
                disponibles.innerHTML = `
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Seleccione un cliente para ver sus artículos
                    </p>
                `;
            }
            
            if (seleccionados) {
                seleccionados.innerHTML = `
                    <div class="alert alert-secondary text-center mb-0">
                        <i class="fas fa-inbox me-2"></i>
                        No hay artículos agregados
                    </div>
                `;
            }
        }

        // ============================================================================
        // NUEVAS FUNCIONES PARA CREAR ARTÍCULO
        // ============================================================================

        function abrirModalCrearArticulo() {
            if (!clienteSeleccionadoId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Primero debes seleccionar un cliente'
                });
                return;
            }

            // Cargar datos en el formulario
            document.getElementById('clienteIdArticulo').value = clienteSeleccionadoId;
            document.getElementById('nombreClienteArticulo').textContent = clienteSeleccionadoNombre;

            // Cargar marcas
            cargarMarcasEnSelect();
            
            // Cargar modelos
            cargarModelosEnSelect();

            // Limpiar formulario
            document.getElementById('formCrearArticulo').reset();
            document.getElementById('clienteIdArticulo').value = clienteSeleccionadoId;

            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modalCrearArticulo'));
            modal.show();
        }

        function cargarMarcasEnSelect() {
            const select = document.getElementById('marca_articulo');
            select.innerHTML = '<option value="">Seleccione una marca...</option>';
            
            marcasDisponibles.forEach(marca => {
                const option = document.createElement('option');
                option.value = marca.Id_Marca;
                option.textContent = marca.Nombre;
                select.appendChild(option);
            });
        }

        function cargarModelosEnSelect() {
            const select = document.getElementById('modelo_articulo');
            select.innerHTML = '<option value="">Seleccione un modelo...</option>';
            
            modelosDisponibles.forEach(modelo => {
                const option = document.createElement('option');
                option.value = modelo.Id_Modelo;
                option.textContent = modelo.Nombre;
                select.appendChild(option);
            });
        }

        function guardarNuevoArticulo(event) {
            event.preventDefault();

            const formData = new FormData(event.target);

            Swal.fire({
                title: 'Creando artículo...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/admin/articulos/crear-desde-levantamiento', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrearArticulo'));
                    modal.hide();

                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Artículo creado!',
                        text: 'El artículo se ha creado y asociado al cliente correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Recargar artículos del cliente
                    cargarArticulosCliente(clienteSeleccionadoId);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al crear el artículo'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al crear el artículo'
                });
            });
        }

        // ============================================================================
        // FUNCIONES DE GUARDADO (mantener igual)
        // ============================================================================

        function guardarLevantamiento(event) {
            event.preventDefault();

            if (articulosSeleccionados.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Debe agregar al menos un artículo al levantamiento',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            const formData = new FormData(event.target);

            Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/admin/levantamientos', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Levantamiento creado exitosamente',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al crear el levantamiento'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar el levantamiento'
                });
            });
        }

        // ============================================================================
        // FUNCIONES DE ESTATUS (mantener igual)
        // ============================================================================

        function cambiarEstatus(id, nuevoEstatus) {
            Swal.fire({
                title: '¿Cambiar estatus?',
                text: `El levantamiento cambiará a: ${nuevoEstatus}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    ejecutarCambioEstatus(id, nuevoEstatus);
                }
            });
        }

        function ejecutarCambioEstatus(id, nuevoEstatus) {
            Swal.fire({
                title: 'Actualizando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/levantamientos/${id}/estatus`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ estatus: nuevoEstatus })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: 'Estatus actualizado correctamente',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al cambiar el estatus'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cambiar el estatus'
                });
            });
        }

        // ============================================================================
        // FUNCIONES PARA CREAR TIPO (mantener igual)
        // ============================================================================

        let campoIndex = 0;

        function agregarCampo() {
            const container = document.getElementById('camposContainer');
            const html = `
                <div class="card mb-3 campo-item" data-index="${campoIndex}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="mb-0">
                                <i class="fas fa-grip-vertical me-2 text-muted"></i>
                                Campo #${campoIndex + 1}
                            </h6>
                            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarCampo(${campoIndex})">
                                <i class="fas fa-trash me-1"></i> Eliminar
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Nombre del campo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="campos[${campoIndex}][nombre]" required placeholder="ej: cantidad_personas">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Etiqueta <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="campos[${campoIndex}][etiqueta]" required placeholder="ej: Cantidad de Personas">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-select" name="campos[${campoIndex}][tipo]" required>
                                    <option value="text">Texto</option>
                                    <option value="number">Número</option>
                                    <option value="textarea">Área de texto</option>
                                    <option value="select">Lista desplegable</option>
                                    <option value="date">Fecha</option>
                                    <option value="email">Email</option>
                                    <option value="tel">Teléfono</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Placeholder</label>
                                <input type="text" class="form-control" name="campos[${campoIndex}][placeholder]" placeholder="Texto de ayuda">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Valor por defecto</label>
                                <input type="text" class="form-control" name="campos[${campoIndex}][valor_default]" placeholder="Valor predeterminado">
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="campos[${campoIndex}][requerido]" id="req${campoIndex}">
                                    <label class="form-check-label" for="req${campoIndex}">
                                        <i class="fas fa-asterisk text-danger" style="font-size: 0.6rem;"></i>
                                        Campo requerido
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            campoIndex++;
        }

        function eliminarCampo(index) {
            const campo = document.querySelector(`.campo-item[data-index="${index}"]`);
            if (campo) {
                Swal.fire({
                    title: '¿Eliminar campo?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        campo.remove();
                    }
                });
            }
        }

        function guardarNuevoTipo(event) {
            event.preventDefault();

            const formData = new FormData(event.target);

            Swal.fire({
                title: 'Creando tipo...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/admin/levantamientos/tipos', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Tipo de levantamiento creado exitosamente',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al crear el tipo'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al crear el tipo'
                });
            });
        }

        // ============================================================================
        // EVENTOS Y LISTENERS
        // ============================================================================

        document.addEventListener('DOMContentLoaded', function() {
            const selectCliente = document.getElementById('cliente_id');
            if (selectCliente) {
                selectCliente.addEventListener('change', function() {
                    const clienteId = this.value;
                    if (clienteId) {
                        cargarArticulosCliente(clienteId);
                    } else {
                        limpiarArticulos();
                    }
                });
            }

            const modalFormulario = document.getElementById('modalFormularioLevantamiento');
            if (modalFormulario) {
                modalFormulario.addEventListener('show.bs.modal', function () {
                    limpiarArticulos();
                    const form = document.getElementById('formLevantamiento');
                    if (form) {
                        form.reset();
                    }
                    // Deshabilitar botón de crear artículo
                    document.getElementById('btnCrearArticulo').disabled = true;
                    clienteSeleccionadoId = null;
                    clienteSeleccionadoNombre = '';
                });
            }
        });

        console.log('Sistema de Levantamientos con Crear Artículos inicializado correctamente');