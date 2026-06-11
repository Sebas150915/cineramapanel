<?php

//require_once("dompdf/dompdf_config.inc.php");
$rutat=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$rutat= str_replace("plugins/dompdf/index.php", "", $rutat);

require_once 'lib/html5lib/Parser.php';
require_once 'lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'lib/php-svg-lib/src/autoload.php';
require_once 'src/Autoloader.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;
use Dompdf\Options;

include "../phpqrcode/qrlib.php";

require "../../config/Conexion.php";
require "../../modelos/numeros-letras.php";

$id=$_GET['id'];
$idlocal=$_GET['idlocal'];

$sql="SELECT *FROM manifiesto WHERE id='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sqll="SELECT *FROM guia_vehiculo WHERE id='$mostrar[idbuss]' ";
$vehiculo= ejecutarConsultaSimpleFila($sqll);

$sqlre="SELECT *FROM guia_chofer WHERE id='$vehiculo[idchofer]' ";
$re= ejecutarConsultaSimpleFila($sqlre);

$sqlre2="SELECT *FROM guia_chofer WHERE id='$vehiculo[copiloto]' ";
$re2= ejecutarConsultaSimpleFila($sqlre2);

$sqlhr="SELECT *FROM rutas_horario WHERE id='$mostrar[idhorario]' ";
$hor= ejecutarConsultaSimpleFila($sqlhr);

$sqllru="SELECT *FROM rutas WHERE numero='$hor[ruta]' ORDER BY orden DESC ";
$rut= ejecutarConsultaSimpleFila($sqllru);

$sqllru2="SELECT *FROM rutas WHERE numero='$hor[ruta]' AND idempresa='$mostrar[idempresa]' ORDER BY orden ASC ";
$rut2= ejecutarConsultaSimpleFila($sqllru2);

$sqll="SELECT *FROM sucursal WHERE id='$rut2[id_sede]' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sqll2="SELECT *FROM sucursal WHERE id='$rut[id_sede]' ";
$local2= ejecutarConsultaSimpleFila($sqll2);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

$ruta=RUTA."/api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero='M-'.$mempresa['ruc'].'-'.$mostrar['serie'].'-'.$mostrar['numero'];


$tdocumento='MAPA DE USUARIOS';

$num=0;

// Crear array con los datos de TODOS los asientos en orden
$divs = array();

foreach(range("1", $vehiculo['asientos']) as $letra ) {

    $sqls = "SELECT * FROM venta_pasajes WHERE asiento='$letra' AND idhorario='$mostrar[idhorario]' AND estado='0'  AND pagodestino='NO' ";
    $su = ejecutarConsultaSimpleFila($sqls);
    
    // Validar si se obtuvo resultado
    $su_valido = ($su && !empty($su['id']));

    if($su_valido){
        $sql2 = "SELECT * FROM venta WHERE idventa='".$su['idventa']."'";
        $ve = ejecutarConsultaSimpleFila($sql2);
        
        // Validar resultado de venta
        $ve_valido = ($ve && !empty($ve['txtSERIE']));
        
        if($ve_valido){
            $serie = $ve['txtSERIE'].'-'.$ve['txtNUMERO'];
            
            // Obtener datos del pasajero con validación
//$sqls="SELECT *FROM venta_pasajes WHERE asiento='$letra' AND idhorario='$mostrar[idhorario]' AND estado='0' ";
if($idlocal==43){
$sqls="SELECT *FROM venta_pasajes WHERE asiento='$letra' AND idhorario='$mostrar[idhorario]' AND estado='0' AND pagodestino='NO' ORDER BY idembarque DESC ";
}else{
$sqls="SELECT *FROM venta_pasajes WHERE asiento='$letra' AND idhorario='$mostrar[idhorario]' AND estado='0' AND pagodestino='NO' ";
}
$su= ejecutarConsultaSimpleFila($sqls);

$sqlcliente="SELECT *FROM persona WHERE txtID_CLIENTE='$su[dni]' AND idempresa='$mostrar[idempresa]'  ";
$cliente= ejecutarConsultaSimpleFila($sqlcliente);
            
            $nombre_pasajero = $su['nombre'];
            $documento = $su['dni'] ? $su['dni']: 'SIN DOC';
            $telefono = ($pasajero && !empty($pasajero['telefono'])) ? $pasajero['telefono'] : 'SIN TELF';

$sqll="SELECT *FROM sucursal WHERE id='$su[idembarque]' ";
$localf1= ejecutarConsultaSimpleFila($sqll);
	
$sqll2="SELECT *FROM sucursal WHERE id='$su[iddestino]' ";
$localf2= ejecutarConsultaSimpleFila($sqll2);
            
            $num++;

            // Construir contenido para asiento OCUPADO
            $contenido_div = '
                <table width="100%" class="cabecera2" style="font-size: 12px; height: 100px;">
                  <tbody>
                    <tr>
                      <td width="15%">'.$letra.'</td>
                      <td colspan="3">'.$nombre_pasajero.'</td>
                    </tr>
                    <tr>
                      <td>Doc.</td>
                      <td width="30%">'.$documento.'</td>
                      <td width="15%">Telf.</td>
                      <td>'.$cliente['telefono'].'</td>
                    </tr>
                    <tr>
                      <td>Ori.</td>
                      <td>'.$localf1['sucursal'].'</td>
                      <td>Des.</td>
                      <td>'.$localf2['sucursal'].'</td>
                    </tr>';
            

                $contenido_div .= '
                    <tr>
                      <td colspan="4" >'.$serie.'</td>
                    </tr>';
            $contenido_div .= '
                  </tbody>
                </table>';
                
        } else {
            // Asiento con venta_pasajes pero sin venta válida
            $contenido_div = '
                <table width="100%" class="cabecera2" style="font-size: 12px; height: 100px;">
                  <tbody>
                    <tr>
                      <td width="15%">'.$letra.'</td>
                      <td colspan="3"></td>
                    </tr>
                    <tr>
                      <td>Doc.</td>
                      <td width="30%"></td>
                      <td width="15%">Telf.</td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>Ori.</td>
                      <td></td>
                      <td>Des.</td>
                      <td></td>
                    </tr>
                    <tr>
                      <td colspan="4" ></td>
                    </tr>
                  </tbody>
                </table>';
        }
    } else {
            $contenido_div = '
                <table width="100%" class="cabecera2" style="font-size: 12px; height: 100px;">
                  <tbody>
                    <tr>
                      <td width="15%">'.$letra.'</td>
                      <td colspan="3"></td>
                    </tr>
                    <tr>
                      <td>Doc.</td>
                      <td width="30%"></td>
                      <td width="15%">Telf.</td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>Ori.</td>
                      <td></td>
                      <td>Des.</td>
                      <td></td>
                    </tr>
                    <tr>
                      <td colspan="4" ></td>
                    </tr>
                  </tbody>
                </table>';
    }

    // Siempre agregar al array
    $divs[] = $contenido_div;
}

// Si no hay divs con contenido, mostrar mensaje
if(empty($divs)) {
    $divs[] = '
        <table width="100%" class="cabecera2" cellspacing="0" style="font-size: 18px; height: 100px;">
          <tbody>
            <tr>
              <td colspan="4" style="text-align: center;">NO HAY ASIENTOS DISPONIBLES</td>
            </tr>
          </tbody>
        </table>';
}

$divs_por_fila = 4;
$filas = array_chunk($divs, $divs_por_fila);

$html ='
<html> 
   <head> 
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   <style> 
body{
font:14px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
.cabecera table {
	width: 100%;
    color:black;
    margin-top: 0em;
    text-align: left; font-size: 14px;
}
.cabecera h1 {
    font-size:18px; padding-bottom: 0px; margin-bottom: 0px;
}

.cabecera2 { 
    border-collapse: collapse; 
    border: solid 0px #000000;
    width: 100%;
    height: 100px;
}
.cabecera2 th, .cabecera2 td { 
    text-align: left; 
    border-collapse: collapse; 
    border: solid 0px #000000; 
    font-size: 12px;
    padding: 1px;
    vertical-align: top;
} 

.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 0.5px #000000; }
.cuerpo thead { border: solid 0.5px #000000; } 
.cuerpo th, .cuerpo td { border-collapse: collapse; border: solid 0.5px #000000; font-size:12px; } 
.cuerpo2 thead { border: solid 1px #000000; } 

table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 1pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt; border: solid 1px #000000; }
.footer { font-size:9px;  width: 100%; border: solid 0px #000000; }
.fg { font-size: 10px;} 
.fg2 { text-align: center; }
.fg3 { border: solid 0px; } 
.total td { border: solid 0px; padding: 0px; } 
.total2 { text-align: right; } 

/* Estilos para los divs en 4 columnas */
.divfinal {
    width: 100%;
    height: 100px;
    border: solid 1px #000000;
    padding: 1px;
    box-sizing: border-box;
    font-size: 12px;
}

.celda-div {
    width: 25%;
    padding: 0px;
    vertical-align: top;
}

.tabla-divs {
    width: 100%;
    padding: 2px;
    border-collapse: collapse;
}

/* Colores para diferentes estados */
.div-libre {
    background-color: #f0f0f0;
    color: #666;
}

.div-ocupado {
    background-color: #ffffff;
}

.div-error {
    background-color: #ffe6e6;
    color: #cc0000;
}
   </style>
    
   </head> 
    
   <body>        





<table width="100%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
	
<td width="20%"><img src="../../files/logo/'.$mempresa['id'].'.png" style="max-width: 400px; max-height: 100px; margin-bottom: -10px; " /></td>
	
<td width="42%" class="cabeza"><h1>'.$mempresa['nombre_comercial'].$rut['numero'].'</h1>';

$rsptadf=ejecutarConsulta("SELECT r.*, s.* FROM rutas r, sucursal s  where s.id = r.id_sede and  r.numero ='" . $mostrar['ruta'] . "' and  r.estado='1' AND r.idempresa='$mostrar[idempresa]' order by r.orden ");
while ($regdf= $rsptadf->fetch_object()){
$html.=' '.$regdf->sucursal.' -';
}
$html.='</td>
		
      <td width="38%">
        
        
        <table width="100%" class="cabecera2" cellspacing="0" >
          <tbody>
            <tr>
<td ><br>RUC N° '.$mempresa['ruc'].' <br> '.$tdocumento.' <br> '.$mostrar['serie'].'-'.$mostrar['numero'].'<br><br></td>
            </tr>
          </tbody>
        </table>
 
        
      </td>
    </tr>
  </tbody>
</table>

<br>
<table width="101%" class="cuerpo" cellspacing="0">
<thead>
    <tr>
      <td width="26%">PLACA: '.$vehiculo['placa'].'</td>
      <td width="26%">MARCA: '.$vehiculo['marca'].'</td>
      <td width="21%">TARJ. CIRC. N°: '.$vehiculo['circulacion'].'</td>
      <td width="27%">F/H: '.$hor['fecha'].' '.$hor['hora'].':'.$hor['minuto'].$hor['ampm'].'</td>
    </tr>
    <tr>
      <td colspan="2">PUNTO DE PARTIDA: '.$local['sucursal'].'</td>
      <td colspan="2">DESTINO: '.$local2['sucursal'].'</td>
    </tr>
    <tr>
      <td colspan="3">NOMBRE DEL PILOTO: '.$re['nombre'].'</td>
      <td>BREVETE N°: '.$re['lisencia'].'</td>
    </tr>
    <tr>
      <td colspan="3">NOMBRE DEL COPILOTO: '.$re2['nombre'].'</td>
      <td>BREVETE N°: '.$re2['lisencia'].'</td>
    </tr>
    <tr>
      <td colspan="2">NOMBRE AUX. SERV.:</td>
      <td colspan="2">TOTAL ASIENTOS BUS: '.$vehiculo['asientos'].'</td>
    </tr>
    <tr>
      <td>CANT. PASAJ. TER.: '.$num.'</td>
      <td>CANT. PASAJ. RUTA: '.$num.'</td>
      <td colspan="2">CANT. ASI. TRIP.: '.$num.'</td>
    </tr>

  </thead>
</table>

<br>









        <table class="tabla-divs" width="100%" border="0" cellspacing="0" cellpadding="0">';

// Generar las filas con los divs
foreach ($filas as $fila) {
    $html .= '<tr>';
    foreach ($fila as $contenido) {
        // Determinar la clase según el contenido
        if (strpos($contenido, 'LIBRE') !== false) {
            $clase = 'div-libre';
        } elseif (strpos($contenido, 'ERROR') !== false) {
            $clase = 'div-error';
        } else {
            $clase = 'div-ocupado';
        }
        
        $html .= '<td class="celda-div">
                    <div class="divfinal ' . $clase . '">' . $contenido . '</div>
                  </td>';
    }
    
    // Rellenar celdas vacías si la última fila no tiene 4 elementos
    for ($i = count($fila); $i < $divs_por_fila; $i++) {
        $html .= '<td class="celda-div">
                    <div class="divfinal" style="border: none; background: transparent;"></div>
                  </td>';
    }
    $html .= '</tr>';
}

$html .= '        </table>
</body> </html>';

header("Content-Type: application/pdf; charset=utf-8");

$dompdf = new DOMPDF();
$dompdf->set_paper('legal','portrait');
$dompdf->load_html($html, 'UTF-8');
$dompdf->render();
$pdf = $dompdf->output();
echo $pdf;

?>