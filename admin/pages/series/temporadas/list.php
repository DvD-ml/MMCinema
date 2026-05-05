<?php
require_once __DIR__ . "/../../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../../config/conexion.php";
require_once __DIR__ . "/../../../../helpers/CSRF.php";
require_once __DIR__ . "/../../../helpers/series_admin_ui.php";

$idSerieFiltro = isset($_GET['id_serie']) ? (int)$_GET['id_serie'] : 0;
$temporadas = [];

try {
    $sql = "
        SELECT t.*, s.titulo AS serie_titulo
        FROM temporada t
        INNER JOIN serie s ON t.id_serie = s.id
    ";
    $params = [];

    if ($idSerieFiltro > 0) {
        $sql .= " WHERE t.id_serie = ? ";
        $params[] = $idSerieFiltro;
    }

    $sql .= " ORDER BY s.titulo ASC, t.numero_temporada ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $temporadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en series/temporadas/list.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Temporadas | Admin MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../../../../favicon.svg">
    <link rel="stylesheet" href="../../../../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/../../../../admin/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Temporadas</h1>
            <p>Puedes filtrar por serie para trabajar más cómodo.</p>
        </div>
        <a href="<?= htmlspecialchars(mm_admin_url('series/temporadas/form.php') . ($idSerieFiltro > 0 ? '?id_serie=' . $idSerieFiltro : '')) ?>" class="btn btn-primary">+ Añadir temporada</a>
    </div>

    <div class="admin-glass-card p-3 p-lg-4">
        <div class="admin-table-wrap">
            <table class="admin-table table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Serie</th>
                        <th>Nº</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($temporadas as $temporada): ?>
                        <tr>
                            <td><?= (int)$temporada['id'] ?></td>
                            <td><?= htmlspecialchars($temporada['serie_titulo']) ?></td>
                            <td><?= (int)$temporada['numero_temporada'] ?></td>
                            <td><?= htmlspecialchars($temporada['titulo'] ?? '—') ?></td>
                            <td><?= !empty($temporada['fecha_estreno']) ? htmlspecialchars($temporada['fecha_estreno']) : '—' ?></td>
                            <td>
                                <div class="acciones">
                                    <a href="<?= htmlspecialchars(mm_admin_url('series/temporadas/form.php') . '?id=' . (int)$temporada['id']) ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <a href="<?= htmlspecialchars(mm_admin_url('series/episodios/list.php') . '?id_temporada=' . (int)$temporada['id']) ?>" class="btn btn-sm btn-outline-light">Episodios</a>
                                    <form method="POST" action="<?= htmlspecialchars(mm_admin_url('series/temporadas/delete.php')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que quieres borrar esta temporada?');">
                                        <input type="hidden" name="id" value="<?= (int)$temporada['id'] ?>">
                                        <?php echo CSRF::campoFormulario(); ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($temporadas)): ?>
                        <tr><td colspan="6">No hay temporadas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>




