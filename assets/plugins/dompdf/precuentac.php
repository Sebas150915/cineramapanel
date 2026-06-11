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

$sqllp="SELECT *FROM sucursal_pisos WHERE idlocal='$mostrar[idlocal]' ";
$localpiso= ejecutarConsultaSimpleFila($sqllp);

$valmoneda='SOLES'; $mf='S/';

if($mostrar['moneda']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['moneda']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['moneda']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }

$tdocumento='BAR';

$pisos='PISOS  ';
if($mesa['idpiso']=='1'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='2'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='3'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='4'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='5'){ $pisos1='SEGUNDO PISO';}
if($mesa['idpiso']=='6'){ $pisos1='TERCER PISO';}
if($mesa['idpiso']=='7'){ $pisos1='SEGUNDO PISO';}
if($mesa['idpiso']=='8'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='9'){ $pisos1='SEGUNDO PISO';}
if($mesa['idpiso']=='10'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='11'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='12'){ $pisos1='SEGUNDO PISO';}
if($mesa['idpiso']=='13'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='14'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='15'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='16'){ $pisos1='PRIMER PISO';}
if($mesa['idpiso']=='17'){ $pisos1='PISO 3';}
if($mesa['idpiso']=='18'){ $pisos1='HAKUNA';}
if($mesa['idpiso']=='19'){ $pisos1='KOWEN';}
if($mesa['idpiso']=='20'){ $pisos1='SALON 1';}
if($mesa['idpiso']=='23'){ $pisos1='SALOM 2';}
if($mesa['idpiso']=='24'){ $pisos1='OTROS';}
if($mesa['idpiso']=='25'){ $pisos1='RESERVAS';}

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
//selecione para la empresa que deseas
if($mempresa['id']=='0'||$mempresa['id']=='2'){
    $cont.='
<table class="tabla" border="0" cellspacing="0">
  <tbody>';

    $cont.='<tr>
      <td style="text-align: center" >';
    $cont.='<b style="font-size: 28px" >'.$mostrar['serie'].' - '.$mostrar['numero'].'</b><br>';
    $cont.='<b style="font-size: 55px">'.$mostrar['comanda'].'</b><br>';
    $cont.='<b style="font-size: 28px" >'.$pisos1.' - '.$mesa['mesa'].'</b><br>';

    $cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['fecha'])).' / ';
    $cont.=date("H:i:s", strtotime($mostrar['fecha'])).'<br>';
    $cont.='Pedido: '.$fechahoy.'</b><br>';
    $cont.='</td>
    </tr>
    <tr>
      <td >';

    $cont.='MOZO: '.$usuario.'</b><br>';



    $cont.='</td>
    </tr>
  </tbody>
</table>

';
    $cont.='
<table class="tabla" border="0" cellspacing="0">

    <tr>
     <td class="negrita2"  width="5%" >Cant</td>
      <td class="negrita2"  width="60%" >Descripcion</td>

    </tr>

<tr><td colspan="4" style="text-align: center" ><hr></td></tr>


';

    $total=$mostrar['total'];

    if(isset($_GET['bar'])){

        $sqlco="SELECT SUM(importe) AS total FROM comandas_detalle WHERE idcomanda='$idventa' ";
        $mostrarco= ejecutarConsultaSimpleFila($sqlco);

        $total=$mostrarco['total'];

        $sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='0' AND unidadmedida ='LTR' ";
        $rspta =ejecutarConsulta($sql);

        $sqlsT="UPDATE comandas_detalle SET impresion='1' WHERE idcomanda='$idventa' AND impresion='0' ";
        ejecutarConsulta($sqlsT);

    }else{

        $sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='1' AND unidadmedida ='LTR' ";
        $rspta =ejecutarConsulta($sql);

    }


    while ($reg = $rspta->fetch_object()){

        $contcom='';
        if($reg->comentario!=''){ $contcom='<br>(<em>'.$reg->comentario.'</em>)'; }

        $nombre=$reg->unidadmedida;

        $cont.='<tr>    
 <td width="5%" >'.round($reg->cantidad).'</td>
      <td width="40%" >'.$reg->nombreproducto.$contcom.'</td>
     

    </tr>';
    }
    $cont.='


</table>';


    $cont.='
<table class="tabla" border="0" cellspacing="0">
<tr>
      <td style="text-align: center" >';



    $cont.='</td>
  </tr>
  
  

    <tr>
      <td style="text-align: center" >';

    $cont.='______________________________________';

    $cont.='</td>
    </tr>

</table>';
} else {

    $cont.='
<table class="tabla" border="0" cellspacing="0">
  <tbody>';

    $cont.='<tr>
      <td style="text-align: center" >';
    $cont.='<b style="font-size: 28px" >'.$mostrar['serie'].' - '.$mostrar['numero'].'</b><br>';
    $cont.='<b style="font-size: 55px">'.$mostrar['comanda'].'</b><br>';
    $cont.='<b style="font-size: 28px" >'.$pisos1.' - '.$mesa['mesa'].'</b><br>';

    $cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['fecha'])).' / ';
    $cont.=date("H:i:s", strtotime($mostrar['fecha'])).'<br>';
    $cont.='Pedido: '.$fechahoy.'</b><br>';
    $cont.='</td>
    </tr>
    <tr>
      <td >';

    $cont.='MOZO: '.$usuario.'</b><br>';



    $cont.='</td>
    </tr>
  </tbody>
</table>

';
    $cont.='
<table class="tabla" border="0" cellspacing="0">

    <tr>
     <td class="negrita2"  width="5%" >Cant</td>
      <td class="negrita2"  width="60%" >Descripcion</td>
       
      <td class="negrita2"  width="10%" style="text-align:center" >Precio</td>
    </tr>

<tr><td colspan="4" style="text-align: center" ><hr></td></tr>


';

    $total=$mostrar['total'];

    if(isset($_GET['bar'])){

        $sqlco="SELECT SUM(importe) AS total FROM comandas_detalle WHERE idcomanda='$idventa' ";
        $mostrarco= ejecutarConsultaSimpleFila($sqlco);

        $total=$mostrarco['total'];

        $sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='0' AND unidadmedida !='LTR' ";
        $rspta =ejecutarConsulta($sql);

        $sqlsT="UPDATE comandas_detalle SET impresion='1' WHERE idcomanda='$idventa' AND impresion='0' ";
        ejecutarConsulta($sqlsT);

    }else{

        $sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='1' AND unidadmedida !='LTR' ";
        $rspta =ejecutarConsulta($sql);

    }


    while ($reg = $rspta->fetch_object()){

        $contcom='';
        if($reg->comentario!=''){ $contcom='<br>(<em>'.$reg->comentario.'</em>)'; }

        $nombre=$reg->unidadmedida;

        $cont.='<tr>    
 <td width="5%" >'.round($reg->cantidad).'</td>
      <td width="40%" >'.$reg->nombreproducto.$contcom.'</td>
     
      <td width="10%" style="text-align:center">'.round($reg->precio, 2).'</td>
    </tr>';
    }
    $cont.='


</table>';


    $cont.='
<table class="tabla" border="0" cellspacing="0">
<tr>
      <td style="text-align: center" >';



    $cont.='</td>
  </tr>
  
  

    <tr>
      <td style="text-align: center" >';

    $cont.='______________________________________';

    $cont.='</td>
    </tr>

</table>';




}

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