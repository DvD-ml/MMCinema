<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once "config/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location: noticias.php");
    exit();
}

$stm = $pdo->prepare("SELECT * FROM noticia WHERE id = ?");
$stm->execute([$id]);
$noticia = $stm->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    header("Location: noticias.php");
    exit();
}

/* -------- Datos interesantes (auto-generados) -------- */
$contenido = trim($noticia["contenido"] ?? "");
$palabras = preg_split('/\s+/u', strip_tags($contenido));
$wordCount = is_array($palabras) ? count(array_filter($palabras)) : 0;
$minLectura = max(1, (int)ceil($wordCount / 200)); // 200 palabras/min aprox

$fechaPub = new DateTime($noticia["publicado"]);
$hoy = new DateTime();
$diff = $fechaPub->diff($hoy);
$hace = ($diff->days === 0) ? "hoy" : "hace " . $diff->days . " dÃ­a(s)";

function topKeywordsES(string $text, int $limit = 5): array {
    $stop = [
        "para","pero","porque","como","que","quÃ©","con","sin","sobre","entre","desde",
        "este","esta","estos","estas","ese","esa","esos","esas","aqui","aquÃ­","allÃ­",
        "los","las","del","por","una","uno","unos","unas","mÃ¡s","menos","muy","tambiÃ©n",
        "ser","estar","hay","han","fue","son","era","sus","mis","tus","tu","su","se",
        "al","lo","la","el","y","o","u","de","en","a","un","is","are"
    ];

    $clean = mb_strtolower(strip_tags($text), "UTF-8");
    $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $clean);
    $tokens = preg_split('/\s+/u', $clean);

    $freq = [];
    foreach ($tokens as $t) {
        $t = trim($t);
        if ($t === "" || mb_strlen($t, "UTF-8") < 4) continue;
        if (in_array($t, $stop, true)) continue;

        $freq[$t] = ($freq[$t] ?? 0) + 1;
    }

    arsort($freq);
    return array_slice(array_keys($freq), 0, $limit);
}

$keywords = topKeywordsES($contenido, 5);

/* -------- Noticias relacionadas (3 Ãºltimas) -------- */
$stmRel = $pdo->prepare("SELECT id, titulo, imagen, publicado, contenido
                         FROM noticia
                         WHERE id <> ?
                         ORDER BY publicado DESC
                         LIMIT 3");
$stmRel->execute([$id]);
$relacionadas = $stmRel->fetchAll(PDO::FETCH_ASSOC);

$img = $noticia["imagen"] ?: "noticia-placeholder.jpg";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>MMCinema | <?= htmlspecialchars($noticia["titulo"]) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include "navbar.php"; ?>

<main class="container my-5">
    <a href="noticias.php" class="btn btn-outline-light mb-4">â† Volver a noticias</a>

    <article class="noticia-detalle">
        <header class="mb-4">
            <h1 class="fw-bold"><?= htmlspecialchars($noticia["titulo"]) ?></h1>
            <p class="text-muted">
                Publicado: <?= date("d/m/Y H:i", strtotime($noticia["publicado"])) ?> Â· <?= htmlspecialchars($hace) ?>
            </p>
        </header>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="noticia-hero mb-3">
<img src="assets/img/noticias/<?= htmlspecialchars($img) ?>" alt="Imagen de <?= htmlspecialchars($noticia["titulo"]) ?>" class="img-fluid rounded">

                </div>

                <div class="noticia-texto">
                    <p class="lead"><?= nl2br(htmlspecialchars($contenido)) ?></p>
                </div>
            </div>

        </div>
    </article>

    <?php if (!empty($relacionadas)): ?>
        <section class="mt-5">
            <h2 class="fw-bold mb-3">Noticias relacionadas</h2>
            <div class="row gy-4">
                <?php foreach($relacionadas as $r): ?>
                    <?php $imgR = $r["imagen"] ?: "noticia-placeholder.jpg"; ?>
                    <div class="col-md-4">
                        <article class="card noticia-card h-100">
<img src="assets/img/noticias/<?= htmlspecialchars($imgR) ?>" class="card-img-top" alt="Imagen de <?= htmlspecialchars($r['titulo']) ?>">

                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold"><?= htmlspecialchars($r["titulo"]) ?></h5>
                                <p class="text-muted small">Publicado: <?= date('d/m/Y', strtotime($r['publicado'])) ?></p>
                                <p class="flex-grow-1"><?= htmlspecialchars(mb_strimwidth($r['contenido'],0,120,"...")) ?></p>
                                <a href="noticia.php?id=<?= (int)$r["id"] ?>" class="btn btn-primary mt-2 align-self-start">
                                    Saber mÃ¡s
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php include "footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



