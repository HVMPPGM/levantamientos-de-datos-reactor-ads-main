    // Sidebar toggle
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

        // Búsqueda en tiempo real
        document.getElementById('searchActivos').addEventListener('keyup', function() {
            filtrarClientes(this.value, 'clientesActivosContainer');
        });

        document.getElementById('searchInactivos').addEventListener('keyup', function() {
            filtrarClientes(this.value, 'clientesInactivosContainer');
        });

        function filtrarClientes(searchTerm, containerId) {
            const term = searchTerm.toLowerCase();
            const container = document.getElementById(containerId);
            const clientes = container.querySelectorAll('.cliente-item');

            clientes.forEach(cliente => {
                const nombre = cliente.dataset.nombre;
                const correo = cliente.dataset.correo;
                const telefono = cliente.dataset.telefono;

                if (nombre.includes(term) || correo.includes(term) || telefono.includes(term)) {
                    cliente.style.display = '';
                } else {
                    cliente.style.display = 'none';
                }
            });
        }

        // Limitar artículos a 10
        function limitarArticulos(checkbox) {
            const checkboxes = document.querySelectorAll('input[name="articulos[]"]:checked');
            const count = checkboxes.length;
            
            document.getElementById('countArticulos').textContent = count;
            
            if (count > 10) {
                checkbox.checked = false;
                alert('Solo puedes seleccionar un máximo de 10 artículos');
                return;
            }

            // Si se desmarca, quitar también el radio de principal si estaba marcado
            if (!checkbox.checked) {
                const radioId = checkbox.value;
                const radio = document.querySelector(`input[name="articulo_principal"][value="${radioId}"]`);
                if (radio && radio.checked) {
                    radio.checked = false;
                }
            }
        }

        function marcarPrincipal(articuloId) {
            // Asegurarse de que el checkbox esté marcado
            const checkbox = document.getElementById(`art_${articuloId}`);
            if (!checkbox.checked) {
                checkbox.checked = true;
                limitarArticulos(checkbox);
            }
        }

        // Crear nuevo cliente
        document.getElementById('formCliente').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Deshabilitar botón de submit
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnHTML = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            const method = document.getElementById('formMethod').value;
            const idCliente = document.getElementById('id_cliente').value;
            
            let url = '{{ route("admin.clientes.store") }}';
            if (method === 'PUT') {
                url = `/admin/clientes/${idCliente}`;
            }

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
                    
                    // Mostrar mensaje
                    mostrarMensaje('success', data.message);
                    
                    // Recargar después de un breve delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    alert(data.message || 'Error al guardar el cliente');
                    submitBtn.innerHTML = originalBtnHTML;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
                submitBtn.innerHTML = originalBtnHTML;
                submitBtn.disabled = false;
            });
        });

        // Editar cliente
        function editarCliente(id) {
            fetch(`/admin/clientes/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cliente = data.cliente;
                        
                        // Cambiar título y método
                        document.getElementById('modalTitle').textContent = 'Editar Cliente';
                        document.getElementById('formMethod').value = 'PUT';
                        document.getElementById('id_cliente').value = id;
                        
                        // Llenar datos generales
                        document.getElementById('nombre').value = cliente.Nombre;
                        document.getElementById('correo').value = cliente.Correo;
                        document.getElementById('telefono').value = cliente.Telefono;
                        
                        // Llenar dirección
                        if (cliente.direccion) {
                            document.getElementById('pais').value = cliente.direccion.Pais;
                            document.getElementById('estado').value = cliente.direccion.Estado;
                            document.getElementById('municipio').value = cliente.direccion.Municipio;
                            document.getElementById('colonia').value = cliente.direccion.Colonia || '';
                            document.getElementById('calle').value = cliente.direccion.calle || '';
                            document.getElementById('codigo_postal').value = cliente.direccion.Codigo_Postal;
                            document.getElementById('no_ex').value = cliente.direccion.No_Ex || '';
                            document.getElementById('no_in').value = cliente.direccion.No_In || '';
                        }
                        
                        // Marcar artículos seleccionados
                        document.querySelectorAll('input[name="articulos[]"]').forEach(cb => cb.checked = false);
                        document.querySelectorAll('input[name="articulo_principal"]').forEach(rb => rb.checked = false);
                        
                        if (cliente.articulos && cliente.articulos.length > 0) {
                            cliente.articulos.forEach(articulo => {
                                const checkbox = document.getElementById(`art_${articulo.Id_Articulos}`);
                                if (checkbox) {
                                    checkbox.checked = true;
                                    
                                    if (articulo.pivot.Es_Principal) {
                                        const radio = document.querySelector(`input[name="articulo_principal"][value="${articulo.Id_Articulos}"]`);
                                        if (radio) radio.checked = true;
                                    }
                                }
                            });
                            
                            document.getElementById('countArticulos').textContent = cliente.articulos.length;
                        }
                        
                        // Mostrar modal
                        new bootstrap.Modal(document.getElementById('modalCliente')).show();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Ver detalles
        function verDetalles(id) {
            fetch(`/admin/clientes/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cliente = data.cliente;
                        let html = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-danger mb-3"><i class="fas fa-info-circle me-2"></i>Información General</h6>
                                    <div class="info-row">
                                        <div class="info-label">Nombre:</div>
                                        <div class="info-value">${cliente.Nombre}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Correo:</div>
                                        <div class="info-value">${cliente.Correo}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Teléfono:</div>
                                        <div class="info-value">${cliente.Telefono}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Estatus:</div>
                                        <div class="info-value">
                                            <span class="badge ${cliente.Estatus === 'Activo' ? 'bg-success' : 'bg-secondary'}">
                                                ${cliente.Estatus}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Fecha Registro:</div>
                                        <div class="info-value">${new Date(cliente.fecha_registro).toLocaleDateString('es-MX')}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-danger mb-3"><i class="fas fa-map-marker-alt me-2"></i>Dirección</h6>
                        `;
                        
                        if (cliente.direccion) {
                            html += `
                                    <div class="info-row">
                                        <div class="info-label">País:</div>
                                        <div class="info-value">${cliente.direccion.Pais}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Estado:</div>
                                        <div class="info-value">${cliente.direccion.Estado}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Municipio:</div>
                                        <div class="info-value">${cliente.direccion.Municipio}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Colonia:</div>
                                        <div class="info-value">${cliente.direccion.Colonia || 'N/A'}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Calle:</div>
                                        <div class="info-value">${cliente.direccion.calle || 'N/A'}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Código Postal:</div>
                                        <div class="info-value">${cliente.direccion.Codigo_Postal}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">No. Exterior:</div>
                                        <div class="info-value">${cliente.direccion.No_Ex || 'N/A'}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">No. Interior:</div>
                                        <div class="info-value">${cliente.direccion.No_In || 'N/A'}</div>
                                    </div>
                            `;
                        }
                        
                        html += `
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="text-danger mb-3"><i class="fas fa-box me-2"></i>Artículos Asociados</h6>
                        `;
                        
                        if (cliente.articulos && cliente.articulos.length > 0) {
                            cliente.articulos.forEach(articulo => {
                                const isPrincipal = articulo.pivot.Es_Principal;
                                html += `
                                    <span class="articulo-badge ${isPrincipal ? 'principal' : ''}">
                                        ${articulo.Nombre}
                                        ${isPrincipal ? '<i class="fas fa-star ms-1"></i>' : ''}
                                    </span>
                                `;
                            });
                        } else {
                            html += '<p class="text-muted">Sin artículos asociados</p>';
                        }
                        
                        html += `
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('detallesContent').innerHTML = html;
                        new bootstrap.Modal(document.getElementById('modalDetalles')).show();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Confirmar inactivar
        function confirmarInactivar(id, nombre) {
            if (confirm(`¿Estás seguro de inactivar al cliente "${nombre}"?`)) {
                // Mostrar loading
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                // Crear FormData para enviar el CSRF token correctamente
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');

                fetch(`{{ url('/admin/clientes') }}/${id}/inactivar`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        mostrarMensaje('success', data.message);
                        
                        // Recargar después de un breve delay para que se vea el mensaje
                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
                    } else {
                        alert(data.message || 'Error al inactivar el cliente');
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    // Si funciona pero hay error en el catch, simplemente recargar
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                });
            }
        }

        // Confirmar reactivar
        function confirmarReactivar(id, nombre) {
            if (confirm(`¿Deseas reactivar al cliente "${nombre}"?`)) {
                // Mostrar loading
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                // Crear FormData para enviar el CSRF token correctamente
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');

                fetch(`{{ url('/admin/clientes') }}/${id}/reactivar`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        mostrarMensaje('success', data.message);
                        
                        // Recargar después de un breve delay para que se vea el mensaje
                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
                    } else {
                        alert(data.message || 'Error al reactivar el cliente');
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    // Si funciona pero hay error en el catch, simplemente recargar
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                });
            }
        }

        // Función para mostrar mensajes temporales
        function mostrarMensaje(tipo, mensaje) {
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            const icon = tipo === 'success' ? 'check-circle' : 'exclamation-circle';
            
            const alertHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="fas fa-${icon} me-2"></i>${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', alertHTML);
            
            // Auto-dismiss después de 3 segundos
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 3000);
        }

        // Resetear formulario al cerrar modal
        document.getElementById('modalCliente').addEventListener('hidden.bs.modal', function () {
            document.getElementById('formCliente').reset();
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('id_cliente').value = '';
            document.getElementById('modalTitle').textContent = 'Nuevo Cliente';
            document.getElementById('countArticulos').textContent = '0';
            document.querySelectorAll('input[name="articulos[]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name="articulo_principal"]').forEach(rb => rb.checked = false);
        });