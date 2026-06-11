<?php

header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
date_default_timezone_set('America/Lima');


header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

require_once("../../config/config2.php");
//require_once("../../helpers/helpers.php");
//require_once("../../libraries/conexion.php");
session_start();

header('Content-Type: application/json');

$idcine=isset($_POST["idcine"])? ($_POST["idcine"]):"2";
$id_funcion=isset($_POST["id_funcion"])? ($_POST["id_funcion"]):"252";
$id_venta=isset($_POST["id_venta"])? ($_POST["id_venta"]):"0";
$idcategoria=isset($_POST["idcategoria"])? ($_POST["idcategoria"]):"0";

$ncomercial=isset($_POST["ncomercial"])? ($_POST["ncomercial"]):"";
$dir=isset($_POST["dir"])? ($_POST["dir"]):"";
$departamento=isset($_POST["departamento"])? ($_POST["departamento"]):"";
$provincia=isset($_POST["provincia"])? ($_POST["provincia"]):"";
$distrito=isset($_POST["distrito"])? ($_POST["distrito"]):"";
$codpais=isset($_POST["codpais"])? ($_POST["codpais"]):"";
$ubigeo=isset($_POST["ubigeo"])? ($_POST["ubigeo"]):"";
$fono=isset($_POST["fono"])? ($_POST["fono"]):"";
$correo=isset($_POST["correo"])? ($_POST["correo"]):"";
$usuario=isset($_POST["usuario"])? ($_POST["usuario"]):"";
$clave=isset($_POST["clave"])? ($_POST["clave"]):"";
$firma=isset($_POST["firma"])? ($_POST["firma"]):"";

$tipo=isset($_POST["tipo"])? ($_POST["tipo"]):"";

$directo=isset($_POST["directo"])? ($_POST["directo"]):"";
$articulo=isset($_POST["articulo"])? ($_POST["articulo"]):"CINE";

$pie=isset($_POST["pie"])? ($_POST["pie"]):"";
$color=isset($_POST["color"])? ($_POST["color"]):"#008080";
$ose=isset($_POST["ose"])? ($_POST["ose"]):"";
$detraccion=isset($_POST["detraccion"])? ($_POST["detraccion"]):"";

$percepcion12=isset($_POST["percepcion12"])? ($_POST["percepcion12"]):"";
$percepcion40=isset($_POST["percepcion40"])? ($_POST["percepcion40"]):"";
$ctadetraccion=isset($_POST["ctadetraccion"])? ($_POST["ctadetraccion"]):"";
$ctaretencion=isset($_POST["ctaretencion"])? ($_POST["ctaretencion"]):"";


$origencobros=isset($_POST["cobros"])? ($_POST["cobros"]):"";
$origenpagos=isset($_POST["pagos"])? ($_POST["pagos"]):"";
$ccomprassoles=isset($_POST["ccomprassoles"])? ($_POST["ccomprassoles"]):"";
$ccomprasdolares=isset($_POST["ccomprasdolares"])? ($_POST["ccomprasdolares"]):"";

$igv=isset($_POST["igv"])? ($_POST["igv"]):"";
$precio_porcentaje=isset($_POST["precio_porcentaje"])? ($_POST["precio_porcentaje"]):"0";
$porcentajeigv=isset($_POST["porcentajeigv"])? ($_POST["porcentajeigv"]):"18";
$percepcioncompra40=isset($_POST["percepcioncompra40"])? ($_POST["percepcioncompra40"]):"0";

$detraccioncompras=isset($_POST["detraccioncompras"])? ($_POST["detraccioncompras"]):"42123";

$id_local=isset($_POST["id_local"])? ($_POST["id_local"]):"1";
$rucInput=trim((string)($_POST['ruc'] ?? ''));
$user = trim((string)($_POST['usuario'] ?? ''));
$pass = (string)($_POST['clave'] ?? '');

//$id_local
$id_asiento= isset($_POST['id_asiento']) ? $_POST['id_asiento'] : 0;
$tokenid= isset($_POST['tokenid']) ? $_POST['tokenid'] : '0001';

$anio=isset($_POST["anio"])? ($_POST["anio"]):date('Y');
$mes=isset($_POST["mes"])? ($_POST["mes"]):date('m');

$fechahoy= date('Y-m-d');

$anioactual=date('Y');
$mesactual=date('m');

/*INICIO DE VARIABLES DE VENTAS

$articulo = $_POST["articulo"] ?? "CINE";
$articulo = trim($articulo);
*/

// EMPRESA / LOCAL
$idempresa         = $_POST["idempresa"]      ?? '1';
$local             = $_POST["idcine"]  ?? '1';

// CONFIGURACIÓN
$exportacion       = $_POST["exportacion"]      ?? 'NO';
$kardex            = $_POST["kardex"]           ?? 'SI';
$tipomovimiento    = $_POST["tipomovimiento"]   ?? '01';
$tipooperacion     = $_POST["tipooperacion"]    ?? '01';
$origen            = $_POST["origen"]           ?? '50';

// DOCUMENTO
$voucher           = $_POST["voucher"]          ?? 0;
$tipocomp          = $_POST["tipocomp"]         ?? '03';
$serie             = $_POST["serie"]            ?? '';
$correlativo       = $_POST["correlativo"]      ?? 0;

// FECHAS
$fechaemision      = $_POST["fechaemision"]     ?? date('Y-m-d');
$fechavencimiento  = $_POST["fechavencimiento"] ?? $fechaemision;
$horaemision       = $_POST["horaemision"]      ?? date('H:i:s');

// TIPO CAMBIO / REFERENCIAS
$tc                = $_POST["tc"]               ?? 3.00000;
$ordecompra        = $_POST["ordencompra"]      ?? '';
$cotizacion        = $_POST["cotizacion"]       ?? '';
$guiaremision      = $_POST["guiaremision"]     ?? '';

// CONDICIÓN VENTA
$condicionventa    = $_POST["condicionventa"]   ?? '1'; // 1 contado
$cuotas            = $_POST["cuotas"]           ?? 0;
$diascredito       = $_POST["dias"]             ?? 0;

// MONEDA
$moneda            = $_POST["moneda"]           ?? 'PEN';

// TOTALES
$op_gravadas       = $_POST["op_gravadas"]      ?? 0;
$op_exoneradas     = $_POST["op_exoneradas"]    ?? 0;
$op_inafectas      = $_POST["op_inafectas"]     ?? 0;
$igv               = $_POST["igv"]              ?? 0;
$total             = $_POST["total"]            ?? 0;
$redondeo          = $_POST["redondeo"]         ?? 0;

// CLIENTE
$idcliente         = $_POST["idcliente"]        ?? 0;

// OTROS
$vendedor          = $_POST["vendedor"]         ?? 0;
$obs               = $_POST["obs"]              ?? '';

// DETRACCIONES / RETENCIONES
$iepnd             = $_POST["iepnd"]            ?? 0;

// CINE (FUNCION)
$codfuncion        = $_POST["codfuncion"]       ?? 0;
$codpelicula       = $_POST["codpelicula"]      ?? 0;
$codsala           = $_POST["idsala"]           ?? 0;
$fechafuncion      = $_POST["fechafuncion"]     ?? date('Y-m-d');

// CONCEPTO
$conceptoventa     = $_POST["conceptoventa"]    ?? '01';
$rentavariable     = $_POST["rentavariable"]    ?? 'SI';

function getOrigen($connect, $idempresa, $local) {
    $sql = "SELECT origen, origen_pago 
            FROM tbl_origen_local 
            WHERE idempresa = ? AND local = ? AND estado = '1'";

    $stmt = $connect->prepare($sql);
    $stmt->execute([$idempresa, $local]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        throw new Exception("No existe origen configurado para este local");
    }

    return $data;
}

function getNextVoucher($connect, $idempresa, $local, $origen, $fecha) {

    $anio = date('Y', strtotime($fecha));
    $mes  = date('m', strtotime($fecha));

    // 1. Intentar obtener registro
    $sql = "SELECT ultimo_numero 
            FROM tbl_correlativos
            WHERE idempresa = ?
            AND local = ?
            AND origen = ?
            AND anio = ?
            AND mes = ?
            FOR UPDATE";

    $stmt = $connect->prepare($sql);
    $stmt->execute([$idempresa, $local, $origen, $anio, $mes]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // 2. Crear registro si no existe
        $sqlInsert = "INSERT INTO tbl_correlativos 
            (idempresa, local, origen, anio, mes, ultimo_numero)
            VALUES (?, ?, ?, ?, ?, 0)";
        
        $stmtInsert = $connect->prepare($sqlInsert);
        $stmtInsert->execute([$idempresa, $local, $origen, $anio, $mes]);

        $nuevo = 1;

    } else {
        $nuevo = $row['ultimo_numero'] + 1;
    }

    // 3. Actualizar
    $sqlUpdate = "UPDATE tbl_correlativos 
                  SET ultimo_numero = ?
                  WHERE idempresa = ?
                  AND local = ?
                  AND origen = ?
                  AND anio = ?
                  AND mes = ?";

    $stmtUpdate = $connect->prepare($sqlUpdate);
    $stmtUpdate->execute([$nuevo, $idempresa, $local, $origen, $anio, $mes]);

    return $nuevo;
}











switch ($_GET["op"]){




case 'iniciarsesion':





$err = '';
$userInput = '';
$debug = '';

  $userInput = $user;

  $rucClean = preg_replace('/\D+/', '', $rucInput);
  
  
  if (!preg_match('/^[0-9]{11}$/', $rucClean) || $user === '' || $pass === '') {
    $err = 'Ingrese RUC, usuario y clave';
  } else {
  	
//echo $rucClean.'|';
  	
$data[]=array();
  
  
      $logged = false;

        $stmt = $db->prepare("SELECT * FROM tbl_usuarios WHERE usuario ='$user' AND estado = '1' LIMIT 1");
        $stmt->execute();
        $u = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        $debugParts[] = 'tbl_user=' . ($u ? '1' : '0');

        if ($u) {
          $idLocal = (int)($u['id_local'] ?? 0);
          $localRuc = $u['id_local'];

          $empresaId = 0;

              $s = $db->prepare("SELECT empresa AS id_empresa FROM tbl_locales WHERE id = ? LIMIT 1");
              $s->execute([$idLocal]);
              $empresaId = (int)($s->fetchColumn() ?: 0);

          $rucOk = true;

//echo $rucOk;

          if ($rucOk) {
            $_SESSION['iniciarSesion'] = 'poscaja';
            $_SESSION['idusuario'] = (int)($u['id'] ?? 0);
            $_SESSION['usuario'] = (string)($u['usuario'] ?? '');
            $_SESSION['usuario_nombre'] = (string)($u['nombre'] ?? '');
            $_SESSION['local_id'] = $idLocal > 0 ? $idLocal : 1;
            $_SESSION['idempresa'] = $empresaId > 0 ? $empresaId : 1;
            $_SESSION['empresa_ruc'] = $rucClean;
            $logged = true;
            //header('Location: ' . $url . 'pos');
            
            $u['idempresa']=$empresaId;
            $u['local_id']=$idLocal;
            $u['empresa_ruc']=$rucClean;
           echo json_encode($u);
          exit;
          }
        }


      $empresaIdFromRuc = pos_find_empresa_id_by_ruc($db, $rucClean);

      $row = null;
      $stmt = $db->prepare("SELECT idusuario, idempresa, idlocal, nombre, login, clave, condicion FROM usuario WHERE login = ? LIMIT 1");
      $stmt->execute([$user]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
      $debugParts[] = 'usuario=' . ($row ? '1' : '0');

      $ok = false;
      if ($row && isset($row['clave'])) {
        $ok = pos_verify_pass($pass, (string)$row['clave']);
      }


      $rucOk = true;


      if (!$row || !$ok || !$rucOk) {
        $err = 'RUC/usuario/clave incorrectos';
      } elseif (isset($row['condicion']) && (int)$row['condicion'] !== 1) {
        $err = 'Usuario desactivado';
      } else {
        $_SESSION['iniciarSesion'] = 'poscaja';
        $_SESSION['idusuario'] = (int)$row['idusuario'];
        $_SESSION['usuario'] = (string)$row['login'];
        $_SESSION['usuario_nombre'] = (string)($row['nombre'] ?? '');
        $_SESSION['idempresa'] = (int)($row['idempresa'] ?? 1);
        $_SESSION['local_id'] = (int)($row['idlocal'] ?? 1);
        $_SESSION['empresa_ruc'] = $rucClean;
        header('Location: ' . $url . 'pos');
        exit;
      }
      if ($isLocal) $debug = implode(' | ', $debugParts);





  }







break;

case 'chat_users':
$stmt = $db->prepare("SELECT * FROM tbl_usuarios WHERE estado = '1' ORDER BY nombre ASC, usuario ASC");
$stmt->execute();
$rows = $stmt->fetchAll();

$out = [];
foreach ($rows as $r) {
    $uid = (int)($r['id'] ?? 0);
    $online = ((int)($r['chat'] ?? 0) === 1); // sin condición $hasChat
    $out[] = [
        'id' => $uid,
        'usuario' => (string)($r['usuario'] ?? ''),
        'nombre' => (string)($r['nombre'] ?? ''),
        'id_local' => (int)($r['id_local'] ?? 0),
        'online' => $online,
    ];
}

echo json_encode($out);

break;


case 'locales':

$stmtFun = $db->prepare(" SELECT * FROM tbl_locales WHERE estado = '1' ORDER BY orden ASC, nombre ASC ");
    $stmtFun->execute();
    $rows = $stmtFun->fetchAll();

    // Agrupar resultados


foreach ($rows as $row) {
$data[]=array(
'imagen'=>!empty($row['img']) ? ASSETS_URL . 'uploads/cines/' . $row['img'] : 'https://kiosko.smartbase.club/assets/img/cine/pacifico.jpg',
'nombre'=>htmlspecialchars($row['nombre']),
'direccion'=>htmlspecialchars($row['direccion']),
'id'=>$row['id']

);

    }
    

echo json_encode($data);


break;


case 'listarbanner':

$stmtFun = $db->prepare(" SELECT * FROM tbl_slider WHERE estado = '1' ORDER BY orden ASC ");
    $stmtFun->execute();
    $rows = $stmtFun->fetchAll();

    // Agrupar resultados

//!empty($slide['link']) ? htmlspecialchars($slide['link']) : 'cartelera.php'; 

foreach ($rows as $row) {
$data[]=array(
'imagen'=>!empty($row['img']) ? ASSETS_URL . 'uploads/sliders/' . $row['img']: 'https://kiosko.smartbase.club/assets/img/cine/pacifico.jpg',
'titulo'=>htmlspecialchars($rows['titulo']),
'link'=>!empty($slide['link']) ? htmlspecialchars($slide['link']) : 'cartelera.php',
'img'=>$row['img'],


//'id'=>$row['id']

);

    }
    



echo json_encode($data);


break;

case 'listarusuarios':

$stmtFun = $db->prepare(" SELECT * FROM tbl_usuarios  ");
    $stmtFun->execute();
    $rows = $stmtFun->fetchAll();

    // Agrupar resultados

//!empty($slide['link']) ? htmlspecialchars($slide['link']) : 'cartelera.php'; 

foreach ($rows as $row) {
$data[]=array(
'usuario'=>htmlspecialchars($rows['usuario']),
'nombre'=>$row['nombre'],
'id_local'=>$row['id_local'],
'id'=>$row['id']

);

    }
    



echo json_encode($data);


break;


case 'listarcartelera':
		
//$idcine='1';

//echo $idcine.'|';


// Obtener info del cine
try {
    $stmt = $db->prepare("SELECT * FROM tbl_locales WHERE id = ? AND estado = '1'");
    $stmt->execute([$idcine]);
    $cine = $stmt->fetch();

    if (!$cine) {
        die("Cine no encontrado");
    }

    // Obtener funciones detalladas para este cine (Película + Función + Hora)
    $stmtFun = $db->prepare("
        SELECT 
            p.id as peli_id, 
            p.nombre as peli_nombre, 
            p.img as peli_img, 
            p.duracion, 
            p.trailer as peli_trailer, 
            g.nombre as genero_nombre,
            c.codigo as censura_codigo,
            f.id as funcion_id, f.fecha, f.id_sala, 
            h.hora, s.nombre as sala_nombre
        FROM tbl_funciones f
        JOIN tbl_pelicula p ON f.id_pelicula = p.id
        LEFT JOIN tbl_censura c ON p.censura = c.id
        LEFT JOIN tbl_genero g ON p.genero = g.id
        JOIN tbl_hora h ON f.id_hora = h.id
        JOIN tbl_sala s ON f.id_sala = s.id
        WHERE s.local = ?
        AND f.fecha >= CURRENT_DATE
        AND f.estado = '1'
        AND p.estado = '1'
        ORDER BY p.nombre ASC, f.fecha ASC, h.hora ASC
    ");
    $stmtFun->execute([$idcine]);
    $rows = $stmtFun->fetchAll();

    // Agrupar resultados
    $cartelera = [];
    foreach ($rows as $row) {
        $pId = $row['peli_id'];

        if (!isset($cartelera[$pId])) {
            $cartelera[$pId] = [
                'info' => [
                    'id' => $row['peli_id'],
                    'nombre' => $row['peli_nombre'],
                    'duracion' => $row['duracion'],
                    'trailer' => $row['peli_trailer'] ?? '',
                    'genero' => htmlspecialchars($row['genero_nombre']),
                    'censura' => htmlspecialchars($row['censura_codigo']),
                    'img' => !empty($row['peli_img']) ? ASSETS_URL . 'uploads/peliculas/' . $row['peli_img'] : ASSETS_URL . 'uploads/peliculas/images.jpg',
                ],
                'fechas' => []
            ];
        }

        $fecha = $row['fecha'];
        if (!isset($cartelera[$pId]['fechas'][$fecha])) {
            $cartelera[$pId]['fechas'][$fecha] = [];
        }

        $cartelera[$pId]['fechas'][$fecha][] = [
            'id' => $row['funcion_id'],
            'hora' => date('h:i A', strtotime($row['hora'])),
            'sala' => $row['sala_nombre']
        ];
    }
    
    // Preparar la respuesta final incluyendo la información del cine
    $respuesta = [
        'cine' => [
            'id' => $cine['id'],
            'nombre' => $cine['nombre'] ?? '',
            'direccion' => $cine['direccion'] ?? '',
            'telefono' => $cine['telefono'] ?? '',
            'email' => $cine['email'] ?? '',
            'ciudad' => $cine['ciudad'] ?? '',
            'horario_atencion' => $cine['horario_atencion'] ?? '',
            // Agrega aquí cualquier otro campo que tenga tu tabla tbl_locales
        ],
        'cartelera' => $cartelera
    ];
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Codificar todo el array como JSON
echo json_encode($respuesta, JSON_PRETTY_PRINT);


break;




		
case 'motrarasientos':



//$id_funcion = isset($_GET['id_funcion']) ? (int)$_GET['id_funcion'] : 116;

if ($id_funcion <= 0) {
    echo json_encode(['error' => 'ID de función inválido']);
    exit;
}

try {
    // 1. Get Function & Room Info
    $stmt = $db->prepare("
        SELECT f.id, h.hora as hora_inicio, p.nombre as pelicula, s.nombre as sala, 
               s.filas, s.columnas, s.id as id_sala, l.nombre as cine
        FROM tbl_funciones f
        JOIN tbl_pelicula p ON f.id_pelicula = p.id
        JOIN tbl_sala s ON f.id_sala = s.id
        JOIN tbl_locales l ON s.local = l.id
        JOIN tbl_hora h ON f.id_hora = h.id
        WHERE f.id = ? AND f.estado = '1'
    ");
    $stmt->execute([$id_funcion]);
    $funcion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$funcion) {
        echo json_encode(['error' => 'Función no encontrada o inactiva']);
        exit;
    }

    // 2. Get All Seats (Layout)
    $stmtAsientos = $db->prepare("SELECT id, fila, columna, num_asiento, tipo FROM tbl_sala_asiento WHERE idsala = ? ORDER BY fila, columna");
    $stmtAsientos->execute([$funcion['id_sala']]);
    $asientos = $stmtAsientos->fetchAll(PDO::FETCH_ASSOC);


/*
    // 3. Clean up expired pending reservations
    try {
        $connect->exec("DELETE FROM tbl_boletos WHERE id_venta IN (
            SELECT id FROM (SELECT id FROM tbl_ventas WHERE estado = 'PENDIENTE' AND created_at < (NOW() - INTERVAL 10 MINUTE)) as v_temp
        )");
        $connect->exec("DELETE FROM tbl_ventas WHERE estado = 'PENDIENTE' AND created_at < (NOW() - INTERVAL 10 MINUTE)");
    } catch (Exception $e) {
        // Continue even if cleanup fails
        error_log("Cleanup warning: " . $e->getMessage());
    }
*/
    // 4. Get Occupied Seats
    
$sql="
        SELECT b.id_asiento, b.tokenid
        FROM tbl_boletos b
        JOIN tbl_ventas v ON b.id_venta = v.id
        WHERE v.id_funcion = '$id_funcion' 
        AND v.estado IN ('PAGADO', 'PENDIENTE')
        AND b.estado IN ('ACTIVO', 'PENDIENTE')
    ";
    

$stmtOcupados = $db->prepare($sql);
$stmtOcupados->execute();
$ocupadosIds = $stmtOcupados->fetchAll(PDO::FETCH_ASSOC);

//var_dump($ocupados);

    // 5. Build Response
    $response = [
        'info' => [
            'pelicula' => $funcion['pelicula'],
            'cine' => $funcion['cine'],
            'sala' => $funcion['sala'],
            'hora' => $funcion['hora_inicio'],
            'cols' => (int)$funcion['columnas']
        ],
        'layout' => $asientos,
        'occupied' => $ocupadosIds
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}


break;


case 'listartarifas':

//$id_local='1';

$stmtFun = $db->prepare(" SELECT * FROM tbl_tarifa WHERE local = ? ORDER BY precio DESC ");
$stmtFun->execute([$id_local]);
$rows = $stmtFun->fetchAll();

    // Agrupar resultados

foreach ($rows as $row) {
$data[]=array(
'precio'=>number_format($row['precio'], 2),
'nombre'=>htmlspecialchars($row['nombre']),
'id'=>$row['id']

);

    }
    

echo json_encode($data);

break;


case 'preventaseleccionada':


$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR GENERAL';

$id_funcion = isset($_REQUEST['id_funcion']) ? (int)$_REQUEST['id_funcion'] : 0;
$entradasclientes= isset($_REQUEST['entradasclientes']) ? $_REQUEST['entradasclientes'] : 0;
$tokenid= isset($_REQUEST['tokenid']) ? $_REQUEST['tokenid'] : 0;

// Try to get seats from various possible inputs
$seats_ids = [];

if (!empty($_REQUEST['selected_seats'])) {
    // 1. Comma separated string
    $seats_ids = explode(',', $_REQUEST['selected_seats']);
} elseif (!empty($_REQUEST['asientos']) && is_array($_REQUEST['asientos'])) {
    // 2. Array 'asientos'
    $seats_ids = $_REQUEST['asientos'];
} elseif (!empty($_REQUEST['id_asiento']) && is_array($_REQUEST['id_asiento'])) {
    // 3. Array 'id_asiento'
    $seats_ids = $_REQUEST['id_asiento'];
}

// Sanitize IDs: Remove empty/null values and ensure integers
$seats_ids = array_filter($seats_ids, function($v) { 
    return $v !== '' && $v !== null; 
});
$seats_ids = array_map('intval', $seats_ids);
$seats_ids = array_values($seats_ids); // Re-index array

try {
    $db->beginTransaction();

    // 1. Verify availability AGAIN (Race condition check)
    $placeholders = implode(',', array_fill(0, count($seats_ids), '?'));
    $params = $seats_ids;
    $params[] = $id_funcion; // Add ID funcion at the end

    // Check if any of these seats are already sold/pending for this function
    
    $sqlprep="
        SELECT Count(*) 
        FROM tbl_boletos b
        JOIN tbl_ventas v ON b.id_venta = v.id
        WHERE b.id_asiento IN ($placeholders)
        AND v.id_funcion = ?
        AND v.estado IN ('PAGADO', 'PENDIENTE')
        AND b.estado = 'ACTIVO'
    ";
    
//echo $sqlprep;
    
    
    $stmtCheck = $db->prepare($sqlprep);
    $stmtCheck->execute($params);
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
    if ($count > 0) {
$jsondata['mensaje'] = 'HAY ASIENTOS QUE YA FUERON VENDIDOS';
    }

    }

    // 2. Create Pending Transaction
    $codigo = 'PEND-' . time() . '-' . rand(100, 999);
    $stmtVenta = $db->prepare("INSERT INTO tbl_ventas (codigo, id_funcion, total, estado, tokenid) VALUES (?, ?, 0, 'PENDIENTE', '$tokenid')");
    $stmtVenta->execute([$codigo, $id_funcion]);
    $id_venta = $db->lastInsertId();

    // 3. Create Pending Seats (Lock them)
    foreach ($seats_ids as $sid) {
        if ($sid <= 0) continue; // Skip invalid IDs

        // Get visual data for the seat record
        $stmtS = $db->prepare("SELECT * FROM tbl_sala_asiento WHERE id = ?");
        $stmtS->execute([$sid]);
        $sData = $stmtS->fetch(PDO::FETCH_ASSOC);

        if (!$sData) {
            // If seat not found, rollback and error
            throw new Exception("El asiento con ID $sid no existe en la base de datos.");
        }


$stmtboleto = $db->prepare("SELECT * FROM tbl_boletos WHERE tokenid = ? AND id_asiento = ?");
$stmtboleto->execute([$tokenid, $sid]);
$sDatafinal = $stmtboleto->fetch(PDO::FETCH_ASSOC);

if(!$sDatafinal){

$sql="INSERT INTO tbl_boletos (id_venta, id_asiento, fila, columna, numero, precio, estado, tokenid) 
VALUES ('$id_venta', '$sid', '$sData[fila]', '$sData[columna]', '$sData[num_asiento]', 0, 'ACTIVO', '$tokenid')";
//echo $sql;
        $stmtB = $db->prepare($sql);
        // Note: id_tarifa is NULL initially, will be updated in payment
        $stmtB->execute();
}


 
}


//var_dump($entradasclientes);

if (is_array($entradasclientes)) {
foreach ($entradasclientes as $sid) {


$idproducto=$sid['id'];
$cantidad=$sid['cantidad'];

        // Get visual data for the seat record
        $stmtS = $db->prepare("SELECT * FROM tbl_tarifa WHERE id = ?");
        $stmtS->execute([$idproducto]);
        $sData = $stmtS->fetch(PDO::FETCH_ASSOC);

$exonerado=0;
$inafecta=0;

$total=$cantidad*$sData['precio'];

$subtotal=$total/ 1.18;
$subtotalcab=$subtotal;
$igv=$total-$subtotal;

        $stmtB = $db->prepare("INSERT INTO tbl_boletos_detalle (idventa, idproducto, nombre_producto, precio_unitario, cantidad, igv, subtotal, total, exonerado, inafecta, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        // Note: id_tarifa is NULL initially, will be updated in payment
        $stmtB->execute([$id_venta, $idproducto, $sData['nombre'], $sData['precio'], $cantidad, $igv, $subtotal, $total, $exonerado, $inafecta]);
    }
}

    $db->commit();

    // 4. Redirect to Payment (with Venta ID)
    //header("Location: compra_pago.php?id_venta=$id_venta");
    //exit;



$jsondata['mensaje'] = 'PREVENTA REGISTRADA CON ÉXITO';
$jsondata['estado'] = '1';
$jsondata['id_venta'] =$id_venta;
    
} catch (PDOException $e) {
    $db->rollBack();
    die("Error: " . $e->getMessage());
}

echo json_encode($jsondata);

break;



case 'listarconfiteria':

//empty($row['peli_img']) ? UPLOADS_URL . 'peliculas/' . $row['peli_img'] : 'https://kiosko.smartbase.club/assets/img/peliculas/images.jpg',

//echo $idcategoria;

$data=array();

if($idcategoria==0){
$sql = " SELECT id, nombre, tipo, codigo_barras, precio_venta,es_vendible, imagen from tbl_productos WHERE tipo!='insumo' ";
}else{
$sql=" SELECT id, nombre, tipo, codigo_barras, precio_venta,es_vendible, imagen from tbl_productos WHERE idcategoria='$idcategoria' AND tipo!='insumo' ";
}

//echo $sql;

$stmtFun = $db->prepare($sql);
$stmtFun->execute();
$rows = $stmtFun->fetchAll();
//https://via.placeholder.com/480x300?text=Snack
foreach ($rows as $row) {
$data[]=array(
'precio'=>number_format($row['precio_venta'], 2),
'nombre'=>htmlspecialchars($row['nombre']),
'imagen'=>!empty($row['imagen'])?ASSETS_URL . 'uploads/productos/'.$row['imagen']: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&h=400&fit=crop',
'imagencodigo'=>$row['imagen'],
'id'=>$row['id']

);

    }
    
echo json_encode($data);

break;


case 'listarcategoria':

//$id_local='1';

$stmtFun = $db->prepare(" SELECT * FROM tbl_categoria ORDER BY nombre ASC ");
$stmtFun->execute();
$rows = $stmtFun->fetchAll();

    // Agrupar resultados

foreach ($rows as $row) {
$data[]=array(
'nombre'=>htmlspecialchars($row['nombre']),
'id'=>$row['id']

);

    }
    

echo json_encode($data);

break;

case 'eliminarasiento':

// Asegúrate de usar la misma variable de conexión
// Si antes usabas $db, usa $db aquí también

$jsondata['mensaje'] = 'ASIENTO ELIMINADO';
$jsondata['estado'] = '0';

try {
    // Usa prepared statements para evitar inyección SQL
    $sql = "DELETE FROM tbl_boletos WHERE id_asiento = :id_asiento AND tokenid = :tokenid";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id_asiento' => $id_asiento,
        ':tokenid' => $tokenid
    ]);
    
    // Verificar si se eliminó algún registro
    $filasAfectadas = $stmt->rowCount();
    
    if ($filasAfectadas > 0) {
        $jsondata['mensaje'] = 'PROCESADO CORRECTAMENTE';
        $jsondata['estado'] = '1';
        $jsondata['id_asiento'] = $id_asiento;
        $jsondata['filas_afectadas'] = $filasAfectadas;
    } else {
        $jsondata['mensaje'] = 'No se encontró el boleto con esos datos';
        $jsondata['estado'] = '0';
    }
    
} catch (PDOException $e) {
    $jsondata['mensaje'] = 'Error en la base de datos';
    $jsondata['estado'] = '0';
    $jsondata['error'] = $e->getMessage();
    error_log("Error DELETE: " . $e->getMessage());
}

echo json_encode($jsondata);

break;


case 'cancelarventa':

// Inicializar respuesta por defecto
$jsondata = [];
$jsondata['mensaje'] = 'PREVENTA REGISTRADA CON ÉXITO';
$jsondata['estado'] = '0';

// Verificar que la conexión existe
if (!$db) {
    $jsondata['mensaje'] = 'Error de conexión a la base de datos';
    $jsondata['estado'] = '0';
    echo json_encode($jsondata);
    exit;
}

// Verificar que $id_venta existe
if (!isset($tokenid) || empty($tokenid)) {
    $jsondata['mensaje'] = 'ID de venta no válido';
    $jsondata['estado'] = '0';
    echo json_encode($jsondata);
    exit;
}

try {
    // Usar prepared statements para evitar inyección SQL
    // Y eliminar en el orden correcto (primero las tablas hijas)
    
    // 1. Eliminar detalles de boletos (tabla hija)
    
    
    /*
    $sql1 = "DELETE FROM tbl_boletos_detalle WHERE tokenid = :id_venta";
    $stmt1 = $connect->prepare($sql1);
    $stmt1->execute([':id_venta' => $tokenid]);
    */
    
    
    // 2. Eliminar boletos (tabla que referencia a ventas)
    $sql2 = "DELETE FROM tbl_boletos WHERE tokenid = :id_venta";
    $stmt2 = $db->prepare($sql2);
    $stmt2->execute([':id_venta' => $tokenid]);
    
    // 3. Eliminar la venta (tabla padre)
    $sql3 = "DELETE FROM tbl_ventas WHERE tokenid = :id_venta";
    $stmt3 = $db->prepare($sql3);
    $stmt3->execute([':id_venta' => $tokenid]);
    
    $jsondata['mensaje'] = 'PROCESADO CORRECTAMENTE';
    $jsondata['estado'] = '1';
    $jsondata['tokenid'] = $tokenid;
    $jsondata['filas_afectadas'] = [
        'detalles' => $stmt1->rowCount(),
        'boletos' => $stmt2->rowCount(),
        'ventas' => $stmt3->rowCount()
    ];
    
} catch (PDOException $e) {
    // Error específico de base de datos
    $jsondata['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
    $jsondata['estado'] = '0';
    error_log("Error DELETE: " . $e->getMessage());
} catch (Exception $e) {
    // Otros errores
    $jsondata['mensaje'] = 'Error general: ' . $e->getMessage();
    $jsondata['estado'] = '0';
    error_log("Error general: " . $e->getMessage());
}

echo json_encode($jsondata);

break;

case 'resumenventas':


$response = array();
$response['estado'] = '0';
$response['mensaje'] = 'ERROR GENERAL';

try {
    // Preparar y ejecutar la consulta de ventas
    $stmtS = $db->prepare("SELECT * FROM tbl_ventas WHERE tokenid = ?");
    if (!$stmtS) {
        $response['mensaje'] = 'Error en preparación de consulta';
        echo json_encode($response);
        exit;
    }
    
    $stmtS->execute([$tokenid]);
    $sData = $stmtS->fetch(PDO::FETCH_ASSOC);
    
    // OBTENER TODOS LOS BOLETOS (corregido)
    $stmtboleto = $db->prepare("SELECT * FROM tbl_boletos WHERE tokenid = ?");
    $stmtboleto->execute([$tokenid]);
    $databoleto = $stmtboleto->fetchAll(PDO::FETCH_ASSOC); // CAMBIADO: fetchAll en lugar de fetch
    
    // 1. Get Function & Room Info
    $stmt = $db->prepare("
        SELECT f.id, h.hora as hora_inicio, p.nombre as pelicula, s.nombre as sala, 
               s.filas, s.columnas, s.id as id_sala, l.nombre as cine
        FROM tbl_funciones f
        JOIN tbl_pelicula p ON f.id_pelicula = p.id
        JOIN tbl_sala s ON f.id_sala = s.id
        JOIN tbl_locales l ON s.local = l.id
        JOIN tbl_hora h ON f.id_hora = h.id
        WHERE f.id = ? AND f.estado = '1'
    ");
    $stmt->execute([$id_funcion]);
    $funcion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar si se encontró la función
    if (!$funcion) {
        $response['mensaje'] = 'Función no encontrada';
        echo json_encode($response);
        exit;
    }
    
    // 5. Build Response
    $response = [
        'estado' => '1',
        'mensaje' => 'Datos obtenidos correctamente',
        'info' => [
            'pelicula' => $funcion['pelicula'],
            'cine' => $funcion['cine'],
            'sala' => $funcion['sala'],
            'hora' => $funcion['hora_inicio'],
            'cols' => (int)$funcion['columnas']
        ],
        'boletos' => $databoleto, // Ahora es un array con todos los boletos
        'venta' => $sData
    ];
    
    // Opcional: contar cuántos boletos se encontraron
    $response['total_boletos'] = count($databoleto);
    
} catch (PDOException $e) {
    $response['mensaje'] = 'Error en BD: ' . $e->getMessage();
    $response['estado'] = '0';
    error_log("Error en consulta: " . $e->getMessage());
}

echo json_encode($response);

break;




case 'guardarsnack':


$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR GENERAL';

$snackcliente= isset($_REQUEST['snackcliente']) ? $_REQUEST['snackcliente'] : 0;

try {
    $connect->beginTransaction();

    // 2. Create Pending Transaction
    $codigo = 'PEND-' . time() . '-' . rand(100, 999);
    $stmtVenta = $connect->prepare("INSERT INTO tbl_ventas (codigo, id_funcion, total, estado) VALUES (?, ?, 0, 'PENDIENTE')");
    $stmtVenta->execute([$codigo, $id_funcion]);
    $id_venta = $connect->lastInsertId();


//var_dump($snackcliente);

foreach ($snackcliente as $sid) {


$idproducto=$sid['id'];
$cantidad=$sid['cantidad'];

        // Get visual data for the seat record
        $stmtS = $connect->prepare("SELECT * FROM tbl_productos WHERE id = ?");
        $stmtS->execute([$idproducto]);
        $sData = $stmtS->fetch(PDO::FETCH_ASSOC);

$exonerado=0;
$inafecta=0;

$total=$cantidad*$sData['precio_venta'];

$subtotal=$total/ 1.18;
$subtotalcab=$subtotal;
$igv=$total-$subtotal;

        $stmtB = $connect->prepare("INSERT INTO tbl_boletos_detalle (idventa, idproducto, nombre_producto, precio_unitario, cantidad, igv, subtotal, total, exonerado, inafecta, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        // Note: id_tarifa is NULL initially, will be updated in payment
        $stmtB->execute([$id_venta, $idproducto, $sData['nombre'], $sData['precio_venta'], $cantidad, $igv, $subtotal, $total, $exonerado, $inafecta]);
    }


    $connect->commit();

    // 4. Redirect to Payment (with Venta ID)
    //header("Location: compra_pago.php?id_venta=$id_venta");
    //exit;



$jsondata['mensaje'] = 'PREVENTA REGISTRADA CON ÉXITO';
$jsondata['estado'] = '1';
$jsondata['id_venta_snack'] =$id_venta;
    
} catch (PDOException $e) {
    $connect->rollBack();
    die("Error: " . $e->getMessage());
}

echo json_encode($jsondata);

break;

case 'guardarventa':


/*   
//echo 'EL ID DE EMPRESA ES : '.$idempresa.'|'.$local;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h3>Datos recibidos por POST:</h3>";
    echo "<ul>";
    foreach ($_POST as $key => $value) {
        // htmlspecialchars evita problemas de seguridad (XSS)
        echo "<li><strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars($value) . "</li>";
    }
    echo "</ul>";
}


var_dump($_POST['detalle']);
*/

$response = [
    "ok" => false,
    "msg" => "Error general"
];


    $db->beginTransaction();

    // =========================
    // 🔹 1. EMPRESA / LOCAL
    // =========================
    //$idempresa = $_SESSION['id_empresa'];
    //$local     = $_POST['idcine'];

    if (!$idempresa || !$local) {
        throw new Exception("Falta empresa o local");
    }

    // =========================
    // 🔹 2. OBTENER ORIGEN
    // =========================
    $stmt = $db->prepare("
        SELECT origen, origen_pago 
        FROM tbl_origen_local 
        WHERE idempresa=? AND local=? AND estado='1'
    ");
    $stmt->execute([$idempresa, $local]);
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$config) {
        throw new Exception("No existe configuración de origen");
    }

    $origen = $config['origen'];

    // =========================
    // 🔹 3. GENERAR VOUCHER (LOCK)
    // =========================
    $fecha = $_POST["fechaemision"] ?? date('Y-m-d');

    $anio = date('Y', strtotime($fecha));
    $mes  = date('m', strtotime($fecha));

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

    // =========================
    // 🔹 4. INSERT CABECERA
    // =========================
    $sql = "INSERT INTO tbl_venta_cab (
        idempresa, local, origen, voucher,
        tipocomp, serie, correlativo,
        fecha_emision, hora_emision,
        codmoneda, op_gravadas, igv, total,
        idcliente, vendedor,
        codfuncion, codpelicula, codsala, fechafuncion
    ) VALUES (
        ?, ?, ?, ?,
        ?, ?, ?,
        ?, ?,
        ?, ?, ?, ?,
        ?, ?,
        ?, ?, ?, ?
    )";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        $idempresa,
        $local,
        $origen,
        $voucher,
        $_POST['tipocomp'] ?? '03',
        $_POST['serie'] ?? '',
        $_POST['correlativo'] ?? 0,
        $fecha,
        date('H:i:s'),
        $_POST['moneda'] ?? 'PEN',
        $_POST['op_gravadas'] ?? 0,
        $_POST['igv'] ?? 0,
        $_POST['total'] ?? 0,
        $_POST['idcliente'] ?? 0,
        $_POST['vendedor'] ?? 0,
        $_POST['codfuncion'] ?? 0,
        $_POST['codpelicula'] ?? 0,
        $_POST['codsala'] ?? 0,
        $_POST['fechafuncion'] ?? $fecha
    ]);

    $idventa = $db->lastInsertId();

    // =========================
    // 🔹 5. INSERT DETALLE
    // =========================
    
// Recibir el string JSON
$detalle_json = $_POST['detalle'];

// Intentar decodificar
$detalle = json_decode($detalle_json, true);

// Verificar el error específico
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al decodificar JSON: " . json_last_error_msg() . "\n";
    echo "Contenido recibido: " . $detalle_json . "\n";
    echo "Tipo de dato: " . gettype($detalle_json) . "\n";
    $detalle = [];
} else {
    // Decodificación exitosa
    if (is_array($detalle) && !empty($detalle)) {
        foreach ($detalle as $item) {
            // Procesar cada item
           // echo "Procesando: " . $item['nombre_producto'] . "\n";
        }
    }
}


//if (!is_array($detalle)) $detalle = [];

if (is_array($detalle) && !empty($detalle)) {
	

	
	$item=1;
        foreach ($detalle as $d) {
        
//var_dump($d);	

            $stmt = $db->prepare("
                INSERT INTO tbl_venta_det (
                    idempresa, idlocal, idventa, item,
                    idproducto, nombre_producto,
                    cantidad, precio_unitario,
                    igv, importe_total
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $idempresa,
                $local,
                $idventa,
                $item++,
                $d['idproducto'],
                $d['nombre'],
                $d['cantidad'],
                $d['precio'],
                $d['igv'],
                $d['total']
            ]);
        }
    }








// =========================
// 🔹 6. INSERT PAGOS
// =========================

// Recibir el string JSON
$pagos_json = $_POST['pagos'];

// Intentar decodificar
$pagos = json_decode($pagos_json, true);
//var_dump($pagos);
// Verificar el error específico
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al decodificar JSON: " . json_last_error_msg() . "\n";
    echo "Contenido recibido: " . $pagos_json . "\n";
    echo "Tipo de dato: " . gettype($pagos_json) . "\n";
    $pagos = [];
} else {
    // Decodificación exitosa
    if (is_array($pagos) && !empty($pagos)) {
        foreach ($pagos as $pago) {
            // Procesar cada item
           // echo "Procesando: " . $item['nombre_producto'] . "\n";
        }
    }
}

if (is_array($pagos) && !empty($pagos)) {
	

	
	$item=1;
        foreach ($pagos as $p) {
        
//var_dump($d);	
/*
 [
    {
      "idtipopago": 1,
      "numoperacion": "0002123",
      "importe_pago": 40.00,
      "moneda": "PEN",
      "tc": 1,
      "origen_pago": "",
      "voucher_pago": 0,
      "fecha": "2026-05-02",
      "observacion": "PAGO KIOSKO",
      "tipo_pago":"009"
    }
  ]
*/

            $stmt = $db->prepare("
                INSERT INTO tbl_venta_pago (
                    idempresa, 
                    idlocal, 
                    idventa, 
                    tipopago,
                    numero_operacion,
                    importe_pago,
                    fecha_pago, 
                    tipocambio,
                    moneda,
                    observacion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?)
            ");

            $stmt->execute([
                $idempresa,
                $local,
                $idventa,
                $p['idtipopago'],
                $p['numoperacion'],
                $p['importe_pago'],
                $p['fecha'],
                $p['tc'],
                $p['moneda'],
                $p['observacion']
            ]);
        }
    }








    // =========================
    // 🔹 7. COMMIT
    // =========================
    $db->commit();

    $response = [
        "ok" => true,
        "idventa" => $idventa,
        "voucher" => $voucher,
        "origen" => $origen
    ];









//ENVIO DE CORREOS
//ENVIO DE CORREOS
//ENVIAMOS CORREO

$sql="SELECT *FROM tbl_venta_cab WHERE idventa='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sqlnn=ejecutarConsulta("SELECT *FROM tbl_venta_det WHERE idventa='$idventanew'");
$pasajero= mysqli_fetch_array($sqlnn);

$sqldor="SELECT *FROM sucursal WHERE id='$pasajero[idembarque]' ";
//echo $sqldor;
$dor = ejecutarConsulta($sqldor);
$or=mysqli_fetch_array($dor);
	
$ddes= ejecutarConsulta("SELECT *FROM sucursal WHERE id='$pasajero[iddestino]' ");
$des=mysqli_fetch_array($ddes);

$dhor=ejecutarConsulta("SELECT *FROM rutas_horario WHERE id='$pasajero[idhorario]' ");
$horario=mysqli_fetch_array($dhor);

$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
	
$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$config= ejecutarConsultaSimpleFila($sql3);

if($config['tipo']=='3'){ $proceso='BETA'; }else{ $proceso='PRODUCCION'; }

$link=$config['ruc'].'-'.$mostrar['txtID_TIPO_DOCUMENTO'].'-'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];

$comprobante=$link.'.pdf';
$rutapdf=BASE_URL_PRINCIPAL.'/plugins/dompdf/';	

$serienumero=$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];
$tipodoc='Factura Electrónica';	
if($mostrar['txtID_TIPO_DOCUMENTO']=='03'){ $tipodoc='Boleta Electrónica';	 }


//ticket-pasaje2.php?id=401&nivel=0
$jsondata['rutapdf']=$rutapdf;

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
CURLOPT_URL => $rutapdf."ticket-entradas.php?id=".$idventanew."&nivel=0",
CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_USERAGENT => "Codular Sample cURL Request"
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
curl_close($curl);
	
$mail = new PHPMailer();
//Luego tenemos que iniciar la validación por SMTP:
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';

/**/
$smtp="smtp-relay.brevo.com";
$usuario="9cb308001@smtp-brevo.com";
$contrrasenia="bskCcEmZktrTv9i";
$correoserver='ventapasajes@turismovirgendelcarmen.com.pe';
$correodemo='kikegrafico@gmail.com';
//$segundocorreocopia='contabilidad@turismovirgendelcarmen.com.pe';


$mail->Host = $smtp; // SMTP a utilizar. Por ej. smtp.elserver.com
$mail->Username = $usuario;	
$mail->Password = $contrrasenia;
$mail->Port = 587; // Puerto a utilizar

//$mail->SMTPDebug = SMTP::DEBUG_SERVER; //PARA VER SI HAY ERRORES
$mail->From = $correoserver; // Desde donde enviamos (Para mostrar)
$mail->FromName = $config['razon_social'];

//$mail->AddAddress($mailfinal); // Esta es la dirección a donde enviamos
$mail->AddAddress($correodemo); // Esta es la dirección a donde enviamos
//$mail->AddAddress($correodemo2);
//$mail->AddAddress($segundocorreocopia);
$mail->IsHTML(true); // El correo se envía como HTML
$mail->Subject = "Envío de comprobante: ".$comprobante; // Este es el titulo del email.	

$body ='<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Documento sin título</title>
</head>

<body>


















<div style="margin:0;padding:0;min-width:100%">
<div style="width:100%;table-layout:fixed;text-align:center">
    <div style="max-width:80%;margin-right:28%">
        <table style="margin:0 auto;width:100%;font-family:arial" align="center" cellpadding="0" cellspacing="0" border="0">
            <tbody><tr>
                <td style="width:100%;height:auto;padding:0 50px;line-height:0;text-align:center">
<img src="'.BASE_URL_PRINCIPAL.'/files/logo/'.$config['id'].'.png" style="margin:0 auto;border:0;max-width:650px" alt="" class="CToWUd" data-bit="iit">
                    <hr style="width:100%;padding:2.5px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>
            
            <tr>
                
                <td style="width:100%;height:auto;padding:10px 50px;text-align:center">
                    
                    
                    
                    
                    
                    
                    
                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:10px 50px;text-align:center">
                    <p style="margin:0px;font-size:15px;text-align:center;color:#ed3968;font-weight:bold"></p>
                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:10px 50px;text-align:center">
                    <p style="margin:0px;font-size:38px;text-align:center;color:#0d1f40;font-weight:bold">Alien: Romulus</p>
                    <p style="font-size:20px;text-align:center;color:#05589e;margin-bottom:0px">Nro. de Compra:
                        <b>WMGMRMM</b></p>
                    <img alt="" style="width:230px">
                    <p style="margin:0px;font-size:20px;text-align:center;color:#05589e;font-weight:bold">DOBLADA</p>
                    <hr style="width:100%;padding:2.5px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>


            <tr>
                <td style="width:100%;height:auto;padding:0px 50px;text-align:center;letter-spacing:-5px">

                    <div style="display:inline-block;width:10%;letter-spacing:normal"><img src="tiempo-rojo.jpg" alt="" width="35" height="39" class="CToWUd" data-bit="iit">
                    </div>
                    <div style="display:inline-block;width:90%;min-width:300px;letter-spacing:normal;font-size:12px;color:#ed3968">
                        Muestra el código QR desde tu celular para canjear tus combos e ingresar a la sala. No necesitas
                        pasar por
                        Boletería ni imprimir este documento
                    </div>
                    <hr style="width:100%;padding:10px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>
            <tr>
                <td style="width:100%;height:auto;padding:5px 50px;text-align:center">
 
                    <p style="color:#05589e;font-size:20px;line-height:1.2;text-align:center;margin:5px 0">
                        Cliente:
                        <b>Paloma Zambrano</b></p>

                    <div style="text-align:center;font-size:11px;color:#05589e;letter-spacing:-5px">
                        <div style="width:22%;display:inline-block;min-width:100px"><img src="maps.jpg" class="CToWUd" data-bit="iit">
                            <p style="letter-spacing:normal;color:#05589e">CP Santa Clara</p>
                        </div>
                        <div style="width:22%;display:inline-block;min-width:100px"><img src="calendario.jpg" class="CToWUd" data-bit="iit">
                            <p style="letter-spacing:normal;color:#05589e">30/08/2024</p>
                        </div>
                        
                        <div style="width:22%;display:inline-block;min-width:100px"><img src="timpo.jpg" class="CToWUd" data-bit="iit">
                            <p style="letter-spacing:normal;color:#05589e">07:30 PM</p>
                        </div>
                        <div style="width:22%;display:inline-block;min-width:100px"><img src="salas.jpg" class="CToWUd" data-bit="iit">
                            <p style="letter-spacing:normal;color:#05589e">SALA 1</p>
                        </div>

                    </div>


                </td>
            </tr>
            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">
                    <hr style="width:100%;padding:10px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">

                    <table style="margin:0px;text-align:left;font-size:16px;color:#05589e;width:50%">
                        <tbody><tr>
                            <td style="width:10%"><img src="asiento.jpg" style="vertical-align:middle" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td style="width:50%">Tus Butacas: <b>M12, M11, M10</b></td>
                        </tr>
                    </tbody></table>
                    <hr style="width:100%;padding:2px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">
                    <table style="margin:0px;text-align:left;font-size:16px;color:#05589e;width:50%">
                        <tbody><tr>
                            <td style="width:10%"><img src="ticket.jpg" style="vertical-align:middle" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td style="width:50%"><b> Entradas</b></td>
                        </tr>
                    </tbody></table>

                    <table style="width:100%;height:auto;margin-top:10px">
                        
                            <tbody><tr style="font-size:16px;color:#05589e">
                                <td style="text-align:left;width:50%">General 2D OL</td>
                                <td style="text-align:center;font-style:italic">Cant. <span>3</span>
                                </td>
                                <td style="text-align:right"><span>S/</span>
                                    <span>54.00</span>
                                </td>
                            </tr>
                             
                        
                    </tbody></table>

                    <hr style="width:100%;padding:10px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                    <table style="width:100%;height:auto">
                        <tbody><tr style="font-size:18px;color:#05589e">
                            <td style="text-align:left"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:right;font-weight:bold">
                                <span>SubTotal: </span><span>S/</span>
                                <span>54.00</span>
                            </td>
                        </tr>
                    </tbody></table>

                    <hr style="width:100%;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">
                    <table style="margin:30px 0px 0px 0px;text-align:left;font-size:16px;color:#05589e;width:50%">
                        <tbody><tr>
                            <td style="width:9%"><img src="dulceria.jpg" style="vertical-align:middle" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td style="width:50%"><b>Dulcería</b></td>
                        </tr>
                    </tbody></table>
                    <table style="width:100%;height:auto;margin-top:10px">
                        

                            <tbody><tr style="font-size:16px;color:#05589e">
                                <td style="text-align:left;width:50%">Combo 2 Salado OL CC</td>
                                <td style="text-align:center;font-style:italic">Cant. <span>1</span></td>
                                <td style="text-align:right"><span>S/</span> <span>39.00</span></td>
                            </tr>

                            <tr style="font-size:13px;color:#9c9c9c">
                                <td style="text-align:left;width:30%">
                                    <span></span>
                                </td>
                                <td colspan="2"></td>
                            </tr>


                        
                    </tbody></table>

                    <hr style="width:100%;padding:10px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                    <table style="width:100%;height:auto">
                        <tbody><tr style="font-size:18px;color:#05589e">
                            <td style="text-align:left"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:right;font-weight:bold">
                                <span>SubTotal: </span><span>S/</span><span>39.00</span>
                            </td>
                        </tr>
                    </tbody></table>

                    <hr style="width:100%;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">

                    <table style="width:100%;height:auto">
                        <tbody><tr style="font-size:18px;color:#05589e">
                            <td style="text-align:left"></td>
                            <td style="text-align:center"></td>
                            <td style="color:#ed3968;font-size:24px;line-height:0px;text-align:right">
                                <b>TOTAL: </b><b>S/</b><b>93.00</b>
                            </td>
                        </tr>
                    </tbody></table>
                    <hr style="width:100%;padding:5px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>

            <tr>
                <td>
                    <table>

                        <tbody><tr>
                            
                        </tr>
                        <tr>
                            
                        </tr>

                    </tbody></table>
                </td>
            </tr>
            <tr>
                <td style="width:100%;height:auto;padding:0px 50px;text-align:center">
                    <table style="margin:0px;text-align:left;font-size:14px;color:#05589e;width:90%">
                        <tbody><tr>
                            <td colspan="2" style="font-weight:bold;font-size:20px!important;margin-bottom:10px">
                                <p style="margin-bottom:10px;margin-top:5px">Recuerda</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style="width:8%;text-align:right;margin-right:10px"><img height="25" width="30" style="margin-right:10px" src="colas.jpg" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td>
                                <p style="color:#05589e;margin:10px">¡Sin colas! Dirígete directamente a la sala o a la zona de despacho online en la Dulcería para recoger tu combo. No necesitas pasar por boletería.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:8%;text-align:right;margin-right:10px"><img height="25" width="40" style="height:35px;margin-right:10px" src="celular.jpg" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td>
                                <p style="color:#05589e;margin:10px">Muestra tu Orden de Compra directamente desde tu
                                    celular, no es
                                    necesario imprimir.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:8%;text-align:right;margin-right:10px"><img height="25" width="40" style="height:30px;margin-right:10px" src="tiempo.jpg" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td>
                                <p style="color:#05589e;margin:10px">El horario de la función indica el inicio de
                                    proyección de
                                    publicidad y avances de los próximos estrenos. Luego de éstos, iniciará la
                                    película.</p>
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
            <tr>

                <td style="width:100%;height:auto;padding:0px 50px;text-align:left;color:#05589e;font-size:14px">

                    <b>Condiciones de compra </b>
                    <ul>
                        <li>La compra y el canje de las entradas y/o combos, solo son válidos para el mismo día de la
                            función.
                        </li>
                        <li>Si utilizaste códigos promocionales o boletos corporativos debes presentar los cupones
                            físicos en el
                            ingreso a la sala.
                        </li>
                        <li>Si utilizaste vales de consumo corporativos debes presentar los cupones físicos en la zona
                            del
                            despacho de dulcería.
                        </li>
                        <li style="color:#e40646">Esta compra no permite cambio de función, anulación y/o devolución de
                            dinero.
                        </li>
                        <li style="color:#e40646">Respetar los protocolos de prevención covid-19 en nuestras
                            instalaciones por
                            disposición del estado. Caso contrario el establecimiento se reserva el derecho de retirar a
                            la persona
                            de sala.
                        </li>
                        <li style="color:#e40646">En función a los protocolos de bioseguridad, no contamos con custodia
                            de
                            productos, por favor tomar sus precauciones.
                        </li>
                    </ul>
                      <b>Estimado Cliente </b><br>
                    Para un mejor servicio realiza los siguientes pasos:<br>
                  <ul>
                        <li>Presenta desde tu smartphone este documento con el código QR en el ingreso a salas. No tiene que pasar por boletería ni imprimirlo</li>
                        <li>Si compraste un producto en Dulcería dirígete a la
                            zona de
                            despacho para recoger tu pedido.
                        </li>
                        <li>Si solo compraste entradas, dirígete al ingreso de tu sala.
                        </li>
                        <li>Cualquier duda respecto al pago, realízala directamente con tu banco emisor.</li>
                        <li>Si deseas la boleta electrónica de tu compra ingresa a <a href="http://boletadigital.cineplanet.com.pe" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://boletadigital.cineplanet.com.pe&amp;source=gmail&amp;ust=1778704181961000&amp;usg=AOvVaw1i1p7bV2l0O7thQl8m-ucO">http://boletadigital.<wbr>cineplanet.com.pe</a> y búscala por el N° de compra o tu DNI.</li>
                    </ul>
                  <span style="font-size:9pt;color:#767171">Cineplex S.A - Av. José Larco N° 663 – Pisos 4 - 5  - Miraflores - Lima - Lima.</span><br>


                    <hr style="border-bottom:5px solid #e50246;width:100%;margin-bottom:10px;margin-top:10px">


                </td>

            </tr>


        </tbody></table>

    </div>
</div>
</div>
	
































</body>
</html>';
	
//$adjuntardocumento=BASE_URL_PRINCIPAL."/api_cpe/".$proceso."/".$config['ruc']."/".$comprobante;
//echo $adjuntardocumento;

$adjuntardocumento =BASE_URL_PRINCIPAL."/api_cpe/".$proceso."/".$config['ruc']."/".$comprobante;
//echo $adjuntardocumento;
//$pdfContent = descargarPDF($adjuntardocumento);
//Descargar el PDF

    $tempFile = tempnam(sys_get_temp_dir(), 'pdf_');
    
    // Descargar y guardar temporalmente
    $pdfContent = file_get_contents($adjuntardocumento);
    
if ($pdfContent !== false && file_put_contents($tempFile, $pdfContent) !== false) {
$mail->AddAttachment($tempFile, $comprobante);
}


//$body .= "Acá continuo el <strong>mensaje</strong>";
$mail->Body = $body; // Mensaje a enviar
$mail->CharSet = 'UTF-8';
$exito = $mail->Send(); // Envía el correo.

//unlink(BASE_URL_PRINCIPAL."/api_cpe/".$proceso."/".$config['ruc']."/".$comprobante);	

if($exito){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El Correo fue enviado exitosamente';
	
}else{
	
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'No se pudo enviar el correo, Intenta más tarde';
	
}

//ENVIO DE CORREOS
//ENVIO DE CORREOS
//ENVIO DE CORREOS










echo json_encode($response);

break;




	
case 'ventasenviarcorreo':
    
$jsondata = array();

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'ERROR';

$id=isset($_GET["idventa"])? ($_GET["idventa"]):"0";

$sql="SELECT *FROM venta WHERE idventa='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
	
$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
	
$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$config= ejecutarConsultaSimpleFila($sql3);

if($config['tipo']=='3'){ $proceso='BETA'; }else{ $proceso='PRODUCCION'; }

$link=$config['ruc'].'-'.$mostrar['txtID_TIPO_DOCUMENTO'].'-'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];
		
$comprobante=$link.'.pdf';
$comprobante2=$link.'.XML';
$comprobante3='R-'.$link.'.XML';
	
$rutapdf=media().'/plugins/dompdf/';	

$serienumero=$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];
$tipodoc='Factura Electrónica';	
		
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
CURLOPT_URL => $rutapdf."index.php?id=".$id."&correo=1",
CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_USERAGENT => "Codular Sample cURL Request"
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
curl_close($curl);
	
$mail = new PHPMailer();
//Luego tenemos que iniciar la validación por SMTP:
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';

/*

$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
*/		

/*
$smtp="mail.solutions.net.pe";
$usuario="facturacion@solutions.net.pe";
$correoserver='facturacion@solutions.net.pe';
$contrrasenia="@Facturacion2024";
*/



$smtp="smtp-relay.brevo.com";
$usuario="8d6f50001@smtp-brevo.com";
$contrrasenia="B8bngD6yAN0Jw7UG";
$correoserver='facturacion@solutions.net.pe';
$correodemo='kikegrafico@gmail.com';

/*
$smtp="smtp.mailersend.net";
$usuario="MS_fuJZb9@test-51ndgwvrepxlzqx8.mlsender.net";
$contrrasenia="mssp.mhfkv5y.ynrw7gy09ool2k8e.ctBH5BK";
$correoserver='facturacion@solutions.net.pe';
$correodemo='kikegrafico@gmail.com';
*/


$mail->Host = $smtp; // SMTP a utilizar. Por ej. smtp.elserver.com
$mail->Username = $usuario;	
$mail->Password = $contrrasenia;
$mail->Port = 587; // Puerto a utilizar

//$mail->SMTPDebug = SMTP::DEBUG_SERVER; //PARA VER SI HAY ERRORES
$mail->From = $correoserver; // Desde donde enviamos (Para mostrar)
$mail->FromName = $config['razon_social'];

$mail->AddAddress($mcliente['email']); // Esta es la dirección a donde enviamos
//$mail->AddAddress($correodemo);
if($mcliente['email2']!=''){
$mail->AddAddress($mcliente['email2']); // Esta es la dirección a donde enviamos
}

$mail->IsHTML(true); // El correo se envía como HTML
$mail->Subject = "Envío de comprobante: ".$comprobante; // Este es el titulo del email.	

$body ="<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<title>Documento sin título</title>
</head>

<body>
<img src='".media()."/files/logo/".$config['id'].".png' style='max-width: 300px; height: auto;' >
<p>Estimado ".$mcliente['nombre'].",</p>
<p>Por la presente le comunicamos que se ha emitido el siguiente comprobante electrónico:</p>
<table border='0'>
  <tbody>
    <tr>
      <td><strong>Tipo de documento</strong></td>
      <td>:</td>
      <td><strong>".$tipodoc."</strong></td>
    </tr>
    <tr>
      <td><strong>Serie y número</strong></td>
      <td>:</td>
      <td><strong>".$serienumero."</strong></td>
    </tr>
    <tr>
      <td><strong>Fecha de emisión</strong></td>
      <td>:</td>
      <td><strong>".date("d/m/Y", strtotime($mostrar['txtFECHA_DOCUMENTO']))."</strong></td>
    </tr>
  </tbody>
</table>
<p>Se adjunta el Comprobante Electrónico.</p>
<p>Le informamos además que a través del Portal Web puede consultar y descargar este Comprobante de Pago Electrónico.<br>
  <a href='".media()."/comprobantes' target='_blank' >".media()."/comprobantes</a>.</p>
<p>Atentamente,</p>
<p>".$config['nombre_comercial']."<br>
</p>
</body>
</html>";
	
	
//$body .= "Acá continuo el <strong>mensaje</strong>";
$mail->Body = $body; // Mensaje a enviar
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante, $comprobante);
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante2, $comprobante2);
$mail->AddAttachment("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante3, $comprobante3);
$mail->CharSet = 'UTF-8';
$exito = $mail->Send(); // Envía el correo.

unlink("../api_cpe/".$proceso."/".$config['ruc']."/".$comprobante);	

if($exito){
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'El Correo fue enviado exitosamente';
	
}else{
	
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'No se pudo enviar el correo, Intenta más tarde';
	
}
	
echo json_encode($jsondata);

		
break;



case 'agregarproductolector':
/*
$codigo='7759847000812';
$id=1;
$idlocal=1;
*/

$sqlarticulo="SELECT * FROM  articulo WHERE codigo ='$codigo' AND idempresa='$id' ";
$articulo=ejecutarConsultaSimpleFila($sqlarticulo);

$sqla="SELECT * FROM  articulo_stock WHERE idarticulo ='$articulo[txtCOD_ARTICULO]' AND idlocal='$idlocal' ";
$stock=ejecutarConsultaSimpleFila($sqla);
		
if($stock){	
	$sql="SELECT a.txtCOD_ARTICULO, a.comision, a.idcategoria, a.bolsa, a.marca, a.medida, a.codigo, a.txtDESCRIPCION_ARTICULO, a.precio, a.mayor, a.precio_mayor, a.exonerado_igv, a.imagen, a.condicion, a.resaltado, s.stock AS stock, a.stockmin, a.stockmax, a.comisionm, a.comisionmp, a.codigosunat, a.sanitario, a.idproveedor, a.linea, a.sublinea, a.subfamilia, a.precio_compra, a.ctaventas, a.ctacompras, a.existencia, a.idlocal, a.precio_porcentaje, a.principioactivo, a.preciooferta, a.canje, a.canjepuntos, a.canjecobro FROM articulo a LEFT JOIN articulo_stock s ON a.txtCOD_ARTICULO=s.idarticulo  WHERE a.txtCOD_ARTICULO='$articulo[txtCOD_ARTICULO]' AND s.idlocal='$idlocal' ";
}else{
	$sql="SELECT a.txtCOD_ARTICULO, a.comision, a.idcategoria, a.bolsa, a.marca, a.medida, a.codigo, a.txtDESCRIPCION_ARTICULO, a.precio, a.mayor, a.precio_mayor, a.exonerado_igv, a.imagen, a.condicion, a.resaltado, s.stock AS stock, a.stockmin, a.stockmax, a.comisionm, a.comisionmp, a.codigosunat, a.sanitario, a.idproveedor, a.linea, a.sublinea, a.subfamilia, a.precio_compra, a.ctaventas, a.ctacompras, a.existencia, a.idlocal, a.precio_porcentaje, a.principioactivo, a.preciooferta, a.canje, a.canjepuntos, a.canjecobro FROM articulo a LEFT JOIN articulo_stock s ON a.txtCOD_ARTICULO=s.idarticulo  WHERE a.txtCOD_ARTICULO='$articulo[txtCOD_ARTICULO]' ";	
}
//echo $sql;
$rspta=ejecutarConsultaSimpleFila($sql);


if($rspta){
$rspta['estado']=1;
}else{
$rspta['estado']=0;
$rspta['mensaje']='No se encontro ningun registro';
}


echo json_encode($rspta);
break;








case 'guardaryeditarcliente':
		
if (empty($idpersona)){
			
			
$sql="SELECT *FROM persona WHERE txtID_CLIENTE='$txtID_CLIENTE' AND idempresa='$_COOKIE[id]' AND tipo_persona='$tipo_persona' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
			
if($mostrar){

echo "EL PROVEEDOR YA ESTA REGISTRADO";
	
}else{
	
/*
$rspta=$persona->insertar($tipo_persona, $nombre, $tipo_documento, $txtID_CLIENTE, $direccion, $pais, $ciudad, $telefono,$email, $email2, $txtRAZON_SOCIAL, $descuento, $descuentomayor, $sector, $lat, $lon, $codigo, $cci, $vendedor, $obs);
*/

header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
		
if($descuento==''){ $descuento='0'; }
if($descuentom==''){ $descuentom='0'; }
if($lat==''){ $lat='0'; }
if($lon==''){ $lon='0'; }
if($sector==''){ $sector='0'; }	

$llevadetracciones='NO';
$detraccion_codigo='';
$detraccion_nombre='';
$montominimo='0.00';
$detraccion_porcentaje=0;

$sql="INSERT INTO persona VALUES (NULL, '$id', '$vendedor', '$tipo_persona', '$codigo', '$descuentom',  '$nombre', '$tipo_documento', '$txtID_CLIENTE', '$sector', '$direccion', '$pais', '$ciudad', '$lat', '$lon', '$telefono', '$email', '$email2','', '$txtRAZON_SOCIAL', '$llevadetracciones', '$detraccion_codigo', '$detraccion_nombre', '$montominimo', '$detraccion_porcentaje', '$descuento', '$cci', '0', '$obs')";
		//return ejecutarConsulta($sql);
//echo $sql;
$id=ejecutarConsulta_retornarID($sql);

//echo $sql;
	
$jsondata = array();
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'Error no se pudo agregar.';

if($id!=''){
$jsondata['numerodocumento'] = $txtID_CLIENTE;
$jsondata['estado'] = '1';
$jsondata['id'] = $id;
$jsondata['mensaje'] = 'Cliente: '.$nombre.', agregado con exito';
$jsondata['doc'] =$tipo_documento;
$jsondata['nombre'] = $nombre;
}
		
echo json_encode($jsondata);
			
}
			
}else {
	/*
$rspta=$persona->editar($idpersona,$tipo_persona,$nombre,$tipo_documento,$txtID_CLIENTE,$direccion, $pais, $ciudad,$telefono,$email, $email2, $txtRAZON_SOCIAL, $descuento, $descuentomayor, $sector, $lat, $lon, $codigo, $cci, $vendedor, $obs);
echo $rspta ? "Persona actualizada" : "Persona no se pudo actualizar";
*/

if($descuento==''){ $descuento='0'; }
if($sector==''){ $sector='0'; }
$descuentom='0';

$sql="UPDATE persona SET tipo_persona='$tipo_persona', nombre='$nombre', tipo_documento='$tipo_documento', txtID_CLIENTE='$txtID_CLIENTE', direccion='$direccion', telefono='$telefono', email='$email', email2='$email2', txtRAZON_SOCIAL='$txtRAZON_SOCIAL', descuento='$descuento', descuentom='$descuentom', sector='$sector', lat='$lat', lon='$lon', codigo='$codigo', cci='$cci', vendedor='$vendedor', obs='$obs', pais='$pais', ciudad='$ciudad'  WHERE idpersona='$idpersona'";

//echo $sql;

$id=ejecutarConsulta($sql);
	
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

$jsondata = array();
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'Error no se pudo modificar.';

if($id!=''){
$jsondata['estado'] = '1';
$jsondata['id'] = $id;
$jsondata['mensaje'] = 'Cliente: '.$nombre.', modificado con exito';
$jsondata['doc'] =$tipo_documento;
$jsondata['nombre'] = $nombre;
}
		
echo json_encode($jsondata);

}

break;


case 'mostrarcliente':
    
		$sql="SELECT * FROM persona WHERE idpersona='$idpersona' ";
		$rspta=ejecutarConsultaSimpleFila($sql);
		
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
		
 		echo json_encode($rspta);
break;

//PROUCTOS
//PROUCTOS

case 'listarproducto':
		
$sql3="SELECT *FROM config WHERE id='$id' ";
$fa= ejecutarConsultaSimpleFila($sql3);

		$idlocalfinal=''; $idlocal2='';	
if($fa['articulo']=='LOCAL'){ $idlocalfinal=' AND idlocal="'.$idlocal.'" '; $idlocal2=' AND a.idlocal="'.$idlocal.'" ';  }

		
$data= Array();

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
	
## Search 
$searchQuery = " ";
		
if($searchValue != ''){
   	$searchQuery .= " and ( txtCOD_ARTICULO LIKE '%".$_POST['search']['value']."%' ";
   	$searchQuery .=" OR txtDESCRIPCION_ARTICULO LIKE '%".$_POST['search']['value']."%' ";
   	$searchQuery .=" OR codigo LIKE '%".$_POST['search']['value']."%' ) ";
	//$searchQuery .=" OR txtCOD_ARTICULO IN (SELECT cod_articulo FROM articulo_serie WHERE serie LIKE '%".$_POST['search']['value']."%' OR lote LIKE '%".$_POST['search']['value']."%' ) ";
	
	//txtCOD_ARTICULO IN (SELECT cod_articulo FROM articulo_serie WHERE serie LIKE '%F0011%')
}
		
$records= ejecutarConsultaSimpleFila("select count(*) as allcount from articulo WHERE idempresa='$id'  $idlocalfinal ");
$totalRecords = $records['allcount'];

## Total number of record with filtering
$records= ejecutarConsultaSimpleFila("select count(*) as  allcount  from articulo WHERE idempresa='$id' $idlocalfinal  ".$searchQuery);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select a.txtCOD_ARTICULO, a.txtDESCRIPCION_ARTICULO, a.codigo, a.condicion, a.resaltado, c.nombre, a.exonerado_igv, a.mayor, a.precio, a.precio_mayor, comision, comisionm, comisionmp, a.medida, a.existencia, a.idproveedor, a.precio_compra, a.idcategoria from articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.idempresa='$id' $idlocal2   ".$searchQuery." ORDER BY txtCOD_ARTICULO DESC limit ".$row.",".$rowperpage." ";
$empRecords = ejecutarConsulta($empQuery);
		

while ($reg=$empRecords->fetch_object()){


$botones='
      <div class="input-group-btn">
        <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="menuvencli" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i><span class="caret"></span> OPC
        </button>
        <ul class="dropdown-menu" aria-labelledby="menuvencli">
';

$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="mostrar('.$reg->txtCOD_ARTICULO.', 1)" ><i class="fa fa-pen"></i> Editar</a>';
			
$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="addserie(\''.$reg->txtCOD_ARTICULO.'\')" ><i class="fa fa-barcode"></i> Series/Lotes</a>';

$sqlpr="SELECT *FROM persona WHERE idpersona='$reg->idproveedor' ";
$lab= ejecutarConsultaSimpleFila($sqlpr);
			
$sqllt="SELECT *FROM articulo_serie WHERE cod_articulo='$reg->txtCOD_ARTICULO' AND estado='1' ORDER BY id ASC ";
$lot= ejecutarConsultaSimpleFila($sqllt);
	
$resultado='';
$lote='';
$fecha_vto='';

$proveedores='';
if($lab){
$proveedores = substr($lab['nombre'], 0, 5);
}
if($lot){	
$lote=$lot['lote'];
$fecha_vto=$lot['fecha_vto'];
}			
	
			
$sqlst="SELECT *FROM articulo_stock WHERE idarticulo='$reg->txtCOD_ARTICULO' AND idlocal='$idlocal' ";
$st= ejecutarConsultaSimpleFila($sqlst);

$stockini='0.00';
$precioini='0.00';	
if($st){
$stockini=$st['stock_inicial'];
$precioini=$st['precio_inicial'];	
}
	
$sqlart="SELECT DATE(d.fecha) AS fecha, d.precio_compra, d.txtCANTIDAD_ARTICULO FROM detalle_ingreso d LEFT JOIN ingreso i ON d.idingreso=i.idingreso WHERE d.txtCOD_ARTICULO='$reg->txtCOD_ARTICULO' AND d.idlocal='$idlocal' AND serie_comprobante='GI001' AND nivel='0' ";
$art= ejecutarConsultaSimpleFila($sqlart);

$fechaini='0000-00-00';	
if($art){
$fechaini=$art['fecha'];	
}
	
$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="stockinicial(\''.$reg->txtCOD_ARTICULO.'\', \''.$stockini.'\', \''.$precioini.'\', \''.$fechaini.'\')" ><i class="fa fa-list"></i> Stock inicial</a>';
			
$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="artrelacioados(\''.$reg->txtCOD_ARTICULO.'\')" ><i class="fa fa-book"></i> Paquetes</a>';
			
$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="imprimircode('.$reg->txtCOD_ARTICULO.')" ><i class="fa fa-print"></i> Imprimir</a>';
			
			
if($reg->condicion=='0'){ 
	
$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="activar('.$reg->txtCOD_ARTICULO.')" ><span class="glyphicon glyphicon-ok-sign"></span> Activar</a>';	
	
}else{ 
	
$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="desactivar('.$reg->txtCOD_ARTICULO.')" ><span class="glyphicon glyphicon-ok-sign"></span> Desactivar</a>';
	
	
}
			
$sqlcat="SELECT * FROM categoria WHERE idcategoria='$reg->idcategoria' ";
$cate=ejecutarConsultaSimpleFila($sqlcat);	
	
$marca='GENERICA';
if($cate){
$marca=$cate['nombre'];
}
	
$stockf='0.00';
if($st){
$stockf=$st['stock'];
}

	
$botones.='</ul></div>';
			
 			$data[]=array(
				"0"=>$reg->txtCOD_ARTICULO,
 				"1"=>$botones,
				"2"=>$reg->txtCOD_ARTICULO,
 				"3"=>$reg->codigo,
				"4"=>$marca,
 				"5"=>$reg->txtDESCRIPCION_ARTICULO,
 				"6"=>$stockf,
				"7"=>$reg->precio,
				"8"=>$reg->precio_compra,
				"9"=>$proveedores,
 				"10"=>$lote,
 				"11"=>$fecha_vto,
				
 				"12"=>($reg->condicion)?'<div class="label label-success">Activado</div>':
 				'<div class="label label-danger">Desactivado</div>'
 				);
 		}
		
$json_data = array(
        "draw"            =>intval($draw),
        "recordsTotal"    =>$totalRecords,
        "recordsFiltered" =>$totalRecordwithFilter,
        "data"            => $data   // total data array
        );
		
		
 		echo json_encode($json_data);


break;



case 'guardararticulo':
		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'EL CÓDIGO DEL PRODUCTO NO EXISTE';		

//echo $tipoac;

if ($tipoac=='2'){
		
$sql="SELECT * FROM articulo  WHERE codigo='$codigo' AND idempresa='$id' ";
$art=ejecutarConsultaSimpleFila($sql);
	
$jsondata['codigo'] =$codigo;
$jsondata['tipoac'] =$tipoac;
$jsondata['nombre'] =$txtDESCRIPCION_ARTICULO;


if($art&&$codigo!=''){		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'EL CÓDIGO DEL PRODUCTO YA EXISTE';
}else{
//$rspta=$articulo->insertar($cat);

$jsondata['estado'] = '1';

$sql3="SELECT *FROM config WHERE id='$id' ";
$fa= ejecutarConsultaSimpleFila($sql3);


if($idcategoria=='null'){ $idcategoria='0'; }
if($idmarca=='null'){ $idmarca='0'; }
if($pactivo=='null'){ $pactivo=''; }
if($precio_porcentaje==''){ $precio_porcentaje='1'; }
if($canjepuntos==''){ $canjepuntos='0'; }
if($canjecobro==''){ $canjecobro='0'; }

if($resaltado==1){
$sql="UPDATE articulo SET resaltado='0'  WHERE idempresa='$id' ";
ejecutarConsulta($sql);
}

$sql="INSERT INTO articulo VALUES(NULL, '$cat', '$id', '$idcategoria', '$idmarca', '$linea', '$sublinea', '$subfamilia', '$medida', '$sanitario', '$pactivo', '$idlocal', '$idproveedora', '$codigo', '$codigos', '$existencia', '$txtDESCRIPCION_ARTICULO', '$stock', '$stockmin', '$stockmax', '$precio', '$oferta', '$moneda', '$precio_porcentaje', '$precioc', '$ctimayor', '$pmayor', '$exonerado', '$comision', '$comisionm', '$comisionmp', '$bolsa', '$ctacompras', '$ctaventas', '$canje', '$canjepuntos', '$canjecobro', '$imagen', '1', '$resaltado') ";
$idventanew=ejecutarConsulta_retornarID($sql);

$jsondata['idventanew'] =$idventanew;

//echo $sql;

/*
echo 'stock'.$stock.'|';
echo 'precio'.$precio.'|';
*/

$sql="INSERT INTO articulo_stock VALUES (NULL, '$id', '$fa[tipo]', '$idventanew', '0', '$idlocal', '$stock', '$precioc', '$stock')";
ejecutarConsulta($sql);

$jsondata['mensaje'] = 'ARTÍCULO GUARDADO';

}	
		
}else {
	
$sql="SELECT * FROM articulo  WHERE codigo='$codigo' AND idempresa='$id' AND txtCOD_ARTICULO!='$txtCOD_ARTICULO' ";
$art=ejecutarConsultaSimpleFila($sql);	

if($art&&$codigo!=''){		
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'EL CÓDIGO DEL PRODUCTO YA EXISTE';
}else{
	
if($codigo==''){ $codigo=$txtCOD_ARTICULO; }

$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'ARTÍCULO ACTUALIZADO';

$sqlas="SELECT *FROM articulo_stock WHERE idarticulo='$txtCOD_ARTICULO' AND idlocal='$idlocal' ";
$as= ejecutarConsultaSimpleFila($sqlas);
		
if($as){
$sql="UPDATE articulo_stock SET stock='$stock' WHERE idarticulo='$txtCOD_ARTICULO' AND idlocal='$idlocal' ";
ejecutarConsulta($sql);	
}else{

$sql3="SELECT *FROM config WHERE id='$id' ";
$fa= ejecutarConsultaSimpleFila($sql3);

$sql="INSERT INTO articulo_stock VALUES (NULL, '$id', '$fa[tipo]', '$txtCOD_ARTICULO', '0', '$idlocal', '$stock', '$precioc', '$stock')";
ejecutarConsulta($sql);	
}

if($idcategoria=='null'){ $idcategoria='0'; }
if($idmarca=='null'){ $idmarca='0'; }

//echo $precioc.'--|';
//resaltado

if($resaltado==1){
$sql="UPDATE articulo SET resaltado='0'  WHERE idempresa='$id' ";
//echo $sql;
ejecutarConsulta($sql);
}

$sql="UPDATE articulo SET idcategoria='$idcategoria', marca='$idmarca', codigo='$codigo', txtDESCRIPCION_ARTICULO='$txtDESCRIPCION_ARTICULO', precio='$precio', mayor='$ctimayor', precio_mayor='$pmayor', imagen='$imagen',  exonerado_igv='$exonerado', comision='$comision', comisionm='$comisionm', comisionmp='$comisionmp', codigosunat='$codigos', idproveedor='$idproveedor', sanitario='$sanitario', precio_compra='$precioc', linea='$linea', sublinea='$sublinea', subfamilia='$subfamilia', ctacompras='$ctacompras', ctaventas='$ctaventas', bolsa='$bolsa', stockmin='$stockmin', stockmax='$stockmax', existencia='$existencia', medida='$medida', idlocal='$idlocal', precio_porcentaje='$precio_porcentaje', moneda='$moneda', principioactivo='$pactivo', preciooferta='$oferta', canje='$canje', canjepuntos='$canjepuntos', canjecobro='$canjecobro', resaltado='$resaltado'  WHERE txtCOD_ARTICULO='$txtCOD_ARTICULO' ";

//echo $sql;

ejecutarConsulta($sql);
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'ARTÍCULO ACTUALIZADO';

}

}

echo json_encode($jsondata);
		
break;

case 'mostrarproducto':
	
$sqla="SELECT * FROM  articulo_stock WHERE idarticulo ='$txtCOD_ARTICULO' AND idlocal='$idlocal' ";
$stock=ejecutarConsultaSimpleFila($sqla);
		
	if($stock){	
	$sql="SELECT a.txtCOD_ARTICULO, a.comision, a.idcategoria, a.bolsa, a.marca, a.medida, a.codigo, a.txtDESCRIPCION_ARTICULO, a.precio, a.mayor, a.precio_mayor, a.exonerado_igv, a.imagen, a.condicion, a.resaltado, s.stock AS stock, a.stockmin, a.stockmax, a.comisionm, a.comisionmp, a.codigosunat, a.sanitario, a.idproveedor, a.linea, a.sublinea, a.subfamilia, a.precio_compra, a.ctaventas, a.ctacompras, a.existencia, a.idlocal, a.precio_porcentaje, a.principioactivo, a.preciooferta, a.canje, a.canjepuntos, a.canjecobro FROM articulo a LEFT JOIN articulo_stock s ON a.txtCOD_ARTICULO=s.idarticulo  WHERE a.txtCOD_ARTICULO='$txtCOD_ARTICULO' AND s.idlocal='$idlocal' ";
}else{
	$sql="SELECT a.txtCOD_ARTICULO, a.comision, a.idcategoria, a.bolsa, a.marca, a.medida, a.codigo, a.txtDESCRIPCION_ARTICULO, a.precio, a.mayor, a.precio_mayor, a.exonerado_igv, a.imagen, a.condicion, a.resaltado, s.stock AS stock, a.stockmin, a.stockmax, a.comisionm, a.comisionmp, a.codigosunat, a.sanitario, a.idproveedor, a.linea, a.sublinea, a.subfamilia, a.precio_compra, a.ctaventas, a.ctacompras, a.existencia, a.idlocal, a.precio_porcentaje, a.principioactivo, a.preciooferta, a.canje, a.canjepuntos, a.canjecobro FROM articulo a LEFT JOIN articulo_stock s ON a.txtCOD_ARTICULO=s.idarticulo  WHERE a.txtCOD_ARTICULO='$txtCOD_ARTICULO' ";	
	}
	$rspta=ejecutarConsultaSimpleFila($sql);
	
 		echo json_encode($rspta);
break;



case 'dashboard':
	
$sql3="SELECT COUNT(*) AS totalclientes  FROM articulo WHERE idempresa='$id' ";
$fa= ejecutarConsultaSimpleFila($sql3);

$sqlnn="SELECT SUM(txtTOTAL) AS totalventas FROM venta WHERE YEAR(txtFECHA_DOCUMENTO)='$anioactual' AND MONTH(txtFECHA_DOCUMENTO)='$mesactual' AND idempresa='$id'  ";
$mostrarnn= ejecutarConsultaSimpleFila($sqlnn);

$data[]=array(
"clientes"=>$fa['totalclientes'],
"ventas"=>number_format($mostrarnn['totalventas'], 2),
);


 echo json_encode($data);

break;

//compras
//compras
case 'listarcompras':
		
		$cat=$_GET['cat'];
		$fechaini=$_GET['fechaini'];
		$fechafin=$_GET['fechafin'];
		$id=$_GET['id'];
 		//Vamos a declarar un array
$data= Array();
$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha,i.idproveedor,p.nombre as proveedor,u.idusuario,u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.impuesto,i.total_compra,i.estado, i.idlocal, i.tipopago FROM ingreso i LEFT JOIN persona p ON i.idproveedor=p.idpersona LEFT JOIN usuario u ON i.idusuario=u.idusuario WHERE DATE(fecha_hora)>='$fechaini' AND DATE(fecha_hora)<='$fechafin' AND i.idempresa='$id' AND i.nivel='$cat' AND tipo_comprobante!='17' AND tipo_comprobante!='16' ORDER BY i.idingreso desc";

//echo $sql;
$rspta=ejecutarConsulta($sql);	
while ($reg=$rspta->fetch_object()){

$botones='
      <div class="input-group-btn">
        <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="menuvencli" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i><span class="caret"></span> OPC
        </button>
        <ul class="dropdown-menu" aria-labelledby="menuvencli">
';

$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="tipopago('.$reg->idingreso.', 1, \''.$reg->tipopago.'\')">
<i class="fa fa-shopping-cart"></i> Pagos</a>';	
			
if($reg->estado=='0'){ 
$estado='<div class="label label-success">Activo</div>'; 
$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="anular('.$reg->idingreso.')"><i class="fa fa-trash"></i> Anular</a>';
	
if($_COOKIE['configuracion']==1){
$botones.='<a class="dropdown-item" href="javascript:void(0)" onclick="mostrar('.$reg->idingreso.')" ><i class="fa fa-eye"></i> Editar</a>';
	
}	
	
}else{ 
$estado='<div class="label label-secondary">Anulado</div>'; 
}
			
if($reg->tipo_comprobante=='04'||$cat=='2'){					
$botones.='<a class="dropdown-item" target="_blank" href="'.media().'/plugins/dompdf/liquidacion-compra.php?id='.$reg->idingreso.'" ><i class="fa fa-file-pdf" aria-hidden="true"></i> Ver PDF</a>';
}else if($reg->tipo_comprobante=='201'){
$botones.='<a class="dropdown-item" target="_blank" href="'.media().'/plugins/dompdf/solicitud-pedido.php?id='.$reg->idingreso.'" ><i class="fa fa-file-pdf" aria-hidden="true"></i> Ver PDF</a>';
}else{
$botones.='<a class="dropdown-item" target="_blank" href="'.media().'/plugins/dompdf/vale-ingreso.php?id='.$reg->idingreso.'" ><i class="fa fa-file-pdf" aria-hidden="true"></i> Ver PDF</a>';	
}
			
$botones.='</ul></div>';
									
if($reg->tipo_comprobante=='01'){ $tipo='FACTURA'; 
}else if($reg->tipo_comprobante=='03'){ $tipo='BOLETA'; 
}else if($reg->tipo_comprobante=='04'){ $tipo='LIQUIDACIÓN DE COMPRA'; 
}else if($reg->tipo_comprobante=='11'){ $tipo='TRANSFERENCIA ENTRE ALMACENES'; 
}else if($reg->tipo_comprobante=='99'){ $tipo='VALE DE INGRESO'; 	
}else{ $tipo='OTROS';  }
			
$sql2="SELECT *FROM sucursal WHERE id='$reg->idlocal' ";
$mos= ejecutarConsultaSimpleFila($sql2);
			
$data[]=array(
 				"0"=>$botones,
	"1"=>$reg->idingreso,
	"2"=>$mos['sucursal'],
 				"3"=>$reg->fecha,
 				"4"=>$reg->proveedor,
 				"5"=>$reg->usuario,
 				"6"=>$tipo,
 				"7"=>$reg->serie_comprobante.'-'.$reg->num_comprobante,
 				"8"=>$reg->total_compra,
 				"9"=>$estado
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

break;




case 'guardarcompraimagen':

/*
$porciones = explode("|", $proveedores);
$tipodocumento=$porciones[0];
$numerodderuc=$porciones[1];
$texto=$porciones[2];
*/



$codigotransaccion=$documento_ruc.'-'.$documento_tipo.'-'.$cnumerodocumento;
$origen='01';
$origenfinal='60001';
$doc_relaciona='';
$docmodifica_tipo='';
$docmodifica='';
$modifica_motivo='';
$modifica_motivod='';
$exonerado='0.00';
$estadosunat='1';
$inafecta=0;

$subtotal2=0;
$igv2=0;
$total2=0;

$subtotal3=0;
$igv3=0;
$total3=0;
$estado_sistema=0;

$detraccion_operacion='';

$conf=" SELECT * from config WHERE id='$idempresacompra' ";
$config=ejecutarConsultaSimpleFila($conf);

$idempresa=1;
if($config){ $idempresa=$config['id']; }

$idcliente=cliente($razonsocialemisor, $documento_ruc, 'Cliente', '', '', '', $id);

/**/
$fecha_vencimiento=$documento_fecha;
$totalicvper=0;

$subtotal = str_replace(',', '', $subtotalcompra);
$igv = str_replace(',', '', $igvcompra);
$totalventa = str_replace(',', '', $totalventa);

//idusuario
//idlocal

$idempresa=$idempresacompra;
$beta=$config['tipo'];
$nivel=0;
$idproveedor=$idcliente;
$ccostos='0';
$controlpresupuestal='';
$tipopago='CONTADO';
$mediopago='008';
$impuesto=$igv;
$iscporcentaje=0;
$isc=0;
$otrosimpuestos=0;
$total_compra=$totalventa;
$exonerado=0;
$inafecto=0;
$gratuito=0;
$detracciones=0;
$iddetraccion=0;
$retenciones=0;
$percepcion=0;
$otrosgastos=0;
$obs='';
$idcaja=0;

$tipodocumento='01';

if($tipodocumento=='BOLETA DE VENTA ELECTRONICA'){
$tipodocumento='03';
}
if($tipodocumento=='BOLETA ELECTRONICA'){
$tipodocumento='03';
}

$insetventa=" INSERT INTO ingreso VALUES (NULL, '$nivel', '$idempresa', '$beta', '$idproveedor', '$idusuariocompra', '$idlocalcompra', '$ccostos', '$controlpresupuestal', '$tipodocumento', '$documento_serie', '$documento_numero', '$tipopago', '$mediopago', '$comprobantemoneda', '$documento_fecha', '$fecha_vencimiento', '$impuesto', '$subtotal', '$igv', '$iscporcentaje', '$isc', '$otrosimpuestos', '$total_compra', '$exonerado', '$inafecto', '$gratuito', '$detracciones', '$iddetraccion', '$retenciones', '$percepcion', '$otrosgastos', '$obs', '$idcaja', '$estado_sistema') ";

$idventa=ejecutarConsulta_retornarID($insetventa);

if($idventa){
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'COMPROBANTE REGISTRADO';
$jsondata['numero'] = $documento_numero;
$jsondata['idcompra'] = $idventa;
$jsondata['documento_serie'] = $documento_serie;
$jsondata['cnumerodocumento'] = $cnumerodocumento;

}else{
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'COMPROBANTE NO SE PUDO REGISTRAR';
$jsondata['numero'] = $documento_numero;
$jsondata['sql'] = $insetventa;

}

/*
$jsondata['sql'] = $insetventa;
$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'COMPROBANTE NO SE PUDO REGISTRAR';
$jsondata['numero'] = '00000000';
*/

header('Content-Type: application/json; charset=utf-8');
echo json_encode($jsondata);

break;



case 'guardaingresos':

$porigv=$porcentajeigv;
$porigv=$porigv/100;
$porigv=$porigv+1.00;

$fechadoc=$fecha_hora;
$fechadocf=$fecha_hora;

$fecha_hora=$fecha_hora.' '.date('H:i:s');

if($idproveedor==''){ $idproveedor='0'; }
if($impuesto==''){ $impuesto='0'; }

//Implementamos un método para insertar registros
//public function insertar($idingreso, $idproveedor, $idusuario, $idlocal, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_compra, $cat, $pago, $medio, $moneda, $igvtot, $subtot, $condiciones, $obs, $destino, $ccostos, $otrosimport, $otros, $exoneradof, $infecto, $gratuito, $txtCOD_ARTICULO, $txtCANTIDAD_ARTICULO, $precio_compra, $otros, $precio_venta, $precio_mayor, $fechvto, $series, $igv, $subtotal, $titd, $otrosd, $afectoigv){
	
$fechaActual = date('Y-m-d');
$periodo=date("Y-m",strtotime($fecha_hora));

$jsondata['estado'] = '0';
$jsondata['mensaje'] = 'DOCUMENTO NO SE PUDO PROCESAR';
$jsondata['idproveedor'] =$idproveedor;
	
$conf="SELECT * from config WHERE id='$id' ";
$config=ejecutarConsultaSimpleFila($conf);
	

if($idingreso!=''){
	
$idingresonew=$idingreso;

if($total_compra==''){ $total_compra='0'; }
if($otrosimport==''){ $otrosimport='0'; }
if($infecto==''){ $infecto='0'; }

$sqlup="UPDATE ingreso SET idproveedor='$idproveedor', ccostos='$ccostos', tipopago='$pago', mediopago='$medio', moneda='$moneda', fecha_hora='$fecha_hora', fechaven='$fechavto', impuesto='$impuesto', subtotal='$subtot', igv='$igvtot',  total_compra='$total_compra', exonerado='$exoneradof', inafecto='$infecto', gratuito='$gratuito', detracciones='$montodetraccion', iddetraccion='$iddetraccion', retenciones='$retencion', otrosgastos='$otrosimport', obs='$obs', percepcion='$percepcion', controlpresupuestal='$controlpresupuestal', tipo_comprobante='$tipo_comprobante', isc='$isctot', iscporcentaje='$isc', otrosimpuestos='$otrosimpuestos', num_comprobante='$num_comprobante'  WHERE idingreso='$idingreso' ";
//echo $sqlup;
ejecutarConsulta($sqlup);
	

$isc=isset($_POST["isc"])? ($_POST["isc"]):"0";
$otrosimpuestos=isset($_POST["otrosimpuestos"])? ($_POST["otrosimpuestos"]):"0";
	
$sqlc="DELETE FROM detalle_ingreso WHERE idingreso='$idingreso' ";
ejecutarConsulta($sqlc);
	
$sqlc="DELETE FROM ingreso_importacion WHERE idingreso='$idingreso' ";
ejecutarConsulta($sqlc);
	
$sqlc="DELETE FROM cardex_detalle WHERE iddocumento='$idingreso' AND operacion='02' AND tipooperacion='1' AND nivel='1' ";
ejecutarConsulta($sqlc);

}else{
	
if($total_compra==''){ $total_compra='0'; }
if($otrosimport==''){ $otrosimport='0'; }
if($infecto==''){ $infecto='0'; }

$idcaja='0';

$sql="INSERT INTO ingreso VALUES (NULL, '$cat', '$id', '$config[tipo]', '$idproveedor', '$idusuario', '$destino', '$ccostos', '$controlpresupuestal', '$tipo_comprobante', '$serie_comprobante', '$num_comprobante', '$pago', '$medio', '$moneda', '$fecha_hora', '$fechavto', '$impuesto', '$subtot', '$igvtot', '$isc', '$isctot', '$otrosimpuestos', '$total_compra', '$exoneradof', '$infecto', '$gratuito', '$montodetraccion', '$iddetraccion', '$retencion', '$percepcion', '$otrosimport', '$obs', '$idcaja', '0')";
//return ejecutarConsulta($sql);
//echo $sql;
$idingresonew=ejecutarConsulta_retornarID($sql);
	
}

$num_elementos=0;
$sw=true;

	
//$jsondata['mensaje'] = 'DOCUMENTO NO SE PUDO PROCESAR';

$detalle2 = isset($_POST['detalle2']) ? $_POST['detalle2'] : array();

// Verificar si $detalle2 es un array y tiene elementos
if (is_array($detalle2) && count($detalle2) > 0) {
    // Iterar sobre cada elemento del array
    for ($i = 0; $i < count($detalle2); $i++) {
        // Asignar valores con verificación de existencia
        $otrosd = isset($detalle2[$i]["otros"]) ? $detalle2[$i]["otros"] : "0";
        $igv = isset($detalle2[$i]["igv"]) ? $detalle2[$i]["igv"] : "0";
        $txtCOD_ARTICULO = isset($detalle2[$i]["txtCOD_ARTICULO"]) ? $detalle2[$i]["txtCOD_ARTICULO"] : "0";
        $precio_compra = isset($detalle2[$i]["precio_compra"]) ? $detalle2[$i]["precio_compra"] : "0";
        $txtCANTIDAD_ARTICULO = isset($detalle2[$i]["cantidad"]) ? $detalle2[$i]["cantidad"] : "0";
        $subtotal = isset($detalle2[$i]["subtotal"]) ? $detalle2[$i]["subtotal"] : "0";
        $precio_mayor = isset($detalle2[$i]["precio_mayor"]) ? $detalle2[$i]["precio_mayor"] : "0";
        $precio_venta = isset($detalle2[$i]["precio_venta"]) ? $detalle2[$i]["precio_venta"] : "0";
        $titd = isset($detalle2[$i]["nombreproducto"]) ? $detalle2[$i]["nombreproducto"] : "";
        $afectoigv = isset($detalle2[$i]["afectoigv"]) ? $detalle2[$i]["afectoigv"] : "0";
        $fechvto = isset($detalle2[$i]["fechvto"]) ? $detalle2[$i]["fechvto"] : "0000-00-00";
        $series = isset($detalle2[$i]["series"]) ? $detalle2[$i]["series"] : "0";
        $cuentacompra = isset($detalle2[$i]["cuentacompra"]) ? $detalle2[$i]["cuentacompra"] : "90001";
        $ccostos = isset($detalle2[$i]["ccostos"]) ? $detalle2[$i]["ccostos"] : "0";
        $controlpresupuestal = isset($detalle2[$i]["controlpresupuestal"]) ? $detalle2[$i]["controlpresupuestal"] : "0";
        
        // Aquí puedes procesar cada fila de datos
        // Por ejemplo: insertar en base de datos, calcular totales, etc.


echo $txtCANTIDAD_ARTICULO.'|';

if($ccostos=='undefined'){
	$ccostos='0';	
}
if($controlpresupuestal=='undefined'){
	$controlpresupuestal='0';	
}

$titd=str_replace("'", "\'", $titd);
	
if($idingreso==''){
if($cat==0){

$totalotros='0.00';
	
if($otrosd>'0.0000000'){	
$totalotros=$otrosd;	
if($igv>'0.00'){
$igvotros=$otrosd/0.18;
$totalotros=$otrosd+$igvotros;
}
}
		
$precompra=round($precio_compra+$totalotros, 2);
	
$sql22="SELECT * FROM articulo_stock WHERE idarticulo='$txtCOD_ARTICULO' AND idlocal='$destino' ";
$faa= ejecutarConsultaSimpleFila($sql22);
	
if($faa){

if($tipo_comprobante=='07'){
$sqlp="UPDATE articulo_stock SET stock=stock-'$txtCANTIDAD_ARTICULO' WHERE idarticulo='$txtCOD_ARTICULO' AND idlocal='$destino' ";
ejecutarConsulta($sqlp);
}else{
$sqlp="UPDATE articulo_stock SET stock=stock+'$txtCANTIDAD_ARTICULO' WHERE idarticulo='$txtCOD_ARTICULO' AND idlocal='$destino' ";
ejecutarConsulta($sqlp);

$sqlp="UPDATE articulo SET precio_compra='$precompra' WHERE txtCOD_ARTICULO='$txtCOD_ARTICULO' ";
ejecutarConsulta($sqlp);

}
}else{

$sqls="INSERT INTO articulo_stock VALUES (NULL, '$_COOKIE[id]', '$config[tipo]', '$txtCOD_ARTICULO', '0', '$destino', '$txtCANTIDAD_ARTICULO', '$precompra', '$txtCANTIDAD_ARTICULO')";
ejecutarConsulta($sqls);
	
}

}
}

$sqlart="SELECT * FROM articulo WHERE txtCOD_ARTICULO='$txtCOD_ARTICULO' ";
$art= ejecutarConsultaSimpleFila($sqlart);
	
$sql22="SELECT * FROM articulo_stock WHERE idarticulo='$txtCOD_ARTICULO' AND idlocal='$destino' ";
$faa= ejecutarConsultaSimpleFila($sql22);			
			
$sqlcost="SELECT SUM(txtCANTIDAD_ARTICULO) AS cti, SUM(subtotal) AS tot  FROM detalle_ingreso WHERE txtCOD_ARTICULO='$txtCOD_ARTICULO' AND idlocal='$destino'  AND txtCANTIDAD_ARTICULO!='0.00000' AND estado='0' ";
$cost= ejecutarConsultaSimpleFila($sqlcost);
		
$subtotalfinal=$subtotal;
	
if($otrosd>'0.0000000'){
$multicant=$otrosd*$txtCANTIDAD_ARTICULO;
$subtotalfinal=$subtotal+$multicant;
}
		
	
/*-------KARDEX------*/	
$prcompra=$precio_compra;	
$importe=$precio_compra*$txtCANTIDAD_ARTICULO;
$otrosgastos=$otrosd;

if($moneda=='USD'){
$sqlca="SELECT *FROM tipo_cambio WHERE fecha='$fechadocf' ";
$conca= ejecutarConsultaSimpleFila($sqlca);
$cambioventa='1';
if($conca['venta']){ $cambioventa=$conca['venta']; }
	
$prcompra=$cambioventa*$precio_compra;
$importe=$cambioventa*$importe;
if($otrosd!='0.0000000'){
$otrosgastos=$otrosd*$cambioventa;
}	
}
	
if($afectoigv=='0'){
$pscinigv=$prcompra/$porigv;
$importe=$importe/$porigv;
}else{
$pscinigv=round($prcompra, 7);
$importe=round($importe, 7);	
}

$pscinigv=$pscinigv+$otrosgastos;
	
$totkardexini=$pscinigv*$txtCANTIDAD_ARTICULO;
$totkardex=$pscinigv*$txtCANTIDAD_ARTICULO;

if($totkardex>0) {
$preciopromedio=$totkardex/$txtCANTIDAD_ARTICULO;
}else{
$preciopromedio=$precio_compra;
}

$ctikardex=$txtCANTIDAD_ARTICULO;
	
$mensaje='|SUMASTOCK:'.$ctikardex.'|';
	
$preciopromedio=round($preciopromedio, 7);	
$totkardex=round($totkardex, 7);
$pscinigv=round($pscinigv, 7);	
	
if($tipo_comprobante=='07'){	
$preciopromedio=round($preciopromedio, 7);	
$totkardex=round($totkardex, 7);
$pscinigv=round($pscinigv, 7);	
}
	
/*-------KARDEX------*/		
	
$preciocompra=$precio_compra;
$totalcompra=$precio_compra;
$otrosgastos='0';	

$preciocompra=round($preciocompra+$otrosgastos, 5);
$totalcompra=round($preciocompra*$txtCANTIDAD_ARTICULO, 5);
	
$sumcti=round($cost['cti']+$txtCANTIDAD_ARTICULO, 2);
$sumtot=$cost['tot']+$subtotalfinal;
if($sumtot>0&&$sumcti>0){
$preprom=round($sumtot/$sumcti, 2);
}else{
$preprom=$preciocompra;
}
$fechadoc=$fechvto;	
if($fechvto==''){ $fechadoc='0000-00-00';}
			
$total=round($subtotal+$igv, 2);
if($faa){ $stockact=$faa['stock']; }else{ $stockact=$txtCANTIDAD_ARTICULO; }

$precioventa=$precio_venta;	

	
if($preprom=='0'){
$preprom=$precio_compra;
}

//echo $txtCANTIDAD_ARTICULO.'--|';

$sql_detalle = "INSERT INTO detalle_ingreso VALUES (NULL, '$id', '$config[tipo]', '$idingresonew', '$destino', '$txtCOD_ARTICULO', '$titd', '$txtCANTIDAD_ARTICULO', '$subtotal', '$igv', '$total', '$precio_compra', '$otrosd',  '$precioventa', '$preprom', '$stockact', '$afectoigv', '$fechadoc', '$fecha_hora', '$cuentacompra', '$ccostos', '$controlpresupuestal', '0')";

//echo $sql_detalle;

$idingresodet=ejecutarConsulta_retornarID($sql_detalle);

	
$num_elementos=$num_elementos + 1;
			
    }
}	
	
	
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'DOCUMENTO GUARDADO';
$jsondata['id'] =$idingresonew;	
	

echo json_encode($jsondata);




break;

case 'mostraringreso':
	
//v.iddetraccion, v.detraccion, dt.codigo AS codidetracciones, dt.porcentaje, 
//, i.porcentaje AS porcentajeigv
$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha, i.fechaven, i.idproveedor, p.nombre as proveedor, p.txtID_CLIENTE as rucproveedor, u.idusuario,u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado, i.moneda, i.tipopago, i.mediopago, i.ccostos, i.controlpresupuestal, i.iddetraccion, i.detracciones, dt.codigo AS codidetracciones, i.igv, i.subtotal FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario LEFT JOIN detracciones dt ON dt.id=i.iddetraccion WHERE i.idingreso='$idingreso'  ";

//echo $sql;

$rspta=ejecutarConsultaSimpleFila($sql);
		
//$rspta=$ingreso->mostrar($idingreso);
//Codificar el resultado utilizando json
echo json_encode($rspta);

break;

case 'mostrardetalleingreso':

//$id=$_GET['id'];
		$responce=[];
		
		
		
		
$sql="SELECT * FROM detalle_ingreso where idingreso='$idingreso' ";
$rspta =ejecutarConsulta($sql);
		
		$total=0;
$i=0;
while ($reg = $rspta->fetch_object()){
					
$rows[$i]['id']=$reg->txtCOD_ARTICULO;
$rows[$i]['cell']=array($reg->txtCOD_ARTICULO, $reg->txtCANTIDAD_ARTICULO, $reg->subtotal, $reg->igv, $reg->total, $reg->descripcion, $reg->precio_compra, $reg->precio_venta, $reg->subtotal, $reg->igv, $reg->total, $reg->iddetalle_ingreso, $reg->otrosgastos, $reg->tipo);
$responce=$rows;
$i++;		
}
		
echo json_encode($responce);
		

break;

//compras
//compras



//REPORTES
//REPORTES

case 'reportescomprasventas':

//$id=$_GET['id'];

$responce=[];

$sql="SELECT SUM(txtTOTAL) as totalventa, SUM(txtSUB_TOTAL) as subtotalventa, SUM(txtIGV) igvventa, count(*)cantidadventas FROM venta where idempresa='$id' AND YEAR(txtFECHA_DOCUMENTO)='$anio' AND MONTH(txtFECHA_DOCUMENTO)='$mes'  ";
//echo $sql;
$sqlventa= ejecutarConsultaSimpleFila($sql);

$responce['ventas']=$sqlventa;

$sql="SELECT SUM(total_compra) as totalcompra, SUM(subtotal) as subtotalcompra, SUM(igv) igvcompra, count(*)cantidadcompras FROM ingreso where idempresa='$id' AND YEAR(fecha_hora)='$anio' AND MONTH(fecha_hora)='$mes' ";
$sqlcompras= ejecutarConsultaSimpleFila($sql);

$responce['compras']=$sqlcompras;

$gancianeta=$sqlventa['totalventa']-$sqlcompras['totalcompra'];
$gananciabruta=$sqlventa['totalventa'];
$impuesto=$sqlventa['igvventa']-$sqlcompras['igvcompra'];

$resumen['ganancianeta']=number_format($gancianeta, 3);
$resumen['gananciabruta']=number_format($gananciabruta, 3);
$resumen['impuesto']=number_format($impuesto, 3);

$responce['resumen']=$resumen;

echo json_encode($responce);

break;
//REPORTES
//REPORTES

//USUARIOS

case 'listarusuarios':

 		//Vamos a declarar un array
$data= Array();

$sql="SELECT * FROM usuario WHERE  idempresa='$id' ";

$rspta=ejecutarConsulta($sql);	

while ($reg=$rspta->fetch_object()){

$data[]=array(
"0"=>($reg->condicion)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idusuario.')"><i class="fas fa-edit"></i></button>'.
' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idusuario.')"><i class="fas fa-eye-slash"></i></button>':
'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idusuario.')"><i class="fas fa-edit"></i></button>'.
' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->idusuario.')"><i class="fas fa-check-square"></i></button>',
"1"=>$reg->idusuario,
 				"2"=>$reg->nombre,
 				"3"=>$reg->tipo_documento,
 				"4"=>$reg->txtID_CLIENTE,
 				"5"=>$reg->telefono,
 				"6"=>$reg->login,
 				"7"=>base64_decode($reg->clave),
 				"8"=>"<img src='files/usuarios/".$reg->imagen."' height='30px' width='30px' >",
 				"9"=>($reg->condicion)?'<span class="label bg-green">Activado</span>':
 				'<span class="label bg-red">Desactivado</span>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
			
echo json_encode($results);

break;


case 'mostrarusuarios':
		
		header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");	

		$jsondata = array();
		
		$sql="SELECT * FROM usuario WHERE idusuario='$idusuario'";
		$rspta=ejecutarConsultaSimpleFila($sql);
		
		$jsondata['idusuario'] = $rspta['idusuario'];
		$jsondata['nombre'] = $rspta['nombre'];
		$jsondata['tipo_documento'] = $rspta['tipo_documento'];
		$jsondata['txtID_CLIENTE'] = $rspta['txtID_CLIENTE'];
		$jsondata['direccion'] = $rspta['direccion'];
		$jsondata['telefono'] = $rspta['telefono'];
		$jsondata['login'] = $rspta['login'];
		$jsondata['nivel'] = $rspta['nivel'];
		//$clavehash=hash("SHA256",$clave);
		$clave=base64_decode($rspta['clave']);
		$jsondata['clave'] =$clave;
		$jsondata['imagen'] = $rspta['imagen'];
		echo json_encode($jsondata);
 		//echo json_encode($rspta);

break;


case 'guardaryeditarusuario':
		//Hash SHA256 en la contraseña
		$clavehash=base64_encode($clave);


$sql="SELECT * FROM usuario WHERE login='$login'";
$rspta=ejecutarConsultaSimpleFila($sql);

if(!$rspta){

if (empty($idusuario)){

$nivel=0;
//$idlocal=0;
$comisions=0;
$comisiond=0;
$imagen='';
		    
$sql="INSERT INTO usuario VALUES (NULL, '$id', '$nivel', '$idlocal', '$nombre', '$tipo_documento', '$txtID_CLIENTE', '$direccion', '$telefono', '$email', '$cargo', '$login', '$clavehash', '$imagen', '$comisions', '$comisiond', '1')";
//echo $sql;
			//return ejecutarConsulta($sql);
			$rspta=ejecutarConsulta($sql);

			
			echo $rspta ? "Usuario registrado" : "No se pudieron registrar todos los datos del usuario";
}else {

$sql="UPDATE usuario SET nombre='$nombre',tipo_documento='$tipo_documento',txtID_CLIENTE='$txtID_CLIENTE', direccion='$direccion',telefono='$telefono', email='$email', nivel='$cargo', login='$login', clave='$clavehash'  WHERE idusuario='$idusuario'";
//echo $sql;

$rspta=ejecutarConsulta($sql);
		//Eliminamos todos los permisos asignados para volverlos a registrar
		$sqldel="DELETE FROM usuario_permiso WHERE idusuario='$idusuario'";
		ejecutarConsulta($sqldel);

		$num_elementos=0;
		$sw=true;

//echo $_POST["permiso"].'|';


			echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar";
}

}else{
echo "El usuario ya existe en el sistema registre otro usuario";
}


break;


case 'mostrarperfil':
         $sql="SELECT * FROM config WHERE id='$id'";
        $rspta=ejecutarConsultaSimpleFila($sql);
        echo json_encode($rspta);
break;

case 'guardaryeditarperfil':
	
$percepcion12=isset($_POST["percepcion12"])? ($_POST["percepcion12"]):"";
$percepcion40=isset($_POST["percepcion40"])? ($_POST["percepcion40"]):"";
$ctadetraccion=isset($_POST["ctadetraccion"])? ($_POST["ctadetraccion"]):"";
$ctaretencion=isset($_POST["ctaretencion"])? ($_POST["ctaretencion"]):"";

            $sql="UPDATE config SET ruc='$ruc', razon_social='$rsocial', nombre_comercial='$ncomercial', direccion='$dir', departamento='$departamento', provincia='$provincia', distrito='$distrito', codpais='$codpais', ubigeo='$ubigeo', telefono='$fono', usuario='$usuario', clave='$clave', firma='$firma', correo='$correo', directo='$sunat', articulo='$articulo', pie='$pie', sunat='$ose', detraccion='$detraccion', color='$color', precio_porcentaje='$precio_porcentaje', igv='$porcentajeigv' WHERE id='$id'";
            $rspta=ejecutarConsulta($sql);
            echo $rspta ? "Actualizado correctamente" : "No se pudo actualizar";

break;

case 'renovarcuenta':

$sql="SELECT * FROM config WHERE id='$id'";
$rspta=ejecutarConsultaSimpleFila($sql);
//echo json_encode($rspta);

if($rspta['renovacio_tipo']=='ANUAL'){

$fecha = new DateTime($rspta['fechafin']);
$fecha->add(new DateInterval('P1Y'));
$fechafinal=$fecha->format('Y-m-d');  

}else{

$fecha = new DateTime($rspta['fechafin']);
$fecha->add(new DateInterval('P1M'));
$fechafinal=$fecha->format('Y-m-d');
    
}

$sql="INSERT INTO venta_renovar VALUES (NULL, '$id', NOW(), '$fechafinal', '$rspta[monto]', '$rspta[renovacio_tipo]', '1')";
//echo $sql;
//return ejecutarConsulta($sql);
$rspta2=ejecutarConsulta($sql);

$sql="UPDATE config SET fechafin='$fechafinal' WHERE id='$id'";
$rspta=ejecutarConsulta($sql);
//echo $rspta ? "Actualizado correctamente" : "No se pudo actualizar";

		$jsondata = array();
		
		$jsondata['estado'] = 1;
		$jsondata['fechavencimiento'] = $fechafinal;

		echo json_encode($jsondata);


break;





}





?>