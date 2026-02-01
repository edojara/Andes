<?php
/**
 * Página de contacto del Club Deportivo Andes
 * Formulario de contacto e información
 */
require_once __DIR__ . '/../includes/header.php';
?>

<main class="contact-main">
    <div class="container">
        <h1 class="page-title">Contacto</h1>
        
        <div class="contact-content">
            <!-- Información de contacto -->
            <div class="contact-info">
                <h2>Información del Club</h2>
                <div class="info-item">
                    <span class="info-icon">📍</span>
                    <div class="info-text">
                        <h3>Ubicación</h3>
                        <p>Santiago, Chile</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">📧</span>
                    <div class="info-text">
                        <h3>Email</h3>
                        <p>contacto@andes.cl</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">📞</span>
                    <div class="info-text">
                        <h3>Teléfono</h3>
                        <p>+56 9 1234 5678</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">⏰</span>
                    <div class="info-text">
                        <h3>Horarios</h3>
                        <p>Lunes a Viernes: 18:00 - 21:00</p>
                        <p>Sábados: 10:00 - 14:00</p>
                    </div>
                </div>
            </div>

            <!-- Formulario de contacto -->
            <div class="contact-form-container">
                <h2>Envíanos un mensaje</h2>
                <form id="contact-form" class="contact-form">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="asunto">Asunto *</label>
                        <input type="text" id="asunto" name="asunto" required>
                    </div>
                    <div class="form-group">
                        <label for="mensaje">Mensaje *</label>
                        <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                </form>
                <div id="form-message" class="form-message"></div>
            </div>
        </div>
    </div>
</main>

<script>
// Manejar envío del formulario
document.getElementById('contact-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const formMessage = document.getElementById('form-message');
    
    // Deshabilitar botón
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enviando...';
    
    try {
        const response = await fetch('/api/contacto.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            formMessage.innerHTML = `
                <div class="message success">
                    <p>${data.message || 'Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.'}</p>
                </div>
            `;
            form.reset();
        } else {
            formMessage.innerHTML = `
                <div class="message error">
                    <p>${data.message || 'Error al enviar el mensaje. Por favor, intenta nuevamente.'}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al enviar formulario:', error);
        formMessage.innerHTML = `
            <div class="message error">
                <p>Error al enviar el mensaje. Por favor, intenta nuevamente.</p>
            </div>
        `;
    } finally {
        // Habilitar botón
        submitBtn.disabled = false;
        submitBtn.textContent = 'Enviar Mensaje';
        
        // Ocultar mensaje después de 5 segundos
        setTimeout(() => {
            formMessage.innerHTML = '';
        }, 5000);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
