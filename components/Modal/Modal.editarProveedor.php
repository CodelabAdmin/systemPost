                <div class="content-modal-edit" style="display: none;" >
                    <div class="content-modal-container">
                        <div class="content-form">
                            <div class="header-modal">
                            <div class="content-title">
                                <div class="title">Editar Proveedor</div>
                                <div class="close-modal" onclick='toggleModalEdit()' >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="cancel-icon-modal">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            <div class="separator"></div>
                        </div>
                        <div class="content-info">
                            <form id="provider-form" class="content-form">
                                <input type="hidden" id="edit-id" name="id">
                                <div class="form-row">
                                    <input type="text" id="edit-nombre" placeholder="Nombre" name="nombre">
                                    <input type="text" id="edit-telefono" placeholder="Telefono" name="telefono">

                                </div>
                                <div class="form-row">
                                    <input type="text" id="edit-direccion" placeholder="Dirección" name="direccion">
                                    <input type="text" id="edit-categoria" placeholder="Categoria" name="categoria">
                                </div>
                                <div class="form-row">
                                    <input type="text" id="edit-descripcion" placeholder="Descripción" name="descripcion">

                                </div>
                            </form>
                        </div>
                        <div class="content-buttons-suppliers">
                            <div class="btn-modal button-cancel" onclick='toggleModalEdit()'>Cancelar</div>
                            <div class="btn-modal button-confirm" onclick='editsuppliers()'>Agregar</div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    async function editsuppliers() {
                        try {
                            //validacion
                            const providerId = document.querySelector('input[id="edit-id"]').value;
                            const fullname = document.querySelector('input[id="edit-nombre"]').value;
                            const phone = document.querySelector('input[id="edit-telefono"]').value;
                            const address = document.querySelector('input[id="edit-direccion"]').value;
                            const description = document.querySelector('input[id="edit-descripcion"]').value;
                            const category = document.querySelector('input[id="edit-categoria"]').value;
                            
                           
                            // Validar que los campos no estén vacíos
                            if (!fullname || !phone || !category || !address || !description) {
                                await Swal.fire({
                                    title: 'Error',
                                    text: 'Todos los campos son requeridos',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                                return;
                            }

                            // Crear el payload estructurado
                            const supplierData = {
                                fullname: fullname.trim(),
                                phone: phone.replace(/\s/g, ''), // Eliminar espacios en blanco
                                address: address.trim(),
                                description: description.trim(),
                                category: category.trim()
                            };

                            // Validaciones específicas
                            if (supplierData.phone.length < 8 || supplierData.phone.length > 10) {
                                await Swal.fire({
                                    title: 'Error',
                                    text: 'El número de teléfono debe tener entre 8 y 10 dígitos',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                                return;
                            }

                            // Validar que todos los campos tengan el formato correcto
                            if (!supplierData.fullname || supplierData.fullname.length < 3) {
                                await Swal.fire({
                                    title: 'Error',
                                    text: 'El nombre del proveedor debe tener al menos 3 caracteres',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                                return;
                            }

                            // Mostrar loading
                            Swal.fire({
                                title: 'Procesando',
                                text: 'Guardando proveedor...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            try {
                                const response = await fetch(`http://localhost/server/systemPost/api/suppliers?id=${providerId}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(supplierData)
                                });
                                
                                // Verificar si la respuesta es exitosa
                                if (!response.ok) {
                                    throw new Error('Error en la respuesta del servidor: ' + response.statusText);
                                }

                                // Capturar el texto de la respuesta primero
                                const responseText = await response.text();
                                
                                // Intentar parsear como JSON
                                let data;
                                try {
                                    data = JSON.parse(responseText);
                                } catch (e) {
                                    console.error('Respuesta del servidor:', responseText);
                                    throw new Error('El servidor no respondió en formato JSON. Respuesta: ' + responseText.substring(0, 100));
                                }

                                if (response.status === 409) {
                                    await Swal.fire({
                                        title: 'Error',
                                        text: data.message || 'El proveedor ya existe',
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar'
                                    });
                                    return;
                                }

                                if (data.status === 'Success') {
                                    await Swal.fire({
                                        title: 'Éxito',
                                        text: 'Proveedor editado correctamente',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar',
                                        allowOutsideClick: false
                                    });
                                    toggleModalEdit();
                                    
                                    // Recargar la página
                                    location.reload();
                                } else {
                                    throw new Error(data.message || 'Error al editar el proveedor');
                                }

                            } catch (error) {
                                console.error('Error en la solicitud:', error);
                                await Swal.fire({
                                    title: 'Error en el Sistema',
                                    text: error.message || 'Error al procesar la solicitud',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            }

                        } catch (error) {
                            console.error('Error completo:', error);
                            await Swal.fire({
                                title: 'Error en el Sistema',
                                text: error.message || 'Error al procesar la solicitud',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    }

                </script>