# 📡 API - MMCinema

Documentación de endpoints y funciones principales de MMCinema.

## Autenticación

### Login
**Endpoint**: `POST /backend/login.php`

**Parámetros**:
```json
{
  "email": "usuario@example.com",
  "password": "contraseña"
}
```

**Respuesta**:
```json
{
  "success": true,
  "message": "Login exitoso",
  "user_id": 1
}
```

### Registro
**Endpoint**: `POST /backend/registro.php`

**Parámetros**:
```json
{
  "nombre": "Juan",
  "apellido": "Pérez",
  "email": "juan@example.com",
  "password": "contraseña",
  "password_confirm": "contraseña"
}
```

### Logout
**Endpoint**: `GET /backend/logout.php`

## Películas

### Listar Películas
**Endpoint**: `GET /pages/index.php`

**Parámetros**: Ninguno

**Respuesta**: HTML con lista de películas

### Detalle de Película
**Endpoint**: `GET /pages/detalle.php?id=1`

**Parámetros**:
- `id` (int): ID de la película

### Agregar Película (Admin)
**Endpoint**: `POST /admin/agregar_pelicula.php`

**Parámetros**:
```json
{
  "titulo": "Título",
  "descripcion": "Descripción",
  "genero": "Acción",
  "año": 2026,
  "director": "Director",
  "duracion": 120,
  "clasificacion": "PG-13",
  "poster": "archivo.jpg"
}
```

## Series

### Listar Series
**Endpoint**: `GET /pages/series.php`

### Detalle de Serie
**Endpoint**: `GET /pages/detalle_serie.php?id=1`

**Parámetros**:
- `id` (int): ID de la serie

## Proyecciones

### Listar Proyecciones
**Endpoint**: `GET /pages/proyecciones.php`

### Reservar Entradas
**Endpoint**: `POST /backend/crear_ticket.php`

**Parámetros**:
```json
{
  "proyeccion_id": 1,
  "asientos": [1, 2, 3],
  "cantidad": 3,
  "total": 45.00
}
```

**Respuesta**:
```json
{
  "success": true,
  "ticket_id": 123,
  "pdf_url": "/storage/tickets/ticket_123.pdf"
}
```

## Favoritos

### Agregar a Favoritos
**Endpoint**: `POST /backend/toggle_favorito.php`

**Parámetros**:
```json
{
  "pelicula_id": 1
}
```

### Agregar Serie a Favoritos
**Endpoint**: `POST /backend/toggle_favorito_serie.php`

**Parámetros**:
```json
{
  "serie_id": 1
}
```

## Críticas

### Enviar Crítica de Película
**Endpoint**: `POST /backend/enviar_critica.php`

**Parámetros**:
```json
{
  "pelicula_id": 1,
  "calificacion": 8,
  "comentario": "Excelente película"
}
```

### Enviar Crítica de Serie
**Endpoint**: `POST /backend/enviar_critica_serie.php`

**Parámetros**:
```json
{
  "serie_id": 1,
  "calificacion": 9,
  "comentario": "Muy buena serie"
}
```

## Noticias

### Listar Noticias
**Endpoint**: `GET /pages/noticias.php`

### Detalle de Noticia
**Endpoint**: `GET /pages/detalle_noticia.php?id=1`

## Códigos de Respuesta

- `200` - OK
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `500` - Internal Server Error

## Seguridad

- Todos los endpoints requieren CSRF token en formularios POST
- La autenticación se valida con sesiones PHP
- Las contraseñas se hashean con bcrypt
- Las queries usan prepared statements

---

**Última actualización**: 30 de Abril de 2026
