<?php
require_once "auth.php";
require_once "../config/conexion.php";

// Obtener películas en cartelera (ya estrenadas)
$sqlCartelera = "
    SELECT 
        p.id,
        p.titulo,
        p.poster,
        p.fecha_estreno,
        COUNT(DISTINCT pr.id) as total_proyecciones,
        SUM(CASE WHEN pr.fecha >= CURDATE() THEN 1 ELSE 0 END) as proyecciones_futuras
    FROM pelicula p
    LEFT JOIN proyeccion pr ON p.id = pr.id_pelicula
    WHERE p.fecha_estreno <= CURDATE()
    GROUP BY p.id
    ORDER BY p.fecha_estreno DESC
";
$peliculasCartelera = $pdo->query($sqlCartelera)->fetchAll(PDO::FETCH_ASSOC);

// Obtener películas próximamente (no estrenadas)
$sqlProximamente = "
    SELECT 
        p.id,
        p.titulo,
        p.poster,
        p.fecha_estreno,
        COUNT(DISTINCT pr.id) as total_proyecciones,
        SUM(CASE WHEN pr.fecha >= CURDATE() THEN 1 ELSE 0 END) as proyecciones_futuras
    FROM pelicula p
    LEFT JOIN proyeccion pr ON p.id = pr.id_pelicula
    WHERE p.fecha_estreno > CURDATE()
    GROUP BY p.id
    ORDER BY p.fecha_estreno ASC
";
$peliculasProximamente = $pdo->query($sqlProximamente)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar proyecciones</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .proyecciones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .pelicula-card {
            background: #1f2937;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .pelicula-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
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
            color: #e5e7eb;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .pelicula-meta {
            font-size: 12px;
            color: #9ca3af;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .proyecciones-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(245, 158, 11, 0.95);
            color: #000;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        .sin-proyecciones {
            opacity: 0.6;
        }
        
        .sin-proyecciones .proyecciones-badge {
            background: rgba(107, 114, 128, 0.95);
        }
        
        /* Modal */
        .modal-proyecciones {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            overflow-y: auto;
            padding: 20px;
        }
        
        .modal-proyecciones.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content-proyecciones {
            background: #111827;
            border-radius: 16px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }
        
        .modal-header-proyecciones {
            display: flex;
            gap: 20px;
            padding: 30px;
            border-bottom: 1px solid #374151;
        }
        
        .modal-poster {
            width: 120px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }
        
        .modal-info {
            flex: 1;
        }
        
        .modal-titulo {
            font-size: 24px;
            font-weight: 700;
            color: #f59e0b;
            margin-bottom: 10px;
        }
        
        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #374151;
            border: none;
            color: #e5e7eb;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: #f59e0b;
            color: #000;
        }
        
        .modal-body-proyecciones {
            padding: 30px;
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .proyecciones-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .proyeccion-item {
            background: #1f2937;
            border-radius: 10px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .proyeccion-detalles {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            flex: 1;
        }
        
        .proyeccion-dato {
            display: flex;
            flex-direction: column;
        }
        
        .proyeccion-label {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .proyeccion-value {
            font-size: 14px;
            color: #e5e7eb;
            font-weight: 600;
        }
        
        .proyeccion-actions {
            display: flex;
            gap: 8px;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #374151;
        }
        
        .section-icon {
            font-size: 28px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #f59e0b;
            margin: 0;
        }
        
        .section-count {
            background: #374151;
            color: #9ca3af;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }
        
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Administrar proyecciones</h1>
            <p>Gestiona las proyecciones de películas por cartelera y próximamente.</p>
        </div>
        <a href="proyeccion_form.php" class="btn btn-primary">+ Añadir proyección</a>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Proyección guardada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Proyección eliminada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- CARTELERA -->
    <div class="admin-glass-card mb-5">
        <div class="section-header">
            <span class="section-icon">🎬</span>
            <h2 class="section-title">En Cartelera</h2>
            <span class="section-count"><?= count($peliculasCartelera) ?> películas</span>
        </div>

        <?php if (empty($peliculasCartelera)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🎬</div>
                <p>No hay películas en cartelera todavía.</p>
            </div>
        <?php else: ?>
            <div class="proyecciones-grid">
                <?php foreach ($peliculasCartelera as $pelicula): ?>
                    <div class="pelicula-card <?= $pelicula['total_proyecciones'] == 0 ? 'sin-proyecciones' : '' ?>" 
                         onclick="abrirModalProyecciones(<?= (int)$pelicula['id'] ?>, '<?= htmlspecialchars($pelicula['titulo'], ENT_QUOTES) ?>', '<?= htmlspecialchars($pelicula['poster']) ?>')">
                        
                        <div class="proyecciones-badge">
                            <?= (int)$pelicula['total_proyecciones'] ?> proyecciones
                        </div>
                        
                        <img src="../assets/img/posters/<?= htmlspecialchars($pelicula['poster']) ?>" 
                             alt="<?= htmlspecialchars($pelicula['titulo']) ?>" 
                             class="pelicula-poster"
                             onerror="this.src='../assets/img/posters/placeholder.jpg'">
                        
                        <div class="pelicula-info">
                            <div class="pelicula-titulo"><?= htmlspecialchars($pelicula['titulo']) ?></div>
                            <div class="pelicula-meta">
                                <span>📅 <?= date('d/m/Y', strtotime($pelicula['fecha_estreno'])) ?></span>
                                <?php if ($pelicula['proyecciones_futuras'] > 0): ?>
                                    <span style="color: #f59e0b;">⏰ <?= (int)$pelicula['proyecciones_futuras'] ?> futuras</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- PRÓXIMAMENTE -->
    <div class="admin-glass-card">
        <div class="section-header">
            <span class="section-icon">🔜</span>
            <h2 class="section-title">Próximamente</h2>
            <span class="section-count"><?= count($peliculasProximamente) ?> películas</span>
        </div>

        <?php if (empty($peliculasProximamente)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🔜</div>
                <p>No hay películas próximamente todavía.</p>
            </div>
        <?php else: ?>
            <div class="proyecciones-grid">
                <?php foreach ($peliculasProximamente as $pelicula): ?>
                    <div class="pelicula-card <?= $pelicula['total_proyecciones'] == 0 ? 'sin-proyecciones' : '' ?>" 
                         onclick="abrirModalProyecciones(<?= (int)$pelicula['id'] ?>, '<?= htmlspecialchars($pelicula['titulo'], ENT_QUOTES) ?>', '<?= htmlspecialchars($pelicula['poster']) ?>')">
                        
                        <div class="proyecciones-badge">
                            <?= (int)$pelicula['total_proyecciones'] ?> proyecciones
                        </div>
                        
                        <img src="../assets/img/posters/<?= htmlspecialchars($pelicula['poster']) ?>" 
                             alt="<?= htmlspecialchars($pelicula['titulo']) ?>" 
                             class="pelicula-poster"
                             onerror="this.src='../assets/img/posters/placeholder.jpg'">
                        
                        <div class="pelicula-info">
                            <div class="pelicula-titulo"><?= htmlspecialchars($pelicula['titulo']) ?></div>
                            <div class="pelicula-meta">
                                <span>📅 <?= date('d/m/Y', strtotime($pelicula['fecha_estreno'])) ?></span>
                                <?php if ($pelicula['proyecciones_futuras'] > 0): ?>
                                    <span style="color: #f59e0b;">⏰ <?= (int)$pelicula['proyecciones_futuras'] ?> futuras</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL DE PROYECCIONES -->
<div class="modal-proyecciones" id="modalProyecciones" onclick="cerrarModal(event)">
    <div class="modal-content-proyecciones" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="cerrarModal(event)">×</button>
        
        <div class="modal-header-proyecciones">
            <img id="modalPoster" src="" alt="" class="modal-poster">
            <div class="modal-info">
                <h2 class="modal-titulo" id="modalTitulo"></h2>
                <div class="modal-actions">
                    <a id="btnAñadirProyeccion" href="#" class="btn btn-primary btn-sm">+ Añadir proyección</a>
                    <a id="btnVerPelicula" href="#" class="btn btn-secondary btn-sm" target="_blank">👁️ Ver película</a>
                </div>
            </div>
        </div>
        
        <div class="modal-body-proyecciones">
            <h3 style="color: #e5e7eb; margin-bottom: 20px; font-size: 18px;">Proyecciones programadas</h3>
            <div id="proyeccionesList" class="proyecciones-list">
                <!-- Se llenará con JavaScript -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let peliculaIdActual = null;

function abrirModalProyecciones(peliculaId, titulo, poster) {
    peliculaIdActual = peliculaId;
    
    document.getElementById('modalTitulo').textContent = titulo;
    document.getElementById('modalPoster').src = '../assets/img/posters/' + poster;
    document.getElementById('btnAñadirProyeccion').href = 'proyeccion_form.php?pelicula_id=' + peliculaId;
    document.getElementById('btnVerPelicula').href = '../pages/pelicula.php?id=' + peliculaId;
    
    // Cargar proyecciones
    fetch('proyecciones_api.php?pelicula_id=' + peliculaId)
        .then(response => response.json())
        .then(data => {
            const lista = document.getElementById('proyeccionesList');
            
            if (data.length === 0) {
                lista.innerHTML = '<div style="text-align: center; padding: 40px; color: #9ca3af;">No hay proyecciones programadas para esta película.</div>';
            } else {
                lista.innerHTML = data.map(proy => `
                    <div class="proyeccion-item">
                        <div class="proyeccion-detalles">
                            <div class="proyeccion-dato">
                                <span class="proyeccion-label">Fecha</span>
                                <span class="proyeccion-value">${formatearFecha(proy.fecha)}</span>
                            </div>
                            <div class="proyeccion-dato">
                                <span class="proyeccion-label">Hora</span>
                                <span class="proyeccion-value">${proy.hora.substring(0, 5)}</span>
                            </div>
                            <div class="proyeccion-dato">
                                <span class="proyeccion-label">Sala</span>
                                <span class="proyeccion-value">${proy.sala}</span>
                            </div>
                            <div class="proyeccion-dato">
                                <span class="proyeccion-label">Ocupación</span>
                                <span class="proyeccion-value">${proy.asientos_vendidos}/${proy.capacidad_total} (${proy.porcentaje}%)</span>
                            </div>
                        </div>
                        <div class="proyeccion-actions">
                            <a href="proyeccion_form.php?id=${proy.id}" class="btn btn-warning btn-sm">Editar</a>
                            <a href="proyeccion_borrar.php?id=${proy.id}" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta proyección?')">Eliminar</a>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('proyeccionesList').innerHTML = '<div style="text-align: center; padding: 40px; color: #ef4444;">Error al cargar las proyecciones.</div>';
        });
    
    document.getElementById('modalProyecciones').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function cerrarModal(event) {
    document.getElementById('modalProyecciones').classList.remove('active');
    document.body.style.overflow = '';
}

function formatearFecha(fecha) {
    const date = new Date(fecha + 'T00:00:00');
    const dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    
    return `${dias[date.getDay()]} ${date.getDate()} ${meses[date.getMonth()]}`;
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal(e);
    }
});
</script>

</body>
</html>
