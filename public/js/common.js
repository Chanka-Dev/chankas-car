/**
 * Funciones JavaScript comunes para todo el sistema
 * Chankas Car - Sistema de Gestión
 */

/**
 * Confirmación de eliminación con SweetAlert2 (si está disponible) o confirm nativo
 * @param {string} formId - ID del formulario a enviar
 * @param {string} nombre - Nombre del elemento a eliminar
 * @param {string} tipo - Tipo de elemento (opcional, ej: 'cargo', 'empleado')
 */
function confirmarEliminacion(formId, nombre, tipo = 'elemento') {
    const mensaje = `¿Estás seguro de eliminar ${tipo === 'elemento' ? '' : 'el ' + tipo} "${nombre}"?`;
    const submensaje = 'Esta acción no se puede deshacer.';
    
    // Si SweetAlert2 está disponible, usarlo
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Confirmar eliminación?',
            html: `<p>${mensaje}</p><small class="text-muted">${submensaje}</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    } else {
        // Fallback a confirm nativo
        if (confirm(`${mensaje}\n${submensaje}`)) {
            document.getElementById(formId).submit();
        }
    }
}

/**
 * Formatear número como moneda boliviana
 * @param {number} monto - Monto a formatear
 * @returns {string} - Monto formateado (ej: "Bs 1,234.50")
 */
function formatearMoneda(monto) {
    return 'Bs ' + Number(monto).toLocaleString('es-BO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Mostrar mensaje de éxito temporal
 * @param {string} mensaje - Mensaje a mostrar
 * @param {number} duracion - Duración en ms (default: 3000)
 */
function mostrarExito(mensaje, duracion = 3000) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: mensaje,
            timer: duracion,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else {
        alert(mensaje);
    }
}

/**
 * Mostrar mensaje de error
 * @param {string} mensaje - Mensaje a mostrar
 */
function mostrarError(mensaje) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje,
            confirmButtonText: 'Entendido'
        });
    } else {
        alert('Error: ' + mensaje);
    }
}

/**
 * Auto-cerrar alertas de Bootstrap después de 5 segundos
 */
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
