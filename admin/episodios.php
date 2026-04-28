<?php
session_start();
require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");
require_once(__DIR__ . "/auth.php");

$idTemporadaFiltro = isset($_GET['id_temporada']) ? (int)$_GET['id_temporada'] : 0;

$sql = "
    SELECT 
        e.*,
        t.numero_temporada,
        s.titulo AS serie_titulo
    FROM episodio e
    INNER JOIN temporada t ON e.id_temporada = t.id
    INNER JOIN serie s ON t.id_serie = s.id
";
$params = [];

if ($idTemporadaFiltro > 0) {
    $sql .= " WHERE e.id_temporada = ? ";
    $params[] = $idTemporadaFiltro;
}

$sql .= " ORDER BY s.titulo ASC, t.numero_temporada ASC, e.numero_episodio ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$episodios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Episodios | Admin MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="mb-1">Episodios</h1>
            <p class="text-muted mb-0">Puedes filtrar por temporada para tenerlo ordenado.</p>
        </div>
        <a href="agregar_episodio.php<?= $idTemporadaFiltro > 0 ? '?id_temporada=' . $idTemporadaFiltro : '' ?>" class="btn btn-primary">+ Añadir episodio</a>
    </div>

    <?php mm_render_series_admin_nav('episodios', ['id_temporada' => $idTemporadaFiltro]); ?>

    <div class="admin-table-wrap">
        <table class="admin-table table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Serie</th>
                    <th>Temporada</th>
                    <th>Episodio</th>
                    <th>Título</th>
                    <th>Duración</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($episodios as $episodio): ?>
                    <tr>
                        <td><?= (int)$episodio['id'] ?></td>
                        <td><?= htmlspecialchars($episodio['serie_titulo']) ?></td>
                        <td><?= (int)$episodio['numero_temporada'] ?></td>
                        <td><?= (int)$episodio['numero_episodio'] ?></td>
                        <td><?= htmlspecialchars($episodio['titulo']) ?></td>
                        <td><?= !empty($episodio['duracion']) ? (int)$episodio['duracion'] . ' min' : '—' ?></td>
                        <td><?= !empty($episodio['fecha_estreno']) ? htmlspecialchars($episodio['fecha_estreno']) : '—' ?></td>
                        <td>
                            <div class="acciones">
                                <a href="editar_episodio.php?id=<?= (int)$episodio['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                <a href="borrar_episodio.php?id=<?= (int)$episodio['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que quieres borrar este episodio?');">Borrar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($episodios)): ?>
                    <tr><td colspan="8">No hay episodios.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>