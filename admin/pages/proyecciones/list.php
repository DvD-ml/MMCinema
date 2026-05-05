<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

$peliculasCartelera = [];
$peliculasProximamente = [];

// Obtener películas en cartelera
try {
    $sqlCartelera = "SELECT p.id, p.titulo, p.poster, p.fecha_estreno, COUNT(DISTINCT pr.id) as total_proyecciones FROM pelicula p LEFT JOIN proyeccion pr ON p.id = pr.id_pelicula WHERE p.fecha_estreno <= CURDATE() GROUP BY p.id ORDER BY p.fecha_estreno DESC";
    $peliculasCartelera = $pdo->query($sqlCartelera)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en proyecciones/list.php (cartelera): " . $e->getMessage());
}

// Obtener películas próximamente
try {
    $sqlProximamente = "SELECT p.id, p.titulo, p.poster, p.fecha_estreno, COUNT(DISTINCT pr.id) as total_proyecciones FROM pelicula p LEFT JOIN proyeccion pr ON p.id = pr.id_pelicula WHERE p.fecha_estreno > CURDATE() GROUP BY p.id ORDER BY p.fecha_estreno ASC";
    $peliculasProximamente = $pdo->query($sqlProximamente)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en proyecciones/list.php (próximamente): " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyecciones</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../../../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pelicula-card {
            background: rgba(10, 18, 32, 0.6);
            border: 1px solid rgba(249, 115, 22, 0.3);
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pelicula-card:hover {
            border-color: rgba(249, 115, 22, 0.8);
            background: rgba(10, 18, 32, 0.8);
        }

        .pelicula-poster {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            display: block;
        }

        .pelicula-info {
            padding: 12px;
        }

        .pelicula-titulo {
            font-weight: 600;
            font-size: 14px;
            color: #f8fafc;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .pelicula-meta {
            font-size: 12px;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .proyecciones-count {
            display: inline-block;
            background: #f97316;
            color: #ffffff;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .btn-editar {
            background: #f97316;
            color: #ffffff !important;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            text-decoration: none !important;
            display: inline-block;
        }

        .btn-editar:hover {
            background: #ea580c;
            color: #ffffff !important;
        }

        .tabs-separator {
            display: flex;
            gap: 40px;
            margin-bottom: 32px;
            border-bottom: 2px solid rgba(249, 115, 22, 0.2);
            padding-bottom: 16px;
        }

        .tab-item {
            font-size: 16px;
            font-weight: 600;
            color: #cbd5e1;
            cursor: pointer;
            padding-bottom: 8px;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .tab-item.active {
            color: #f97316;
            border-bottom-color: #f97316;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #f97316;
            margin-bottom: 16px;
        }

        .empty-state {
            text-align: center;
            padding: 30px 20px;
            color: #9ca3af;
            font-size: 14px;
        }
    </style>
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/../../../admin/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Proyecciones</h1>
            <p>Gestiona las proyecciones de películas.</p>
        </div>
        <a href="form.php" class="btn btn-primary">+ Añadir</a>
    </div>

    <!-- EN CARTELERA -->
    <div class="tabs-separator">
        <div class="tab-item active" onclick="mostrarTab('cartelera')">En Cartelera</div>
        <div class="tab-item" onclick="mostrarTab('proximamente')">Próximamente</div>
    </div>

    <div id="cartelera" class="tab-content active">
        <div class="admin-glass-card p-4 mb-4">

            <?php if (empty($peliculasCartelera)): ?>
                <div class="empty-state">No hay películas en cartelera.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($peliculasCartelera as $p): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="pelicula-card">
                                <img src="../../../assets/img/posters/<?= htmlspecialchars($p['poster']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="pelicula-poster" onerror="this.src='../assets/img/posters/placeholder.jpg'">
                                <div class="pelicula-info">
                                    <div class="pelicula-titulo"><?= htmlspecialchars($p['titulo']) ?></div>
                                    <div class="pelicula-meta"><?= date('d/m/Y', strtotime($p['fecha_estreno'])) ?></div>
                                    <span class="proyecciones-count"><?= (int)$p['total_proyecciones'] ?> proyecciones</span>
                                    <a href="form.php?pelicula_id=<?= (int)$p['id'] ?>" class="btn-editar">Editar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- PRÓXIMAMENTE -->
    <div id="proximamente" class="tab-content">
        <div class="admin-glass-card p-4">

            <?php if (empty($peliculasProximamente)): ?>
                <div class="empty-state">No hay películas próximamente.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($peliculasProximamente as $p): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="pelicula-card">
                                <img src="../../../assets/img/posters/<?= htmlspecialchars($p['poster']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="pelicula-poster" onerror="this.src='../assets/img/posters/placeholder.jpg'">
                                <div class="pelicula-info">
                                    <div class="pelicula-titulo"><?= htmlspecialchars($p['titulo']) ?></div>
                                    <div class="pelicula-meta"><?= date('d/m/Y', strtotime($p['fecha_estreno'])) ?></div>
                                    <span class="proyecciones-count"><?= (int)$p['total_proyecciones'] ?> proyecciones</span>
                                    <a href="form.php?pelicula_id=<?= (int)$p['id'] ?>" class="btn-editar">Editar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function mostrarTab(tabName) {
    // Ocultar todos los tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Desactivar todos los items
    const items = document.querySelectorAll('.tab-item');
    items.forEach(item => item.classList.remove('active'));
    
    // Mostrar el tab seleccionado
    document.getElementById(tabName).classList.add('active');
    
    // Activar el item seleccionado
    event.target.classList.add('active');
}
</script>

</body>
</html>






