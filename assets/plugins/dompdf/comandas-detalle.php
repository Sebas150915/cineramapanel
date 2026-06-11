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

$idventa=$_GET['id'];

$sql="SELECT *FROM comandas WHERE id='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
$sql2="SELECT * FROM persona WHERE idpersona='$mostrar[idcliente]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
		
$sqluser="SELECT *FROM usuario WHERE idusuario='$mostrar[idusuario]' ";
$user= ejecutarConsultaSimpleFila($sqluser);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);
		
if($mostrar['moneda']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['moneda']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['moneda']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }
		
$tdocumento='TICKET DE VENTA';
	
$cont='<style type="text/css">
.tabla { font-size: 17px; width: 108%; }
.centrado { text-align: center; }
.negrita { font-weight: bold; }


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


		  
$cont.='</td>
    </tr>
    <tr>
      <td >';

$cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['fecha'])).' / ';
$cont.=date("H:i:s", strtotime($mostrar['fecha'])).'<br>';

$cont.='VENDEDOR: '.$user['nombre'].'<br>';	
$cont.='COMANDA: '.$mostrar['comanda'];
$cont.='<hr>';	  
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
		
$sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' ";
$rspta =ejecutarConsulta($sql);

while ($reg = $rspta->fetch_object()){
$tit=''.$reg->nombreproducto;	
if($reg->estado=='1'){ $tit='('.$reg->nombreproducto.') CANCELADA: '.$reg->comentario2; }
	
$cont.='<tr>
      <td width="45%" >'.$tit.'</td>
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
    
      <td align="right" >Importe total  '.$mf.' '.$mostrar['total'].'</td>
      </tr>

</table>';



	

$filename = "newpdffile";
   $dompdf = new DOMPDF();
   //$dompdf->set_paper('letter','portrait');

$dompdf->set_paper(array(0,0,300,800));

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


   $dompdf->load_html($cont);
   $dompdf->render();

$dompdf = new Dompdf();
$dompdf->set_paper(array(0,0,300,$GLOBALS['bodyHeight']+50));
$dompdf->loadHtml($cont);
$dompdf->render();
$dompdf->stream($filename,array("Attachment"=>0));



?>