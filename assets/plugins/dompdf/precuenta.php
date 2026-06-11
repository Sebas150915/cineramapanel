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

$fechahoy= date('H:i:s');

$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$diascomandas= $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;

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
.negrita2 { font-size: 20px; font-family: Times, "Times New Roman"; font-weight: bold; }

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
$cont.=''.$mempresa['nombre_comercial'].'<br>';
//$cont.='RUC: '.$mempresa['ruc'].$mostrar['idempresa'].'<br>';

$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['serie'].'-'.$mostrar['numero'].'- '.$mesa['mesa'].'</b><br>';
$cont.='<b style="font-size: 18px">'.$mostrar['nombre'].'</b><br>';
$cont.='<b style="font-size: 18px">'.$diascomandas.'/'.date("H:i:s", strtotime($mostrar['fecha'])).'|</b><br>';


$cont.='</td>
    </tr>
    <tr>
      <td >';


$cont.='MOZO: '.'<b style="font-size: 20px" >'.$usuario.'</b><br>';

$cont.='</td>
    </tr>
  </tbody>
</table>

';
$cont.='
<table class="tabla" border="0" cellspacing="0">

    <tr>
     <td class="negrita2"  width="5%" >Cant.</td>
      <td class="negrita2"  width="40%" >Producto</td>
     
      <td class="negrita2"  width="10%" style="text-align:center" >Prec.</td>
      <td class="negrita2"  width="10%" style="text-align: center" >Total</td>
    </tr>

<tr><td colspan="4" style="text-align: center" ><hr></td></tr>


';



if(isset($_GET['bar'])){

    $sqlco="SELECT SUM(importe) AS total FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0'  ";
    $mostrarco= ejecutarConsultaSimpleFila($sqlco);

    $total=$mostrarco['total'];

    $sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0'  ";
    $rspta =ejecutarConsulta($sql);

    $sqlsT="UPDATE comandas_detalle SET impresion='1' WHERE idcomanda='$idventa' ";
    ejecutarConsulta($sqlsT);

}else{

    $sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0'  ";
    $rspta =ejecutarConsulta($sql);

}
$sqls1="SELECT  SUM(importe) AS importe  FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0'  ";
$s1= ejecutarConsultaSimpleFila($sqls1);


while ($reg = $rspta->fetch_object()){


    $nombre=$reg->unidadmedida;

    $cont.='<tr>
        <td width="5%" >'.round($reg->cantidad).'</td>
      <td width="40%" >'.$reg->nombreproducto.'</td>
      
      <td width="10%" style="text-align:center">'.round($reg->precio, 2).'</td>
      <td width="10%" style="text-align: center" >'.round($reg->importe, 2).'</td>
    </tr>';
}

$cont.='
</table>
<table class="tabla" border="0" cellspacing="0">
<tr><td style="text-align: center" ><hr></td></tr>
</table>

<table border="0" class="tabla" cellspacing="0">   
      <td style="font-size: 22px" ><strong>IMPORTE TOTAL  '.$mf.' </strong></td>
      <td width="30%" style="font-size: 25px" ><strong>'.$s1['importe'].'</strong></td> 
      

</table>';
$cont.='

<table class="tabla" border="0" cellspacing="25">
      <td style="text-align: center" >';

$cont.='Solicite Su Comprobante Electrónico';

$cont.='</td>
    </tr>

</table>';

$cont.='
<table class="tabla" border="0" cellspacing="25">
      <td style="text-align: center" >';

$cont.='DATOS:___________________________';

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