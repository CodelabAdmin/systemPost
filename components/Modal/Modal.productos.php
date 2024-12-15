<link rel="stylesheet" href="./assets/css/producto.css">
<div class="content-modal" style="display: none;">
    <div class="content-modal-container">
        <div class="content-form">
            <div class="header-modal">
                <div class="content-title">
                    <div class="title">Agregar Producto</div>
                    <div class="close-modal" onclick='toggleModal()'>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="cancel-icon-modal">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </div>
            <form id="product-form" class="content-form">
                <div class="container-product">
                    <div class="column">
                        <input type="text" placeholder="Nombre" name="nombre" id="nombre" class="input-product">
                        <input type="number" placeholder="Precio" name="precio" id="precio" class="input-product">
                        <input type="number" placeholder="Stock" name="stock" id="stock" class="input-product">
                        <select name="proveedor" id="proveedor" required>
                            <option value="" selected disabled>Selecciona un proveedor</option>
                            <option value="1">Proveedor 1</option>
                        </select>
                    </div>
                    <div class="column">
                        <select name="categoria" id="categoria" required>
                            <option value="" selected disabled>Selecciona una categoría</option>
                            <option value="Alimentos">Alimentos</option>
                            <option value="Bebidas">Bebidas</option>
                            <option value="Electronica">Electronica</option>
                            <option value="Hogar">Hogar</option>
                            <option value="Muebles">Muebles</option>
                            <option value="Ropa">Ropa</option>
                            <option value="Tecnologia">Tecnologia</option>
                            <option value="Vehiculos">Vehiculos</option>
                            <option value="Otros">Otros</option>
                        </select>
                        
                        <textarea placeholder="Descripción" name="descripcion" id="descripcion" class="input-product textarea"></textarea>
                    </div>
                </div>
            </form>
            <div class="content-buttons-product">
                <div class="btn-modal button-cancel-product" onclick='toggleModal()'>Cancelar</div>
                <div class="btn-modal button-confirm-product" onclick='addProduct()'>Agregar</div>
            </div>
        </div>
    </div>

    <script>
        async function addProduct() {
            try {
                // Validar campos requeridos
                const nombre = document.querySelector('input[id="nombre"]').value;
                const precio = document.querySelector('input[id="precio"]').value;
                const stock = document.querySelector('input[id="stock"]').value;
                const categoria = document.querySelector('select[id="categoria"]').value;
                const descripcion = document.querySelector('textarea[id="descripcion"]').value;

                // Validar que los campos no estén vacíos
                if (!nombre || !precio || !stock || !categoria) {
                    await Swal.fire({
                        title: 'Error',
                        text: 'Todos los campos son requeridos',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                // Validar que precio y stock sean números válidos
                if (isNaN(precio) || precio <= 0) {
                    await Swal.fire({
                        title: 'Error',
                        text: 'El precio debe ser un número válido mayor a 0',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                if (isNaN(stock) || stock <= 0) {
                    await Swal.fire({
                        title: 'Error',
                        text: 'El stock debe ser un número válido mayor a 0',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                const proveedorId = document.querySelector('select[id="proveedor"]').value;

                const productData = {
                    name: nombre.trim(),
                    product_price: parseFloat(precio),
                    stock: parseInt(stock),
                    category: categoria,
                    description: descripcion.trim(),
                    suppliers: [
                        {
                            "id_supplier": proveedorId
                        }
                    ]
                };

                // Mostrar loading
                Swal.fire({
                    title: 'Procesando',
                    text: 'Guardando producto...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch('http://localhost/server/systemPost/api/products', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(productData)
                });

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
                        text: data.message || 'El producto ya existe',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                if (data.status === 'Success') {
                    await Swal.fire({
                        title: 'Éxito',
                        text: 'Producto agregado correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        allowOutsideClick: false
                    });
                    
                    // Cerrar el modal de producto
                    document.querySelector('.content-modal').style.display = 'none';
                    
                    // Limpiar el formulario
                    document.getElementById('product-form').reset();
                    
                    // Recargar la página
                    location.reload();
                } else {
                    throw new Error(data.message || 'Error al agregar el producto');
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