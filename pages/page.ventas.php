<?php
function getProducts()
{
    try {
        $url = "https://systempost.onrender.com/api/inventories/products?status=activo";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data && isset($data['data'])) {
            // return $data['data'];
            // Filtrar productos con stock mayor a 0
            return array_filter($data['data'], function ($producto) {
                return $producto['stock'] > 0;
            });
        } else {
            return [];
        }
    } catch (Exception $e) {
        throw new Exception("Error al cargar los productos: " . $e->getMessage());
        return [];
    }
}

$productosEnCarrito = getProducts();
if (!is_array($productosEnCarrito)) {
    $productosEnCarrito = [];
}

$productosPorPagina = 5;
$paginaActual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$totalProductos = count($productosEnCarrito);
$totalPaginas = ceil($totalProductos / $productosPorPagina);

$paginaActual = max(1, min($paginaActual, $totalPaginas));
$indiceInicio = ($paginaActual - 1) * $productosPorPagina;
$productosEnPagina = array_slice($productosEnCarrito, $indiceInicio, $productosPorPagina);

function getPaginationRange($paginaActual, $totalPaginas, $maxPaginas = 3)
{
    $mitad = floor($maxPaginas / 2);
    $inicio = max(1, min($paginaActual - $mitad, $totalPaginas - $maxPaginas + 1));
    $fin = min($totalPaginas, $inicio + $maxPaginas - 1);
    return range($inicio, $fin);
}

$paginasAMostrar = getPaginationRange($paginaActual, $totalPaginas, 3);

function formatText($text)
{
    if ($text == null) {
        return $text;
    }
    if (strlen($text) > 50) {
        return substr($text, 0, 30) . '...';
    }
    return $text;
};

?>

<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-content {
        text-align: center;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #5555AD;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .loading-content p {
        color: #5555AD;
        font-size: 1.2rem;
        margin: 0;
    }

    /* Estilos para el Modal de Confirmación */
    .content-modal-ventas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .content-modal-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .content-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .title-modal {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
        text-align: center;
        margin-bottom: 15px;
    }

    .total-field {
        width: 100%;
        padding: 10px;
        text-align: center;
        font-size: 1.2rem;
        font-weight: bold;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 10px 0;
    }

    .question {
        text-align: center;
        color: #666;
        margin: 15px 0;
    }

    .content-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .btn-confirmar-venta,
    .btn-cancelar-venta {
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-confirmar-venta {
        background: #5555AD;
        color: white;
    }

    .btn-cancelar-venta {
        background: #dc3545;
        color: white;
    }



    .modal-dialog {
        background: white;
        border-radius: 8px;
        width: 90%;
        max-width: 400px;
        margin: 1.75rem auto;
    }

    .modal-content {
        position: relative;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        background: #5555AD;
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .modal-title {
        margin: 0;
        font-size: 1.25rem;
    }

    .btn-close {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
    }

    .modal-body {
        padding: 1rem;
        text-align: center;
    }

    .payment-details {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin: 20px 0;
    }

    .payment-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .payment-field label {
        font-weight: 600;
        color: #333;
    }

    .payment-field input {
        width: 150px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-align: right;
        font-size: 1.1rem;
    }

    .payment-field .result {
        font-weight: bold;
        font-size: 1.1rem;
        color: #5555AD;
    }

    .btn-confirmar-venta:disabled {
        background: #cccccc;
        cursor: not-allowed;
    }
</style>

<div class="content-sales">
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p>Cargando...</p>
        </div>
    </div>

    <div class="header-sales">Realizar una venta</div>
    
    <div class="content-info">
        <div class="content-1">
            <div class="container">
                <div class="table-container table-sales">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Stock</th>
                                <th>Precio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosEnPagina as $producto): ?>
                                <tr>
                                    <td><?php echo $producto['id_product']; ?></td>
                                    <td><?php echo $producto['name']; ?></td>
                                    <td><?php echo $producto['stock'] ?></td>
                                    <td><?php echo $producto['product_price']; ?></td>
                                    <td class="text-center acciones">
                                        <button class="btn-accions" onclick="agregarProducto(<?php
                                                                                                echo htmlspecialchars(json_encode([
                                                                                                    'id_product' => $producto['id_product'],
                                                                                                    'name' => $producto['name'],
                                                                                                    'stock' => $producto['stock'],
                                                                                                    'product_price' => $producto['product_price']
                                                                                                ]));
                                                                                                ?>)">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="container-pagination">
                    <div class="pagination">
                        <a href="?pagina=<?php echo max(1, $paginaActual - 1); ?>"
                            class="pagination-arrow <?php echo $paginaActual == 1 ? 'disabled' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="icon-paginator">
                                <path fill-rule="evenodd"
                                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-4.28 9.22a.75.75 0 0 0 0 1.06l3 3a.75.75 0 1 0 1.06-1.06l-1.72-1.72h5.69a.75.75 0 0 0 0-1.5h-5.69l1.72-1.72a.75.75 0 0 0-1.06-1.06l-3 3Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <?php foreach ($paginasAMostrar as $pagina): ?>
                            <a href="?pagina=<?php echo $pagina; ?>"
                                class="pagination-number <?php echo $pagina == $paginaActual ? 'active' : ''; ?>">
                                <?php echo $pagina; ?>
                            </a>
                        <?php endforeach; ?>
                        <a href="?pagina=<?php echo min($totalPaginas, $paginaActual + 1); ?>"
                            class="pagination-arrow <?php echo $paginaActual == $totalPaginas ? 'disabled' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="icon-paginator">
                                <path fill-rule="evenodd"
                                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm4.28 10.28a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 1 0-1.06 1.06l1.72 1.72H8.25a.75.75 0 0 0 0 1.5h5.69l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-2">
            <div class="content-2-header">Detalle de venta</div>
            <div class="product-info">
                <table class="sale-details-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio $</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody id="detalleVentaBody">
                        <!-- Los items se agregarán dinámicamente -->
                    </tbody>
                </table>
            </div>
            <div class="content-info-total">
                <div class="content-2-total">
                    <div class="content-subtotal">
                        <div class="">Subtotal</div>
                        <div id="subtotal">$0.00</div>
                    </div>
                    <div class="content-iva">
                        <div class="">Iva 16%</div>
                        <div id="iva">$0.00</div>
                    </div>
                    <div class="content-total">
                        <div class="">Total</div>
                        <div id="total">$0.00</div>
                    </div>
                </div>
                <div class="content-2-button">
                    <button class="btn-cancelar btn" onclick="cancelarVenta()">Cancelar</button>
                    <button class="btn-confirmar btn" onclick="confirmarVenta()">Confirmar Venta</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="content-modal-ventas" style="display: none;">
        <div class="content-modal-container">
            <div class="content-form">
                <div class="content-title">
                    <div class="title-modal">Confirmar Venta</div>
                </div>
                <div class="payment-details">
                    <div class="payment-field">
                        <label>Total a Pagar:</label>
                        <div id="modal-total" class="result">$0.00</div>
                    </div>
                    <div class="payment-field">
                        <label>Efectivo Recibido:</label>
                        <input type="number" id="efectivo-recibido" min="0" step="0.01" onchange="calcularCambio()">
                    </div>
                    <div class="payment-field">
                        <label>Cambio:</label>
                        <div id="cambio" class="result">$0.00</div>
                    </div>
                </div>
                <div class="content-buttons">
                    <div class="btn-confirmar-venta" id="btn-procesar" onclick="procesarVenta()" disabled>Procesar Venta</div>
                    <div class="btn-cancelar-venta" onclick="document.querySelector('.content-modal-ventas').style.display='none'">Cancelar</div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        let user_login = <?php echo json_encode($_SESSION['data_user']); ?>;
        let user_rol = user_login.rol;
        let user_name = user_login.nombre;

        let carritoVenta = [];
        const IVA = 0.16;

        function agregarProducto(producto) {
            producto.product_price = parseFloat(producto.product_price);
            const itemExistente = carritoVenta.find(item => item.id_product === producto.id_product);

            if (itemExistente) {
                if (itemExistente.cantidad < producto.stock) {
                    itemExistente.cantidad++;
                    itemExistente.subtotal = itemExistente.cantidad * itemExistente.product_price;
                } else {
                    Swal.fire({
                        title: 'Alerta',
                        text: 'No hay más stock disponible para este producto',
                        icon: 'warning',
                        timer: 2000,
                        showConfirmButton: false,
                        showCancelButton: false,
                    });
                    return;
                }
            } else {
                carritoVenta.push({
                    ...producto,
                    cantidad: 1,
                    subtotal: producto.product_price
                });
            }

            actualizarTablaDetalle();
            calcularTotales();
        }

        function actualizarTablaDetalle() {
            const tbody = document.getElementById('detalleVentaBody');
            tbody.innerHTML = '';

            carritoVenta.forEach(item => {
                const precio = parseFloat(item.product_price);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.name}</td>
                    <td>$${precio.toFixed(2)}</td>
                    <td class="action-buttons">
                        <button type="button" class="decrease-btn" onclick="actualizarCantidad('${item.id_product}', -1)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon">
                                <circle cx="12" cy="12" r="10" fill="#5555AD" />
                                <path d="M7 12h10" stroke="white" stroke-width="2" />
                            </svg>
                        </button>
                        <span class="quantity">${item.cantidad}</span>
                        <button type="button" class="increase-btn" onclick="actualizarCantidad('${item.id_product}', 1)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon">
                                <circle cx="12" cy="12" r="10" fill="#5555AD" />
                                <path d="M12 7v10M7 12h10" stroke="white" stroke-width="2" />
                            </svg>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function actualizarCantidad(idProducto, cambio) {
            const item = carritoVenta.find(item => item.id_product === parseInt(idProducto) || item.id_product === idProducto);

            if (item) {
                const nuevaCantidad = item.cantidad + cambio;

                if (nuevaCantidad <= 0) {
                    Swal.fire({
                        title: 'Alerta',
                        text: '¿Desea eliminar este producto del carrito?',
                        icon: 'warning',
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            carritoVenta = carritoVenta.filter(item =>
                                item.id_product !== parseInt(idProducto) &&
                                item.id_product !== idProducto
                            );
                            actualizarTablaDetalle();
                            calcularTotales();
                        }
                    });
                } else if (nuevaCantidad > item.stock) {
                    Swal.fire({
                        title: 'Alerta',
                        text: 'No hay más stock disponible para este producto',
                        icon: 'warning',
                        timer: 2000,
                        showConfirmButton: false,
                        showCancelButton: false,
                    });
                    return;
                } else {
                    item.cantidad = nuevaCantidad;
                    item.subtotal = item.cantidad * parseFloat(item.product_price);
                }

                actualizarTablaDetalle();
                calcularTotales();
            }
        }

        function calcularTotales() {
            const subtotal = carritoVenta.reduce((sum, item) => sum + item.subtotal, 0);
            const ivaCalculado = subtotal * IVA;
            const total = subtotal + ivaCalculado;

            document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('iva').textContent = `$${ivaCalculado.toFixed(2)}`;
            document.getElementById('total').textContent = `$${total.toFixed(2)}`;
        }

        function cancelarVenta() {
            if (confirm('¿Está seguro de cancelar la venta?')) {
                carritoVenta = [];
                actualizarTablaDetalle();
                calcularTotales();
            }
        }

        function showLoading() {
            document.getElementById('loading-overlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loading-overlay').style.display = 'none';
        }

        window.toggleModal = function(show = null) {
            const modal = document.querySelector('.content-modal-ventas');
            if (!modal) {
                console.error('Modal no encontrado');
                return;
            }

            if (show === null) {
                // Toggle basado en el estado actual
                modal.style.display = modal.style.display === 'none' || !modal.style.display ? 'flex' : 'none';
            } else {
                // Establecer un estado específico
                modal.style.display = show ? 'flex' : 'none';
            }
        }

        function confirmarVenta() {
            if (carritoVenta.length === 0) {
                Swal.fire({
                    title: 'Alerta',
                    text: 'Agregue productos a la venta',
                    icon: 'warning',
                    timer: 2000,
                    showConfirmButton: false,
                    showCancelButton: false,
                });
                return;
            }

            const modal = document.querySelector('.content-modal-ventas');
            if (!modal) {
                console.error('Modal no encontrado');
                return;
            }

            // Actualizar valores del modal
            const totalActual = document.getElementById('total').textContent;
            document.getElementById('modal-total').textContent = totalActual;
            document.getElementById('efectivo-recibido').value = '';
            document.getElementById('cambio').textContent = '$0.00';

            // Deshabilitar botón de procesar
            const btnProcesar = document.getElementById('btn-procesar');
            if (btnProcesar) {
                btnProcesar.disabled = true;
                btnProcesar.style.opacity = '0.5';
            }

            // Mostrar modal
            modal.style.display = 'flex';
        }

        function generarTicket() {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                unit: 'mm',
                format: [80, 220] 
            });

            let y = 5; // Empezamos más arriba

            // Función helper para centrar texto
            const centrarTexto = (texto, y) => {
                pdf.text(texto, 40, y, { align: 'center' });
            };

            // Función helper para línea divisoria
            const lineaDivisoria = (y) => {
                centrarTexto('========================================', y);
                return y + 4;
            };

            // Encabezado Principal
            pdf.setFontSize(13);
            centrarTexto('SystemPost', y);
            y += 6;
            
            // Información de la empresa
            pdf.setFontSize(8);
            centrarTexto('CODELAB S.A.S', y); y += 4;
            centrarTexto('NIT: 0101010101', y); y += 4;
            centrarTexto('Medellin, Colombia', y); y += 4;
            centrarTexto('Tel: (+57) 123-456789', y); y += 4;
            centrarTexto('www.codelab.com', y); y += 4;

            // Agregar después de la información de la empresa
            centrarTexto('Régimen: Responsable de IVA', y); y += 4;
            centrarTexto('Facturación Electrónica Res. DIAN 000012', y); y += 4;
            centrarTexto('Autorización: 18764000000001-99999999', y); y += 4;
            centrarTexto('Vigencia: 24/01/2024 - 24/01/2025', y); y += 4;

            // Tipo de Comprobante
            y = lineaDivisoria(y);
            pdf.setFontSize(10);
            centrarTexto('TICKET DE VENTA', y); y += 4;
            pdf.setFontSize(9);
            centrarTexto('ORIGINAL', y); y += 4;

            // Información de la venta
            y = lineaDivisoria(y);
            pdf.setFontSize(8);
            
            const fechaActual = new Date();
            const numeroTicket = 'T-' + Math.floor(Math.random() * 100000).toString().padStart(5, '0');
            
            pdf.text(`TICKET: ${numeroTicket}`, 5, y); y += 4;
            pdf.text(`FECHA: ${fechaActual.toLocaleDateString('es-ES')}`, 5, y); y += 4;
            pdf.text(`HORA: ${fechaActual.toLocaleTimeString('es-ES')}`, 5, y); y += 4;
            pdf.text(`CAJERO: ${user_name}`, 5, y); y += 4;
            pdf.text(`ROL: ${user_rol}`, 5, y); y += 4;
            pdf.text('CLIENTE: 2222', 5, y); y += 4;
            pdf.text(`FORMA DE PAGO: CONTADO`, 5, y); y += 4;
            pdf.text(`MEDIO DE PAGO: EFECTIVO`, 5, y); y += 4;
            pdf.text(`ORDEN DE COMPRA: N/A`, 5, y); y += 4;



            // Encabezado de productos
            y = lineaDivisoria(y);
            pdf.text('CANT  DESCRIPCION          P.UNIT    IMPORTE', 5, y);
            y = lineaDivisoria(y+4);

            // Detalles de productos
            carritoVenta.forEach(item => {
                const subtotal = item.cantidad * item.product_price;
                
                // Formatear campos
                const cantidad = item.cantidad.toString().padStart(2, ' ');
                const nombre = item.name.padEnd(20, ' ').substring(0, 20);
                const precio = item.product_price.toFixed(2).padStart(7, ' ');
                const total = subtotal.toFixed(2).padStart(9, ' ');
                
                // Primera línea: cantidad y descripción
                pdf.text(`${cantidad} ${nombre}`, 5, y);
                y += 4;
                // Segunda línea: precio unitario y total
                pdf.text(`${' '.repeat(4)}${precio} X ${cantidad} = ${total}`, 5, y);
                y += 5;
            });

            // Totales
            y = lineaDivisoria(y);
            const subtotal = carritoVenta.reduce((sum, item) => sum + (item.cantidad * item.product_price), 0);
            const iva = subtotal * IVA;
            const total = subtotal + iva;

            pdf.text(`SUBTOTAL:${' '.repeat(24)}$${subtotal.toFixed(2)}`, 5, y); y += 4;
            pdf.text(`I.V.A 16%:${' '.repeat(22)}$${iva.toFixed(2)}`, 5, y); y += 4;
            pdf.setFontSize(10);
            pdf.text(`TOTAL:${' '.repeat(24)}$${total.toFixed(2)}`, 5, y); y += 6;

            // Información de pago
            pdf.setFontSize(8);
            y = lineaDivisoria(y);
            const efectivoRecibido = document.getElementById('efectivo-recibido').value;
            const cambio = efectivoRecibido - total;

            pdf.text('DESGLOSE DE PAGO:', 5, y); y += 4;
            pdf.text(`EFECTIVO:${' '.repeat(25)}$${parseFloat(efectivoRecibido).toFixed(2)}`, 5, y); y += 4;
            pdf.text(`CAMBIO:${' '.repeat(27)}$${cambio.toFixed(2)}`, 5, y); y += 4;

            // Letras
            y = lineaDivisoria(y);
           
            // Información fiscal y políticas
            pdf.setFontSize(7);
            centrarTexto('ESTE DOCUMENTO ES UN COMPROBANTE FISCAL', y); y += 3;
            centrarTexto('EFECTOS FISCALES AL PAGO', y); y += 6;

            // Políticas
            pdf.setFontSize(6);
            centrarTexto('POLÍTICAS DE DEVOLUCIÓN:', y); y += 3;
            centrarTexto('- 7 DÍAS CON TICKET Y PRODUCTO EN BUEN ESTADO', y); y += 3;
            centrarTexto('- NO APLICA EN PRODUCTOS PERECEDEROS', y); y += 3;
            centrarTexto('- CAMBIOS ÚNICAMENTE POR EL MISMO ARTÍCULO', y); y += 6;

            // Mensaje de agradecimiento
            pdf.setFontSize(9);
            centrarTexto('¡GRACIAS POR SU PREFERENCIA!', y); y += 4;
            pdf.setFontSize(7);
            centrarTexto('Lo esperamos pronto', y); y += 6;

            // Agregar después de los totales
            y = lineaDivisoria(y);
            

            // Información adicional fiscal
            pdf.setFontSize(7);
            centrarTexto('FACTURA ELECTRÓNICA DE VENTA', y); y += 3;
            centrarTexto(`No. FEVE-${numeroTicket}`, y); y += 3;
            centrarTexto('Fecha validación DIAN:', y); y += 3;
            centrarTexto(`${fechaActual.toLocaleString('es-ES')}`, y); y += 6;

            // Abrir el ticket en una nueva ventana
            window.open(pdf.output('bloburl'), '_blank');
        }

        async function procesarVenta() {
            try {
                showLoading();
                const modalVentas = document.querySelector('.content-modal-ventas');
                if (modalVentas) {
                    modalVentas.style.display = 'none';
                }

                const ventaData = {
                    id_user: 1,
                    sales: carritoVenta.map(item => ({
                        id_product: parseInt(item.id_product),
                        quantity: parseInt(item.cantidad)
                    }))
                };

                const response = await fetch('https://systempost.onrender.com/api/sales', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(ventaData)
                });

                const data = await response.json();

                if (data.status === 'ok') {
                    // Generar e imprimir el ticket
                    generarTicket();
                    
                    carritoVenta = [];
                    actualizarTablaDetalle();
                    calcularTotales();

                    Swal.fire({
                        title: 'Venta Exitosa',
                        text: 'Venta creada correctamente',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        showCancelButton: false,
                    }).then(() => {
                        window.location.href = 'https://systempost.onrender.com/ventas';
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al procesar la venta',
                        icon: 'error',
                        timer: 1000,
                        showConfirmButton: false,
                        showCancelButton: false,
                    });
                }
            } catch (error) {
                console.error('Error completo:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al procesar la venta: ' + error.message,
                    icon: 'error',
                    timer: 2000,
                    showConfirmButton: false,
                    showCancelButton: false,
                });
            } finally {
                hideLoading();
            }
        }

        function calcularCambio() {
            const totalStr = document.getElementById('modal-total').textContent;
            const totalVenta = parseFloat(totalStr.replace('$', '').replace(',', ''));
            const efectivoRecibido = parseFloat(document.getElementById('efectivo-recibido').value) || 0;
            const cambio = efectivoRecibido - totalVenta;

            document.getElementById('cambio').textContent = `$${Math.max(0, cambio).toFixed(2)}`;

            // Habilitar/deshabilitar botón de procesar
            const btnProcesar = document.getElementById('btn-procesar');
            if (btnProcesar) {
                btnProcesar.disabled = efectivoRecibido < totalVenta;
                btnProcesar.style.opacity = efectivoRecibido < totalVenta ? '0.5' : '1';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            showLoading();
            setTimeout(hideLoading, 500);
        });

    </script>
</div>