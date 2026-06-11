<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
date_default_timezone_set('America/Lima');

require_once("../../config/config2.php");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$id_venta = isset($_GET['id_venta']) ? (int)$_GET['id_venta'] : 0;

if ($id_venta <= 0) {
    echo json_encode(['error' => 'ID Venta inválido']);
    exit;
}

try {
    // Obtener datos de la venta
    $stmt = $db->prepare("
        SELECT v.*, 
               f.id as funcion_id,
               f.fecha as funcion_fecha,
               f.hora as funcion_hora,
               p.nombre as pelicula_nombre
        FROM tbl_ventas v
        LEFT JOIN tbl_boletos b ON b.id_venta = v.id
        LEFT JOIN tbl_funciones f ON b.id_funcion = f.id
        LEFT JOIN tbl_peliculas p ON f.id_pelicula = p.id
        WHERE v.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id_venta]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$venta) {
        echo json_encode(['error' => 'Venta no encontrada']);
        exit;
    }
    
    // Obtener boletos
    $stmtBoletos = $db->prepare("
        SELECT b.*, t.nombre as tarifa_nombre
        FROM tbl_boletos b
        LEFT JOIN tbl_tarifa t ON b.id_tarifa = t.id
        WHERE b.id_venta = ?
    ");
    $stmtBoletos->execute([$id_venta]);
    $boletos = $stmtBoletos->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'venta' => $venta,
        'boletos' => $boletos
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>