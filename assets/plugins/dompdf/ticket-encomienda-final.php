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
include "../phpqrcode/qrlib.php";

require "../../modelos/resumen.php";
require "../../modelos/numeros-letras.php";

$resumen=new Resumen();

$idventa=$_GET['id'];



$sql="SELECT *FROM venta WHERE idventa='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);
		
$sqlen="SELECT *FROM venta_pasajes WHERE idventa='$idventa' ";
$enc= ejecutarConsultaSimpleFila($sqlen);
		
$grabada=round(($mostrar['txtSUB_TOTAL']-$mostrar['exonerado']), 2);
		
$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
$sqlus="SELECT *FROM usuario WHERE idusuario='$mostrar[idusuario]' ";
$us= ejecutarConsultaSimpleFila($sqlus);
		
$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);
		
$sqll2="SELECT *FROM sucursal WHERE id='$enc[iddestino]' ";
$local2= ejecutarConsultaSimpleFila($sqll2);
		
$sqls="SELECT *FROM categoria WHERE idcategoria='$mcliente[sector]' ";
$se= ejecutarConsultaSimpleFila($sqls);
		
if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; }
		
if($mostrar['txtID_TIPO_DOCUMENTO']=='03'){ $tdocumento='BOLETA DE VENTA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='90'){ $tdocumento='RECIBO'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='91'){ $tdocumento='COTIZACIÓN'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='92'){ $tdocumento='NOTA DE PEDIDO'; }
		
$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['txtSERIE'].' | '.$mostrar['txtNUMERO'].' | '.$mostrar['txtIGV'].' | '.$mostrar['txtTOTAL'].' | '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';
		
$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['txtSERIE'].' | '.$mostrar['txtNUMERO'].' | '.$mostrar['txtIGV'].' | '.$mostrar['txtTOTAL'].' | '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';
		
$rutaqr=$mempresa['ruc'].".png";
QRcode::png($text, $rutaqr, 'Q', 15, 0);

$cont='

<style type="text/css">
@page {
            margin-top: 0.9em;
            margin-left: 0.9em;
        }

.cuerpo { float: left; width: 100%; font-size: 12px; font-family: Arial; }
.tabla { font-size: 12px;  width: 255px; }
.centrado { text-align: center; }
.negrita { font-weight: bold; }
.logo { max-width: inherit;  width: 100%; height: auto; }
H3 { font-size: 13px; margin:0px; padding: 0px; }
body{
font:16px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
  </style>
<table class="tabla" border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
    <td colspan="2" align="center">
    <br>
  
  ';


if($nivel=='0'){
  $cont.='<img src="../../files/logo/'.$mempresa['id'].'.png" width="70%"  />';
}else{
$cont.='<img src="'.RUTA.'/files/logo/'.$mempresa['id'].'.png" width="70%"  />';	
}
	
$cont.='
</td>
  </tr>

<tr>
    <td colspan="2" align="center">
    <br>'.$mempresa['razon_social'].'<br>';
$cont.='RUC: '.$mempresa['ruc'].' - '.$mempresa['direccion'].' Telf:'.$mempresa['telefono'].' <br>';
$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</b>
</td>
</tr>
<tr>
<td colspan="2">
  ';

$cont.='DATOS DEL CLIENTE: <br>';		
$cont.=$mcliente['nombre'].'<br>';		
$cont.=$mcliente['tipo_documento'].': '.$mcliente['txtID_CLIENTE'].'<br>';
$cont.='<hr>';		
$cont.='CONSIGNATARIO: '.$enc['nombre'].'<br>';		
$cont.='DNI: '.$enc['dni'].'<br><br>';
		
$cont.='
</td>
</tr>
<tr>
<td colspan="2">
<b>ORIGEN OFICINA: '.$local['sucursal'].'<br>';	
$cont.='DESTINO OFICINA: '.$local2['sucursal'].'</b>
</td>
</tr><br>
</tbody>
</table>

';
		

$cont.='

<table class="tabla" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td width="80%" >Detalle</td>
      <td width="5%" >Cant.</td>
      <td width="15%" style="text-align: right" >Imp</td>
    </tr>

<tr>
<td colspan="3">
<hr>
</td>
</tr>

';
		

$rspta = $resumen->detfactura($mostrar['idventa']);
while ($reg = $rspta->fetch_object()){	
	
$sqls1="SELECT *FROM articulo_unidad WHERE idproducto='0' AND medida='$reg->unidadmedida' ";
$umedida= ejecutarConsultaSimpleFila($sqls1);

if(!$umedida){
$sqls1="SELECT *FROM articulo_unidad WHERE idproducto='$reg->idproducto' AND medida='$reg->unidadmedida' ";
$umedida= ejecutarConsultaSimpleFila($sqls1);
}
	
$cont.='<tr>
      <td width="80%" >'.$reg->nombreproducto.'</td>
      <td width="5%" >'.$reg->txtCANTIDAD_ARTICULO.'</td>
      <td width="15%" style="text-align: right" >'.$reg->importe.'</td>
    </tr>';
}	
$cont.='</tbody>
</table>

<table width="100%" border="0" class="tabla" cellspacing="0">
  <tbody>
 
  <tr>
    <td colspan="2" align="center">
 <hr>
</tr>
 
    
<tr>
      <td width="59%" align="right" >Op Gravada</td>
      <td width="41%" style="text-align: right" >'.$grabada.'</td>
    </tr>
	
<tr>
      <td align="right" >Gratuita</td>
      <td style="text-align: right" >'.$mostrar['gratuita'].'</td>
</tr>
	
<tr>
      <td align="right" >Exonerado</td>
      <td style="text-align: right" >'.$mostrar['exonerado'].'</td>
    </tr>
	

    
<tr>
      <td align="right" >I.G.V. S/</td>
      <td style="text-align: right" >'.$mostrar['txtIGV'].'</td>
    </tr>
  <tr>
      <td align="right">Total S/</td>
      <td style="text-align: right" >'.$mostrar['txtTOTAL'].'</td>
    </tr>  
</tbody>
</table>
';
$cont.='


<table class="tabla" border="0" cellpadding="0" cellspacing="0">
  <tbody>
  <tr>
    <td colspan="2" align="center">
 <hr>
</tr>
<tr>
    
  <tr>
    <td colspan="2" align="center">


CLAVE: '.$enc['clave'].'<br>';

$cont.='VEND.: '.$us['nombre'].'<br>';

$cont.='FECHA DE EMISIÓN: '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';
$cont.='HORA: '.date("H:i:s", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';

$cont.='SON: '.numtoletras($mostrar['txtTOTAL']).'</td>
</tr>
<tr>
<td colspan="2">
<br>';
//$cont.='<div style="text-align: center"><br><img src="'.$rutaqr.'" style="width: 40%; height: auto;"  /><br><br></div>';	

if($nivel=='0'){
  $cont.='<tr><td colspan="2" align="center"><img src="'.$rutaqr.'"  width="50" height="50" /></td></tr>';
}else{
$cont.='<tr><td colspan="2" align="center"><img src="'.RUTA.'/plugins/dompdf/'.$rutaqr.'"  width="110" height="110" /></td></tr>';	
}


$cont.='<tr><td colspan="2">Representación impresa de la '.$tdocumento.'  <br></tr>
<tr>';	
$cont.='
<tr><td colspan="2" style="font-size:9px;" >
<br>GRACIAS POR SU PREFERENCIA

<br><br>
Terminos y condiciones de encomiendas. 
1 al momento de su entrega a la empresa . La encomienda debe estar debidamente embalada para su trasporte la empresa no sera responsable por el deteriorio debido al mal embalaje la empresa tampoco responde por la descomposicion de los productos peresibles contenido en la encomienda 
2 el remitente y el destinatario estan obligados a mostrar el contenido de la encomienda en el momento que sea requerido por el personal durante el viaje por la autoridad policial , fiscal y aduanera la empresa no se responzabiliza del contenido de las encomiendas 
3 las encomiendas que no hubieran sido requeridas o que no hubieran sido entregadas por error de la direccion estaran depositadas 60 dias en la oficina de la empresa del lugar del destino vence el plazo se entregara a las autoridades conpetentes 
4 la empresa indemnizara la perdida de la encomienda tan solo DIEZ VECES el flete pagado conforme al Art 21/10/21 regl transportes previa presentacion de este documento .La empresa no responde por perdida de encomienda que contenga dinero , joyas u otros objetos de valor que no hubiesen sido previamente  declarados  
5 no sera aceptado ningun reclamo luego que el destinatario firme este documento de conformidad con la recepcion de su encomienda 
6 el  cliente acepta condiciones del trasporte de la encomienda

</tr>
<tr>

';

$cont.='</tbody>
</table>';

if($nivel=='0'){
header("Content-type:application/pdf");
	
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

   $dompdf->load_html($cont);
   $dompdf->render();

$dompdf = new Dompdf();
$dompdf->set_paper(array(0,0,210,$GLOBALS['bodyHeight']+50));
$dompdf->loadHtml($cont);
$dompdf->render();
$dompdf->stream($filename,array("Attachment"=>0));
$pdf = $dompdf->output();

echo $dompdf;

}else{
	echo $cont;
}
?>