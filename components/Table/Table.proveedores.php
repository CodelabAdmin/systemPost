<?php
function getProveedores()
{
   try {
      $url = "http://localhost/server/systemPost/api/suppliers?status=activo";
      $response = file_get_contents($url);
      $data = json_decode($response, true);

      if ($data && isset($data['supplier'])) {
         return $data['supplier'];
      } else {
         return [];
      }
   } catch (Exception $e) {
      error_log("Error al cargar los proveedores: " . $e->getMessage());
      return [];
   }
}

$proveedores = getProveedores();
if (!is_array($proveedores)) {
   $proveedores = [];
}

$proveedoresPorPagina = 5;
$totalProveedores = count($proveedores);
$totalPaginas = ceil($totalProveedores / $proveedoresPorPagina);

$paginaActual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$paginaActual = max(1, min($paginaActual, $totalPaginas));
$indiceInicio = ($paginaActual - 1) * $proveedoresPorPagina;
$proveedoresEnPagina = array_slice($proveedores, $indiceInicio, $proveedoresPorPagina);

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

<div class="container">
   <div class="table-container">
      <table class="product-table">
         <thead>
            <tr>
               <th>ID</th>
               <th>Nombre Completo</th>
               <th>Teléfono</th>
               <th>Dirección</th>
               <th>Descripción</th>
               <th>Categoría</th>
               <th>acciones</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($proveedoresEnPagina as $proveedor): ?>
               <tr>
                  <td><?php echo $proveedor['id_supplier']; ?></td>
                  <td><?php echo $proveedor['fullname']; ?></td>
                  <td><?php echo $proveedor['phone']; ?></td>
                  <td><?php echo $proveedor['address']; ?></td>
                  <td><?php echo $proveedor['description']; ?></td>
                  <td><?php echo $proveedor['category']; ?></td>
                  <td class="text-center acciones">
                     <button class="btn-accions edit">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon">
                           <path
                              d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                           <path
                              d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                        </svg>
                     </button>
                     <button class="btn-accions delete" onclick="eliminarProveedor(<?php echo $proveedor['id_supplier']; ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon">
                           <path fill-rule="evenodd"
                              d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                              clip-rule="evenodd" />
                        </svg>
                     </button>
                     <script>
                        function eliminarProveedor(id) {
                           if (confirm('¿Estás seguro de que deseas eliminar este proveedor?')) {
                              // Realiza la solicitud AJAX para eliminar el proveedor
                              fetch(`http://localhost/server/systemPost/api/suppliers/deactivate?id=${id}`, {
                                 method: 'PATCH'
                              })
                              .then(async response => {
                                 if (response.ok) {
                                    alert('Proveedor eliminado correctamente');
                                    location.reload(); // Recargar la página para ver los cambios
                                 } else {
                                    alert('Error al eliminar el proveedor.');
                                 }
                              })
                              .catch(error => {
                                 console.error('Error:', error);
                              });
                           }
                        }
                     </script>
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
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon-paginator">
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
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon-paginator">
               <path fill-rule="evenodd"
                  d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm4.28 10.28a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 1 0-1.06 1.06l1.72 1.72H8.25a.75.75 0 0 0 0 1.5h5.69l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3Z"
                  clip-rule="evenodd" />
            </svg>
         </a>
      </div>
   </div>

</div>