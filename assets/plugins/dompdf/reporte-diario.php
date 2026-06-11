<?php

//require_once("dompdf/dompdf_config.inc.php");
session_start();
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

require_once "../../modelos/Consultas.php";

$consulta=new Consultas();

header("HTTP/1.1");
header("Content-Type: application/json; charset=UTF-8");
	
$jsondata = array();
		
$jsondata['estado'] = '1';
$jsondata['mensaje'] = 'ERROR';	

if(isset($_GET['fecha_inicio'])){  $fecha_inicio=$_REQUEST["fecha_inicio"]; }else{ $fecha_inicio=''; }
if(isset($_GET['fecha_fin'])){  $fecha_fin=$_REQUEST["fecha_fin"]; }else{ $fecha_fin=''; }
if(isset($_GET['idcliente'])){  $idcliente=$_REQUEST["idcliente"]; }else{ $idcliente=''; }
if(isset($_GET['idusuario'])){  $idusuario=$_REQUEST["idusuario"];  }else{ $idusuario=''; }
if(isset($_GET['idlocal'])){ $idlocal=$_REQUEST["idlocal"]; }else{ $idlocal=''; }


if($idcliente==''){ $buscar=""; }else{ $buscar=" AND v.txtID_CLIENTE='$idcliente'"; }
if($idusuario==''){ $buscaru=""; }else{ $buscaru=" AND v.idusuario='$idusuario'"; }	
if($idlocal==''){ $buscarl=""; }else{ $buscarl=" AND v.idlocal='$idlocal'"; }	
		
$sqls3="SELECT SUM(v.txtTOTAL) AS totale, SUM(v.txtSUB_TOTAL) AS stotal, SUM(v.txtIGV) AS igv FROM venta v INNER JOIN persona p ON v.txtID_CLIENTE=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE DATE(v.txtFECHA_DOCUMENTO)>='$fecha_inicio' AND DATE(v.txtFECHA_DOCUMENTO)<='$fecha_fin' AND (v.estado='0' OR v.estado='1' OR v.estado='2' ) AND (v.txtID_TIPO_DOCUMENTO='01' OR v.txtID_TIPO_DOCUMENTO='03')  $buscar $buscaru $buscarl ";
$s3= ejecutarConsultaSimpleFila($sqls3);
		
$sqls4="SELECT SUM(v.txtTOTAL) AS totale, SUM(v.txtSUB_TOTAL) AS stotal, SUM(v.txtIGV) AS igv FROM venta v INNER JOIN persona p ON v.txtID_CLIENTE=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE DATE(v.txtFECHA_DOCUMENTO)>='$fecha_inicio' AND DATE(v.txtFECHA_DOCUMENTO)<='$fecha_fin' AND (v.estado='0' OR v.estado='1' OR v.estado='2' ) AND v.txtID_TIPO_DOCUMENTO='07'  $buscar $buscaru $buscarl ";
$s4= ejecutarConsultaSimpleFila($sqls4);
		
$tote=$s3['totale']-$s4['totale'];
$tote=number_format($tote, 2, '.', '');

$total= $s3['totale'];
$subtotal= $s3['stotal'];
$igv= $s3['igv'];
$restar= $s4['totale'];

$ruta="../../images/descargas/";
$fichero=$fecha_inicio.'-'.$fecha_fin;


 $html =
   '
   
   
   
   <html> 
   <head> 
   <style> 
body{
font:9px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
.cabecera table {
	width: 100%;
    color:black;
    margin-top: 0em;
    text-align: left; font-size: 9px;
}
.cabecera h1 {
    font-size:17px; padding-bottom: 0px; margin-bottom: 0px; te
}

.cabecera2 table { border-collapse: collapse; border: solid 1px #000000;}
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; border: solid 1px #000000; font-size:11px; } 
.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 1px #000000; }
.cuerpo thead { border: solid 1px #000000; } 
.cuerpo2 thead { border: solid 1px #000000; } 

table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 3pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt;  }
.footer { position: fixed; bottom: 150px; font-size:9px;  width: 100%; border: solid 0px #000000; }
.fg { font-size: 10px;} 
.fg2 { text-align: center; }
.fg3 { border: solid 0px; } 
.total td { border: solid 0px; padding: 0px; } 
.total2 { text-align: right; } 

body{
font:16px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}

   </style>
    
   </head> 
    
   <body>        

   


<table width="100%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
	
<td align="center"><h1>REPORTE DE VENTAS Y DEVOLUCIONES</h1>fecha inicia: '.$fecha_inicio.' / fecha final: '.$fecha_fin.'</td>
</tr>
  </tbody>
</table>

<hr>

<table width="100%" class="cuerpo2" border="0" cellspacing="0">
  <thead> 
    <tr>
      <td width="10%">FECHA</td>
      <td width="25%">VENDEDOR</td>
      <td width="30%">CLIENTE</td>
      <td width="15%">COMPROBANTE</td>
      <td width="12%">SERIE-NUM</td>
      <td width="10%"  align="center" >TOTAL</td>
      <td width="10%"  align="center" >IGV</td>
    </tr>
</thead>
<tbody>
';
$rspta=$consulta->ventasfechacliente($fecha_inicio,$fecha_fin,$idcliente, $idusuario, $idlocal);
 		//Vamos a declarar un array
 		$data= Array();
while ($reg=$rspta->fetch_object()){

$tventa='';
	
if($reg->txtID_TIPO_DOCUMENTO=='03'){ $tdocumento='BOLETA'; }
if($reg->txtID_TIPO_DOCUMENTO=='01'){ $tdocumento='FACTURA'; }
if($reg->txtID_TIPO_DOCUMENTO=='90'){ $tdocumento='RECIBO'; }
if($reg->txtID_TIPO_DOCUMENTO=='07'){ $tdocumento='NOTA DE CREDITO'; $tventa='-'; }
if($reg->txtID_TIPO_DOCUMENTO=='08'){ $tdocumento='NOTA DE DEBITO'; }
	
$html.='
<tr>
      <td>'.$reg->fecha.'</td>
      <td>'.$reg->usuario.'</td>
    <td>'.$reg->cliente.' </td>
     <td>'.$tdocumento.'</td>
      <td>'.$reg->txtSERIE.'-'.$reg->txtNUMERO.'</td>
      <td align="center">'.$tventa.$reg->txtTOTAL.'</td>
      <td align="center">'.$tventa.$reg->txtIGV.'</td>
  </tr>';
}
$html.='
  </tbody>
</table>
<hr>

<table width="100%"  border="0" cellspacing="0">
  <tbody>

    <tr>
  <td><br>
    
  </td>
      
  <td width="20%" >
    
    
    <table width="100%" border="0" cellspacing="0"  class="total"  >
      <tbody>
        <tr>
          <td width="50%" class="total2"><strong> INGRESOS:</strong></td><td><strong>'.$total.'</strong></td></tr>
        <tr>
          <td class="total2"><strong>SALIDAS:</strong></td><td><strong>'.$restar.'</strong></td></tr>
        <tr>
          <td class="total2"><strong>CAJA:</strong></td><td><strong>'.$tote.'</strong></td></tr>
        
        </tbody>
      </table>
    
  </td>
      
    </tr>
  </tbody>
</table>





</body> </html>







   
   
 ';
   $dompdf = new DOMPDF();
  $dompdf->set_paper('legal','potrait');
   //$dompdf->set_paper('legal','landscape');
   $dompdf->load_html($html);
   $dompdf->render();
   //$dompdf->stream("pdf".Date('Y-m-d').".pdf");
//$dompdf->stream("ejemplo-basico.pdf", array('Attachment' => 0));
$pdf = $dompdf->output();
//file_put_contents('../'.$ruta, $pdf);
file_put_contents($ruta.$fichero.'.pdf', $pdf);

$jsondata['mensaje'] = 'TODO BIEN!';	
echo json_encode($jsondata);

?>