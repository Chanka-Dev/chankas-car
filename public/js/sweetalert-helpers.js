/**
 * Helper para confirmación de eliminación con SweetAlert2
 * @param {string} formId - ID del formulario a enviar
 * @param {string} itemName - Nombre del elemento (ej: "este cliente", "este empleado")
 */
function confirmarEliminacion(formId, itemName = 'este registro') {
    event.preventDefault();
    
    Swal.fire({
        title: '¿Estás seguro?',
        html: `Se eliminará <strong>${itemName}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

/**
 * Mostrar alerta de éxito
 * @param {string} message - Mensaje a mostrar
 * @param {number} timer - Tiempo en ms (opcional)
 */
function mostrarExito(message, timer = 3000) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: message,
        showConfirmButton: false,
        timer: timer,
        toast: true,
        position: 'top-end'
    });
}

/**
 * Mostrar alerta de error
 * @param {string} message - Mensaje a mostrar
 */
function mostrarError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#1a3a47',
        confirmButtonText: 'Entendido'
    });
}

/**
 * Mostrar alerta de información
 * @param {string} title - Título
 * @param {string} message - Mensaje a mostrar
 */
function mostrarInfo(title, message) {
    Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        confirmButtonColor: '#1a3a47',
        confirmButtonText: 'Entendido'
    });
}

/**
 * Confirmar una acción genérica
 * @param {string} title - Título de confirmación
 * @param {string} text - Texto descriptivo
 * @param {function} callback - Función a ejecutar si se confirma
 */
function confirmarAccion(title, text, callback) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1a3a47',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}

/**
 * Mostrar loading durante operación asíncrona
 * @param {string} message - Mensaje de carga
 */
function mostrarCargando(message = 'Procesando...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * Cerrar modal de loading
 */
function cerrarCargando() {
    Swal.close();
}
