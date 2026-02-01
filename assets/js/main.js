/**
 * JavaScript principal del Club Deportivo Andes
 * Funcionalidades comunes del sitio
 */

// ===== Utilidades =====

/**
 * Formatea una fecha en formato chileno
 * @param {Date|string} date - Fecha a formatear
 * @param {Object} options - Opciones de formato
 * @returns {string} Fecha formateada
 */
function formatDate(date, options = {}) {
    const defaultOptions = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    return new Date(date).toLocaleDateString('es-CL', { ...defaultOptions, ...options });
}

/**
 * Formatea una hora en formato chileno
 * @param {Date|string} date - Fecha con hora a formatear
 * @returns {string} Hora formateada
 */
function formatTime(date) {
    return new Date(date).toLocaleTimeString('es-CL', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Muestra un mensaje de notificación
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de mensaje (success, error, info)
 */
function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Estilos
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: bold;
        z-index: 3000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    `;
    
    // Colores según tipo
    const colors = {
        success: '#27F53F',
        error: '#ff4444',
        info: '#2196F3'
    };
    notification.style.backgroundColor = colors[type] || colors.info;
    
    // Agregar al DOM
    document.body.appendChild(notification);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Realiza una petición fetch con manejo de errores
 * @param {string} url - URL de la petición
 * @param {Object} options - Opciones de fetch
 * @returns {Promise} Promesa con la respuesta
 */
async function fetchWithErrorHandling(url, options = {}) {
    try {
        const response = await fetch(url, options);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

/**
 * Escapa caracteres HTML para prevenir XSS
 * @param {string} text - Texto a escapar
 * @returns {string} Texto escapado
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Trunca un texto a una longitud máxima
 * @param {string} text - Texto a truncar
 * @param {number} maxLength - Longitud máxima
 * @returns {string} Texto truncado
 */
function truncateText(text, maxLength = 100) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// ===== Animaciones =====

// Agregar keyframes para animaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
`;
document.head.appendChild(style);

/**
 * Aplica una animación de fade-in a un elemento
 * @param {HTMLElement} element - Elemento a animar
 * @param {number} duration - Duración en ms
 */
function fadeIn(element, duration = 300) {
    element.style.opacity = '0';
    element.style.transition = `opacity ${duration}ms ease`;
    
    requestAnimationFrame(() => {
        element.style.opacity = '1';
    });
}

/**
 * Aplica una animación de pulse a un elemento
 * @param {HTMLElement} element - Elemento a animar
 * @param {number} duration - Duración en ms
 */
function pulse(element, duration = 500) {
    element.style.animation = `pulse ${duration}ms ease`;
    
    setTimeout(() => {
        element.style.animation = '';
    }, duration);
}

// ===== Manejo de Formularios =====

/**
 * Valida un formulario
 * @param {HTMLFormElement} form - Formulario a validar
 * @returns {Object} Objeto con errores y validez
 */
function validateForm(form) {
    const errors = {};
    let isValid = true;
    
    // Validar campos requeridos
    form.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            errors[field.name] = 'Este campo es requerido';
            isValid = false;
        }
    });
    
    // Validar email
    const emailField = form.querySelector('[type="email"]');
    if (emailField && emailField.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value)) {
            errors[emailField.name] = 'Email inválido';
            isValid = false;
        }
    }
    
    return { errors, isValid };
}

/**
 * Muestra errores de validación en un formulario
 * @param {HTMLFormElement} form - Formulario con errores
 * @param {Object} errors - Objeto con errores
 */
function showFormErrors(form, errors) {
    // Limpiar errores anteriores
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    
    // Mostrar nuevos errores
    Object.entries(errors).forEach(([fieldName, message]) => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            const errorEl = document.createElement('div');
            errorEl.className = 'error-message';
            errorEl.textContent = message;
            errorEl.style.cssText = `
                color: #ff4444;
                font-size: 0.875rem;
                margin-top: 0.25rem;
            `;
            field.parentNode.appendChild(errorEl);
        }
    });
}

// ===== Scroll y Navegación =====

/**
 * Desplaza suavemente a un elemento
 * @param {string|HTMLElement} target - Elemento o selector
 * @param {number} offset - Offset en px
 */
function scrollToElement(target, offset = 0) {
    const element = typeof target === 'string' 
        ? document.querySelector(target) 
        : target;
    
    if (element) {
        const top = element.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top, behavior: 'smooth' });
    }
}

/**
 * Detecta cuando un elemento es visible en el viewport
 * @param {HTMLElement} element - Elemento a observar
 * @param {Function} callback - Función a ejecutar
 * @returns {IntersectionObserver} Observador creado
 */
function observeVisibility(element, callback) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                callback(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    observer.observe(element);
    return observer;
}

// ===== Inicialización =====

/**
 * Inicializa la aplicación
 */
function initApp() {
    // Marcar página activa en navegación
    const currentPage = window.location.pathname;
    document.querySelectorAll('.nav-list a').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
    
    // Agregar animaciones de fade-in a elementos
    document.querySelectorAll('.player-card, .gallery-item, .event-card').forEach(el => {
        observeVisibility(el, (element) => {
            fadeIn(element, 500);
        });
    });
    
    // Manejar errores globales
    window.addEventListener('error', (event) => {
        console.error('Error global:', event.error);
        showNotification('Ha ocurrido un error inesperado', 'error');
    });
    
    // Manejar promesas rechazadas no capturadas
    window.addEventListener('unhandledrejection', (event) => {
        console.error('Promesa rechazada:', event.reason);
        showNotification('Ha ocurrido un error inesperado', 'error');
    });
}

// Ejecutar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

// ===== Exportar funciones para uso global =====
window.AndesApp = {
    formatDate,
    formatTime,
    showNotification,
    fetchWithErrorHandling,
    escapeHtml,
    truncateText,
    fadeIn,
    pulse,
    validateForm,
    showFormErrors,
    scrollToElement,
    observeVisibility
};
