<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "../config/conexion.php";

$usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

// Paginación
$porPagina = 9;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $porPagina;

// Contar total
$sqlCount = "SELECT COUNT(*) FROM pelicula WHERE fecha_estreno > CURDATE()";
$totalPeliculas = (int)$pdo->query($sqlCount)->fetchColumn();

$totalPaginas = max(1, (int)ceil($totalPeliculas / $porPagina));
if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
    $offset = ($pagina - 1) * $porPagina;
}

$sql = "
    SELECT
        p.*,
        g.nombre AS genero
    FROM pelicula p
    LEFT JOIN genero g ON p.id_genero = g.id
    WHERE p.fecha_estreno > CURDATE()
    ORDER BY p.fecha_estreno ASC
    LIMIT $porPagina OFFSET $offset
";

$stm = $pdo->prepare($sql);
$stm->execute();
$peliculas = $stm->fetchAll(PDO::FETCH_ASSOC);

function mm_prox_url(int $paginaDestino): string {
    return 'proximamente.php?pagina=' . $paginaDestino;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>MMCinema | Próximamente</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

<?php include "../components/navbar.php"; ?>

<main class="container my-4">
    <div class="hero mb-4">
        <h1 class="h2 mb-1">Próximamente</h1>
        <p class="text-muted mb-0">Descubre los próximos estrenos de MMCinema.</p>
    </div>

    <div class="row">
        <?php if (empty($peliculas)): ?>
            <p class="text-center text-muted">No hay próximos estrenos disponibles.</p>
        <?php endif; ?>

        <?php foreach ($peliculas as $loopIndex => $p): ?>
            <div class="col-md-4 mb-4">
                <div class="card pelicula-card proximamente-card reveal-card h-100" style="--delay: <?= ($loopIndex % 3) * 140 ?>ms;">

                    <img class="card-img-top"
                        src="../assets/img/posters/<?= htmlspecialchars($p['poster'] ?: 'placeholder.jpg') ?>"
                        alt="<?= htmlspecialchars($p['titulo']) ?>">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($p['titulo']) ?></h5>

                        <p class="mb-1 text-muted small">
                            Género: <?= htmlspecialchars($p['genero'] ?: 'Sin género') ?>
                        </p>

                        <p class="mb-2 text-muted small">
                            Estreno: <?= date('d/m/Y', strtotime($p['fecha_estreno'])) ?>
                        </p>

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
        <nav class="cine-pagination-wrap" aria-label="Paginación próximos estrenos">
            <div class="cine-pagination">
                <?php if ($pagina > 1): ?>
                    <a class="cine-page cine-page-nav" href="<?= htmlspecialchars(mm_prox_url($pagina - 1)) ?>">
                        &#10094;
                    </a>
                <?php else: ?>
                    <span class="cine-page cine-page-nav disabled">&#10094;</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a class="cine-page cine-page-number <?= $i === $pagina ? 'active' : '' ?>"
                       href="<?= htmlspecialchars(mm_prox_url($i)) ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a class="cine-page cine-page-nav" href="<?= htmlspecialchars(mm_prox_url($pagina + 1)) ?>">
                         &#10095;
                    </a>
                <?php else: ?>
                    <span class="cine-page cine-page-nav disabled">&#10095;</span>
                <?php endif; ?>
            </div>
        </nav>
    <?php endif; ?>
</main>

<?php include "../components/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include "../includes/lenis-scripts.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.reveal-card');

    if (!('IntersectionObserver' in window)) {
        cards.forEach(card => card.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -40px 0px'
    });

    cards.forEach(card => observer.observe(card));
});
</script>

</body>
</html>