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

require "../../modelos/numeros-letras.php";
include "../phpqrcode/qrlib.php";
require "../../modelos/resumen.php";
require "../../modelos/Venta.php";


$venta=new Venta();
$resumen=new Resumen();

$id=$_GET['id'];

$sql="SELECT *FROM venta WHERE idventa='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sqla="SELECT *FROM categoria WHERE idcategoria='1' ";
$area= ejecutarConsultaSimpleFila($sqla);

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

$ruta="../../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero='D-'.$mempresa['ruc'].'-'.$mostrar['serie'];

$valmoneda='SOLES';


$tdocumento='ORDEN DE CARGA';

if($mostrar['tipo']=='10'){ $tdocumento='VALE DE SALIDA'; }
if($mostrar['tipo']=='11'){ $tdocumento='TRANSFERENCIA ENTRE ALMACENES'; }
if($mostrar['tipo']=='12'){ $tdocumento='RETIRO'; }
if($mostrar['tipo']=='04'){ $tdocumento='CONSIGNACIÓN ENTREGADA'; }


 $html ='






<html> 
   <head> 
   <style> 
body{
font:13px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}

@page {
            margin-top: 0.9em;
            margin-left: 0.9em;
        }

.cabecera table {
	width: 100%;
    color:black;
    margin-top: 0em;
    text-align: left; font-size: 14px;
}
.cabecera h1 {
    font-size:22px; padding-bottom: 0px; margin-bottom: 0px;
}

.cabecera2 table { border-collapse: collapse; width: 100%; }
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; border: font-size:14px; } 
.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }

.cuerpo { text-align: center;  width: 115%; font-size:14px; ; padding: 0px; margin: 0px; }
.cuerpo table { border-collapse: collapse; margin-top:1px; text-align: center; }
.cuerpo thead { #000000; text-align: center; } 

.cuerpo3 {  width: 115%; font-size:14px; }
.cuerpo3 table { border-collapse: collapse; }
.cuerpo3 thead { #000000;  } 

.cuerpo2 thead { border: solid 1px #000000; } 

table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 3pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt; border: solid 1px #000000; }
.footer { font-size:11px;  width: 100%; border: solid 0px #000000; }
.fg { font-size: 11px;} 
.fg2 { text-align: center; }
.fg3 { border: solid 0px; } 
.total td { border: solid 0px; padding: 0px; } 
.total2 { text-align: right; } 

   </style>
    
   </head> 
    
   <body>        

   



<table width="100%" class="cuerpo cabecera" border="0" cellspacing="0">
  <tbody>
    <tr>
      <td >
		  
<h1>'.$mempresa['nombre_comercial'].'</h1>
  <strong>SUCURSAL:</strong> '.$local['direccion'].'<br>
  <strong>TELF. PRINCIPAL:</strong> '.$mempresa['telefono'].'
		  
		  RUC N° '.$mempresa['ruc'].' <br>'.$tdocumento.'<br>'.$mostrar['serie'].'-'.$mostrar['numero'].'<br>
		  
O.Servicio° '.$mostrar['oservicio'].'  
		  
		  </td>
    </tr>

  </tbody>
</table>


<table width="100%" class="cuerpo3 cabecera" cellspacing="0">
<thead>

    <tr>
      <td width="33%">FECHA:<br>ORIGEN:<br>ÁREA DESTINO:</td>
      <td >'.date("Y-m-d", strtotime($mostrar['fecha'])).' <br>'.$local['sucursal'].'<br>'.$area['nombre'].'/'.$mostrar['area'].'</td>
    </tr>

  </thead>
</table>

<table width="100%" class="cuerpo2" border="0" cellspacing="0">
  <thead> 
    <tr>

      <td width="39%">DESCRIPCION</td>
	  <td width="15%" align="center">U/MED</td>
      <td width="8%" align="center">PRECIO</td>
      <td width="8%" align="center">CTI</td>
      <td width="8%" align="center">TOTAL</td>

    </tr>
</thead>
<tbody>
';

$datos = "SELECT * FROM venta_opdetallet WHERE idpedido='$id' ORDER BY id DESC  ";
$sql = ejecutarConsulta($datos);
while ($rowd = mysqli_fetch_array($sql)) {

$sqls1="SELECT *FROM articulo_unidad WHERE id='$rowd[idpresentacion]' ";
$suc1= ejecutarConsultaSimpleFila($sqls1);
	
$nombre=$suc1['nombre'];
if(!$suc1){
$nombre='UNIDAD';
}	
	
$html.='
<tr>

    <td>'.$rowd['nombreproducto'].' </td>
	<td>'.$nombre.'</td>
      <td align="center">'.number_format($rowd['precio'], 2).'</td>
      <td align="center">'.$rowd['cti'].'</td>
      <td align="center">'.number_format($rowd['importe'], 2).'</td>
    </tr>';

}
					//}
$html.='
  </tbody>
</table>


OBSERVACIONES: '.$mostrar['observaciones'].'


</div><br>
<table width="100%" class="cuerpo" border="0" cellspacing="0">
  <tbody>
    <tr>
      <td>
		  <br><br><br><br>
		  <hr></td>
    </tr>
    <tr>
      <td align="center">RESPONSABLE DE ENTREGA</td>
    </tr>
    <tr>
      <td align="left">NOMBRE:</td>
    </tr>

    <tr>
      <td><br><br><br><br><hr></td>
    </tr>
    <tr>
      <td align="center">RESPONSABLE DE RECEPCION</td>
    </tr>
    <tr>
      <td align="left">NOMBRE:</td>
    </tr>
  </tbody>
</table>
</body> </html>



   
 ';



$filename = "newpdffile";
   $dompdf = new DOMPDF();
   //$dompdf->set_paper('letter','portrait');
$dompdf->set_paper(array(0,0,210,400));
$GLOBALS['bodyHeight'] = 0;
$dompdf->setCallbacks(
  array(
    'myCallbacks' => array(
      'event' => 'end_frame', 'f' => function ($infos) {
        $frame = $infos["frame"];
        if (strtolower($frame->get_node()->nodeName) === "body") {
            $padding_box = $frame->get_padding_box();
            $GLOBALS['bodyHeight'] += $padding_box['h'];
        }
      }
    )
  )
);

/*
echo $html;
*/
   $dompdf->load_html($html);
   $dompdf->render();

$dompdf = new Dompdf();
$dompdf->set_paper(array(0,0,210,$GLOBALS['bodyHeight']+60));
$dompdf->loadHtml($html);

$dompdf->render();
$dompdf->stream($filename,array("Attachment"=>0));


?>