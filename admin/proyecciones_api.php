<?php
require_once "auth.php";
require_once "../config/conexion.php";

header('Content-Type: application/json');

$pelicula_id = isset($_GET['pelicula_id']) ? (int)$_GET['pelicula_id'] : 0;

if ($pelicula_id <= 0) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT 
        pr.id,
        pr.fecha,
        pr.hora,
        pr.sala,
        COUNT(DISTINCT ta.id) as asientos_vendidos,
        sc.filas * sc.columnas as capacidad_total,
        ROUND((COUNT(DISTINCT ta.id) / (sc.filas * sc.columnas)) * 100) as porcentaje
    FROM proyeccion pr
    JOIN sala_config sc ON pr.sala = sc.sala
    LEFT JOIN ticket_asiento ta ON pr.id = ta.id_proyeccion
    WHERE pr.id_pelicula = ?
    GROUP BY pr.id
    ORDER BY pr.fecha ASC, pr.hora ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$pelicula_id]);
$proyecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($proyecciones);
