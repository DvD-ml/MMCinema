/**
 * Mobile Scroll Enhancement
 * Mejora la experiencia de scroll en móviles para favoritas y tablas
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mejorar scroll en contenedores de favoritas
    const letterboxdScroll = document.querySelector('.letterboxd-scroll-container');
    const listaScroll = document.querySelector('.lista-scroll-container');
    const tableWrap = document.querySelector('.perfil-table-wrap');
    
    // Indicadores desactivados
    // if (letterboxdScroll) {
    //     addScrollIndicator(letterboxdScroll, 'Desliza para ver más favoritas →');
    // }
    
    // if (listaScroll) {
    //     addScrollIndicator(listaScroll, 'Desliza para ver más →');
    // }
    
    // if (tableWrap) {
    //     addScrollIndicator(tableWrap, 'Desliza para ver más datos →');
    // }
    
    // Mejorar experiencia de scroll con momentum
    enableMomentumScroll(letterboxdScroll);
    enableMomentumScroll(listaScroll);
    enableMomentumScroll(tableWrap);
});

/**
 * Agregar indicador visual de scroll
 */
function addScrollIndicator(element, text) {
    if (!element) return;
    
    // Crear indicador
    const indicator = document.createElement('div');
    indicator.className = 'scroll-indicator';
    indicator.textContent = text;
    indicator.style.cssText = `
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.75rem;
        color: rgba(255, 138, 0, 0.6);
        white-space: nowrap;
        pointer-events: none;
        animation: fadeInOut 3s ease-in-out infinite;
    `;
    
    // Agregar animación CSS si no existe
    if (!document.querySelector('style[data-scroll-indicator]')) {
        const style = document.createElement('style');
        style.setAttribute('data-scroll-indicator', 'true');
        style.textContent = `
            @keyframes fadeInOut {
                0%, 100% { opacity: 0; }
                50% { opacity: 1; }
            }
            
            @keyframes slideRight {
                0% { transform: translateX(-5px); }
                50% { transform: translateX(5px); }
                100% { transform: translateX(-5px); }
            }
            
            .scroll-indicator {
                animation: fadeInOut 3s ease-in-out infinite;
            }
            
            .scroll-indicator.active {
                animation: slideRight 1s ease-in-out infinite;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Agregar indicador al elemento
    element.parentElement.style.position = 'relative';
    element.parentElement.appendChild(indicator);
    
    // Mostrar indicador solo si hay scroll
    if (element.scrollWidth > element.clientWidth) {
        indicator.classList.add('active');
        
        // Ocultar después de 5 segundos
        setTimeout(() => {
            indicator.classList.remove('active');
        }, 5000);
    }
    
    // Ocultar indicador cuando el usuario empieza a hacer scroll
    element.addEventListener('scroll', function() {
        indicator.classList.remove('active');
    }, { once: true });
}

/**
 * Habilitar scroll con momentum (inercia)
 */
function enableMomentumScroll(element) {
    if (!element) return;
    
    let isDown = false;
    let startX;
    let scrollLeft;
    let velocity = 0;
    let lastX = 0;
    let lastTime = 0;
    
    element.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - element.offsetLeft;
        scrollLeft = element.scrollLeft;
        lastX = e.pageX;
        lastTime = Date.now();
        element.style.cursor = 'grabbing';
    });
    
    element.addEventListener('mouseleave', () => {
        isDown = false;
        element.style.cursor = 'grab';
    });
    
    element.addEventListener('mouseup', () => {
        isDown = false;
        element.style.cursor = 'grab';
        
        // Aplicar momentum
        if (Math.abs(velocity) > 0.5) {
            applyMomentum(element, velocity);
        }
    });
    
    element.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        
        e.preventDefault();
        const x = e.pageX - element.offsetLeft;
        const walk = (x - startX) * 1;
        
        // Calcular velocidad
        const currentTime = Date.now();
        const timeDiff = currentTime - lastTime;
        if (timeDiff > 0) {
            velocity = (lastX - e.pageX) / timeDiff;
        }
        
        element.scrollLeft = scrollLeft - walk;
        lastX = e.pageX;
        lastTime = currentTime;
    });
    
    // Soporte para touch en móviles
    let touchStartX = 0;
    let touchScrollLeft = 0;
    let touchVelocity = 0;
    let touchLastX = 0;
    let touchLastTime = 0;
    
    element.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].pageX - element.offsetLeft;
        touchScrollLeft = element.scrollLeft;
        touchLastX = e.touches[0].pageX;
        touchLastTime = Date.now();
    });
    
    element.addEventListener('touchmove', (e) => {
        const x = e.touches[0].pageX - element.offsetLeft;
        const walk = (x - touchStartX) * 1;
        
        // Calcular velocidad
        const currentTime = Date.now();
        const timeDiff = currentTime - touchLastTime;
        if (timeDiff > 0) {
            touchVelocity = (touchLastX - e.touches[0].pageX) / timeDiff;
        }
        
        element.scrollLeft = touchScrollLeft - walk;
        touchLastX = e.touches[0].pageX;
        touchLastTime = currentTime;
    });
    
    element.addEventListener('touchend', () => {
        // Aplicar momentum
        if (Math.abs(touchVelocity) > 0.5) {
            applyMomentum(element, touchVelocity);
        }
    });
}

/**
 * Aplicar efecto de momentum (inercia) al scroll
 */
function applyMomentum(element, velocity) {
    let currentVelocity = velocity;
    const friction = 0.95;
    const minVelocity = 0.1;
    
    function animate() {
        if (Math.abs(currentVelocity) > minVelocity) {
            element.scrollLeft += currentVelocity * 10;
            currentVelocity *= friction;
            requestAnimationFrame(animate);
        }
    }
    
    animate();
}

/**
 * Mejorar experiencia de scroll suave
 */
function smoothScroll() {
    // Aplicar scroll suave a todos los contenedores scrollables
    const scrollContainers = document.querySelectorAll(
        '.letterboxd-scroll-container, .lista-scroll-container, .perfil-table-wrap'
    );
    
    scrollContainers.forEach(container => {
        container.style.scrollBehavior = 'smooth';
    });
}

// Ejecutar al cargar
smoothScroll();

// Exportar funciones para uso externo
window.mobileScroll = {
    addScrollIndicator,
    enableMomentumScroll,
    applyMomentum,
    smoothScroll
};
