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

$productos = getProducts();
if (!is_array($productos)) {
   $productos = [];
}

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
               <th>Categoría</th>
               <th>Fecha de Actualización</th>
               <th>Acciones</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($productosEnPagina as $producto): ?>
               <tr>
                  <td><?php echo $producto['id_product']; ?></td>
                  <td><?php echo $producto['name']; ?></td>
                  <td><?php echo  formatText($producto['description']) ?></td>
                  <td><?php echo $producto['product_price']; ?></td>
                  <td class="text-center"><?php echo $producto['stock']; ?></td>
                  <td class="text-center"><?php echo $producto['category']; ?></td>
                  <td class="text-center"><?php echo $producto['create_at']; ?></td>
                  <td class="text-center acciones">
                     <div class="content-actions">
                        <button class="btn-accions view">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon">
                              <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                              <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                           </svg>
                        </button>
                     </div> 
                  </td>
               </tr>
            <?php endforeach; ?>
         </tbody>
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