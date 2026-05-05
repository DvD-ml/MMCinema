<?php
/**
 * CRUD Form Genérico
 * 
 * Variables requeridas:
 * - $entity: nombre de la entidad (pelicula, noticia, etc)
 * - $table: nombre de la tabla en BD
 * - $fields: array de campos del formulario
 * - $id: ID del registro (0 para nuevo)
 * - $data: datos del registro
 * - $pdo: conexión a BD
 */

require_once "auth.php";
require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../helpers/CSRF.php";

// Validar variables requeridas
if (!isset($entity, $table, $fields)) {
    die("Error: Variables requeridas no definidas");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modoEdicion = $id > 0;

// Inicializar datos
$data = [];
foreach ($fields as $field) {
    $data[$field] = '';
}

// Si es edición, obtener datos
if ($modoEdicion) {
    $stm = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
    $stm->execute([$id]);
    $data = $stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        header("Location: {$entity}s.php");
        exit();
    }
}

// Generar token CSRF
$csrfToken = CSRF::generarToken();

// Obtener opciones para selects (si existen)
$selectOptions = [];
if (isset($getSelectOptions) && is_callable($getSelectOptions)) {
    $selectOptions = $getSelectOptions($pdo);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicion ? "Editar " . ucfirst($entity) : "Nuevo " . ucfirst($entity) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1><?= $modoEdicion ? "Editar " . ucfirst($entity) : "Nuevo " . ucfirst($entity) ?></h1>
            <p><?= $modoEdicion ? "Modifica los datos del " . $entity . "." : "Crea un nuevo " . $entity . "." ?></p>
        </div>
        <a href="<?= $entity ?>s.php" class="btn btn-outline-light">Volver</a>
    </div>

    <div class="admin-glass-card p-4">
        <form action="<?= $entity ?>_guardar.php" method="POST" enctype="multipart/form-data">
            <?php echo CSRF::campoFormulario(); ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($data['id'] ?? '') ?>">

            <?php foreach ($fields as $field): ?>
                <?php
                $fieldType = 'text';
                $fieldLabel = ucfirst(str_replace('_', ' ', $field));
                $fieldValue = htmlspecialchars($data[$field] ?? '');
                
                // Detectar tipo de campo
                if (strpos($field, 'fecha') !== false || strpos($field, 'date') !== false) {
                    $fieldType = 'date';
                } elseif (strpos($field, 'email') !== false) {
                    $fieldType = 'email';
                } elseif (strpos($field, 'password') !== false) {
                    $fieldType = 'password';
                } elseif (strpos($field, 'numero') !== false || strpos($field, 'duracion') !== false) {
                    $fieldType = 'number';
                } elseif (strpos($field, 'contenido') !== false || strpos($field, 'sinopsis') !== false || strpos($field, 'descripcion') !== false) {
                    $fieldType = 'textarea';
                } elseif (strpos($field, 'id_') !== false) {
                    $fieldType = 'select';
                } elseif (strpos($field, 'imagen') !== false || strpos($field, 'poster') !== false || strpos($field, 'foto') !== false) {
                    $fieldType = 'file';
                }
                ?>

                <?php if ($fieldType === 'textarea'): ?>
                    <div class="mb-3">
                        <label class="form-label"><?= $fieldLabel ?></label>
                        <textarea name="<?= $field ?>" class="form-control" rows="5" required><?= $fieldValue ?></textarea>
                    </div>

                <?php elseif ($fieldType === 'select'): ?>
                    <div class="mb-3">
                        <label class="form-label"><?= $fieldLabel ?></label>
                        <select name="<?= $field ?>" class="form-select" required>
                            <option value="">Selecciona una opción</option>
                            <?php if (isset($selectOptions[$field])): ?>
                                <?php foreach ($selectOptions[$field] as $option): ?>
                                    <option value="<?= $option['id'] ?>" <?= ($data[$field] == $option['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($option['nombre'] ?? $option['titulo'] ?? $option['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                <?php elseif ($fieldType === 'file'): ?>
                    <div class="mb-3">
                        <label class="form-label"><?= $fieldLabel ?></label>
                        <input type="file" name="<?= $field ?>_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        
                        <?php if (!empty($data[$field])): ?>
                            <div class="mt-3">
                                <p class="mb-2 small text-light"><?= $fieldLabel ?> actual:</p>
                                <img src="../assets/img/<?= str_replace('_', '', $field) ?>/<?= htmlspecialchars($data[$field]) ?>" alt="<?= $fieldLabel ?> actual" style="max-width: 180px; border-radius: 10px;">
                            </div>
                        <?php endif; ?>
                        
                        <small class="text-secondary d-block mt-2">
                            Si no seleccionas una imagen nueva, se mantiene la actual.
                        </small>
                        <input type="hidden" name="<?= $field ?>_actual" value="<?= htmlspecialchars($data[$field] ?? '') ?>">
                    </div>

                <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label"><?= $fieldLabel ?></label>
                        <input type="<?= $fieldType ?>" name="<?= $field ?>" class="form-control" 
                               value="<?= $fieldValue ?>" 
                               <?= (strpos($field, 'id') === 0 || $field === 'id') ? '' : 'required' ?>>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="d-flex gap-3 flex-wrap">
                <button type="submit" class="btn btn-primary btn-lg">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear ' . $entity ?>
                </button>
                <a href="<?= $entity ?>s.php" class="btn btn-outline-light btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
