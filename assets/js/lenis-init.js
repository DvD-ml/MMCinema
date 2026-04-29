/**
 * Lenis Smooth Scroll - Inicialización
 * Proporciona un scroll suave y natural en toda la aplicación
 */

// Inicializar Lenis con configuración optimizada para máxima suavidad
const lenis = new Lenis({
    duration: 1.8,        // Duración más larga para scroll más suave (segundos)
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Easing suave exponencial
    direction: 'vertical', // Dirección del scroll
    gestureDirection: 'vertical',
    smooth: true,
    mouseMultiplier: 0.8,  // Reducido para scroll más controlado con mouse
    smoothTouch: false,    // Desactivado en touch para mejor rendimiento
    touchMultiplier: 1.5,  // Reducido para mejor control en touch
    infinite: false,
    lerp: 0.08,           // Interpolación lineal - más bajo = más suave (0.1 por defecto)
});

// Función de animación
function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

// Detener scroll cuando se abre un modal de Bootstrap
document.addEventListener('shown.bs.modal', () => {
    lenis.stop();
});

// Reanudar scroll cuando se cierra un modal
document.addEventListener('hidden.bs.modal', () => {
    lenis.start();
});

// Smooth scroll para enlaces ancla
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        
        // Ignorar # solo (toggle, modals, etc.)
        if (href === '#' || href.startsWith('#modal') || href.startsWith('#collapse')) {
            return;
        }
        
        e.preventDefault();
        const target = document.querySelector(href);
        
        if (target) {
            lenis.scrollTo(target, {
                offset: -80, // Offset para navbar fija
                duration: 2.0, // Duración más larga para scroll a anclas
                easing: (t) => t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1 // Cubic ease in-out
            });
        }
    });
});

// Exponer Lenis globalmente para uso en otros scripts
window.lenis = lenis;

console.log('✨ Lenis Smooth Scroll inicializado con configuración ultra-suave');
