<?php   
    $listRoutes = array(
        'prueba',
        'index',
        'ventas',
        'proveedores',
        'inventario',
        'productos',
        'empleados',
        'reportes'

    );
    foreach($listRoutes as $route) {
        require 'route.'.$route.'.php';
    }
?>
