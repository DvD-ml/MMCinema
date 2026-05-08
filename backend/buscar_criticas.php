<?php
require_once __DIR__ . "/../config/conexion.php";

header('Content-Type: application/json; charset=utf-8');

$tipo = $_GET['tipo'] ?? 'peliculas';
$buscar = trim((string)($_GET['buscar'] ?? ''));
$filtro = $_GET['filtro'] ?? 'todo';
$limite = 12;

if (!in_array($tipo, ['peliculas', 'series'], true)) {
    $tipo = 'peliculas';
}

if (!in_array($filtro, ['todo', 'obra', 'usuario'], true)) {
    $filtro = 'todo';
}

if ($buscar === '') {
    echo json_encode([
        'ok' => true,
        'tipo' => $tipo,
        'buscar' => '',
        'total' => 0,
        'resultados' => [],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $like = '%' . $buscar . '%';

    if ($tipo === 'series') {
        $where = match ($filtro) {
            'obra' => 's.titulo LIKE ?',
            'usuario' => 'u.username LIKE ?',
            default => 's.titulo LIKE ? OR u.username LIKE ?',
        };

        $sql = "
            SELECT
                cs.id,
                cs.contenido,
                cs.puntuacion,
                cs.creado,
                u.username,
                s.titulo,
                s.poster,
                s.id AS obra_id
            FROM critica_serie cs
            LEFT JOIN usuario u ON cs.id_usuario = u.id
            LEFT JOIN serie s ON cs.id_serie = s.id
            WHERE {$where}
            ORDER BY
                CASE
                    WHEN s.titulo LIKE ? THEN 0
                    WHEN u.username LIKE ? THEN 1
                    ELSE 2
                END,
                cs.creado DESC
            LIMIT {$limite}
        ";
        $stmt = $pdo->prepare($sql);
        $params = $filtro === 'todo' ? [$like, $like, $like, $like] : [$like, $like, $like];
        $stmt->execute($params);
    } else {
        $where = match ($filtro) {
            'obra' => 'p.titulo LIKE ?',
            'usuario' => 'u.username LIKE ?',
            default => 'p.titulo LIKE ? OR u.username LIKE ?',
        };

        $sql = "
            SELECT
                c.id,
                c.contenido,
                c.puntuacion,
                c.creado,
                u.username,
                p.titulo,
                p.poster,
                p.id AS obra_id
            FROM critica c
            LEFT JOIN usuario u ON c.id_usuario = u.id
            LEFT JOIN pelicula p ON c.id_pelicula = p.id
            WHERE {$where}
            ORDER BY
                CASE
                    WHEN p.titulo LIKE ? THEN 0
                    WHEN u.username LIKE ? THEN 1
                    ELSE 2
                END,
                c.creado DESC
            LIMIT {$limite}
        ";
        $stmt = $pdo->prepare($sql);
        $params = $filtro === 'todo' ? [$like, $like, $like, $like] : [$like, $like, $like];
        $stmt->execute($params);
    }

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'ok' => true,
        'tipo' => $tipo,
        'filtro' => $filtro,
        'buscar' => $buscar,
        'total' => count($resultados),
        'resultados' => $resultados,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log("Error en buscar_criticas.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'No se pudieron cargar las criticas.',
    ], JSON_UNESCAPED_UNICODE);
}
