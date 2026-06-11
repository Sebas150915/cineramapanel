<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");
date_default_timezone_set('America/Lima');

require_once("../../config/config2.php"); // $db
session_start();

try {

    // 🔥 1. CAPTURAR JSON
    $inputJSON = file_get_contents("php://input");
    $data = json_decode($inputJSON, true);

    if (!$data) {
        throw new Exception("JSON inválido");
    }

    // 🔥 2. VARIABLES
    $idempresa = $_SESSION['id_empresa'] ?? $data['idempresa'] ?? 1;
    $local     = $data['local'] ?? $data['id_local'] ?? 1;

    $fecha_emision = $data['fechaemision'] ?? date('Y-m-d');
    $hora_emision  = $data['horaemision'] ?? date('H:i:s');

    // 🔥 3. INICIAR TRANSACCIÓN
    $db->beginTransaction();

    // 🔥 4. OBTENER ORIGEN
    $stmt = $db->prepare("
        SELECT origen, origen_pago 
        FROM tbl_origen_local
        WHERE idempresa = ? AND local = ? AND estado = 1
    ");
    $stmt->execute([$idempresa, $local]);
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$config) {
        throw new Exception("No existe configuración de origen");
    }

    $origen       = $config['origen'];
    $origen_pago  = $config['origen_pago'];

    // 🔥 5. GENERAR VOUCHER VENTA
    $anio = date('Y', strtotime($fecha_emision));
    $mes  = date('m', strtotime($fecha_emision));

    $stmt = $db->prepare("
        SELECT ultimo_numero 
        FROM tbl_correlativos
        WHERE idempresa=? AND local=? AND origen=? AND anio=? AND mes=?
        FOR UPDATE
    ");
    $stmt->execute([$idempresa, $local, $origen, $anio, $mes]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $db->prepare("
            INSERT INTO tbl_correlativos 
            (idempresa, local, origen, anio, mes, ultimo_numero)
            VALUES (?, ?, ?, ?, ?, 0)
        ")->execute([$idempresa, $local, $origen, $anio, $mes]);

        $voucher = 1;
    } else {
        $voucher = $row['ultimo_numero'] + 1;
    }

    $db->prepare("
        UPDATE tbl_correlativos 
        SET ultimo_numero = ?
        WHERE idempresa=? AND local=? AND origen=? AND anio=? AND mes=?
    ")->execute([$voucher, $idempresa, $local, $origen, $anio, $mes]);

    // 🔥 6. INSERT CABECERA
    $stmt = $db->prepare("
        INSERT INTO tbl_venta_cab (
            idempresa, local, origen, voucher,
            tipocomp, serie, correlativo,
            fecha_emision, fecha_vencimiento, hora_emision,
            tc, orden_compra, cotizacion, guia_remision,
            condicion_venta, cuotas_credito, dias,
            codmoneda,
            op_gravadas, op_exoneradas, op_inafectas,
            igv, total, redondeo,
            idcliente,
            vendedor,
            obs,
            iepnd,
            codfuncion, codpelicula, codsala, fechafuncion,
            conceptoventa, rentavariable
        ) VALUES (
            ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?,
            ?,
            ?, ?, ?,
            ?, ?, ?,
            ?,
            ?,
            ?,
            ?,
            ?, ?, ?, ?,
            ?, ?
        )
    ");

    $stmt->execute([
        $idempresa,
        $local,
        $origen,
        $voucher,
        $data['tipocomp'],
        $data['serie'],
        $data['correlativo'],
        $fecha_emision,
        $data['fechavencimiento'],
        $hora_emision,
        $data['tc'],
        $data['ordencompra'],
        $data['cotizacion'],
        $data['guiaremision'],
        $data['condicionventa'],
        $data['cuotas'],
        $data['dias'],
        $data['moneda'],
        $data['op_gravadas'],
        $data['op_exoneradas'],
        $data['op_inafectas'],
        $data['igv'],
        $data['total'],
        $data['redondeo'],
        $data['idcliente'],
        $data['vendedor'],
        $data['obs'],
        $data['iepnd'],
        $data['codfuncion'],
        $data['codpelicula'],
        $data['codsala'],
        $data['fechafuncion'],
        $data['conceptoventa'],
        $data['rentavariable']
    ]);

    $idventa = $db->lastInsertId();

    // 🔥 7. INSERT DETALLE
    foreach ($data['detalle'] as $row) {

        $stmtDet = $db->prepare("
            INSERT INTO tbl_venta_det (
                idempresa, idlocal, idventa,
                item, idproducto,
                nombre_producto, descripcion_producto,
                cantidad_factor, factor, cantidad_unitario, cantidad,
                valor_unitario, precio_unitario,
                igv, porcentaje_igv,
                valor_total, importe_total,
                costo, mxmn, vencimiento,
                precio_base, codtarifa, iepnd
            ) VALUES (
                ?, ?, ?,
                ?, ?,
                ?, ?,
                ?, ?, ?, ?,
                ?, ?,
                ?, ?,
                ?, ?,
                ?, ?, ?,
                ?, ?, ?
            )
        ");

        $stmtDet->execute([
            $idempresa,
            $local,
            $idventa,
            $row['item'],
            $row['idproducto'],
            $row['nombre_producto'],
            $row['descripcion_producto'],
            $row['cantidad_factor'],
            $row['factor'],
            $row['cantidad_unitario'],
            $row['cantidad'],
            $row['valor_unitario'],
            $row['precio_unitario'],
            $row['igv'],
            $row['porcentaje_igv'],
            $row['valor_total'],
            $row['importe_total'],
            $row['costo'],
            $row['mxmn'],
            $row['vencimiento'],
            $row['precio_base'],
            $row['codtarifa'],
            $row['iepnd']
        ]);
    }

    // 🔥 8. PAGOS
    foreach ($data['pagos'] as $pago) {

        $stmt = $db->prepare("
            SELECT ultimo_numero 
            FROM tbl_correlativos
            WHERE idempresa=? AND local=? AND origen=? AND anio=? AND mes=?
            FOR UPDATE
        ");
        $stmt->execute([$idempresa, $local, $origen_pago, $anio, $mes]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $db->prepare("
                INSERT INTO tbl_correlativos 
                (idempresa, local, origen, anio, mes, ultimo_numero)
                VALUES (?, ?, ?, ?, ?, 0)
            ")->execute([$idempresa, $local, $origen_pago, $anio, $mes]);

            $voucher_pago = 1;
        } else {
            $voucher_pago = $row['ultimo_numero'] + 1;
        }

        $db->prepare("
            UPDATE tbl_correlativos 
            SET ultimo_numero = ?
            WHERE idempresa=? AND local=? AND origen=? AND anio=? AND mes=?
        ")->execute([$voucher_pago, $idempresa, $local, $origen_pago, $anio, $mes]);

        $stmtPago = $db->prepare("
            INSERT INTO tbl_venta_pago (
                idempresa, local, idventa,
                idtipopago, descripcion,
                monto, moneda, tc,
                origen, voucher,
                fecha, observacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtPago->execute([
            $idempresa,
            $local,
            $idventa,
            $pago['idtipopago'],
            $pago['descripcion'],
            $pago['monto'],
            $pago['moneda'],
            $pago['tc'],
            $origen_pago,
            $voucher_pago,
            $pago['fecha'],
            $pago['observacion']
        ]);
    }

    // 🔥 FINAL
    $db->commit();

    echo json_encode([
        "ok" => true,
        "idventa" => $idventa,
        "voucher" => $voucher,
        "origen" => $origen
    ]);

} catch (Exception $e) {

    $db->rollBack();

    echo json_encode([
        "ok" => false,
        "error" => $e->getMessage()
    ]);
}