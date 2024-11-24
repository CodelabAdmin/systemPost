<link rel="stylesheet" href="./assets/css/empleados.css">
<div class="content-modal" style="display: none;" >
    <div class="content-modal-container">
        <div class="content-form">
            <div class="header-modal">
            <div class="content-title">
                <div class="title">Agregar empleados</div>
                <div class="close-modal" onclick='toggleModal()' >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="cancel-icon-modal">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <div class="separator"></div>
        </div>
        <div class="content-info-empleados">
            <div class="content-modal-form">
                <div class="content-input-form">
                        <input placeholder="Nombre completo" type="text">
                </div>
                <div class="content-input-form2">
                    <div class="content-input">
                        <input placeholder="Celular" type="number">
                        <input placeholder="Rol"  type="text">
                    </div>
                    <div class="content-input">
                        <input placeholder="E-mail"  type="email">
                        <input placeholder="Contraseña"  type="password">
                    </div>
                </div>
            </div>
        </div>
        <div class="content-buttons">
            <div class="btn-modal button-cancel" onclick='toggleModal()'>Cancelar</div>
            <div class="btn-modal button-confirm">Agregar</div>
            </div>
        </div>
    </div>
</div>