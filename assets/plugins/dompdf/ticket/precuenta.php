<?php

$cont='<style type="text/css">
.tabla { font-size: 15px; width: 390px; }


.centrado { text-align: center; }
.negrita { font-weight: bold; }

body{
font:15px Arial, Tahoma, Verdana, Helvetica, sans-serif;
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
//$cont.='RUC: '.$mempresa['ruc'].$mostrar['idempresa'].'<br>';

$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['serie'].'-'.$mostrar['numero'].'- '.$mesa['mesa'].'</b><br>';
$cont.='<b style="font-size: 18px">'.$mostrar['nombre'].'</b><br>';
$cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['fecha'])).' / ';
$cont.=date("H:i:s", strtotime($mostrar['fecha'])).'<br>';


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

    $sqlco="SELECT SUM(importe) AS total FROM comandas_detalle WHERE idcomanda='$idventa' AND ( estado='0' AND impresion='1')";
    $mostrarco= ejecutarConsultaSimpleFila($sqlco);

    $total=$mostrarco['total'];

    $sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='0' ";
    $rspta =ejecutarConsulta($sql);

    $sqlsT="UPDATE comandas_detalle SET impresion='1' WHERE idcomanda='$idventa' ";
    ejecutarConsulta($sqlsT);

}else{

    $sql="SELECT *FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='1' ";
    $rspta =ejecutarConsulta($sql);

}
$sqls1="SELECT  SUM(importe) AS importe  FROM comandas_detalle WHERE idcomanda='$idventa' AND estado='0' AND impresion='1' ";
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

$cont.='DATOS1:___________________________';

$cont.='</td>
    </tr>

</table>';


