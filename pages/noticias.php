<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once "../config/conexion.php";

/* =========================
   Paginación
========================= */
$porPagina = 9;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $porPagina;

/* =========================
   Contar noticias
========================= */
$sqlCount = "SELECT COUNT(*) FROM noticia";
$totalNoticias = (int)$pdo->query($sqlCount)->fetchColumn();

$totalPaginas = max(1, (int)ceil($totalNoticias / $porPagina));

if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
    $offset = ($pagina - 1) * $porPagina;
}

/* =========================
   Obtener noticias paginadas
========================= */
$sql = "SELECT id, titulo, contenido, imagen, publicado
        FROM noticia
        ORDER BY publicado DESC, id DESC
        LIMIT $porPagina OFFSET $offset";

$stm = $pdo->prepare($sql);
$stm->execute();
$noticias = $stm->fetchAll(PDO::FETCH_ASSOC);

function mm_noticias_url(int $paginaDestino): string {
    return 'noticias.php?pagina=' . $paginaDestino;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MMCinema — Noticias</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tu CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

<?php include "../components/navbar.php"; ?>

<div class="container my-5">
    <h1 class="fw-bold mb-4">Noticias de cine</h1>

    <?php if (empty($noticias)): ?>
        <p class="text-muted">No hay noticias publicadas todavía.</p>
    <?php else: ?>
        <div class="row gy-4">
            <?php foreach ($noticias as $n): ?>
                <?php
                    $img = !empty($n['imagen']) ? $n['imagen'] : "noticia-placeholder.jpg";
                ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 noticia-card">
                        <img
                            src="../assets/img/noticias/<?= htmlspecialchars($img) ?>"
                            class="card-img-top"
                            alt="Imagen de <?= htmlspecialchars($n['titulo']) ?>"
                        >

                        <div class="card-body d-flex flex-column">
                            <h4 class="fw-bold">
                                <?= htmlspecialchars($n['titulo']) ?>
                            </h4>

                            <p class="text-muted small">
                                Publicado: <?= date("d/m/Y", strtotime($n['publicado'])) ?>
                            </p>

                            <p class="flex-grow-1">
                                <?= htmlspecialchars(mb_strimwidth($n['contenido'], 0, 180, "...")) ?>
                            </p>

                            <a href="../pages/noticia.php?id=<?= (int)$n['id'] ?>"
                               class="btn btn-primary mt-3 align-self-start">
                                Saber más
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPaginas > 1): ?>
            <nav class="cine-pagination-wrap mt-5" aria-label="Paginación noticias">
                <div class="cine-pagination">
                    <?php if ($pagina > 1): ?>
                        <a class="cine-page cine-page-nav" href="<?= htmlspecialchars(mm_noticias_url($pagina - 1)) ?>">
                            &#10094; 
                        </a>
                    <?php else: ?>
                        <span class="cine-page cine-page-nav disabled">&#10094; </span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a class="cine-page cine-page-number <?= $i === $pagina ? 'active' : '' ?>"
                           href="<?= htmlspecialchars(mm_noticias_url($i)) ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($pagina < $totalPaginas): ?>
                        <a class="cine-page cine-page-nav" href="<?= htmlspecialchars(mm_noticias_url($pagina + 1)) ?>">
                             &#10095;
                        </a>
                    <?php else: ?>
                        <span class="cine-page cine-page-nav disabled"> &#10095;</span>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include "../components/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include "../includes/lenis-scripts.php"; ?>
</body>
</html>