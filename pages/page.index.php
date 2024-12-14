<?php
$filterPath = __DIR__ . '/../components/filter/Filter.php';

if (!file_exists($filterPath)) {
    error_log("Error: No se puede encontrar el archivo Filter.php en: " . $filterPath);
    die("Error: Componente Filter no encontrado");
}

require_once $filterPath;
?>