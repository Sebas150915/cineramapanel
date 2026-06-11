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
<table class="tabla" border="0" cellspacing="0" style="width: 100% !important;">
  <tbody>';

if($mostrar['txtID_TIPO_DOCUMENTO']!='90'){
$cont.='<tr>
      <td style="text-align: center" >';
$cont.='<img src="'.RUTA.'/files/logo/'.$mempresa['id'].'b.jpg?v=0" style="width: 260px; max-height: 190px; " />';
$cont.='</td>
    </tr>';


$cont.='<tr>
      <td style="text-align: center" >';
$cont.=''.$mempresa['razon_social'].'<br>';
$cont.='RUC: '.$mempresa['ruc'].'<br>';
$cont.='DIR: '.$mempresa['direccion'].'<br>';
$cont.='Telf: '.$mempresa['telefono'].'<br>';
$cont.=' <br>';
$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</b><br>';
$cont.=' <br>';

$cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' / ';
$cont.=date("H:i:s", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';


$cont.='</td>
    <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>
      <td >';

$cont.='DATOS DEL CLIENTE: <br>';
$cont.=$mcliente['nombre'].'<br>';
$cont.=$mcliente['tipo_documento'].':' .$mcliente['txtID_CLIENTE'].'<br>';
$cont.='DIR:'.$mcliente['direccion'].'<br>';
$cont.='FORMA PAGO:'.$formapago.'<br>';


$cont.='

<table class="tabla" border="0" cellspacing="0" style="width: 100% !important;"> 
       <tr >
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>      
    <tr>
      <td width="40%" >Prod.</td>
      <td width="5%" style="text-align:center" >Cant.</td>
      <td width="10%" style="text-align:center" >Precio</td>
      <td width="10%" style="text-align: center" >Total</td>
    </tr>
    
        <tr >
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>

   <td >';


    $sql="SELECT *FROM detalle_venta WHERE idventa='$idventa' ";
    $rspta =ejecutarConsulta($sql);

while ($reg = $rspta->fetch_object()){
    $nombre=$reg->unidadmedida;


    $cont.='<tr>
      <td width="40%" >'.strtoupper($reg->nombreproducto, 'UTF-8').'</td>
      <td width="5%" style="text-align:center" >'.round($reg->txtCANTIDAD_ARTICULO, 2).'</td>
      <td width="10%" style="text-align:center">'.round($reg->precio, 2).'</td>
      <td width="10%" style="text-align:center">'.$reg->importe.'</td>
    </tr>';
}
$cont.='
        <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>

    <tr>
      <td colspan="4" style="text-align: right !important;">Op Gravada:  '.$mf.'.</td>
      <td style="text-align: right" >'.number_format($mostrar['txtSUB_TOTAL'], 2).'</td>
    </tr>
	
    <tr>
      <td colspan="4" style="text-align: right !important;">Gratuita:  '.$mf.'.</td>
      <td  style="text-align: right" >'.number_format($mostrar['gratuita'], 2).'</td>
    </tr>
	
    <tr>
      <td colspan="4" style="text-align: right !important;">Exonerado:  '.$mf.'.</td>
      <td  style="text-align: right" >'.number_format($mostrar['exonerado'], 2).'</td>
    </tr>
	
    
    <tr>
      <td colspan="4" style="text-align: right !important;">I.G.V.  '.$mf.'.</td>
      <td  style="text-align: right" >'.number_format($mostrar['txtIGV'], 2).'</td>
    </tr>
	
     <tr>
      <td colspan="4" style="text-align: right !important;">ICBPER:  '.$mf.'.</td>
      <td style="text-align: right" >'.number_format($mostrar['ICB'], 2).'</td>
      </tr>
    
     <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
     </tr>

    <tr>
      <td colspan="4" style="text-align: right !important;">IMPORTE TOTAL:  '.$mf.'.</td>
      <td  style="text-align: right" >'.number_format($mostrar['txtTOTAL'], 2).'</td>
    </tr>
    
      <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
      </tr>
 


     <tr>
     <td  colspan="5" style="text-align: center !important; margin-top: 15px" >SON: '.numtoletras($mostrar['txtTOTAL'], $valmoneda).'
     </td>
     </tr>

    <tr>
    <td  colspan="5" style="text-align: center !important; " >Observación:'.$mostrar['txtOBSERVACION'].'</td>
   </tr>

    <tr>
    <td  colspan="5" style="text-align: center !important; " ><img src="'.RUTA.'/plugins/dompdf/'.$mempresa['ruc'].'.png" style="width: 120px; max-height: 120px; " /></td>
    </tr>
    
    <tr>
    <td  colspan="5" style="text-align: center !important; " >Representación impresa de '.$tdocumento.' Consulte su comprobante (pdf,cdr) aquí: '.RUTA.'/comprobantes/ </td> 
    </tr>
    
</table>';


} else {

    $cont.='<tr>
      <td style="text-align: center" >';
    $cont.='<img src="'.RUTA.'/files/logo/'.$mempresa['id'].'b.jpg" style="width: 260px; max-height: 190px; " />';
    $cont.='</td>
    </tr>';


    $cont.='<tr>
      <td style="text-align: center" >';

    $cont.='Telf: '.$mempresa['telefono'].'<br>';
    $cont.=' <br>';
    $cont.='<b>'.$tdocumento.'</b><br>';
    $cont.='<b>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</b><br>';
    $cont.=' <br>';

    $cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' / ';
    $cont.=date("H:i:s", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';


    $cont.='</td>
    <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>
      <td >';

    $cont.='CLIENTE: <br>';
    $cont.=$mcliente['nombre'].'<br>';
    $cont.=$mcliente['tipo_documento'].':' .$mcliente['txtID_CLIENTE'].'<br>';

    $cont.='

<table class="tabla" border="0" cellspacing="0" style="width: 100% !important;"> 
       <tr >
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>      
    <tr>
      <td width="5%" >Cant</td>
	  <td width="5%" >UM</td>
      <td width="45%" align="center" >Descripción</td>
      <td width="15%" style="text-align:center" >P.U.</td>
      <td width="15%" style="text-align: center" >Total</td>
    </tr>
    
        <tr >
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>

   <td >';


    $sql="SELECT *FROM detalle_venta WHERE idventa='$idventa' ";
    $rspta =ejecutarConsulta($sql);

    while ($reg = $rspta->fetch_object()){

        $nombre=$reg->unidadmedida;
        $cont.='<tr>
      <td width="40%" >'.mb_strtoupper($reg->nombreproducto, 'UTF-8').'</td>
      <td width="5%" style="text-align:center" >'.round($reg->txtCANTIDAD_ARTICULO, 2).'</td>
      <td width="10%" style="text-align:center">'.round($reg->precio, 2).'</td>
      <td width="10%" style="text-align:center">'.$reg->importe.'</td>
    </tr>';
    }
    $cont.='
        <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
       </tr>



    <tr>
      <td colspan="4" style="text-align: right !important;">IMPORTE TOTAL:  '.$mf.'.</td>
      <td  style="text-align: right" >'.number_format($mostrar['txtTOTAL'], 2).'</td>
    </tr>
    
      <tr>
      <td colspan="5" style="text-align: right !important; border-bottom: 2px solid #000d06 !important;"></td>
      </tr>
 


     <tr>
     <td  colspan="5" style="text-align: center !important; margin-top: 15px" >SON: '.numtoletras($mostrar['txtTOTAL'], $valmoneda).'
     </td>
     </tr>

    <tr>
    <td  colspan="5" style="text-align: center !important; " >Observación:'.$mostrar['txtOBSERVACION'].'</td>
   </tr>

    
    <tr>
    <td  colspan="5" style="text-align: center !important; " >Solicite su comprobante electrónico </td> 
    </tr>
    
</table>';

}

