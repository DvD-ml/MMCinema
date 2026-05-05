# 🧪 Cómo Probar el Panel de Proyecciones

## Pasos para Verificar que Todo Funciona

### 1. Abre la Consola del Navegador
- Presiona **F12** en tu navegador
- Ve a la pestaña **Console**
- Aquí verás los mensajes de depuración

### 2. Accede al Panel de Proyecciones
- URL: `http://200.234.233.50/mmcinema/admin/proyecciones.php`
- Deberías ver películas en tarjetas

### 3. Haz Clic en una Película
- Haz clic en cualquier tarjeta de película
- En la consola deberías ver:
  ```
  ✓ DOM Cargado - Inicializando proyecciones
  ✓ Salas cargadas
  → Abriendo modal para película: [ID] [Título]
  → Cargando proyecciones desde API...
  → Respuesta de API: 200
  → Datos recibidos: [...]
  ✓ Modal abierto
  ```

### 4. Verifica que el Modal se Abre
- Deberías ver un modal oscuro con:
  - Poster de la película
  - Título de la película
  - Botones "Añadir proyección" y "Ver película"
  - Lista de proyecciones

### 5. Haz Clic en "Editar" en una Proyección
- En la consola deberías ver:
  ```
  → Abriendo panel de edición para proyección: [ID]
  ✓ Panel de edición abierto
  ```
- Deberías ver un panel lateral deslizable desde la derecha con:
  - Formulario con campos: Fecha, Hora, Sala
  - Botones: Cancelar, Guardar cambios

### 6. Modifica los Datos y Guarda
- Cambia la fecha, hora o sala
- Haz clic en "Guardar cambios"
- En la consola deberías ver:
  ```
  → Guardando proyección...
  → Respuesta del servidor: 200
  ✓ Proyección guardada
  ```
- Deberías ver una alerta verde de éxito

---

## 🔍 Mensajes de Depuración Esperados

### Cuando Carga la Página
```
✓ Script de proyecciones cargado
✓ DOM Cargado - Inicializando proyecciones
✓ Salas cargadas
```

### Cuando Abres el Modal
```
→ Abriendo modal para película: 1 Avatar: Fuego y Ceniza
→ Cargando proyecciones desde API...
→ Respuesta de API: 200
→ Datos recibidos: Array(3) [...]
✓ Modal abierto
```

### Cuando Abres el Panel de Edición
```
→ Abriendo panel de edición para proyección: 13
✓ Panel de edición abierto
```

### Cuando Guardas
```
→ Guardando proyección...
→ Respuesta del servidor: 200
✓ Proyección guardada
```

---

## ⚠️ Si Algo No Funciona

### El Modal No Se Abre
1. Abre la consola (F12)
2. Busca mensajes de error (en rojo)
3. Verifica que veas: `→ Abriendo modal para película:`
4. Si no ves nada, el onclick no se está ejecutando

**Solución:**
- Limpia el caché del navegador (Ctrl+Shift+Delete)
- Recarga la página (Ctrl+F5)
- Intenta en otro navegador

### El Modal Se Abre pero No Muestra Proyecciones
1. En la consola, busca: `→ Respuesta de API:`
2. Si dice `200`, el problema es en los datos
3. Si dice otro número (404, 500), hay error en el servidor

**Solución:**
- Verifica que `proyecciones_api.php` esté en el servidor
- Verifica que la película tenga proyecciones en la BD

### El Panel de Edición No Se Abre
1. En la consola, busca: `→ Abriendo panel de edición`
2. Si no aparece, el botón "Editar" no se está ejecutando

**Solución:**
- Verifica que el modal esté abierto
- Intenta hacer clic en el botón "Editar" nuevamente

### No Se Guarda la Proyección
1. En la consola, busca: `→ Guardando proyección...`
2. Verifica la respuesta del servidor

**Solución:**
- Verifica que todos los campos estén completos
- Verifica que `proyeccion_guardar.php` esté en el servidor
- Revisa los errores en la consola

---

## 📱 Prueba en Móvil

1. Accede desde tu teléfono a: `http://200.234.233.50/mmcinema/admin/proyecciones.php`
2. Haz clic en una película
3. El modal debería ocupar la pantalla
4. Haz clic en "Editar"
5. El panel debería ocupar la pantalla completa
6. Modifica y guarda

---

## 🎯 Checklist de Verificación

- [ ] La página carga sin errores
- [ ] Las películas se muestran en tarjetas
- [ ] Haces clic en una película y se abre el modal
- [ ] El modal muestra las proyecciones
- [ ] Haces clic en "Editar" y se abre el panel lateral
- [ ] El panel muestra el formulario con los datos
- [ ] Modificas los datos y haces clic en "Guardar"
- [ ] Aparece la alerta de éxito
- [ ] El modal se actualiza con los nuevos datos
- [ ] Cierras el modal con ESC o el botón X
- [ ] Todo funciona en móvil

---

## 📞 Información para Reportar Problemas

Si algo no funciona, proporciona:

1. **Captura de pantalla** del problema
2. **Mensajes de la consola** (F12 → Console)
3. **URL** donde ocurre el problema
4. **Navegador** que estás usando
5. **Dispositivo** (PC, móvil, tablet)

---

**Última actualización:** 4 de Mayo de 2026
