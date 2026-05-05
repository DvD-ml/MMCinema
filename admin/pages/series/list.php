<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

$series = [];
try {
    $series = $pdo->query("
        SELECT 
            s.id,
            s.titulo,
            s.sinopsis,
            s.poster,
            s.banner,
            s.fecha_estreno,
            s.edad,
            s.id_genero,
            s.id_plataforma,
            s.estado,
            s.destacada,
            s.puntuacion,
            s.creado,
            s.trailer,
            p.nombre AS plataforma_nombre,
            g.nombre AS genero_nombre,
            COALESCE(AVG(cs.puntuacion), 0) AS puntuacion_media,
            COUNT(cs.id) AS total_criticas
        FROM serie s
        LEFT JOIN plataforma p ON s.id_plataforma = p.id
        LEFT JOIN genero g ON s.id_genero = g.id
        LEFT JOIN critica_serie cs ON cs.id_serie = s.id
        GROUP BY s.id, s.titulo, s.sinopsis, s.poster, s.banner, s.fecha_estreno, s.edad, s.id_genero, s.id_plataforma, s.estado, s.destacada, s.puntuacion, s.creado, s.trailer, p.nombre, g.nombre
        ORDER BY s.creado DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en series/list.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Series | MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../../../favicon.svg">
    <link rel="stylesheet" href="../../../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/../../../admin/admin_header.php"; ?>

<div class="container py-4">
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Panel de series</h1>
            <p class="text-muted mb-0">Gestiona catálogo, temporadas, episodios y críticas.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= htmlspecialchars(mm_admin_url('series/form.php')) ?>" class="btn btn-primary">+ Añadir serie</a>
            <a href="<?= htmlspecialchars(mm_admin_url('series/panel.php')) ?>" class="btn btn-outline-light">Resumen</a>
            <a href="<?= htmlspecialchars(mm_admin_url('series/criticas/list.php')) ?>" class="btn btn-outline-light">Críticas</a>
        </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="admin-alert-inline success">
            <div class="admin-alert-icon">✓</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title">Serie guardada</div>
                <div class="admin-alert-message">La serie se ha guardado correctamente.</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="admin-table-wrap">
        <table class="admin-table table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Título</th>
                    <th>Plataforma</th>
                    <th>Género</th>
                    <th>Estado</th>
                    <th>Destacada</th>
                    <th>Nota</th>
                    <th>Críticas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($series as $serie): ?>
                    <tr>
                        <td><?= (int)$serie['id'] ?></td>

                        <td>
                            <?php if (!empty($serie['poster'])): ?>
                                <img src="<?= htmlspecialchars(mm_asset_url($serie['poster'])) ?>" alt="<?= htmlspecialchars($serie['titulo']) ?>" style="width:60px;height:90px;object-fit:cover;border-radius:8px;">
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($serie['titulo']) ?></td>
                        <td><?= htmlspecialchars($serie['plataforma_nombre'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($serie['genero_nombre'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($serie['estado']) ?></td>
                        <td><?= (int)$serie['destacada'] === 1 ? 'Sí' : 'No' ?></td>
                        <td><?= number_format((float)$serie['puntuacion_media'], 1) ?></td>
                        <td><?= (int)$serie['total_criticas'] ?></td>

                        <td>
                            <div class="acciones">
                                <a href="<?= htmlspecialchars(mm_admin_url('series/form.php') . '?id=' . (int)$serie['id']) ?>" class="btn btn-sm btn-primary">Editar</a>
                                <a href="<?= htmlspecialchars(mm_admin_url('series/temporadas/list.php') . '?id_serie=' . (int)$serie['id']) ?>" class="btn btn-sm btn-outline-light">Temporadas</a>
                                <form method="POST" action="<?= htmlspecialchars(mm_admin_url('series/delete.php')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que quieres borrar esta serie?');">
                                    <input type="hidden" name="id" value="<?= (int)$serie['id'] ?>">
                                    <?php echo CSRF::campoFormulario(); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($series)): ?>
                    <tr>
                        <td colspan="10">No hay series registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>





