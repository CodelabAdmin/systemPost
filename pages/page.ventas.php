<?php
function getProducts()
{
    try {
        $url = "http://localhost/server/systemPost/api/inventories/products?status=activo";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data && isset($data['data'])) {
            return $data['data'];
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

// Asegurarse de que la página actual es válida
$paginaActual = max(1, min($paginaActual, $totalPaginas));

// Calcular el índice de inicio para la página actual
$indiceInicio = ($paginaActual - 1) * $productosPorPagina;

// Obtener los productos para la página actual
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
}
;

?>

<div class="content-sales">
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
                                        <button class="btn-accions">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                class="size-6">
                                                <path fill-rule="evenodd"
                                                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z"
                                                    clip-rule="evenodd" />
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
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productosEnCarrito as $producto): ?>
                            <tr>
                                <td><?php echo $producto['name']; ?></td>
                                <td><?php echo number_format($producto['product_price'], 3, '.', ','); ?></td>
                                <td class="action-buttons">
                                    <button class="decrease-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="icon">
                                            <circle cx="12" cy="12" r="10" fill="#5555AD" />
                                            <path d="M7 12h10" stroke="white" stroke-width="2" />
                                        </svg>
                                    </button>
                                    <span class="quantity"><?php echo $producto['stock']; ?></span>
                                    <button class="increase-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="icon">
                                            <circle cx="12" cy="12" r="10" fill="#5555AD" />
                                            <path d="M12 7v10M7 12h10" stroke="white" stroke-width="2" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="content-info-total">
                <div class="content-2-total">
                    <div class="content-subtotal">
                        <div class="">Subtotal</div>
                        <div class="">$21.000</div>
                    </div>
                    <div class="content-iva">
                        <div class="">Iva</div>
                        <div class="">0.0</div>
                    </div>
                    <div class="content-total">
                        <div class="">Total</div>
                        <div class="">$21.000</div>
                    </div>
                </div>
                <div class="content-2-button">
                    <button class="btn-cancelar btn">Cancelar</button>
                    <button class="btn-confirmar btn " onclick="toggleModal()">Confirmar Venta</button>
                </div>
                
            </div>
        </div>
    </div> 
    <?php require ('./components/Modal/Modal.ventas.php'); ?>
</div>