# CSRF Token Fixes - Detailed Changes

## File 1: admin/agregar_serie.php

### Change 1: Added CSRF require at top
```php
// BEFORE
<?php
require_once "auth.php";
verificarAuth();

require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");
require_once(__DIR__ . "/includes/upload_helper.php");

// AFTER
<?php
require_once "auth.php";
verificarAuth();

require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");
require_once(__DIR__ . "/includes/upload_helper.php");
require_once "../helpers/CSRF.php";
```

### Change 2: Added CSRF validation inside POST check
```php
// BEFORE
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = '';

// AFTER
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    CSRF::validarOAbortar();
    
    $titulo = '';
```

### Change 3: Added CSRF token to form
```php
// BEFORE
<div class="form-card">
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">

// AFTER
<div class="form-card">
    <form method="POST" enctype="multipart/form-data">
        <?php echo CSRF::campoFormulario(); ?>
        <div class="mb-3">
```

---

## File 2: admin/editar_serie.php

### Change 1: Added CSRF require at top
```php
// BEFORE
<?php
require_once "auth.php";
verificarAuth();

require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");
require_once(__DIR__ . "/includes/upload_helper.php");

// AFTER
<?php
require_once "auth.php";
verificarAuth();

require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");
require_once(__DIR__ . "/includes/upload_helper.php");
require_once "../helpers/CSRF.php";
```

### Change 2: Added CSRF validation inside POST check
```php
// BEFORE
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = '';

// AFTER
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    CSRF::validarOAbortar();
    
    $titulo = '';
```

### Change 3: Added CSRF token to form
```php
// BEFORE
<div class="form-card">
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">

// AFTER
<div class="form-card">
    <form method="POST" enctype="multipart/form-data">
        <?php echo CSRF::campoFormulario(); ?>
        <div class="mb-3">
```

---

## File 3: admin/pelicula_form.php

### Change 1: Moved CSRF require to top of PHP section
```php
// BEFORE
<?php
require_once "auth.php";
require_once "../config/conexion.php";

// AFTER
<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";
```

### Change 2: Fixed CSRF token placement in form
```php
// BEFORE
<form action="pelicula_guardar.php" method="POST" enctype="multipart/form-data" class="card card-body bg-black text-white border-secondary">
    <?php require_once "../helpers/CSRF.php"; echo CSRF::campoFormulario(); ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($pelicula['id']) ?>">

// AFTER
<form action="pelicula_guardar.php" method="POST" enctype="multipart/form-data" class="card card-body bg-black text-white border-secondary">
    <?php echo CSRF::campoFormulario(); ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($pelicula['id']) ?>">
```

---

## File 4: admin/noticia_form.php

### Change 1: Moved CSRF require to top of PHP section
```php
// BEFORE
<?php
require_once "auth.php";
require_once "../config/conexion.php";

// AFTER
<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";
```

### Change 2: Fixed CSRF token placement in form
```php
// BEFORE
<form action="noticia_guardar.php" method="POST" enctype="multipart/form-data" class="card card-body bg-black text-white border-secondary">
    <?php require_once "../helpers/CSRF.php"; echo CSRF::campoFormulario(); ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($noticia['id']) ?>">

// AFTER
<form action="noticia_guardar.php" method="POST" enctype="multipart/form-data" class="card card-body bg-black text-white border-secondary">
    <?php echo CSRF::campoFormulario(); ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($noticia['id']) ?>">
```

---

## File 5: admin/proyeccion_form.php

### Change 1: Moved CSRF require to top of PHP section
```php
// BEFORE
<?php
require_once "auth.php";
require_once "../config/conexion.php";

// AFTER
<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";
```

### Change 2: Fixed CSRF token placement in form
```php
// BEFORE
<div class="admin-glass-card p-4">
    <form action="proyeccion_guardar.php" method="POST">
        <?php require_once "../helpers/CSRF.php"; echo CSRF::campoFormulario(); ?>
        <input type="hidden" name="id" value="<?= (int)$proyeccion['id'] ?>">

// AFTER
<div class="admin-glass-card p-4">
    <form action="proyeccion_guardar.php" method="POST">
        <?php echo CSRF::campoFormulario(); ?>
        <input type="hidden" name="id" value="<?= (int)$proyeccion['id'] ?>">
```

---

## File 6: admin/carrusel_destacado.php

### Change 1: Added CSRF require at top
```php
// BEFORE
<?php
session_start();
require_once "../config/conexion.php";
require_once "../includes/optimizar_imagen.php";
require_once "auth.php";

// AFTER
<?php
session_start();
require_once "../config/conexion.php";
require_once "../includes/optimizar_imagen.php";
require_once "auth.php";
require_once "../helpers/CSRF.php";
```

### Change 2: Added CSRF validation inside POST check
```php
// BEFORE
// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

// AFTER
// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validarOAbortar();
    
    $accion = $_POST['accion'] ?? '';
```

### Change 3: Added CSRF token to toggle_activo form
```php
// BEFORE
<form method="POST" style="display: inline;" onsubmit="return confirmarToggle(this, '<?= htmlspecialchars($slide['titulo']) ?>', <?= $slide['activo'] ? 'true' : 'false' ?>)">
    <input type="hidden" name="accion" value="toggle_activo">

// AFTER
<form method="POST" style="display: inline;" onsubmit="return confirmarToggle(this, '<?= htmlspecialchars($slide['titulo']) ?>', <?= $slide['activo'] ? 'true' : 'false' ?>)">
    <?php echo CSRF::campoFormulario(); ?>
    <input type="hidden" name="accion" value="toggle_activo">
```

### Change 4: Added CSRF token to eliminar form
```php
// BEFORE
<form method="POST" style="display: inline;" 
      onsubmit="return confirm('¿Estás seguro de eliminar este slide?')">
    <input type="hidden" name="accion" value="eliminar">

// AFTER
<form method="POST" style="display: inline;" 
      onsubmit="return confirm('¿Estás seguro de eliminar este slide?')">
    <?php echo CSRF::campoFormulario(); ?>
    <input type="hidden" name="accion" value="eliminar">
```

### Change 5: Added CSRF token to modal form
```php
// BEFORE
<form method="POST" enctype="multipart/form-data" id="formSlide">
    <div class="modal-header">

// AFTER
<form method="POST" enctype="multipart/form-data" id="formSlide">
    <?php echo CSRF::campoFormulario(); ?>
    <div class="modal-header">
```

---

## Summary of Changes

| File | Changes | Type |
|------|---------|------|
| agregar_serie.php | 3 changes | Add require, validation, token |
| editar_serie.php | 3 changes | Add require, validation, token |
| pelicula_form.php | 2 changes | Move require, fix token |
| noticia_form.php | 2 changes | Move require, fix token |
| proyeccion_form.php | 2 changes | Move require, fix token |
| carrusel_destacado.php | 5 changes | Add require, validation, 3 tokens |

**Total: 17 changes across 6 files**

All changes follow the same pattern:
1. Ensure CSRF helper is required at top of PHP section
2. Ensure CSRF validation is inside POST check
3. Ensure all forms have CSRF token field

---

## Verification

✅ All files pass PHP syntax check
✅ All files have proper CSRF require statements
✅ All files have CSRF validation in POST checks
✅ All forms have CSRF token fields
✅ No breaking changes to functionality
✅ All changes are backward compatible
