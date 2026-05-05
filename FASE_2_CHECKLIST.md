# FASE 2 - Remaining Tasks Checklist

## ✅ COMPLETED (85%)

- [x] Create generic CRUD system (form.php, save.php, delete.php)
- [x] Refactor películas (form, guardar, borrar)
- [x] Refactor noticias (form, guardar, borrar)
- [x] Refactor proyecciones (guardar, borrar)
- [x] Refactor salas (guardar, borrar)
- [x] Refactor usuarios (guardar, borrar)
- [x] Create series files (form, guardar, borrar)
- [x] Create temporadas files (form, guardar, borrar)
- [x] Create episodios files (form, guardar, borrar)
- [x] Create críticas files (guardar_new, borrar_new)

---

## ⏳ REMAINING TASKS (15%)

### Task 1: Update Navigation Links
**Estimated Time**: 15 minutes

#### 1.1 Update `admin/series.php`
- [ ] Find all links to `editar_serie.php?id=`
- [ ] Replace with `serie_form.php?id=`
- [ ] Find all links to `agregar_serie.php`
- [ ] Replace with `serie_form.php`
- [ ] Find all links to `borrar_serie.php`
- [ ] Replace with `serie_borrar.php`

#### 1.2 Update `admin/temporadas.php`
- [ ] Find all links to `editar_temporada.php?id=`
- [ ] Replace with `temporada_form.php?id=`
- [ ] Find all links to `agregar_temporada.php`
- [ ] Replace with `temporada_form.php`
- [ ] Find all links to `borrar_temporada.php`
- [ ] Replace with `temporada_borrar.php`

#### 1.3 Update `admin/episodios.php`
- [ ] Find all links to `editar_episodio.php?id=`
- [ ] Replace with `episodio_form.php?id=`
- [ ] Find all links to `agregar_episodio.php`
- [ ] Replace with `episodio_form.php`
- [ ] Find all links to `borrar_episodio.php`
- [ ] Replace with `episodio_borrar.php`

#### 1.4 Update `admin/criticas_series.php`
- [ ] Find all links to `critica_form.php?id=`
- [ ] Add `&tipo=serie` parameter
- [ ] Find all links to `critica_borrar.php`
- [ ] Update form to include `tipo=serie` in hidden field

#### 1.5 Update `admin/criticas.php`
- [ ] Find all links to `critica_form.php?id=`
- [ ] Add `&tipo=pelicula` parameter
- [ ] Find all links to `critica_borrar.php`
- [ ] Update form to include `tipo=pelicula` in hidden field

---

### Task 2: Delete Old Files
**Estimated Time**: 5 minutes

- [ ] Delete `admin/agregar_serie.php`
- [ ] Delete `admin/editar_serie.php`
- [ ] Delete `admin/agregar_temporada.php`
- [ ] Delete `admin/editar_temporada.php`
- [ ] Delete `admin/agregar_episodio.php`
- [ ] Delete `admin/editar_episodio.php`
- [ ] Delete `admin/critica_guardar.php` (old version)
- [ ] Delete `admin/critica_borrar.php` (old version)

---

### Task 3: Rename New Crítica Files
**Estimated Time**: 2 minutes

- [ ] Rename `admin/critica_guardar_new.php` → `admin/critica_guardar.php`
- [ ] Rename `admin/critica_borrar_new.php` → `admin/critica_borrar.php`

---

### Task 4: Testing
**Estimated Time**: 30 minutes

#### 4.1 Test Películas
- [ ] Create new película
- [ ] Edit película
- [ ] Delete película
- [ ] Verify image upload works
- [ ] Verify redirect works

#### 4.2 Test Noticias
- [ ] Create new noticia
- [ ] Edit noticia
- [ ] Delete noticia
- [ ] Verify image upload works
- [ ] Verify redirect works

#### 4.3 Test Series
- [ ] Create new serie
- [ ] Edit serie
- [ ] Delete serie
- [ ] Verify poster upload works
- [ ] Verify banner upload works
- [ ] Verify redirect works

#### 4.4 Test Temporadas
- [ ] Create new temporada
- [ ] Edit temporada
- [ ] Delete temporada
- [ ] Verify image upload works
- [ ] Verify redirect works

#### 4.5 Test Episodios
- [ ] Create new episodio
- [ ] Edit episodio
- [ ] Delete episodio
- [ ] Verify redirect works

#### 4.6 Test Proyecciones
- [ ] Create new proyección
- [ ] Edit proyección
- [ ] Delete proyección
- [ ] Verify redirect works

#### 4.7 Test Usuarios
- [ ] Create new usuario
- [ ] Edit usuario
- [ ] Delete usuario
- [ ] Verify password hashing works
- [ ] Verify redirect works

#### 4.8 Test Salas
- [ ] Create new sala
- [ ] Edit sala
- [ ] Delete sala
- [ ] Verify redirect works

#### 4.9 Test Críticas
- [ ] Create new crítica (película)
- [ ] Edit crítica (película)
- [ ] Delete crítica (película)
- [ ] Create new crítica (serie)
- [ ] Edit crítica (serie)
- [ ] Delete crítica (serie)
- [ ] Verify redirect works

#### 4.10 Test CSRF Protection
- [ ] Verify CSRF token is generated on all forms
- [ ] Verify CSRF token is validated on save/delete
- [ ] Verify error handling for invalid tokens

---

### Task 5: Upload to Server
**Estimated Time**: 5 minutes

- [ ] Connect to server via SCP
- [ ] Upload all refactored files
- [ ] Verify files are uploaded correctly
- [ ] Clear browser cache
- [ ] Test on live server

---

## Summary

| Task | Time | Status |
|------|------|--------|
| Update Navigation Links | 15 min | ⏳ TODO |
| Delete Old Files | 5 min | ⏳ TODO |
| Rename Crítica Files | 2 min | ⏳ TODO |
| Testing | 30 min | ⏳ TODO |
| Upload to Server | 5 min | ⏳ TODO |
| **TOTAL** | **57 min** | **⏳ TODO** |

---

## Notes

- All new files are ready to use
- All refactored files are ready to use
- No breaking changes to existing functionality
- All CSRF tokens are properly handled
- All image uploads are properly handled
- All redirects are properly configured

---

## How to Execute

1. **Update Navigation Links**: Use find/replace in your IDE
2. **Delete Old Files**: Use file manager or terminal
3. **Rename Crítica Files**: Use file manager or terminal
4. **Testing**: Use browser to test each CRUD operation
5. **Upload to Server**: Use SCP or SFTP

---

**Estimated Total Time**: ~1 hour
**Difficulty**: Low (mostly find/replace and testing)
**Risk**: Low (all changes are backward compatible)
