<?php
function countProducts()
{
    try {
        $url = "https://systempost.onrender.com/api/inventories/products/count";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        return ['count' => $data["total"]];
    } catch (Exception $e) {
        return ['count' => 0];
    }
}
$countProducts = countProducts();
?>

<div class="page-inventario">
    <div class="container-header-inventario">
        <div class="content-info-inventario">
            <div class="content-title-inventario">
                Inventario
            </div>
            <div class="content-description-inventario">
            El módulo de inventario facilita la consulta de productos activos en el sistema mediante una tabla que permite filtrarlos y ver detalles en un modal. Está enfocado solo en la visualización y control del inventario, sin opciones para modificar o eliminar productos
            </div>
        </div>
        <div class="content-counter-inventario">
            <div class="content-info-counter-inventario">
                <div class="info-counter-inventario">
                    <div class="icono-counter-inventario">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 4.509l-.232-.867A1.875 1.875 0 0 0 3.636 2.25H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
                        </svg>
                    </div>
                    <div class="number-counter-inventario">
                    <?php echo $countProducts['count']; ?>
                    </div>
                </div>
                <div class="title-counter-inventario">
                    Cantidad de productos
                </div>
            </div>
        </div>
    </div>
    <div class="container-table">
       <div class="table-inventario">
            <?php require('./components/Table/Table.inventario.php') ?>
        </div> 
    </div>
    
</div>