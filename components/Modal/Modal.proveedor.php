<div class="content-modal" style="display: none;" >
    <div class="content-modal-container">
        <div class="content-form">
            <div class="header-modal">
            <div class="content-title">
                <div class="title">Agregar Proveedor</div>
                <div class="close-modal" onclick='toggleModal()' >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="cancel-icon-modal">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <div class="separator"></div>
        </div>
        <div class="content-info">
            <form id="add-provider-form">
                <div class="form-row">
                    <input type="text" placeholder="Id proveedor" name="id_proveedor">
                    <input type="text" placeholder="Categoria" name="categoria">
                </div>
                <div class="form-row">
                    <input type="text" placeholder="Nombre" name="nombre">
                    <input type="text" placeholder="Dirección" name="direccion">
                </div>
                <div class="form-row">
                    <input type="text" placeholder="Telefono" name="telefono">
                    <input type="text" placeholder="Descripción" name="descripcion">
                </div>
                <div class="form-row">
                     <input type="email" placeholder="Correo" name="correo">
                </div>
            </form>
        </div>
        <div class="content-buttons">
            <div class="btn-modal button-cancel" onclick='toggleModal()'>Cancelar</div>
            <div class="btn-modal button-confirm">Agregar</div>
            </div>
        </div>
    </div>
</div>
