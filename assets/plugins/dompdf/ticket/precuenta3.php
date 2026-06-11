<?php
//require_once("dompdf/dompdf_config.inc.php");

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

$cont.='MOZO: '.$user['nombre'].'</b><br>';

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

$sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='0' ";
$rspta =ejecutarConsulta($sql);

$sqlsT="UPDATE comandas_detalle SET impresion='1' WHERE idcomanda='$idventa' AND impresion='0' ";
ejecutarConsulta($sqlsT);

}else{

$sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='1' ";
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





