<?php
header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");

require_once("../../config/config2.php");

// Obtener todos los comprobantes

switch ($_GET["op"]){
case 'listarventa':


$fecha_inicio=isset($_GET["fecha_inicio"])? $_GET["fecha_inicio"]:"";
$fecha_fin=isset($_GET["fecha_fin"])? $_GET["fecha_fin"]:"";
$idlocal=isset($_GET["idlocal"])? $_GET["idlocal"]:"0";

if($idlocal>0){

$sql="SELECT 
id,
                        DATE_FORMAT(fecha_emision, '%d/%m/%Y') as fecha,
                        serie,
                        correlativo,
                        CASE 
                            WHEN tipocomp = '03' THEN 'BO'
                            WHEN tipocomp = '01' THEN 'FA'
                            ELSE tipocomp 
                        END as tipocomp,
                        
                        SUM(op_gravadas) AS totalgravadas,
                        SUM(iepnd) AS totaliepnd,
                        SUM(igv) AS totaligv,
                        SUM(total) AS totaltotal,
                        COUNT(*) AS cantidad,
                        op_gravadas,
                        iepnd,
                        igv,
                        total
                    FROM tbl_venta_cab 
                    WHERE estado = '1' AND fecha_emision>='$fecha_inicio' AND fecha_emision<='$fecha_fin' AND local='$idlocal' GROUP BY serie
                    ORDER BY fecha_emision DESC, serie ASC, correlativo ASC";


}else{

$sql="SELECT 
id,
                        DATE_FORMAT(fecha_emision, '%d/%m/%Y') as fecha,
                        serie,
                        correlativo,
                        CASE 
                            WHEN tipocomp = '03' THEN 'BO'
                            WHEN tipocomp = '01' THEN 'FA'
                            ELSE tipocomp 
                        END as tipocomp,
                        
                        SUM(op_gravadas) AS totalgravadas,
                        SUM(iepnd) AS totaliepnd,
                        SUM(igv) AS totaligv,
                        SUM(total) AS totaltotal,
                        COUNT(*) AS cantidad,
                        op_gravadas,
                        iepnd,
                        igv,
                        total
                    FROM tbl_venta_cab 
                    WHERE estado = '1' AND fecha_emision>='$fecha_inicio' AND fecha_emision<='$fecha_fin' GROUP BY serie
                    ORDER BY fecha_emision DESC, serie ASC, correlativo ASC";

}
 
//echo $sql;
                
$stmt = $db->prepare($sql);

$stmt->execute();
$rows = $stmt->fetchAll();

$data = [];
foreach ($rows as $r) {
	
$sminimo = $db->prepare(" SELECT * FROM tbl_venta_cab ORDER BY correlativo ASC ");
$sminimo->execute();
$rowminimo = $sminimo->fetch(PDO::FETCH_ASSOC);

$smaximo = $db->prepare(" SELECT * FROM tbl_venta_cab ORDER BY correlativo DESC ");
$smaximo->execute();
$rowmaximo= $smaximo->fetch(PDO::FETCH_ASSOC);

$susuario = $db->prepare(" SELECT SUM(cantidad) AS total FROM tbl_venta_det WHERE idventa='$r[id]'  ");
$susuario->execute();
$rowusuario= $susuario->fetch(PDO::FETCH_ASSOC);

$valor_venta=$r['totalgravadas']-$r['totaliepnd'];

$totalexpectador=0.00;
if($rowusuario['total']>0){
$totalexpectador=$rowusuario['total'];
}


    $data[] = [
        'fecha' => $r['fecha'],
        'serie' => $r['serie'],
        'tipocomp' => $r['tipocomp'],
        'correlativoinicial' => $rowminimo['correlativo'],
        'correlativofinal' => $rowmaximo['correlativo'],
        'correlativo' => $r['correlativo'],
        'cantidad' => $r['cantidad'],
        'espectador' => $totalexpectador,
        'valor_venta' =>$valor_venta,
        'iepnd' => (float)$r['totaliepnd'],
        'op_gravadas' => (float)$r['totalgravadas'],
        'igv' => (float)$r['totaligv'],
        'total' => (float)$r['totaltotal']
    ];
}

echo json_encode($data);
break;



case 'resumendiario':


$fecha_inicio = isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"] : "";
$fecha_fin = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : "";
$idlocal = isset($_GET["idlocal"]) ? $_GET["idlocal"] : "0";

$localquery = "";

if ($idlocal > 0) {
    $localquery = " AND local='$idlocal' ";
}

$sql = "SELECT
            codfuncion,
            codpelicula,
            codsala,
            id,
            DATE_FORMAT(fecha_emision, '%d/%m/%Y') as fecha,
            serie,
            correlativo,
            CASE 
                WHEN tipocomp = '03' THEN 'BO'
                WHEN tipocomp = '01' THEN 'FA'
                ELSE tipocomp 
            END as tipocomp,
            op_gravadas,
            iepnd,
            igv,
            total
        FROM tbl_venta_cab 
        WHERE estado = '1' 
        AND fecha_emision >= '$fecha_inicio' 
        AND fecha_emision <= '$fecha_fin' 
        $localquery 
        AND conceptoventa = '01' 
        ORDER BY fecha_emision DESC, serie ASC, correlativo ASC";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();

$data = [];

foreach ($rows as $r) {
    
    // Verificar que $r['id'] existe
    $salaId = isset($r['codsala']) ? $r['codsala'] : 0;
    
    // Consulta para tbl_sala - CORREGIDA: ORDER BY no es necesario con WHERE id = valor
    
    $sqlsala="SELECT * FROM tbl_sala WHERE id ='$salaId' ";
    $ssala = $db->prepare($sqlsala);
    $ssala->execute();
    $rowsala = $ssala->fetch(PDO::FETCH_ASSOC);
    
    // Verificar si la consulta devolvió datos
    if (!$rowsala) {
        $rowsala = ['nombre' => 'Sin sala', 'capacidad' => 0];
    }
    
    // Consulta para tbl_pelicula
    $peliculaId = isset($r['codpelicula']) ? $r['codpelicula'] : 0;
    $sminimo = $db->prepare("SELECT * FROM tbl_pelicula WHERE id = :id");
    $sminimo->bindParam(':id', $peliculaId);
    $sminimo->execute();
    $rowminimo = $sminimo->fetch(PDO::FETCH_ASSOC);
    
    if (!$rowminimo) {
        $rowminimo = ['id' => 0, 'nombre' => 'Sin película'];
    }
    
    // Consulta para suma de cantidades
    $ventaId = isset($r['id']) ? $r['id'] : 0;
    $susuario = $db->prepare("SELECT SUM(cantidad) AS total FROM tbl_venta_det WHERE idventa = :idventa");
    $susuario->bindParam(':idventa', $ventaId);
    $susuario->execute();
    $rowusuario = $susuario->fetch(PDO::FETCH_ASSOC);
    
    $totalexpectador = 0.00;
    if ($rowusuario && isset($rowusuario['total']) && $rowusuario['total'] > 0) {
        $totalexpectador = $rowusuario['total'];
    }
    
    // Usar str_pad solo si el valor no es null - LÍNEA 184 CORREGIDA
    $codigoId = isset($rowminimo['id']) ? $rowminimo['id'] : 0;
    $codigo = str_pad((string)$codigoId, 8, "0", STR_PAD_LEFT);
    
    // Calcular porcentaje
    $capacidad = isset($rowsala['capacidad']) && $rowsala['capacidad'] > 0 ? $rowsala['capacidad'] : 1;
    $porcentaje = ($totalexpectador / $capacidad) * 100;
    
    // Consulta para tbl_funciones
    $funcionId = isset($r['codfuncion']) ? $r['codfuncion'] : 0;
    $sfuncion = $db->prepare("SELECT * FROM tbl_funciones WHERE id = :id");
    $sfuncion->bindParam(':id', $funcionId);
    $sfuncion->execute();
    $rowfuncion = $sfuncion->fetch(PDO::FETCH_ASSOC);
    
    $hora = '';
    if ($rowfuncion && isset($rowfuncion['id_hora'])) {
        $shora = $db->prepare("SELECT * FROM tbl_hora WHERE id = :id");
        $shora->bindParam(':id', $rowfuncion['id_hora']);
        $shora->execute();
        $rowhora = $shora->fetch(PDO::FETCH_ASSOC);
        $hora = isset($rowhora['hora']) ? $rowhora['hora'] : '';
    }
    
    // Fecha corregida - LÍNEA 199 CORREGIDA (sin espacio al final)
    $fechaEmision = isset($r['fecha']) ? $r['fecha'] : '';
    
$hora_ampm = date("h:i A", strtotime($hora));

    $data[] = [
        'sala' => isset($rowsala['nombre']) ? $rowsala['nombre'] : '',
        'fecha' => $fechaEmision,
        'hora' => $hora_ampm,
        'codigo' => $codigo,
        'descripcion' => isset($rowminimo['nombre']) ? $rowminimo['nombre'] : '',
        'capacidad' => (int)$capacidad,
        'espectador' => (float)$totalexpectador,
        'porcentaje' => (float)$porcentaje,
        'total' => isset($r['total']) ? (float)$r['total'] : 0.00
    ];
}

echo json_encode($data);



break;



case 'ventavariable':


$anio= isset($_GET["anio"]) ? $_GET["anio"] : "";
$mes= isset($_GET["mes"]) ? $_GET["mes"] : "";
$idlocal = isset($_GET["idlocal"]) ? $_GET["idlocal"] : "0";

$localquery = "";
$localquery2 = "";

if ($idlocal > 0) {
    $localquery = " AND local='$idlocal' ";
    $localquery2 = " AND c.local='$idlocal' ";
}

// Calcular el último día del mes usando date('t')
$ultimoDia = date('t', strtotime("$anio-$mes-01"));

// Crear array con todos los días del mes
$diasDelMes = [];


for ($dia = 1; $dia <= $ultimoDia; $dia++) {
    $fecha = sprintf("%02d/%02d/%04d", $dia, $mes, $anio);
    
    $fechamysql = sprintf("%04d-%02d-%02d", $anio, $mes, $dia);
 
$susuario = $db->prepare("SELECT SUM(cantidad) AS total FROM tbl_venta_det d INNER JOIN tbl_venta_cab c ON d.idventa=c.id WHERE c.fecha_emision ='$fechamysql' 
AND c.conceptoventa='01' $localquery2 ");
$susuario->execute();
$rowusuario = $susuario->fetch(PDO::FETCH_ASSOC);
    
$totalexpectador = 0.00;
if ($rowusuario && isset($rowusuario['total']) && $rowusuario['total'] > 0) {
        $totalexpectador = $rowusuario['total'];
}
    
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision ='$fechamysql' AND conceptoventa='01' $localquery ");
$sminimo->execute();
$rowminimo = $sminimo->fetch(PDO::FETCH_ASSOC);

$totalventa= 0.00;
$totalsinigv= 0.00;
$totalsiniepnd= 0.00;


if ($rowminimo && isset($rowminimo['totalventa']) && $rowminimo['totalventa'] > 0) {
        $totalventa = $rowminimo['totalventa'];
        $totalsinigv= $rowminimo['totalventa']-$rowminimo['totaligv'];
$totalsiniepnd= $rowminimo['totalventa']-$rowminimo['totaliepnd'];
        
}
  

/*CHOCOLATERIA*/
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision ='$fechamysql' AND conceptoventa='02' $localquery ");
$sminimo->execute();
$rowminimochoco = $sminimo->fetch(PDO::FETCH_ASSOC);

$totalventachoco= 0.00;
$totalsinigvchoco= 0.00;
$totalsiniepndchoco= 0.00;

if ($rowminimochoco && isset($rowminimochoco['totalventa']) && $rowminimochoco['totalventa'] > 0) {
$totalventachoco = $rowminimochoco['totalventa'];
$totalsinigvchoco= $rowminimochoco['totalventa']-$rowminimochoco['totaligv'];
$totalsiniepndchoco= $rowminimochoco['totalventa']-$rowminimochoco['totaliepnd'];
}


/*JUEGOS*/
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision ='$fechamysql' AND conceptoventa='03' $localquery ");
$sminimo->execute();
$rowjuegos= $sminimo->fetch(PDO::FETCH_ASSOC);

$totalventajuegos= 0.00;
$totalsinigvjuegos= 0.00;

if ($rowjuegos && isset($rowjuegos['totalventa']) && $rowjuegos['totalventa'] > 0) {
$totalventajuegos = $rowjuegos['totalventa'];
$totalsinigvjuegos= $rowjuegos['totalventa']-$rowjuegos['totaligv'];
}

/*TOTAL*/
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision ='$fechamysql' $localquery ");
$sminimo->execute();
$rowtotales= $sminimo->fetch(PDO::FETCH_ASSOC);



/*CORPORATIVO*/
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision ='$fechamysql' AND conceptoventa>'04' $localquery ");
$sminimo->execute();
$rowcorporativos= $sminimo->fetch(PDO::FETCH_ASSOC);

$totalventacoporativos= 0.00;
$totalsinigvcoporativos= 0.00;

if ($rowcorporativos && isset($rowcorporativos['totalventa']) && $rowcorporativos['totalventa'] > 0) {
$totalventacoporativos = $rowcorporativos['totalventa'];
$totalsinigvcoporativos= $rowcorporativos['totalventa']-$rowcorporativos['totaligv'];
}


$diasDelMes[]= [
        'fecha' => $fecha,
        'totalbruto' => number_format($totalventa, 2),
        'totalsinigv' => number_format($totalsinigv, 2),
        'totalsiniepnd' => number_format($totalsiniepnd, 2),
        'renta' => isset($rowminimo['totaligv']) && is_numeric($rowminimo['totaligv'])? number_format((float)$rowminimo['totaligv'], 2): number_format(0, 2),
        'espectador' => $totalexpectador,
        
        'capacidad' => $totalventacoporativos,
        'porcentaje' => $totalsinigvcoporativos,
        'rentaejecutivo' => isset($totalsinigvcoporativos['totaligv']) && is_numeric($totalsinigvcoporativos['totaligv'])? number_format((float)$totalsinigvcoporativos['totaligv'], 2): number_format(0, 2),
        //chocolateria
        'totalventachoco' => $totalventachoco,
        'totalsinigvchoco' => $totalsinigvchoco,
        'rentachoco' => isset($rowminimochoco['totaligv']) && is_numeric($rowminimochoco['totaligv'])? number_format((float)$rowminimochoco['totaligv'], 2): number_format(0, 2),

        //juegos
        'totalventajuegos' => $totalventajuegos,
        'totalsinigvjuegos' => $totalsinigvjuegos,
        'rentajuegos' => isset($rowjuegos['totaligv']) && is_numeric($rowjuegos['totaligv'])? number_format((float)$rowjuegos['totaligv'], 2): number_format(0, 2),
        
        'total' => isset($rowtotales['totaligv']) && is_numeric($rowtotales['totaligv'])? number_format((float)$rowtotales['totaligv'], 2): number_format(0, 2),
    ];
}

echo json_encode($diasDelMes);



break;





case 'ventavariableacumulada':


$fecha_inicio= isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"] : "";
$fecha_fin= isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : "";
$idlocal = isset($_GET["idlocal"]) ? $_GET["idlocal"] : "0";

$localquery = "";
$localquery2 = "";

if ($idlocal > 0) {
    $localquery = " AND local='$idlocal' ";
    $localquery2 = " AND c.local='$idlocal' ";
}


// Crear array con todos los días del mes
$diasDelMes = [];

$susuario = $db->prepare("SELECT SUM(cantidad) AS total FROM tbl_venta_det d INNER JOIN tbl_venta_cab c ON d.idventa=c.id WHERE c.fecha_emision>='$fecha_inicio'  
AND  c.fecha_emision<='$fecha_fin' 
AND c.conceptoventa='01' $localquery2 ");
$susuario->execute();
$rowusuario = $susuario->fetch(PDO::FETCH_ASSOC);
    
$totalexpectador = 0.00;
if ($rowusuario && isset($rowusuario['total']) && $rowusuario['total'] > 0) {
        $totalexpectador = $rowusuario['total'];
}
    
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision>='$fecha_inicio' AND fecha_emision<='$fecha_fin' AND conceptoventa='01' $localquery ");
$sminimo->execute();
$rowminimo = $sminimo->fetch(PDO::FETCH_ASSOC);

$totalventa= 0.00;
$totalsinigv= 0.00;
$totalsiniepnd= 0.00;


if ($rowminimo && isset($rowminimo['totalventa']) && $rowminimo['totalventa'] > 0) {
        $totalventa = $rowminimo['totalventa'];
        $totalsinigv= $rowminimo['totalventa']-$rowminimo['totaligv'];
$totalsiniepnd= $rowminimo['totalventa']-$rowminimo['totaliepnd'];
        
}
  

/*CHOCOLATERIA*/
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision>='$fecha_inicio' AND fecha_emision<='$fecha_fin' AND conceptoventa='02' $localquery ");
$sminimo->execute();
$rowminimochoco = $sminimo->fetch(PDO::FETCH_ASSOC);

$totalventachoco= 0.00;
$totalsinigvchoco= 0.00;
$totalsiniepndchoco= 0.00;

if ($rowminimochoco && isset($rowminimochoco['totalventa']) && $rowminimochoco['totalventa'] > 0) {
$totalventachoco = $rowminimochoco['totalventa'];
$totalsinigvchoco= $rowminimochoco['totalventa']-$rowminimochoco['totaligv'];
$totalsiniepndchoco= $rowminimochoco['totalventa']-$rowminimochoco['totaliepnd'];
}


/*JUEGOS*/
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision>='$fecha_inicio' AND fecha_emision<='$fecha_fin' AND conceptoventa='03' $localquery ");
$sminimo->execute();
$rowjuegos= $sminimo->fetch(PDO::FETCH_ASSOC);

$totalventajuegos= 0.00;
$totalsinigvjuegos= 0.00;

if ($rowjuegos && isset($rowjuegos['totalventa']) && $rowjuegos['totalventa'] > 0) {
$totalventajuegos = $rowjuegos['totalventa'];
$totalsinigvjuegos= $rowjuegos['totalventa']-$rowjuegos['totaligv'];
}

/*TOTAL*/
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision>='$fecha_inicio' AND fecha_emision<='$fecha_fin'  $localquery ");
$sminimo->execute();
$rowtotales= $sminimo->fetch(PDO::FETCH_ASSOC);



/*CORPORATIVO*/
$sminimo = $db->prepare("SELECT SUM(total) AS totalventa, SUM(igv) AS totaligv, SUM(iepnd) AS totaliepnd FROM tbl_venta_cab 
WHERE fecha_emision>='$fecha_inicio' AND fecha_emision<='$fecha_fin'  AND conceptoventa>'04' $localquery ");
$sminimo->execute();
$rowcorporativos= $sminimo->fetch(PDO::FETCH_ASSOC);

$totalventacoporativos= 0.00;
$totalsinigvcoporativos= 0.00;

if ($rowcorporativos && isset($rowcorporativos['totalventa']) && $rowcorporativos['totalventa'] > 0) {
$totalventacoporativos = $rowcorporativos['totalventa'];
$totalsinigvcoporativos= $rowcorporativos['totalventa']-$rowcorporativos['totaligv'];
}


$diasDelMes[]= [
        'concepto' => 'Base Imponible',
        'cine' => isset($rowminimo['totaligv']) && is_numeric($rowminimo['totaligv'])? number_format((float)$rowminimo['totaligv'], 2): number_format(0, 2),
        'corporativo' => isset($rowminimochoco['totaligv']) && is_numeric($rowminimochoco['totaligv'])? number_format((float)$rowminimochoco['totaligv'], 2): number_format(0, 2),
        'chocolateria' => isset($rowminimochoco['totaligv']) && is_numeric($rowminimochoco['totaligv'])? number_format((float)$rowminimochoco['totaligv'], 2): number_format(0, 2),
        'juegos' => isset($rowjuegos['totaligv']) && is_numeric($rowjuegos['totaligv'])? number_format((float)$rowjuegos['totaligv'], 2): number_format(0, 2),
        'total' => isset($rowtotales['totaligv']) && is_numeric($rowtotales['totaligv'])? number_format((float)$rowtotales['totaligv'], 2): number_format(0, 2),
    ];


$diasDelMes[]= [
        'concepto' => 'IEPND',
        'cine' => number_format($totalsiniepnd, 2),
        'corporativo' => '0.00',
        'chocolateria' => '0.00',
        'juegos' => '0.00',
        'total' => number_format($totalsiniepnd, 2),
    ];


$subtotalesfinales=$totalsiniepnd+$totalsinigvcoporativos+$totalsinigvchoco+$totalsinigvjuegos;

$diasDelMes[]= [
        'concepto' => 'Sub-Total',
        'cine' => number_format($totalsiniepnd, 2),
        'corporativo' => $totalsinigvcoporativos,
        'chocolateria' => $totalsinigvchoco,
        'juegos' => $totalsinigvjuegos,
        'total' => number_format($subtotalesfinales, 2),
    ];

$diasDelMes[]= [
        'concepto' => 'IGV',
        'cine' => isset($rowminimo['totaligv']) && is_numeric($rowminimo['totaligv'])? number_format((float)$rowminimo['totaligv'], 2): number_format(0, 2),
        'corporativo' => isset($totalsinigvcoporativos['totaligv']) && is_numeric($totalsinigvcoporativos['totaligv'])? number_format((float)$totalsinigvcoporativos['totaligv'], 2): number_format(0, 2),
        //chocolateria
        'chocolateria' => isset($rowminimochoco['totaligv']) && is_numeric($rowminimochoco['totaligv'])? number_format((float)$rowminimochoco['totaligv'], 2): number_format(0, 2),

        'juegos' => isset($rowjuegos['totaligv']) && is_numeric($rowjuegos['totaligv'])? number_format((float)$rowjuegos['totaligv'], 2): number_format(0, 2),
        'total' => isset($rowtotales['totaligv']) && is_numeric($rowtotales['totaligv'])? number_format((float)$rowtotales['totaligv'], 2): number_format(0, 2),
    ];


$totalesfinales=$totalventa+$totalventacoporativos+$totalventachoco+$totalventajuegos;

$diasDelMes[]= [
        'concepto' => 'Total',
        'cine' => $totalventa,
        'corporativo' => $totalventacoporativos,
        'chocolateria' => $totalventachoco,
        'juegos' => $totalventajuegos,
        'total' =>$totalesfinales,
    ];


echo json_encode($diasDelMes);



break;





case 'paloteo':


$fecha_inicio = isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"] : "";
$fecha_fin = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : "";
$idlocal = isset($_GET["idlocal"]) ? $_GET["idlocal"] : "0";

$localquery = "";

if ($idlocal > 0) {
    $localquery = " AND c.local='$idlocal' ";
}

$sql = "SELECT * FROM tbl_categoria ORDER BY nombre ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();
$data = [];

foreach ($rows as $r) {
    
    // Verificar que $r['id'] existe
    $salaId = isset($r['id']) ? $r['id'] : 0;
    
    // Consulta para tbl_sala - CORREGIDA: ORDER BY no es necesario con WHERE id = valor
    
    $sqlsala="SELECT * FROM tbl_productos WHERE idcategoria ='$salaId' ";
    $ssala = $db->prepare($sqlsala);
    $ssala->execute();
    $rowproductos= $ssala->fetch(PDO::FETCH_ASSOC);

if($rowproductos){
  
$sqlsala="SELECT SUM(cantidad) AS cantidad, SUM(importe_total) AS importe_total FROM tbl_venta_det d INNER JOIN tbl_venta_cab c ON c.id=d.idventa 
    WHERE d.idproducto  ='$rowproductos[id]' AND c.fecha_emision>='$fecha_inicio' AND c.fecha_emision<='$fecha_fin' $localquery GROUP BY d.idproducto  ";
$ssala = $db->prepare($sqlsala);

$ssala->execute();
$rowventas= $ssala->fetch(PDO::FETCH_ASSOC);
if($rowventas){
    $data[] = [
        'categoria' => $r['nombre'],
        'id' => $rowproductos['id'],
        'nombre' => $rowproductos['nombre'],
        'unidad' => $rowproductos['unidad_medida'],
        'cantidad' => $rowventas['cantidad'],
        'total' => $rowventas['importe_total'],
    ];
} 
}


	
	
}

echo json_encode($data);



break;





case 'iepndporfecha':
 
        
        $anio = isset($_POST["anio"]) ? $_POST["anio"] : "";
        $mes = isset($_POST["mes"]) ? $_POST["mes"] : "";
        $idlocal = isset($_POST["idlocal"]) ? $_POST["idlocal"] : "0";
        $codsala = isset($_POST["codsala"]) ? $_POST["codsala"] : "";
        
        $localquery = "";
        $salaquery = "";
        
        if ($idlocal > 0) {
            $localquery = " AND v.local='$idlocal' ";
        }
        
        if (!empty($codsala)) {
            $salaquery = " AND v.codsala='$codsala' ";
        }
        
        $anioquery = "";
        $mesquery = "";
        
        if (!empty($anio)) {
            $anioquery = " AND YEAR(v.fecha_emision)='$anio' ";
        }
        
        if (!empty($mes)) {
            $mesquery = " AND MONTH(v.fecha_emision)='$mes' ";
        }
        
        $sql = "SELECT 
                    v.codsala,
                    s.nombre AS nombre_sala,
                    dv.codtarifa,
                    t.nombre AS nombre_tarifa,
                    t.precio AS precio_tarifa,
                    dv.cantidad,
                    dv.precio_unitario,
                    dv.importe_total,
                    v.serie,
                    v.correlativo,
                    v.fecha_emision
                FROM tbl_venta_cab v
                LEFT JOIN tbl_sala s ON v.codsala = s.id
                LEFT JOIN tbl_venta_det dv ON v.id = dv.idventa
                LEFT JOIN tbl_tarifa t ON dv.codtarifa = t.id
                WHERE 1=1
                $anioquery
                $mesquery
                $localquery
                $salaquery
                ORDER BY v.codsala, v.fecha_emision, v.serie, v.correlativo";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [];
        foreach ($rows as $row) {
            $precio = isset($row['precio_tarifa']) && $row['precio_tarifa'] > 0 ? number_format((float)$row['precio_tarifa'], 2) : "0.00";
            $importe = isset($row['importe_total']) && $row['importe_total'] > 0 ? number_format((float)$row['importe_total'], 2) : "0.00";
            $precio_unitario = isset($row['precio_unitario']) && $row['precio_unitario'] > 0 ? number_format((float)$row['precio_unitario'], 2) : "0.00";
            $cantidad = isset($row['cantidad']) && $row['cantidad'] > 0 ? number_format((float)$row['cantidad'], 0) : "0";
            
            $data[] = [
                'codsala' => $row['codsala'],
                'nombre_sala' => $row['nombre_sala'],
                'codtarifa' => $row['codtarifa'],
                'nombre_tarifa' => $row['nombre_tarifa'],
                'precio_tarifa' => $precio,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio_unitario,
                'importe_total' => $importe,
                'serie' => $row['serie'],
                'correlativo' => $row['correlativo'],
                'fecha_emision' => $row['fecha_emision']
            ];
        }
        
        echo json_encode(["data" => $data]);

break;













}

?>

