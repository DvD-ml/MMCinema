# MMCINEMA - Deployment Instructions

## Date: May 4, 2026

### Quick Summary

The project has been cleaned up and optimized:
- ✅ 27 obsolete documentation files deleted
- ✅ 18 deployment scripts deleted
- ✅ Color consistency fixed (#f59e0b → #f97316)
- ✅ Alert system verified with orange lines
- ✅ Proyecciones system fully functional
- ✅ All buttons styled consistently (orange #f97316 with white text)

---

## Deployment Methods

### Method 1: Full Project Upload (Recommended)

Upload the entire project to the server:

```bash
scp -r ./* root@200.234.233.50:/var/www/html/mmcinema/
```

**Time**: ~2-3 minutes
**Advantage**: Ensures all files are up-to-date
**Disadvantage**: Uploads all files including unchanged ones

---

### Method 2: Selective File Upload

Upload only modified files:

```bash
# CSS files with color fixes
scp assets/css/profile.css root@200.234.233.50:/var/www/html/mmcinema/assets/css/
scp assets/css/custom-checkbox.css root@200.234.233.50:/var/www/html/mmcinema/assets/css/

# Alert system (already uploaded)
scp assets/css/admin-alerts.css root@200.234.233.50:/var/www/html/mmcinema/assets/css/
scp assets/js/admin-alerts.js root@200.234.233.50:/var/www/html/mmcinema/assets/js/

# Proyecciones system
scp admin/proyecciones.php root@200.234.233.50:/var/www/html/mmcinema/admin/
scp admin/proyeccion_form.php root@200.234.233.50:/var/www/html/mmcinema/admin/
scp admin/proyeccion_guardar.php root@200.234.233.50:/var/www/html/mmcinema/admin/
scp admin/proyeccion_borrar.php root@200.234.233.50:/var/www/html/mmcinema/admin/
```

**Time**: ~30 seconds
**Advantage**: Faster upload
**Disadvantage**: Manual file selection

---

### Method 3: Using SFTP Client

1. Open your SFTP client (FileZilla, WinSCP, etc.)
2. Connect to: `root@200.234.233.50`
3. Navigate to: `/var/www/html/mmcinema/`
4. Drag and drop files from local to remote

---

## Post-Deployment Steps

### Step 1: Clear Browser Cache

Users must clear their browser cache to see CSS changes:

**Windows:**
- Chrome/Edge: `Ctrl+Shift+Supr`
- Firefox: `Ctrl+Shift+Supr`

**Mac:**
- Chrome/Safari: `Cmd+Shift+Supr`
- Firefox: `Cmd+Shift+Supr`

**Or use DevTools:**
1. Press `F12` to open DevTools
2. Go to Settings
3. Click "Clear browsing data"
4. Select "All time"
5. Check "Cached images and files"
6. Click "Clear data"

### Step 2: Test Proyecciones System

1. Log in to admin panel
2. Click "Proyecciones" in the menu
3. Verify tabs work (En Cartelera / Próximamente)
4. Click "Editar" on a movie
5. Verify existing proyecciones display
6. Try adding a new proyección
7. Verify save/delete redirects to same page

### Step 3: Verify Alert System

1. Navigate to any admin page
2. Look for alerts in top-right corner
3. Verify alerts have orange lines (#f97316)
4. Verify alerts auto-dismiss after 4-5 seconds
5. Verify close button works

### Step 4: Check Button Styling

1. Navigate to proyecciones page
2. Verify all buttons are orange (#f97316)
3. Verify button text is white
4. Hover over buttons to verify darker orange (#ea580c)
5. Check on mobile device (responsive)

### Step 5: Test on Mobile

1. Open proyecciones page on mobile device
2. Verify tabs are functional
3. Verify buttons are clickable
4. Verify grid layout adjusts
5. Verify alerts display correctly

---

## Troubleshooting

### Issue: CSS changes not visible

**Solution:**
1. Clear browser cache (Ctrl+Shift+Supr)
2. Hard refresh (Ctrl+F5)
3. Check DevTools → Network → Disable cache
4. Verify files uploaded to correct path

### Issue: Proyecciones buttons not working

**Solution:**
1. Check browser console for JavaScript errors (F12)
2. Verify admin-alerts.js is loaded
3. Check CSRF token in form
4. Verify database connection

### Issue: Alerts not displaying

**Solution:**
1. Check if admin-alerts.css is loaded (F12 → Sources)
2. Check if admin-alerts.js is loaded (F12 → Sources)
3. Verify admin_header.php includes both files
4. Check browser console for errors

### Issue: Tabs not switching

**Solution:**
1. Check browser console for JavaScript errors (F12)
2. Verify JavaScript is enabled
3. Check if tab IDs match in HTML and JavaScript
4. Verify onclick handlers are present

---

## Rollback Instructions

If something goes wrong, you can rollback to the previous version:

```bash
# Backup current version
scp -r root@200.234.233.50:/var/www/html/mmcinema/ ./mmcinema_backup/

# Restore from local backup (if you have one)
scp -r ./mmcinema_old/* root@200.234.233.50:/var/www/html/mmcinema/
```

---

## Files Changed Summary

### CSS Files:
- `assets/css/profile.css` - Color consistency fixes
- `assets/css/custom-checkbox.css` - Color consistency fixes
- `assets/css/admin-alerts.css` - Already verified

### PHP Files:
- `admin/proyecciones.php` - Tabs functionality
- `admin/proyeccion_form.php` - Form with existing proyecciones
- `admin/proyeccion_guardar.php` - Save handler
- `admin/proyeccion_borrar.php` - Delete handler
- `admin/admin_header.php` - Alert system included

### JavaScript Files:
- `assets/js/admin-alerts.js` - Already verified

### Deleted Files:
- 27 obsolete .md files
- 18 deployment scripts
- 2 utility files

---

## Verification Checklist

After deployment, verify:

- [ ] CSS changes visible (orange buttons, colors)
- [ ] Proyecciones tabs work
- [ ] Proyecciones form displays existing proyecciones
- [ ] Add/Edit/Delete proyecciones works
- [ ] Alerts display with orange lines
- [ ] All buttons are orange with white text
- [ ] Mobile responsive design works
- [ ] No JavaScript errors in console
- [ ] No PHP errors in logs

---

## Support

If you encounter any issues:

1. Check browser console (F12)
2. Check server logs: `/var/www/html/mmcinema/logs/`
3. Verify file permissions: `chmod 755 admin/`
4. Verify database connection in `config/conexion.php`

---

## Performance Notes

- Alert system uses minimal resources
- CSS changes are lightweight
- No new dependencies added
- All changes are backward compatible
- No database migrations required

---

## Security Notes

- CSRF tokens properly validated
- Input validation in place
- XSS protection enabled
- No sensitive data exposed
- All changes follow security best practices

---

**Deployment Status**: ✅ READY
**Last Updated**: May 4, 2026
**Verified**: Yes
