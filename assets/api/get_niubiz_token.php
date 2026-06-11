<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header('Content-Type: application/json');
date_default_timezone_set('America/Lima');

require_once("../../config/config2.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // 1. Obtener ID de venta
    $id_venta = isset($_POST['id_venta']) ? (int)$_POST['id_venta'] : 0;
    
    if ($id_venta <= 0) {
        throw new Exception('ID Venta inválido');
    }

    // 2. Verificar la venta
    $stmtCheck = $db->prepare("SELECT * FROM tbl_ventas WHERE id = ? AND estado = 'PENDIENTE'");
    $stmtCheck->execute([$id_venta]);
    $sale = $stmtCheck->fetch();

    if (!$sale) {
        throw new Exception("La orden no es válida o ya expiró.");
    }

    // 3. Guardar datos del cliente y actualizar tarifas
    $cliente_nombre = isset($_POST['cliente_nombre']) ? sanitize($_POST['cliente_nombre']) : '';
    $cliente_doc = isset($_POST['cliente_doc']) ? sanitize($_POST['cliente_doc']) : '';
    $tipo_comprobante = isset($_POST['tipo_comprobante']) ? sanitize($_POST['tipo_comprobante']) : 'BOLETA';
    
    // Calcular total basado en tarifas seleccionadas
    $total = 0;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'tarifa_') === 0 && !empty($value)) {
            $id_boleto = (int)str_replace('tarifa_', '', $key);
            $id_tarifa = (int)$value;
            
            $stmtTarifa = $db->prepare("SELECT precio FROM tbl_tarifa WHERE id = ?");
            $stmtTarifa->execute([$id_tarifa]);
            $precio = $stmtTarifa->fetchColumn();
            
            if ($precio === false) {
                throw new Exception("Tarifa inválida ID: $id_tarifa");
            }
            
            $total += $precio;
            
            $stmtUpdateBoleto = $db->prepare("UPDATE tbl_boletos SET id_tarifa = ?, precio = ? WHERE id = ? AND id_venta = ?");
            $stmtUpdateBoleto->execute([$id_tarifa, $precio, $id_boleto, $id_venta]);
        }
    }
    
    if ($total <= 0) {
        throw new Exception("El monto total debe ser mayor a 0");
    }

    // 4. Actualizar venta
    $stmtUpdateVenta = $db->prepare("
        UPDATE tbl_ventas 
        SET cliente_nombre = ?, 
            cliente_doc = ?, 
            tipo_comprobante = ?,
            total = ?
        WHERE id = ?
    ");
    $stmtUpdateVenta->execute([$cliente_nombre, $cliente_doc, $tipo_comprobante, $total, $id_venta]);

    // 5. Generar purchase number único
    $purchaseNumber = $id_venta . '-' . time();

    // 6. Devolver los datos para el checkout (sin llamar a Niubiz aquí)
    // Los datos de Niubiz los obtendrá tu servidor local
    echo json_encode([
        'success' => true,
        'id_venta' => $id_venta,
        'amount' => $total,
        'purchaseNumber' => $purchaseNumber,
        'cliente_nombre' => $cliente_nombre,
        'cliente_doc' => $cliente_doc,
        'tipo_comprobante' => $tipo_comprobante,
        'cliente_email' => $_POST['cliente_email'] ?? '',
        'cliente_celular' => $_POST['cliente_celular'] ?? '',
        'tokenid' => $_POST['tokenid'] ?? '',
        'id_funcion' => $_POST['id_funcion'] ?? 0
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
