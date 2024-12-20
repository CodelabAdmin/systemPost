<?php
function getProducts()
{
   try {
      $url = "http://localhost/server/systemPost/api/products";
      $response = file_get_contents($url);
      $data = json_decode($response, true);

      if ($data && isset($data['products'])) {
         return $data['products'];
      } else {
         return [];
      }
   } catch (Exception $e) {
      throw new Exception("Error al cargar los productos: " . $e->getMessage());
      return [];
   }
}

$productos = getProducts();
if (!is_array($productos)) {
   $productos = [];
}

// echo "<script>console.log(" . json_encode($productos) . ");</script>";

$productosPorPagina = 5;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$totalProductos = count($productos);
$totalPaginas = ceil($totalProductos / $productosPorPagina);

// Asegurarse de que la página actual es válida
$paginaActual = max(1, min($paginaActual, $totalPaginas));

// Calcular el índice de inicio para la página actual
$indiceInicio = ($paginaActual - 1) * $productosPorPagina;

// Obtener los productos para la página actual
$productosEnPagina = array_slice($productos, $indiceInicio, $productosPorPagina);


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
$activo = false;
?>

<div class="container">
   <div class="table-container">
      <table class="product-table">
         <thead>
            <tr>
               <th>ID</th>
               <th>Nombre</th>
               <th>Descripción</th>
               <th>Precio</th>
               <th>Cantidad</th>
               <th>Activo</th>
               <th>Fecha de Creación</th>
               <th>Acciones</th>
            </tr>
         </thead>
         <tbody>
            <?php if (empty($productosEnPagina)): ?>
               <tr>
                  <td colspan="8" class="text-center">No hay productos disponibles</td>
               </tr>
            <?php else: ?>
               <?php foreach ($productosEnPagina as $producto): ?>
                  <tr>
                     <td><?php echo $producto['id_product']; ?></td>
                     <td><?php echo $producto['name']; ?></td>
                     <td><?php echo formatText($producto['description']); ?></td>
                     <td><?php echo number_format($producto['product_price'], 2, ',', '.'); ?></td>
                     <td class="text-center"><?php echo $producto['stock']; ?></td>
                     <td class="text-center">
                        <span class="<?php
                           if ($producto['status'] === 'activo') {
                              $activo = true;
                           } else {
                              $activo = false;
                           }
                           echo $activo ? 'status-active' : 'status-inactive'; ?>">
                           <?php echo $activo ? 'Si' : 'No'; ?>
                        </span>
                     </td>
                     <td class="text-center"><?php echo date('d/m/Y', strtotime($producto['create_at'])); ?></td>
                     <td class="text-center acciones">
                        <button class="btn-accions edit" onclick="editProduct(<?php echo $producto['id_product']; ?>)">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon">
                              <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                              <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                           </svg>
                        </button>
                     </td>
                  </tr>
               <?php endforeach; ?>
            <?php endif; ?>
         </tbody>
      </table>
      </table>
   </div>

   <div class="container-pagination">
      <div class="pagination">
         <a href="?pagina=<?php echo max(1, $paginaActual - 1); ?>" class="pagination-arrow <?php echo $paginaActual == 1 ? 'disabled' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon-paginator">
               <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-4.28 9.22a.75.75 0 0 0 0 1.06l3 3a.75.75 0 1 0 1.06-1.06l-1.72-1.72h5.69a.75.75 0 0 0 0-1.5h-5.69l1.72-1.72a.75.75 0 0 0-1.06-1.06l-3 3Z" clip-rule="evenodd" />
            </svg>
         </a>
         <?php foreach ($paginasAMostrar as $pagina): ?>
            <a href="?pagina=<?php echo $pagina; ?>" class="pagination-number <?php echo $pagina == $paginaActual ? 'active' : ''; ?>">
               <?php echo $pagina; ?>
            </a>
         <?php endforeach; ?>
         <a href="?pagina=<?php echo min($totalPaginas, $paginaActual + 1); ?>" class="pagination-arrow <?php echo $paginaActual == $totalPaginas ? 'disabled' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon-paginator">
               <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm4.28 10.28a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 1 0-1.06 1.06l1.72 1.72H8.25a.75.75 0 0 0 0 1.5h5.69l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3Z" clip-rule="evenodd" />
            </svg>
         </a>
      </div>
   </div>

</div>