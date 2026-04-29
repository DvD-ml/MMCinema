# ✨ Lenis Smooth Scroll - Implementado

## 📌 ¿Qué es Lenis?

**Lenis** es una librería moderna y ligera de smooth scrolling creada por Studio Freight. Proporciona un scroll suave, natural y de alto rendimiento sin afectar la experiencia del usuario.

### Características:
- ✅ Scroll suave y natural
- ✅ Ligero (~3KB gzipped)
- ✅ Alto rendimiento (60fps)
- ✅ Compatible con todos los navegadores modernos
- ✅ Funciona con scroll nativo
- ✅ Soporte para enlaces ancla (#)

---

## 🎯 Implementación en MMCinema

### Archivos Creados:

1. **`js/lenis-init.js`** - Script de inicialización de Lenis
2. **`includes/lenis-scripts.php`** - Inclusión de scripts CDN

### Páginas con Lenis Activado:

✅ `index.php` - Página principal  
✅ `cartelera.php` - Cartelera de películas  
✅ `proximamente.php` - Próximos estrenos  
✅ `pelicula.php` - Detalle de película  
✅ `serie.php` - Detalle de serie  
✅ `series.php` - Listado de series  
✅ `noticias.php` - Listado de noticias  
✅ `noticia.php` - Detalle de noticia  
✅ `perfil.php` - Perfil de usuario  
✅ `criticas.php` - Críticas  
✅ `login.php` - Login  
✅ `registro.php` - Registro  
✅ `olvide_password.php` - Recuperar contraseña  
✅ `restablecer_password.php` - Restablecer contraseña  

---

## ⚙️ Configuración

### Parámetros de Lenis (en `js/lenis-init.js`):

```javascript
const lenis = new Lenis({
    duration: 1.2,        // Duración de la animación (segundos)
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Easing suave
    direction: 'vertical', // Dirección del scroll
    smooth: true,         // Activar smooth scroll
    smoothTouch: false,   // Desactivado en touch para mejor rendimiento
    mouseMultiplier: 1,   // Velocidad del scroll con mouse
    touchMultiplier: 2,   // Velocidad del scroll en touch
});
```

### Personalización:

Si quieres cambiar la velocidad o el comportamiento del scroll, edita estos valores en `js/lenis-init.js`:

- **`duration`**: Más alto = más lento (recomendado: 1.0 - 1.5)
- **`mouseMultiplier`**: Sensibilidad del mouse (recomendado: 0.8 - 1.2)
- **`smoothTouch`**: `true` para activar en móviles (puede afectar rendimiento)

---

## 🔗 Scroll a Anclas

Lenis maneja automáticamente los enlaces ancla (`#seccion`):

```html
<a href="#horarios">Ver horarios</a>
```

El scroll será suave con un offset de -80px para compensar la navbar fija.

### Excepciones:

Lenis **NO** afecta a:
- Enlaces `#` solos (toggles, dropdowns)
- Modales de Bootstrap (`#modal...`)
- Collapses (`#collapse...`)

---

## 🎮 Control Programático

Lenis está disponible globalmente como `window.lenis`:

```javascript
// Scroll a un elemento
lenis.scrollTo('#seccion', {
    offset: -100,
    duration: 2
});

// Scroll a una posición
lenis.scrollTo(500);

// Detener scroll
lenis.stop();

// Reanudar scroll
lenis.start();

// Destruir instancia
lenis.destroy();
```

---

## 🔄 Integración con Bootstrap

Lenis se detiene automáticamente cuando se abre un modal de Bootstrap y se reanuda al cerrarlo:

```javascript
// Detener scroll cuando se abre un modal
document.addEventListener('shown.bs.modal', () => {
    lenis.stop();
});

// Reanudar scroll cuando se cierra un modal
document.addEventListener('hidden.bs.modal', () => {
    lenis.start();
});
```

---

## 🎨 Efectos Adicionales (Opcional)

### Parallax con Lenis:

```javascript
lenis.on('scroll', ({ scroll }) => {
    const parallaxElement = document.querySelector('.parallax');
    if (parallaxElement) {
        parallaxElement.style.transform = `translateY(${scroll * 0.5}px)`;
    }
});
```

### Scroll Progress Bar:

```javascript
lenis.on('scroll', ({ scroll, limit }) => {
    const progress = (scroll / limit) * 100;
    document.querySelector('.progress-bar').style.width = `${progress}%`;
});
```

---

## 📊 Rendimiento

- **Tamaño**: ~3KB gzipped
- **FPS**: 60fps constantes
- **Impacto**: Mínimo en rendimiento
- **Compatibilidad**: Chrome, Firefox, Safari, Edge

---

## 🐛 Solución de Problemas

### El scroll no es suave:

1. Verifica que los scripts se carguen en orden:
   ```html
   <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>
   <script src="js/lenis-init.js"></script>
   ```

2. Abre la consola del navegador y busca:
   ```
   ✨ Lenis Smooth Scroll inicializado
   ```

### El scroll no funciona en móviles:

Esto es normal. `smoothTouch: false` está desactivado para mejor rendimiento. Si quieres activarlo:

```javascript
smoothTouch: true,
```

### Conflictos con otros scripts:

Si tienes otros scripts de scroll, desactívalos o asegúrate de que no interfieran con Lenis.

---

## 📚 Recursos

- [Lenis GitHub](https://github.com/studio-freight/lenis)
- [Lenis Documentation](https://github.com/studio-freight/lenis#readme)
- [Studio Freight](https://www.studiofreight.com/)

---

## ✅ Verificar Implementación

1. Accede a: `http://localhost/david/MMCINEMA/`
2. Haz scroll con el mouse o trackpad
3. Deberías notar un scroll **suave y fluido**
4. Haz clic en enlaces ancla (ej: "Ver horarios")
5. El scroll debe ser suave con animación

---

¡Lenis está completamente integrado en MMCinema! 🎉
