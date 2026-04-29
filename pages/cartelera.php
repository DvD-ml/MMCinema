<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "../config/conexion.php";

/* =========================================
   FILTROS CARTELERA
========================================= */
$f_genero = isset($_GET['genero']) ? (int)$_GET['genero'] : 0;
$f_fecha  = isset($_GET['fecha']) ? trim($_GET['fecha']) : '';
$f_sala   = isset($_GET['sala']) ? trim($_GET['sala']) : '';

$porPagina = 9;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $porPagina;

$generos = $pdo->query("SELECT id, nombre FROM genero ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$salas = $pdo->query("SELECT DISTINCT sala FROM proyeccion ORDER BY sala")->fetchAll(PDO::FETCH_COLUMN);

$where = ["p.fecha_estreno <= CURDATE()"];
$params = [];

if ($f_genero > 0) {
    $where[] = "p.id_genero = ?";
    $params[] = $f_genero;
}
if ($f_fecha !== '') {
    $where[] = "EXISTS (
        SELECT 1
        FROM proyeccion pr2
        WHERE pr2.id_pelicula = p.id
          AND pr2.fecha = ?
    )";
    $params[] = $f_fecha;
}
if ($f_sala !== '') {
    $where[] = "EXISTS (
        SELECT 1
        FROM proyeccion pr3
        WHERE pr3.id_pelicula = p.id
          AND pr3.sala = ?
    )";
    $params[] = $f_sala;
}

$whereSql = implode(" AND ", $where);

$sqlCount = "
    SELECT COUNT(*)
    FROM pelicula p
    WHERE $whereSql
";
$stmCount = $pdo->prepare($sqlCount);
$stmCount->execute($params);
$totalPeliculas = (int)$stmCount->fetchColumn();

$totalPaginas = max(1, (int)ceil($totalPeliculas / $porPagina));
if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
    $offset = ($pagina - 1) * $porPagina;
}

$sqlPeliculas = "
    SELECT
        p.*,
        g.nombre AS genero,
        (
            SELECT ROUND(AVG(c.puntuacion), 1)
            FROM critica c
            WHERE c.id_pelicula = p.id
        ) AS media_puntuacion
    FROM pelicula p
    LEFT JOIN genero g ON p.id_genero = g.id
    WHERE $whereSql
    ORDER BY p.fecha_estreno DESC
    LIMIT $porPagina OFFSET $offset
";
$stmPeliculas = $pdo->prepare($sqlPeliculas);
$stmPeliculas->execute($params);
$peliculas = $stmPeliculas->fetchAll(PDO::FETCH_ASSOC);

$queryBase = $_GET;
unset($queryBase['pagina']);

function mm_build_page_url(array $queryBase, int $paginaDestino): string {
    $queryBase['pagina'] = $paginaDestino;
    return 'cartelera.php?' . http_build_query($queryBase);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>MMCinema | Cartelera</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

<?php include "../components/navbar.php"; ?>

<main>
    <div class="container my-4">

        <div class="hero mb-4">
            <h1 class="h2 mb-1">Cartelera</h1>
            <p class="text-muted mb-0">Películas disponibles ahora mismo en MMCinema.</p>
        </div>

        <div class="filtro-cartelera">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label">Género</label>
                    <select name="genero" class="form-select">
                        <option value="0">Todos</option>
                        <?php foreach($generos as $g): ?>
                            <option value="<?= (int)$g['id'] ?>" <?= ($f_genero == (int)$g['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Fecha de proyección</label>
                    <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($f_fecha) ?>">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Sala</label>
                    <select name="sala" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach($salas as $s): ?>
                            <option value="<?= htmlspecialchars($s) ?>" <?= ($f_sala === $s) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-1 d-grid">
                    <button class="btn btn-primary">Filtrar</button>
                </div>

                <div class="col-12">
                    <a href="../pages/cartelera.php" class="text-muted small">Quitar filtros</a>
                </div>
            </form>
        </div>

        <div class="row">
            <?php if (empty($peliculas)): ?>
                <p class="text-center text-muted">Aún no hay películas en cartelera.</p>
            <?php endif; ?>

            <?php foreach ($peliculas as $p): ?>
                <div class="col-md-4 mb-4">
                    <div class="card pelicula-card h-100">
                        <img class="card-img-top"
                             src="../assets/img/posters/<?= htmlspecialchars($p['poster'] ?: 'placeholder.jpg') ?>"
                             alt="<?= htmlspecialchars($p['titulo']) ?>">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($p['titulo']) ?></h5>

                            <p class="mb-1 text-muted small">
                                Estreno: <?= date('d/m/Y', strtotime($p['fecha_estreno'])) ?>
                            </p>

                            <?php if ($p['media_puntuacion'] !== null): ?>
                                <?php
                                $m = round(((float)$p['media_puntuacion']) * 2) / 2;
                                $full = floor($m);
                                $half = ($m - $full) == 0.5;
                                ?>
                                <div class="mb-1">
                                    <span class="stars">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <?php if($i <= $full): ?>
                                                <span class="star on">★</span>
                                            <?php elseif($i == $full + 1 && $half): ?>
                                                <span class="star half">★</span>
                                            <?php else: ?>
                                                <span class="star off">★</span>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </span>
                                    <small class="text-muted">(<?= number_format((float)$p['media_puntuacion'], 1) ?>/5)</small>
                                </div>
                            <?php endif; ?>

                            <p class="card-text flex-grow-1">
                                <?= htmlspecialchars(mb_strimwidth($p['sinopsis'] ?? '', 0, 140, '...')) ?>
                            </p>

                            <a href="../pages/pelicula.php?id=<?= (int)$p['id'] ?>" class="btn btn-primary btn-sm mt-2">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPaginas > 1): ?>
            <nav class="cine-pagination-wrap" aria-label="Paginación cartelera">
                <div class="cine-pagination">
                    <?php if ($pagina > 1): ?>
                        <a class="cine-page cine-page-nav" href="<?= htmlspecialchars(mm_build_page_url($queryBase, $pagina - 1)) ?>">&#10094;</a>
                    <?php else: ?>
                        <span class="cine-page cine-page-nav disabled">&#10094;</span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a class="cine-page cine-page-number <?= $i === $pagina ? 'active' : '' ?>"
                           href="<?= htmlspecialchars(mm_build_page_url($queryBase, $i)) ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($pagina < $totalPaginas): ?>
                        <a class="cine-page cine-page-nav" href="<?= htmlspecialchars(mm_build_page_url($queryBase, $pagina + 1)) ?>">&#10095;</a>
                    <?php else: ?>
                        <span class="cine-page cine-page-nav disabled">&#10095;</span>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>

    </div>
</main>

<?php include "../components/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php // include "../includes/lenis-scripts.php"; // Lenis desactivado ?>
</body>
</html>