      // Toggle Sidebar
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
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tablaProductos tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Filtros
        document.getElementById('filterMarca').addEventListener('change', function() {
            filtrarTabla();
        });

        document.getElementById('filterModelo').addEventListener('change', function() {
            filtrarTabla();
        });

        function filtrarTabla() {
            const marcaId = document.getElementById('filterMarca').value;
            const modeloId = document.getElementById('filterModelo').value;
            const rows = document.querySelectorAll('#tablaProductos tbody tr');
            
            // Implementar filtrado por marca y modelo
            // Este es un ejemplo básico, necesitarías agregar data attributes a las filas
        }

        // Ver detalles
        function verDetalles(id) {
            fetch(`/admin/productos/${id}/detalles`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('detallesContent').innerHTML = `
                        <div class="row g-3">
                            <div class="col-12">
                                <h4>${data.Nombre}</h4>
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <strong>ID:</strong> #${data.Id_Articulos}
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha de creación:</strong> ${new Date(data.fecha_creacion).toLocaleDateString()}
                            </div>
                            <div class="col-12">
                                <strong>Descripción:</strong>
                                <p class="text-muted">${data.Descripcion || 'Sin descripción'}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Marca:</strong>
                                <span class="badge bg-info">${data.marca.Nombre}</span>
                                <p class="text-muted small mt-1">${data.marca.Descripcion || ''}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Modelo:</strong>
                                <span class="badge bg-primary">${data.modelo.Nombre}</span>
                                <p class="text-muted small mt-1">${data.modelo.Descripcion || ''}</p>
                            </div>
                            <div class="col-12">
                                <strong>Veces solicitado:</strong>
                                <span class="badge bg-success">${data.veces_solicitado}</span>
                            </div>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('modalDetalles')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los detalles');
                });
        }

        // Editar producto
        function editarProducto(id) {
            fetch(`/admin/productos/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('tituloModal').textContent = 'Editar Producto';
                    document.getElementById('productoId').value = data.Id_Articulos;
                    document.getElementById('nombre').value = data.Nombre;
                    document.getElementById('descripcion').value = data.Descripcion || '';
                    document.getElementById('marca_id').value = data.Id_Marca;
                    document.getElementById('modelo_id').value = data.Id_Modelo;
                    document.getElementById('methodField').value = 'PUT';
                    document.getElementById('formProducto').action = `/admin/productos/${id}`;
                    
                    new bootstrap.Modal(document.getElementById('modalCrearProducto')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del producto');
                });
        }

        // Eliminar producto
        function eliminarProducto(id, nombre) {
            if (confirm(`¿Está seguro de eliminar el producto "${nombre}"?`)) {
                fetch(`/admin/productos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Producto eliminado exitosamente');
                        location.reload();
                    } else {
                        alert('Error al eliminar el producto');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el producto');
                });
            }
        }

        // Resetear modal al cerrar
        document.getElementById('modalCrearProducto').addEventListener('hidden.bs.modal', function () {
            document.getElementById('formProducto').reset();
            document.getElementById('tituloModal').textContent = 'Nuevo Producto';
            document.getElementById('productoId').value = '';
            document.getElementById('methodField').value = 'POST';
            document.getElementById('formProducto').action = '{{ route("admin.productos.store") }}';
        });

        // Manejar creación de marca
        document.getElementById('formMarca').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar nueva marca al select
                    const option = new Option(data.marca.Nombre, data.marca.Id_Marca, true, true);
                    document.getElementById('marca_id').appendChild(option);
                    
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearMarca')).hide();
                    
                    // Limpiar formulario
                    this.reset();
                    
                    alert('Marca creada exitosamente');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear la marca');
            });
        });

        // Manejar creación de modelo
        document.getElementById('formModelo').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar nuevo modelo al select
                    const option = new Option(data.modelo.Nombre, data.modelo.Id_Modelo, true, true);
                    document.getElementById('modelo_id').appendChild(option);
                    
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('modalCrearModelo')).hide();
                    
                    // Limpiar formulario
                    this.reset();
                    
                    alert('Modelo creado exitosamente');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear el modelo');
            });
        });