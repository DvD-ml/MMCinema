/**
 * Lenis Smooth Scroll - Inicialización
 * Proporciona un scroll suave y natural en toda la aplicación
 */

// Inicializar Lenis
const lenis = new Lenis({
    duration: 1.2,        // Duración de la animación (segundos)
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Easing suave
    direction: 'vertical', // Dirección del scroll
    gestureDirection: 'vertical',
    smooth: true,
    mouseMultiplier: 1,
    smoothTouch: false,   // Desactivado en touch para mejor rendimiento
    touchMultiplier: 2,
    infinite: false,
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
                duration: 1.5
            });
        }
    });
});

// Exponer Lenis globalmente para uso en otros scripts
window.lenis = lenis;

console.log('✨ Lenis Smooth Scroll inicializado');
