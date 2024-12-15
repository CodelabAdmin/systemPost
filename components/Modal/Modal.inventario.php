
<link rel="stylesheet" href="./assets/css/producto.css">
<div class="content-modal" style="display: none;">
    <div class="content-modal-container">
        <div class="content-form">
            <div class="header-modal">
                <div class="content-title">
                    <div class="title">Resumen Producto</div>
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
                        <input type="num" placeholder="Id" name="id" class="input-product" readonly>
                        <input type="tetx" placeholder="Nombre" name="nombre" class="input-product" readonly>
                        <input type="num" placeholder="Precio" name="precio" class="input-product" readonly>
                        <input type="num" placeholder="Stock" name="stock" class="input-product" readonly>
                    </div>
                    <div class="column">
                        <input type="tetx" placeholder="Categoria" name="categoria" class="input-product" readonly>
                        <textarea placeholder="Descripción" name="descripcion" class="input-product textarea" readonly></textarea>
                    </div>
                </div>
            </form>
            <div class="content-buttons-product">
                <div class="btn-modal button-cancel-product" onclick='toggleModal()'>Cancelar</div>
            </div>
        </div>
    </div>



