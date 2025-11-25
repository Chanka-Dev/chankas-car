document.addEventListener('DOMContentLoaded', function() {
    // Interceptar todos los clics en enlaces de logout
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a[href*="logout"]');
        
        if (target && target.getAttribute('href').includes('logout')) {
            e.preventDefault();
            
            // Crear formulario de logout
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = target.getAttribute('href');
            
            // Agregar token CSRF
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
        }
    });
});
