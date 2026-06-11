<?php
//require_once("dompdf/dompdf_config.inc.php");
$rutat=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

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

$idventa=$_GET['id'];
$android=$_GET['android'];

$sql="SELECT *FROM comandas WHERE id='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sqlme="SELECT *FROM mesas WHERE id='$mostrar[idmesa]' ";
$mesa= ejecutarConsultaSimpleFila($sqlme);
		
$sql2="SELECT * FROM persona WHERE idpersona='$mostrar[idcliente]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
		
$sqluser="SELECT *FROM usuario WHERE idusuario='$mostrar[idusuario]' ";
$user= ejecutarConsultaSimpleFila($sqluser);
$usuario='';
if($user){
    $usuario=$user['nombre'];
} else {
    $usuario=$_COOKIE['nombre'];
}
$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);
	
$valmoneda='SOLES'; $mf='S/';

if($mostrar['moneda']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['moneda']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['moneda']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }
		
$tdocumento='TICKET DE VENTA';
	
$cont='<style type="text/css">
.tabla { font-size: 20px; width: 390px; }
.centrado { text-align: center; }
.negrita { font-weight: bold; }

body{
font:16px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
@page {
            margin-top: 0.9em;
            margin-left: 0.9em;
        }

  </style>';
		
	
		
$cont.='
<table class="tabla" border="0" cellspacing="0">
  <tbody>';


$cont.='<tr>
      <td style="text-align: center" >';
$cont.=''.$mempresa['razon_social'].'<br>';
$cont.='RUC: '.$mempresa['ruc'].$mostrar['idempresa'].'<br>';

$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['serie'].'-'.$mostrar['numero'].'</b><br>';
$cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['fecha'])).' / ';
$cont.=date("H:i:s", strtotime($mostrar['fecha'])).'<br>';

		  
$cont.='</td>
    </tr>
    <tr>
      <td >';
$cont.='VENDEDOR: '.$usuario.'<br>';
$cont.='MESA: '.$mesa['mesa'].'<hr>';	
$cont.='CLIENTE: '.$mcliente['nombre'].'<br>';		
$cont.=$mcliente['tipo_documento'].': '.$mcliente['txtID_CLIENTE'].'<hr>';	
		  
		  
$cont.='</td>
    </tr>
  </tbody>
</table>

';
$cont.='
<table class="tabla" border="0" cellspacing="0">

    <tr>
      <td width="45%" >Prod.</td>
      <td width="5%" >C.</td>
      <td width="15%" style="text-align:center" >Pre.</td>
      <td width="15%" style="text-align: center" >Tot</td>
    </tr>

';
		
$total=$mostrar['total'];

if(isset($_GET['bar'])){
	
$sqlco="SELECT SUM(importe) AS total FROM comandas_detalle WHERE idcomanda='$idventa' ";
$mostrarco= ejecutarConsultaSimpleFila($sqlco);
	
$total=$mostrarco['total'];
	
$sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='0' ";
$rspta =ejecutarConsulta($sql);

$sqlsT="UPDATE comandas_detalle SET impresion='1' WHERE idcomanda='$idventa' AND impresion='0' ";
ejecutarConsulta($sqlsT);
	
}else{

$sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' ";
$rspta =ejecutarConsulta($sql);
	
}

while ($reg = $rspta->fetch_object()){
	
$nombre=$reg->unidadmedida;	
	
$cont.='<tr>
      <td width="45%" >'.$reg->nombreproducto.'</td>
      <td width="5%" >'.round($reg->cantidad).'</td>
      <td width="15%" style="text-align:center">'.round($reg->precio, 2).'</td>
      <td width="15%" style="text-align: right" >'.round($reg->importe, 2).'</td>
    </tr>';
}	
$cont.='
</table>

<table class="tabla" border="0" cellspacing="0">
<tr><td style="text-align: center" ><hr></td></tr>
</table>

<table border="0" class="tabla" cellspacing="0">
    
      <td >Importe total  '.$mf.'</td>
      <td width="15%" style="text-align: right" >'.$total.'</td>
    </tr>

</table>';


$cont.='
<table class="tabla" border="0" cellspacing="0">
<tr>
      <td style="text-align: center" >';	  
$cont.='<br>SON: '.numtoletras($total, $valmoneda);

		  
$cont.='</td>
  </tr>
  
  

    <tr>
      <td style="text-align: center" >';

$cont.='GRACIAS POR SU PREFERENCIA';
		  
$cont.='</td>
    </tr>

</table>';
	

$filename = "newpdffile";
   $dompdf = new DOMPDF();
   //$dompdf->set_paper('letter','portrait');

$dompdf->set_paper(array(0,0,210,800));

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
echo $cont;
*/

if($android=='0'){

   $dompdf->load_html($cont);
   $dompdf->render();

$dompdf = new Dompdf();
$dompdf->set_paper(array(0,0,210,$GLOBALS['bodyHeight']+50));
$dompdf->loadHtml($cont);
$dompdf->render();
$dompdf->stream($filename,array("Attachment"=>0));
}else{
echo $cont;	
}


?>