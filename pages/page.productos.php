<?php
function countProducts()
{
    try {
        // Obtener la URL base dinámicamente
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . $host;
        
        // Construir la URL completa
        $url = $baseUrl . "/api/products/count";
        
        // Configurar el contexto para manejar posibles errores
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 30
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response === FALSE) {
            throw new Exception("Error al obtener datos");
        }
        
        $data = json_decode($response, true);
        
        if (isset($data["COUNT(*)"]) && $data["COUNT(*)"] !== null) {
            return ['count' => $data["COUNT(*)"]];
        } else {
            return ['count' => 0];
        }
    } catch (Exception $e) {
        error_log("Error en countProducts: " . $e->getMessage());
        return ['count' => 0];
    }
}

// Asegurarnos de que countProducts() devuelva siempre un array válido
$countProducts = countProducts();
if (!is_array($countProducts) || !isset($countProducts['count'])) {
    $countProducts = ['count' => 0];
}
?>

<div class="page-productos">
    <div class="container-header">
        <div class="content-info">
            <div class="content-title">
                Productos
            </div>
            <div class="content-descripcion">
                El módulo de productos gestiona la información de los productos del sistema, permitiendo registrar, actualizar, eliminar y consultar sus datos de forma eficiente. Facilita la organización del inventario y garantiza que los usuarios puedan acceder a detalles clave de cada producto, como nombre, stock, precio y categoría.
            </div>
            <div class="btn-add-product" onclick="toggleModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-btn-add">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <div>Crear</div>
            </div>
        </div>
        <div class="content-counter">
            <div class="content-info-counter">
                <div class="info-counter">
                    <div class="icono-counter">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 4.509l-.232-.867A1.875 1.875 0 0 0 3.636 2.25H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
                        </svg>
                    </div>
                    <div class="number-counter">
                        <?php echo $countProducts['count']; ?>
                    </div>
                </div>
                <div class="title-counter">
                    cantidad de productos
                </div>
            </div>
        </div>
    </div>
    <div class="container-productos">
        <div class="container-Table-productos">
            <?php require('./components/Table/Table.productos.php'); ?>
        </div>
    </div>
    <?php require ('./components/Modal/Modal.productos.php'); ?>

</div>