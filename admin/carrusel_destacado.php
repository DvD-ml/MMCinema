<?php
session_start();
require_once "../config/conexion.php";
require_once "../includes/optimizar_imagen.php";
require_once "auth.php";

// Verificar autenticación
verificarAuth();

$mensaje = '';
$tipo_mensaje = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    switch ($accion) {
        case 'crear':
            $resultado = crearSlide($_POST, $_FILES);
            $mensaje = $resultado['mensaje'];
            $tipo_mensaje = $resultado['tipo'];
            break;
            
        case 'actualizar':
            $resultado = actualizarSlide($_POST, $_FILES);
            $mensaje = $resultado['mensaje'];
            $tipo_mensaje = $resultado['tipo'];
            break;
            
        case 'eliminar':
            $resultado = eliminarSlide($_POST['id']);
            $mensaje = $resultado['mensaje'];
            $tipo_mensaje = $resultado['tipo'];
            break;
            
        case 'toggle_activo':
            $resultado = toggleActivo($_POST['id']);
            $mensaje = $resultado['mensaje'];
            $tipo_mensaje = $resultado['tipo'];
            break;
    }
}

// Obtener slides del carrusel
$sqlSlides = "
    SELECT 
        c.*,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.titulo
            WHEN c.tipo = 'serie' THEN s.titulo
        END as titulo_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.poster
            WHEN c.tipo = 'serie' THEN s.poster
        END as poster_contenido
    FROM carrusel_destacado c
    LEFT JOIN pelicula p ON c.tipo = 'pelicula' AND c.id_contenido = p.id
    LEFT JOIN serie s ON c.tipo = 'serie' AND c.id_contenido = s.id
    ORDER BY c.orden ASC, c.id DESC
";
$stmSlides = $pdo->prepare($sqlSlides);
$stmSlides->execute();
$slides = $stmSlides->fetchAll(PDO::FETCH_ASSOC);

// Obtener películas y series para los selects
$peliculas = $pdo->query("SELECT id, titulo FROM pelicula ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);
$series = $pdo->query("SELECT id, titulo FROM serie ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);

// Funciones auxiliares
function crearSlide($datos, $archivos) {
    global $pdo;
    
    try {
        // Validar datos requeridos
        if (empty($datos['titulo']) || empty($datos['tipo']) || empty($datos['id_contenido'])) {
            return ['mensaje' => 'Faltan datos requeridos', 'tipo' => 'error'];
        }
        
        // Procesar imagen de fondo
        $imagen_fondo = '';
        if (isset($archivos['imagen_fondo']) && $archivos['imagen_fondo']['error'] === UPLOAD_ERR_OK) {
            try {
                $imagen_fondo = optimizarYGuardarWebp(
                    $archivos['imagen_fondo'],
__DIR__ . '/../assets/img/carrusel',

                    'carrusel-' . mm_slug_nombre_archivo($datos['titulo']),
                    90, // Calidad muy alta para fondos (balance perfecto)
                    1920, // Ancho máximo para fondos
                    1080  // Alto máximo para fondos
                );
            } catch (Exception $e) {
                return ['mensaje' => 'Error al procesar imagen de fondo: ' . $e->getMessage(), 'tipo' => 'error'];
            }
        } else {
            return ['mensaje' => 'La imagen de fondo es requerida', 'tipo' => 'error'];
        }
        
        // Procesar logo (opcional)
        $logo_titulo = null;
        if (isset($archivos['logo_titulo']) && $archivos['logo_titulo']['error'] === UPLOAD_ERR_OK) {
            try {
                $logo_titulo = optimizarYGuardarWebp(
                    $archivos['logo_titulo'],
__DIR__ . '/../assets/img/logos',

                    'logo-' . mm_slug_nombre_archivo($datos['titulo']),
                    90, // Calidad muy alta para logos
                    800, // Ancho máximo para logos
                    300  // Alto máximo para logos
                );
            } catch (Exception $e) {
                return ['mensaje' => 'Error al procesar logo: ' . $e->getMessage(), 'tipo' => 'error'];
            }
        }
        
        // Insertar en BD
        $sql = "INSERT INTO carrusel_destacado (titulo, tipo, id_contenido, imagen_fondo, imagen_posicion, logo_titulo, categoria, descripcion, activo, orden) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $datos['titulo'],
            $datos['tipo'],
            $datos['id_contenido'],
            $imagen_fondo,
            $datos['imagen_posicion'] ?? 'center',
            $logo_titulo,
            $datos['categoria'] ?? 'destacada',
            $datos['descripcion'] ?? '',
            isset($datos['activo']) ? 1 : 0,
            $datos['orden'] ?? 0
        ]);
        
        return ['mensaje' => 'Slide creado exitosamente', 'tipo' => 'success'];
        
    } catch (Exception $e) {
        return ['mensaje' => 'Error al crear slide: ' . $e->getMessage(), 'tipo' => 'error'];
    }
}

function actualizarSlide($datos, $archivos) {
    global $pdo;
    
    try {
        $id = $datos['id'];
        
        // Debug: Mostrar todos los datos recibidos
        error_log("=== ACTUALIZAR SLIDE ID: {$id} ===");
        error_log("Datos POST recibidos: " . print_r($datos, true));
        error_log("Categoría recibida: " . ($datos['categoria'] ?? 'NO DEFINIDA'));
        
        // Obtener slide actual
        $stmt = $pdo->prepare("SELECT * FROM carrusel_destacado WHERE id = ?");
        $stmt->execute([$id]);
        $slide_actual = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$slide_actual) {
            return ['mensaje' => 'Slide no encontrado', 'tipo' => 'error'];
        }
        
        error_log("Categoría anterior en BD: " . $slide_actual['categoria']);
        
        // Procesar nueva imagen de fondo si se subió
        $imagen_fondo = $slide_actual['imagen_fondo'];
        if (isset($archivos['imagen_fondo']) && $archivos['imagen_fondo']['error'] === UPLOAD_ERR_OK) {
            try {
                $imagen_fondo = optimizarYGuardarWebp(
                    $archivos['imagen_fondo'],
__DIR__ . '/../assets/img/carrusel',

                    'carrusel-' . mm_slug_nombre_archivo($datos['titulo']),
                    90, // Calidad muy alta para fondos (balance perfecto)
                    1920, // Ancho máximo para fondos
                    1080, // Alto máximo para fondos
                    $slide_actual['imagen_fondo'] // Eliminar imagen anterior
                );
            } catch (Exception $e) {
                return ['mensaje' => 'Error al procesar imagen de fondo: ' . $e->getMessage(), 'tipo' => 'error'];
            }
        }
        
        // Procesar nuevo logo si se subió
        $logo_titulo = $slide_actual['logo_titulo'];
        if (isset($archivos['logo_titulo']) && $archivos['logo_titulo']['error'] === UPLOAD_ERR_OK) {
            try {
                $logo_titulo = optimizarYGuardarWebp(
                    $archivos['logo_titulo'],
__DIR__ . '/../assets/img/logos',

                    'logo-' . mm_slug_nombre_archivo($datos['titulo']),
                    90, // Calidad muy alta para logos
                    800, // Ancho máximo para logos
                    300, // Alto máximo para logos
                    $slide_actual['logo_titulo'] // Eliminar logo anterior
                );
            } catch (Exception $e) {
                return ['mensaje' => 'Error al procesar logo: ' . $e->getMessage(), 'tipo' => 'error'];
            }
        }
        
        // Validar que la categoría venga en el POST
        $categoria = isset($datos['categoria']) && !empty($datos['categoria']) ? $datos['categoria'] : 'destacada';
        
        // Debug: Log de la categoría que se va a guardar
        error_log("Categoría que se guardará en BD: {$categoria}");
        
        // Actualizar en BD
        $sql = "UPDATE carrusel_destacado SET titulo = ?, tipo = ?, id_contenido = ?, imagen_fondo = ?, imagen_posicion = ?, logo_titulo = ?, categoria = ?, descripcion = ?, activo = ?, orden = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            $datos['titulo'],
            $datos['tipo'],
            $datos['id_contenido'],
            $imagen_fondo,
            $datos['imagen_posicion'] ?? 'center',
            $logo_titulo,
            $categoria,
            $datos['descripcion'] ?? '',
            isset($datos['activo']) ? 1 : 0,
            $datos['orden'] ?? 0,
            $id
        ]);
        
        error_log("Resultado de la actualización: " . ($resultado ? 'Ú‰XITO' : 'FALLO'));
        
        // Verificar que se guardó correctamente
        $stmt = $pdo->prepare("SELECT categoria FROM carrusel_destacado WHERE id = ?");
        $stmt->execute([$id]);
        $categoria_guardada = $stmt->fetchColumn();
        error_log("Categoría verificada en BD después de guardar: {$categoria_guardada}");
        
        return ['mensaje' => 'Slide actualizado exitosamente', 'tipo' => 'success'];
        
    } catch (Exception $e) {
        error_log("ERROR al actualizar slide: " . $e->getMessage());
        return ['mensaje' => 'Error al actualizar slide: ' . $e->getMessage(), 'tipo' => 'error'];
    }
}

function eliminarSlide($id) {
    global $pdo;
    
    try {
        // Obtener slide para eliminar archivos
        $stmt = $pdo->prepare("SELECT * FROM carrusel_destacado WHERE id = ?");
        $stmt->execute([$id]);
        $slide = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$slide) {
            return ['mensaje' => 'Slide no encontrado', 'tipo' => 'error'];
        }
        
        // Eliminar archivos usando la función del sistema
        if ($slide['imagen_fondo']) {
            mm_borrar_archivo_si_existe(__DIR__ . '/../assets/img/carrusel/' . $slide['imagen_fondo']);
        }
        if ($slide['logo_titulo']) {
            mm_borrar_archivo_si_existe(__DIR__ . '/../assets/img/logos/' . $slide['logo_titulo']);
        }
        
        // Eliminar de BD
        $stmt = $pdo->prepare("DELETE FROM carrusel_destacado WHERE id = ?");
        $stmt->execute([$id]);
        
        return ['mensaje' => 'Slide eliminado exitosamente', 'tipo' => 'success'];
        
    } catch (Exception $e) {
        return ['mensaje' => 'Error al eliminar slide: ' . $e->getMessage(), 'tipo' => 'error'];
    }
}

function toggleActivo($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE carrusel_destacado SET activo = NOT activo WHERE id = ?");
        $stmt->execute([$id]);
        
        return ['mensaje' => 'Estado actualizado exitosamente', 'tipo' => 'success'];
        
    } catch (Exception $e) {
        return ['mensaje' => 'Error al cambiar estado: ' . $e->getMessage(), 'tipo' => 'error'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión Carrusel Destacado - Admin MMCinema</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/styles.css">

    <style>
        /* Vista previa de slides en la tabla */
        .slide-preview {
            position: relative;
            width: 120px;
            height: 68px;
            border-radius: 8px;
            overflow: hidden;
            background: #000;
        }
        
        .slide-preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .slide-preview-logo {
            position: absolute;
            bottom: 4px;
            left: 4px;
            max-width: 60px;
            max-height: 24px;
            object-fit: contain;
        }
        
        /* Modal oscuro */
        .modal-content {
            background: rgba(15, 23, 42, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #f97316;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.25);
        }
        
        .form-text {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .btn-close {
            filter: invert(1);
        }
    </style>
</head>
<body class="admin-body">

<?php include "admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Carrusel Destacado</h1>
            <p>Administra las slides que aparecen en el carrusel principal del home</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php" class="btn btn-outline-light">Panel</a>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSlide">
                + Nuevo Slide
            </button>
        </div>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= $tipo_mensaje === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="admin-glass-card p-3 p-lg-4">
        <?php if (empty($slides)): ?>
            <p class="text-center py-5" style="color: rgba(255,255,255,0.6);">
                No hay slides configurados. Crea el primero para comenzar.
            </p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Vista Previa</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($slides as $slide): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary"><?= $slide['orden'] ?></span>
                                </td>
                                <td>
                                    <div class="slide-preview">
                                        <img src="../assets/img/carrusel/<?= htmlspecialchars($slide['imagen_fondo']) ?>" 
                                             alt="<?= htmlspecialchars($slide['titulo']) ?>"
                                             class="slide-preview-img">
                                        <?php if ($slide['logo_titulo']): ?>
                                            <img src="../assets/img/logos/<?= htmlspecialchars($slide['logo_titulo']) ?>" 
                                                 alt="Logo"
                                                 class="slide-preview-logo">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($slide['titulo']) ?></strong>
                                    <br>
                                    <small style="color: rgba(255,255,255,0.6);"><?= htmlspecialchars($slide['titulo_contenido']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= ucfirst($slide['tipo']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark"><?= ucfirst($slide['categoria']) ?></span>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;" onsubmit="return confirmarToggle(this, '<?= htmlspecialchars($slide['titulo']) ?>', <?= $slide['activo'] ? 'true' : 'false' ?>)">
                                        <input type="hidden" name="accion" value="toggle_activo">
                                        <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $slide['activo'] ? 'btn-success' : 'btn-secondary' ?>">
                                            <?= $slide['activo'] ? 'œ“ Activo' : 'œ— Inactivo' ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="admin-actions">
                                        <button type="button" class="btn btn-warning btn-sm" 
                                                onclick="editarSlide(<?= htmlspecialchars(json_encode($slide)) ?>)">
                                            Editar
                                        </button>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('¿Estás seguro de eliminar este slide?')">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Borrar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para crear/editar slide -->
<div class="modal fade" id="modalSlide" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formSlide">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSlideTitle">Nuevo Slide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accion" value="crear">
                    <input type="hidden" name="id" id="slide_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título del Slide *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo *</label>
                                <select class="form-select" id="tipo" name="tipo" required onchange="cargarContenido()">
                                    <option value="">Seleccionar...</option>
                                    <option value="pelicula">Película</option>
                                    <option value="serie">Serie</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="id_contenido" class="form-label">Contenido *</label>
                                <select class="form-select" id="id_contenido" name="id_contenido" required>
                                    <option value="">Seleccionar...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria" name="categoria">
                                    <option value="destacada">Destacada</option>
                                    <option value="mejores">Mejores</option>
                                    <option value="proximamente">Próximamente</option>
                                    <option value="nueva_temporada">Nueva Temporada</option>
                                    <option value="nuevo_episodio">Nuevo Episodio</option>
                                    <option value="nuevos">Nuevos</option>
                                    <option value="populares">Populares</option>
                                </select>
                                <div class="form-text">Para "Nueva Temporada" y "Nuevo Episodio" la fecha aparecerá centrada y grande</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="orden" class="form-label">Orden</label>
                                <input type="number" class="form-control" id="orden" name="orden" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
                                    <label class="form-check-label" for="activo">
                                        Activo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="imagen_fondo" class="form-label">Imagen de Fondo *</label>
                                <input type="file" class="form-control" id="imagen_fondo" name="imagen_fondo" accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif">
                                <div class="form-text">Recomendado: 1920x1080px (16:9). Se convertirá automáticamente a WebP</div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="imagen_posicion" name="imagen_posicion" value="center">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="logo_titulo" class="form-label">Logo/Título</label>
                                <input type="file" class="form-control" id="logo_titulo" name="logo_titulo" accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif">
                                <div class="form-text">Opcional: Logo transparente para superponer. Se convertirá automáticamente a WebP</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Datos para los selects
const peliculas = <?= json_encode($peliculas) ?>;
const series = <?= json_encode($series) ?>;

function cargarContenido() {
    const tipo = document.getElementById('tipo').value;
    const selectContenido = document.getElementById('id_contenido');
    
    selectContenido.innerHTML = '<option value="">Seleccionar...</option>';
    
    if (tipo === 'pelicula') {
        peliculas.forEach(pelicula => {
            selectContenido.innerHTML += `<option value="${pelicula.id}">${pelicula.titulo}</option>`;
        });
    } else if (tipo === 'serie') {
        series.forEach(serie => {
            selectContenido.innerHTML += `<option value="${serie.id}">${serie.titulo}</option>`;
        });
    }
}

function confirmarToggle(form, titulo, esActivo) {
    const accion = esActivo ? 'desactivar' : 'activar';
    return confirm(`¿Estás seguro de ${accion} el slide "${titulo}"?`);
}

function editarSlide(slide) {
    console.log('Editando slide:', slide); // Debug
    console.log('Categoría del slide:', slide.categoria); // Debug
    
    document.getElementById('modalSlideTitle').textContent = 'Editar Slide';
    document.getElementById('accion').value = 'actualizar';
    document.getElementById('slide_id').value = slide.id;
    document.getElementById('titulo').value = slide.titulo;
    document.getElementById('tipo').value = slide.tipo;
    document.getElementById('orden').value = slide.orden;
    document.getElementById('descripcion').value = slide.descripcion || '';
    document.getElementById('activo').checked = slide.activo == 1;
    
    // IMPORTANTE: Establecer categoría ANTES de cargar contenido
    const categoriaSelect = document.getElementById('categoria');
    categoriaSelect.value = slide.categoria || 'destacada';
    console.log('Categoría establecida a:', categoriaSelect.value); // Debug
    
    // Cargar contenido después
    cargarContenido();
    
    // Luego establecer el id_contenido con un pequeúo delay
    setTimeout(() => {
        document.getElementById('id_contenido').value = slide.id_contenido;
        console.log('ID contenido establecido a:', slide.id_contenido); // Debug
    }, 100);
    
    // Hacer opcional la imagen de fondo en edición
    document.getElementById('imagen_fondo').required = false;
    
    new bootstrap.Modal(document.getElementById('modalSlide')).show();
}

// Resetear modal al cerrarse
document.getElementById('modalSlide').addEventListener('hidden.bs.modal', function () {
    document.getElementById('formSlide').reset();
    document.getElementById('modalSlideTitle').textContent = 'Nuevo Slide';
    document.getElementById('accion').value = 'crear';
    document.getElementById('slide_id').value = '';
    document.getElementById('imagen_fondo').required = true;
    document.getElementById('id_contenido').innerHTML = '<option value="">Seleccionar...</option>';
    // Resetear categoría a valor por defecto
    document.getElementById('categoria').value = 'destacada';
});

// Debug: Verificar que la categoría se envía en el formulario
document.getElementById('formSlide').addEventListener('submit', function(e) {
    const categoria = document.getElementById('categoria').value;
    const accion = document.getElementById('accion').value;
    
    console.log('=== ENVIANDO FORMULARIO ===');
    console.log('Acción:', accion);
    console.log('Categoría seleccionada:', categoria);
    console.log('Título:', document.getElementById('titulo').value);
    console.log('Todas las opciones del select:');
    
    const selectCategoria = document.getElementById('categoria');
    for (let i = 0; i < selectCategoria.options.length; i++) {
        const opt = selectCategoria.options[i];
        console.log(`  - ${opt.text}: "${opt.value}" ${opt.selected ? '(SELECCIONADA)' : ''}`);
    }
    
    // Verificar que la categoría no esté vacía
    if (!categoria) {
        console.error('¡ADVERTENCIA! La categoría está vacía');
        alert('Error: La categoría está vacía. Por favor selecciona una categoría.');
        e.preventDefault();
        return false;
    }
    
    // Verificar categorías específicas
    if (categoria === 'nueva_temporada' || categoria === 'nuevo_episodio') {
        console.log('œ“ Categoría especial detectada:', categoria);
    }
});
</script>


</body>
</html>


