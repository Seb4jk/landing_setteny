document.addEventListener('DOMContentLoaded', function() {
    // Obtener el formulario
    const contactForm = document.getElementById('contactForm');
    
    // Validación y envío del formulario
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Validar el formulario
            if (!contactForm.checkValidity()) {
                event.stopPropagation();
                contactForm.classList.add('was-validated');
                return;
            }
            
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
            
            // Enviar datos al servidor
            fetch('send_email_phpmailer.php', {
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
