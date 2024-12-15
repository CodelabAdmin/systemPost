<div class="content-modal" style="display: none;">
    <div class="content-modal-container">
        <div class="content-form">
            <div class="header-modal">
                <div class="content-title">
                    <div class="title">Agregar empleados</div>
                    <div class="close-modal" onclick="toggleModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="cancel-icon-modal">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <div class="separator"></div>
            </div>
            <form id="empleado-form" onsubmit="addEmpleado(event)">
                <div class="content-info-empleados">
                    <div class="content-modal-form">
                        <div class="content-input-form">
                            <input id="nombreCompleto" name="nombreCompleto" placeholder="Nombre completo" type="text"
                                required>
                        </div>
                        <div class="content-input-form2">
                            <div class="content-input">
                                <input id="celular" name="celular" placeholder="Celular" type="tel" required>
                                <select id="rol" name="rol" required>
                                    <option value="">Seleccione un rol</option>
                                    <option value="admin">Administrador</option>
                                    <option value="cajero">Cajero</option>
                                </select>
                            </div>
                            <div class="content-input">
                                <input id="email" name="email" placeholder="E-mail" type="email" required>
                                <input id="password" name="password" placeholder="Contraseña" type="password" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-buttons-users">
                    <button type="button" class="btn-modal button-cancel" onclick="toggleModal()">Cancelar</button>
                    <button type="submit" class="btn-modal button-confirm">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // validación en tiempo real
        const form = document.getElementById('empleado-form');

        // Validación del nombre
        const nombreInput = form.querySelector('input[name="nombreCompleto"]');
        nombreInput.addEventListener('input', function () {
            const isValid = /^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/.test(this.value);
            validateField(this, isValid, 'Solo se permiten letras y espacios');
        });

        // Validación del celular
        const celularInput = form.querySelector('input[name="celular"]');
        celularInput.addEventListener('input', function () {
            const isValid = /^\d{0,10}$/.test(this.value);
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
            validateField(this, this.value.length === 10, 'Debe contener 10 dígitos');
        });

        // Validación del email
        const emailInput = form.querySelector('input[name="email"]');
        emailInput.addEventListener('input', function () {
            const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
            validateField(this, isValid, 'Ingrese un correo electrónico válido');
        });

        // Validación de la contraseña
        const passwordInput = form.querySelector('input[name="password"]');
        passwordInput.addEventListener('input', function () {
            const isValid = this.value.length >= 8;
            validateField(this, isValid, 'La contraseña debe tener al menos 8 caracteres');
        });

        // Función para validar campos y mostrar errores
        function validateField(input, isValid, errorMessage) {
            // Buscar o crear el mensaje de error
            let errorDiv = input.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains('error-message')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }

            if (!isValid) {
                input.classList.add('input-error');
                errorDiv.textContent = errorMessage;
                errorDiv.style.display = 'block';
            } else {
                input.classList.remove('input-error');
                errorDiv.style.display = 'none';
            }
        }

        // Declaramos las funciones dentro
        window.addEmpleado = async function (event) {
            event.preventDefault();

            try {
                const form = document.getElementById('empleado-form');

                if (!form) {
                    throw new Error('No se encontró el formulario');
                }

                const nombreCompleto = form.querySelector('input[name="nombreCompleto"]')?.value;
                const celular = form.querySelector('input[name="celular"]')?.value;
                const email = form.querySelector('input[name="email"]')?.value;
                const rol = form.querySelector('select[name="rol"]')?.value;
                const password = form.querySelector('input[name="password"]')?.value;

                if (!nombreCompleto || !celular || !email || !rol || !password) {
                    await Swal.fire({
                        title: 'Error',
                        text: 'Todos los campos son requeridos',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                // Validación del nombre
                if (!/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/.test(nombreCompleto)) {
                    await Swal.fire({
                        title: 'Error',
                        text: 'El nombre solo puede contener letras y espacios',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                // Validación del celular
                if (!/^\d{10}$/.test(celular)) {
                    await Swal.fire({
                        title: 'Error',
                        text: 'El número debe tener máximo 10 digitos',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                // Validación del email
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    await Swal.fire({
                        title: 'Error',
                        text: 'Ingrese un correo electrónico válido',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                // Validación de la contraseña
                if (password.length < 8) {
                    await Swal.fire({
                        title: 'Error',
                        text: 'La contraseña debe tener al menos 8 caracteres',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                const empleadoData = {
                    fullname: nombreCompleto.trim(),
                    phone: celular,
                    email: email.trim().toLowerCase(),
                    rol: rol,
                    pass: password
                };

                // Mostrar loading
                Swal.fire({
                    title: 'Procesando',
                    text: 'Guardando empleado...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch('http://localhost/server/systemPost/api/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(empleadoData)
                });

                // Agregar verificación de tipo de contenido
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('La respuesta del servidor no es JSON válido');
                }

                const data = await response.json();

                if (response.ok) {
                    await Swal.fire({
                        title: 'Éxito',
                        text: 'Empleado agregado correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });

                    toggleModal();
                    form.reset();
                    location.reload();
                } else {
                    throw new Error(data.message || 'Error al agregar el empleado');
                }

            } catch (error) {
                console.error('Error detallado:', error);
                if (error.response) {
                    const text = await error.response.text();
                    console.error('Respuesta del servidor:', text);
                }
                await Swal.fire({
                    title: 'Error en el Sistema',
                    text: 'Error al procesar el formulario: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        };

        // También hacemos global la función toggleModal
        window.toggleModal = function () {
            const modal = document.querySelector('.content-modal');
            modal.style.display = modal.style.display === 'none' ? 'block' : 'none';

            // Limpiar errores al cerrar el modal
            if (modal.style.display === 'none') {
                const errorInputs = form.querySelectorAll('.input-error');
                const errorMessages = form.querySelectorAll('.error-message');
                errorInputs.forEach(input => input.classList.remove('input-error'));
                errorMessages.forEach(msg => msg.style.display = 'none');
                form.reset();
            }
        };
    });
</script>