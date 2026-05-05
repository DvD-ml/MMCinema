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

require_once __DIR__ . "/../auth.php";
verificarAuth();
require_once __DIR__ . "/../../config/conexion.php";
require_once __DIR__ . "/../../helpers/CSRF.php";

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

$getPlaceholder = static function (string $fieldType, string $fieldLabel): string {
    if ($fieldType === 'email') {
        return 'correo@ejemplo.com';
    }

    return $fieldLabel;
};
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
<?php require_once __DIR__ . "/../admin_header.php"; ?>

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
                        <label class="form-label">
                            <?= $fieldLabel ?>
                            <span class="text-danger">*</span>
                        </label>
                        <textarea name="<?= $field ?>"
                                  class="form-control admin-form-textarea"
                                  rows="5"
                                  placeholder="Escribe aquí..."
                                  maxlength="1000"
                                  required><?= $fieldValue ?></textarea>
                        <small class="text-secondary d-block mt-1">
                            <span class="char-count">0</span>/1000 caracteres
                        </small>
                    </div>

                <?php elseif ($fieldType === 'select'): ?>
                    <div class="mb-3">
                        <label class="form-label">
                            <?= $fieldLabel ?>
                            <span class="text-danger">*</span>
                        </label>
                        <select name="<?= $field ?>" class="form-select admin-form-select" required>
                            <option value="">-- Selecciona una opción --</option>
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
                        <label class="form-label">
                            <?= $fieldLabel ?>
                            <?php if (empty($data[$field])): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        <input type="file"
                               name="<?= $field ?>_file"
                               class="form-control admin-form-file"
                               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                               <?= empty($data[$field]) ? 'required' : '' ?>>

                        <?php if (!empty($data[$field])): ?>
                            <div class="mt-3">
                                <p class="mb-2 small text-light"><?= $fieldLabel ?> actual:</p>
                                <img src="../assets/img/<?= str_replace('_', '', $field) ?>/<?= htmlspecialchars($data[$field]) ?>"
                                     alt="<?= $fieldLabel ?> actual"
                                     style="max-width: 180px; border-radius: 10px; border: 1px solid rgba(249,115,22,.3);">
                            </div>
                        <?php endif; ?>

                        <small class="text-secondary d-block mt-2">
                            Formatos: JPG, PNG, WebP | Máximo: 5MB
                        </small>
                        <input type="hidden" name="<?= $field ?>_actual" value="<?= htmlspecialchars($data[$field] ?? '') ?>">
                    </div>

                <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label">
                            <?= $fieldLabel ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="<?= $fieldType ?>"
                               name="<?= $field ?>"
                               class="form-control admin-form-input"
                               value="<?= $fieldValue ?>"
                               placeholder="<?= htmlspecialchars($getPlaceholder($fieldType, $fieldLabel)) ?>"
                               <?= (strpos($field, 'id') === 0 || $field === 'id') ? '' : 'required' ?>>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="d-flex gap-3 flex-wrap">
                <button type="submit" class="btn btn-primary">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear ' . $entity ?>
                </button>
                <a href="<?= $entity ?>s.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/admin-forms.js"></script>
</body>
</html>
