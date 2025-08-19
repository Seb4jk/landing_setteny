document.addEventListener('DOMContentLoaded', function() {
    // Obtener el formulario
    const contactForm = document.getElementById('contactForm');
    
    // Verificar si estamos en Netlify (para usar Netlify Forms)
    const isNetlify = window.location.hostname.includes('netlify.app') || 
                     window.location.hostname.includes('setteny.cl');
    
    // Validación y envío del formulario
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            // Validar el formulario
            if (!contactForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                contactForm.classList.add('was-validated');
                return;
            }
            
            // Si estamos en Netlify, permitir el envío nativo del formulario
            if (isNetlify) {
                // No prevenir el comportamiento predeterminado
                // Mostrar mensaje de carga
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                submitBtn.innerHTML = 'Enviando...';
                return true;
            }
            
            // Si no estamos en Netlify, usar AJAX para enviar el formulario
            event.preventDefault();
            
            // Recopilar datos del formulario
            const formData = {
                nombre: document.getElementById('nombre').value,
                empresa: document.getElementById('empresa').value,
                correo: document.getElementById('correo').value,
                telefono: document.getElementById('telefono').value,
                asunto: document.getElementById('asunto').value
            };
            
            // Mostrar indicador de carga
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Enviando...';
            
            // Enviar datos al servidor local para pruebas
            fetch('send_email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                // Restaurar el botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                if (data.success) {
                    // Mostrar mensaje de éxito
                    showMessage('success', '¡Mensaje enviado correctamente! Te contactaremos pronto.');
                    contactForm.reset();
                    contactForm.classList.remove('was-validated');
                } else {
                    // Mostrar mensaje de error
                    showMessage('danger', 'Error al enviar el mensaje: ' + data.message);
                }
            })
            .catch(error => {
                // Restaurar el botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                // Mostrar mensaje de error
                showMessage('danger', 'Error de conexión. Por favor intenta nuevamente.');
                console.error('Error:', error);
            });
        });
    }
    
    // Función para mostrar mensajes
    function showMessage(type, message) {
        // Verificar si ya existe un mensaje y eliminarlo
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Crear nuevo mensaje
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} mt-3 alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insertar mensaje después del formulario
        contactForm.after(alertDiv);
        
        // Eliminar automáticamente después de 5 segundos
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }
});
