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

$tdocumento='MANIFIESTO DE USUARIOS';

$num=0;

// Crear array con los datos de TODOS los asientos en orden
$divs = array();

foreach(range("1", $vehiculo['asientos']) as $letra ) {}

$divs_por_fila = 4;
$filas = array_chunk($divs, $divs_por_fila);

$html ='
<html> 
   <head> 
   <style> 
body{
font:14px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
.cabecera table {
	width: 100%;
    color:black;
    margin-top: 0em;
    text-align: left; font-size: 12px;
}
.cabecera h1 {
    font-size:22px; padding-bottom: 0px; margin-bottom: 0px;
}

.cabecera2 { 
    border-collapse: collapse; 
    border: solid 1px #000000;
    width: 100%;
    height: 100px;
}
.cabecera2 th, .cabecera2 td { 
    text-align: left; 
    border-collapse: collapse; 
    border: solid 1px #000000; 
    font-size: 8px;
    padding: 1px;
} 

.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 0.5px #000000; }
.cuerpo thead { border: solid 0.5px #000000; } 
.cuerpo th, .cuerpo td { border-collapse: collapse; border: solid 0.5px #000000; font-size:12px; } 
.cuerpo2 thead { border: solid 1px #000000; } 

table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 2pt; }           
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
    font-size: 8px;
}

.celda-div {
    width: 25%;
    padding: 1px;
    vertical-align: top;
}

.tabla-divs {
    width: 100%;
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
   </style>
    
   </head> 
    
   <body>        

<table width="100%" class="cuerpo" border="0" cellspacing="0">
<thead> 
    <tr>
      <td>
        <table class="tabla-divs" width="100%" border="0" cellspacing="0" cellpadding="1">';

// Generar las filas con los divs
foreach ($filas as $fila) {
    $html .= '<tr>';
    foreach ($fila as $contenido) {
        // Determinar la clase según el contenido
        $clase = (strpos($contenido, 'LIBRE') !== false) ? 'div-libre' : 'div-ocupado';
        
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
      </td>
    </tr>
</thead>
<tbody>';

$html.='

</tbody>
</table>

<br>
</body> </html>';

header("Content-type:application/pdf");

$dompdf = new DOMPDF();
$dompdf->set_paper('legal','potrait');
$dompdf->load_html($html);
$dompdf->render();
$pdf = $dompdf->output();
echo $pdf;



?>