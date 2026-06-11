<?php

$rutat=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$rutat= str_replace("plugins/dompdf/index.php", "", $rutat);

require_once 'lib/html5lib/Parser.php';
require_once 'lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'lib/php-svg-lib/src/autoload.php';
require_once 'src/Autoloader.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;
use Dompdf\Options;

require "../../modelos/resumen.php";

$fecha_inicio=$_GET['fecha_inicio'];
$fecha_fin=$_GET['fecha_fin'];


function generateDates(string $since, string $until = null) {
    $dates = [];

    if (! $until) {
        $until = date('Y-m-d');
    }

    $since = strtotime($since);
    $until = strtotime($until);

    do {
        $period           = date('Y-m', $since); // para agrupar por periodo AÑO-MES
        $dates[$period][] = date('Y-m-d', $since);
        $since            = strtotime("+ 1 day", $since);
    } while($since <= $until);

    return $dates;
}


$sql3="SELECT *FROM config WHERE id='$_COOKIE[id]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

$fecha_y_hora = date("d/m/Y");

$html =
'





   <html> 
   <head> 
   <style> 
body{
font:10px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
.cabecera table {
	width: 100%;
    color:black;
    margin-top: 0em;
    text-align: left; font-size: 18px;
}


.cabecera2 table { border-collapse: collapse; border: solid 1px #000000;}
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; border: solid 1px #000000; } 
.cabecera2 { font-size:30px; } 

.cabecera3 table { border-collapse: collapse; }
.cabecera3 th, .cabecera2 td { text-align: center; border-collapse: collapse; } 
.cabecera3 { font-size:15px; text-align: center; } 
.cabecera3 h3 { font-size:22px; margin:0px; padding:0px; } 

.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 1px #000000; }
.cuerpo thead { border: solid 1px #000000; } 
.cuerpo { font-size:15px; text-align: left; } 

.cuerpo2 thead { border: solid 1px #000000; }

.cuenta { font-size: 16px; } 
	   
.cuerpo3 { border: solid 0px #000000; } 
.cuerpo3 th, .cuerpo3 td { border: solid 0px #000000; padding: 0pt; } 
.cuerpo4 { border: solid 1px #000000; } 
.cuerpo4 th, .cuerpo4 td { border: solid 1px #000000;  padding: 3pt; } 

.cu th, .cue td { border: solid 1px #000000; padding: 5pt; } 
.cue {
	width: 50%;
}



table { width: 100%; color:black;  }
  
tbody { background-color: #ffffff; }
th,td { padding: 3pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt; border: solid 1px #000000; }
.footer { position: fixed; bottom: 100px; font-size:14px;  width: 100%; border: solid 1px #000000; }
.fg { font-size: 14px; } 
.fg2 { text-align: center; }
.fg3 { border: solid 1px; } 
.total td { border: solid 1px; padding: 0px; } 
.total2 { text-align: right; }

.totales {
    background-color: #d7e4f2;
}

.contenido {
    background-color: #ffffff;
}

.superior {
    background-color: #f0f0f0;
}

.page-number:after { 
    counter-increment: pages; 
    content: "Página "  counter(page) " de " counter(pages); 
}
	   

   </style>
    
   </head> 
    
   <body>        




<table width="100%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
	
<td width="35%">';

$html.=''.$mempresa['razon_social'].' <br>
	
RUC:  '.$mempresa['ruc'].'<br> ';
	
$html.='DIR: '.$mempresa['direccion'].'<br>

</td>
<td width="40%" align="center" >REPORTE DE VENTAS POR SERIE<br>
  Desde: '.$fecha_inicio.' Hasta: '.$fecha_fin.' - MONEDA: SOLES<br>
  Concepto Ventas: TODOS</td>';
	
$html.='<td width="25%" align="right" valign="middle">
Fecha: '.$fecha_y_hora.'<br>
<spam class="page-number" ></spam>
</td>
    </tr>
    <tr>
      <td colspan="3"  class="cuerpo"  ><br>';


$html.='



  </td>
    </tr>
  </tbody>
</table>


<table width="100%" cellpadding="0" class="cuerpo4" cellspacing="0"  >
  <tr  class="superior" >
    <td width=10%>Serie</td>
	<td width="10%" align="center">T.Doc</td>
    <td width="20%" align="center">Desde</td>
	<td width="10%" align="center">Hasta</td>
    <td width="20%" align="center">Cantidad</td>
    <td width="20%" align="right">Valor Venta</td>
    <td width="20%" align="right">Igv</td>
    <td width="20%" align="right">Total</td>
  </tr>

  ';
  
/*
  $L=new DateTime($inicio2); 
  $fin2 =$L->format( 'Y-m-t' );
  $dates = generateDates($fecha_inicio, $fecha_fin);
  $cantidaddias=date( 't', strtotime($inicio) );
*/

$cantidadfinal=0;
$subtotafinnal=0;
$igvfinal=0;
$totalfinal=0;

for($i=$fecha_inicio;$i<=$fecha_fin;$i = date("Y-m-d", strtotime($i ."+ 1 days"))){
    //echo dia_semana($i). "<br />";


$sqlventa="SELECT COUNT(*)  AS cantidad, SUM(txtSUB_TOTAL) AS subtotal, SUM(txtIGV) AS igv, SUM(txtTOTAL) AS total  FROM venta WHERE DATE(txtFECHA_DOCUMENTO)='$i' AND beta='$mempresa[tipo]' AND idempresa='$_COOKIE[id]' GROUP BY txtSERIE ORDER by txtFECHA_DOCUMENTO ASC ";
$rowventa= ejecutarConsultaSimpleFila($sqlventa);

if($rowventa){

$html.='
  <tr>
<td colspan="8" class="superior" >Fecha: '.$i.'</td>
  </tr>';


$sql="SELECT COUNT(*)  AS cantidad, SUM(txtSUB_TOTAL) AS subtotal, SUM(txtIGV) AS igv, SUM(txtTOTAL) AS total, txtSERIE, txtID_TIPO_DOCUMENTO FROM venta WHERE DATE(txtFECHA_DOCUMENTO)='$i' AND beta='$mempresa[tipo]' AND idempresa='$_COOKIE[id]' GROUP BY txtSERIE ORDER by txtFECHA_DOCUMENTO ASC";
$rspta=ejecutarConsulta($sql);
while ($reg = $rspta->fetch_object()){

if($reg->txtID_TIPO_DOCUMENTO=='03'){ $tipodoc='BO'; }
if($reg->txtID_TIPO_DOCUMENTO=='01'){ $tipodoc='FA'; }
if($reg->txtID_TIPO_DOCUMENTO=='07'){ $tipodoc='NC'; }
if($reg->txtID_TIPO_DOCUMENTO=='08'){ $tipodoc='ND'; }
if($reg->txtID_TIPO_DOCUMENTO=='90'){ $tipodoc='RE'; }
if($reg->txtID_TIPO_DOCUMENTO=='91'){ $tipodoc='CO'; }
if($reg->txtID_TIPO_DOCUMENTO=='92'){ $tipodoc='NP'; }

$sqlprimero="SELECT * FROM venta WHERE DATE(txtFECHA_DOCUMENTO)='$i' AND beta='$mempresa[tipo]' AND idempresa='$_COOKIE[id]' AND  txtSERIE='$reg->txtSERIE' ORDER by txtFECHA_DOCUMENTO ASC ";
$mostrarprimero= ejecutarConsultaSimpleFila($sqlprimero);

$sqlultimo="SELECT * FROM venta WHERE DATE(txtFECHA_DOCUMENTO)='$i' AND beta='$mempresa[tipo]' AND idempresa='$_COOKIE[id]' AND  txtSERIE='$reg->txtSERIE'  ORDER by txtFECHA_DOCUMENTO DESC ";
$mostrarultimo= ejecutarConsultaSimpleFila($sqlultimo);


$html.='<tr  class="contenido"  >
    <td >'.$reg->txtSERIE.'</td>
    <td >'.$tipodoc.'</td>
    <td >'.$mostrarprimero['txtNUMERO'].'</td>
    <td >'.$mostrarultimo['txtNUMERO'].'</td>
    <td align="center" >'.$reg->cantidad.'</td>
    <td align="right" >'.$reg->subtotal.'</td>
    <td align="right" >'.$reg->igv.'</td>
    <td align="right" >'.$reg->total.'</td>
  </tr>  ';
}

$cantidadfinal=$cantidadfinal+$rowventa['cantidad'];
$subtotafinnal=$subtotafinnal+$rowventa['subtotal'];
$igvfinal=$igvfinal+$rowventa['igv'];
$totalfinal=$totalfinal+$rowventa['total'];

$html.='<tr class="totales" >
    <td colspan="4" >&nbsp;</td>
    <td align="center" >'.$rowventa['cantidad'].'</td>
    <td align="right" >'.$rowventa['subtotal'].'</td>
    <td align="right" >'.$rowventa['igv'].'</td>
    <td align="right" >'.$rowventa['total'].'</td>
  </tr>';

}

}

$html.='<tr class="totales" >
    <td colspan="4" >&nbsp;</td>
    <td align="center" >'.$cantidadfinal.'</td>
    <td align="right" >'.$subtotafinnal.'</td>
    <td align="right" >'.$igvfinal.'</td>
    <td align="right" >'.$totalfinal.'</td>
  </tr>';

$html.='</table>
<br>
</body> </html>







';


   $dompdf = new DOMPDF();
   $dompdf->set_paper('letter','portrait');
   //$dompdf->set_paper('legal','landscape');
//Paper orientation ('portrait' or 'landscape') // portrait (ES VERTICAL)
/*
echo $html;
*/

$dompdf->load_html($html);
$dompdf->render();
$dompdf->set_option("isPhpEnabled", true);

$pdf = $dompdf->output();

file_put_contents($ruta.$fichero.'.pdf', $pdf);

header("Content-type:application/pdf");
echo $pdf;


?>