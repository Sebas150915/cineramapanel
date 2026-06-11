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

$id=$_GET['id'];

$sql="SELECT *FROM venta WHERE idventa='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }

$subtotal=$mostrar['txtTOTAL']-$mostrar['txtIGV'];		
$subtotal= number_format($subtotal, 2);

$ruta="../../api_cpe/".$tipop."/".$mempresa['ruc']."/";

if (!file_exists($ruta)){
mkdir($ruta, 0755);
}

$fichero=$mempresa['ruc'].'-'.$mostrar['txtID_TIPO_DOCUMENTO'].'-'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];

if($mostrar['txtID_TIPO_DOCUMENTO']=='03'){ $tdocumento='BOLETA DE VENTA ELECTRÓNICA'; $ndoc='DNI'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; $ndoc='RUC'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='91'){ $tdocumento='COTIZACIÓN'; $ndoc='DOC N°'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='92'){ $tdocumento='NOTA DE PEDIDO'; $ndoc='DOC N°'; }

$medpago=$mostrar['tipo_pago'];

// Los delimitadores pueden ser barra, punto o guión
$fecha = date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO']));
$fechaf = explode('-', $fecha);
$anio= $fechaf[2].'/'.$fechaf[1].'/'.$fechaf[0];

$fechaf2 = explode('-', $mostrar['fecha_vto']);
$vto=$fechaf2[2].'/'.$fechaf2[1].'/'.$fechaf2[0];


//QRcode::png("".$text);
//DATOS OBLIGATORIOS DE LA SUNAT EN EL QR
//RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE //EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |

$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['txtSERIE'].' | '.$mostrar['txtNUMERO'].' | '.$mostrar['txtIGV'].' | '.$mostrar['txtTOTAL'].' | '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';
QRcode::png($text, $mempresa['ruc'].".png", 'Q',15, 0);



 $html =
   '




<html> 
   <head> 
   <style> 
body{
font:14px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
.cabecera table {
	width: 100%;
    color:black;
    margin-top: 0em;
    text-align: left; font-size: 18px;
}
.cabecera h1 {
    font-size:17px; padding-bottom: 0px; margin-bottom: 0px; te
}

.cabecera2 table { border-collapse: collapse; border: solid 1px #000000;}
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; border: solid 1px #000000; font-size:16px; } 
.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 1px #000000; }
.cuerpo thead { border: solid 1px #000000; } 
.cuerpo2 thead { border: solid 1px #000000; }

.cuenta { font-size: 16px; } 
	   
.cuerpo3 { border: solid 0px #000000; } 
.cuerpo3 th, .cuerpo3 td { border: solid 0px #000000; padding: 0pt; } 
.cuerpo4 { border: solid 1px #000000; } 
.cuerpo4 th, .cuerpo4 td { border: solid 1px #000000;  padding: 3pt; } 

.cu th, .cue td { border: solid 1px #000000; padding: 5pt; } 
.cue {
	width: 50%;
}



table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 3pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt; border: solid 1px #000000; }
.footer { position: fixed; bottom: 100px; font-size:14px;  width: 100%; border: solid 1px #000000; }
.fg { font-size: 14px; } 
.fg2 { text-align: center; }
.fg3 { border: solid 1px; } 
.total td { border: solid 1px; padding: 0px; } 
.total2 { text-align: right; } 

   </style>
    
   </head> 
    
   <body>        

   


<table width="100%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
	
<td><img src="'.RUTA.'/files/logo/'.$mempresa['id'].'.png" style="max-width: 600px; max-height: 180px; margin-bottom: -10px; " /></td>
	
<td width="31%" align="center" valign="middle">
        
        
        <table width="100%" class="cabecera2" cellspacing="0" >
          <tbody>
            <tr>
<td ><br>RUC:  '.$mempresa['ruc'].'<br>'.$tdocumento.'<br>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'<br><br><br><br></td>
            </tr>
          </tbody>
        </table>
        
        
        
        
      </td>
    </tr>
    <tr>
      <td colspan="2"><br>';

$html.=$mempresa['razon_social'].'<br>';
$html.=$mempresa['telefono'].'<br>';
$html.=$mempresa['correo'].'<br>';

$results=ejecutarConsulta("SELECT *FROM sucursal WHERE idempresa='$mostrar[idempresa]' AND nivel='0' ");
while($obj = $results->fetch_object()){
$html.='<strong>'.$obj->sucursal.':</strong> '.$obj->direccion.'<br>';
$html.=$obj->telefono.'<br>';
}
$html.='<br>
  
  CLIENTE: '.$mcliente['nombre'].'<br>
  '.$ndoc.': '.$mcliente['txtID_CLIENTE'].'<br>
  DIRECCIÓN: '.$mcliente['direccion'].'<br>
  MONEDA: '.$valmoneda.' | FORMA DE PAGO:'.$medpago.'<br>
  <br><br>
  
  </td>
    </tr>
  </tbody>
</table>
<table width="100%"  class="cuerpo4" cellpadding="0" cellspacing="0">
  <tr>
    <td width="20%" align="center">ORDEN COMPRA</td>
	<td width="20%" align="center">NOT. PED.</td>
	<td width="20%" align="center">GUIA REMISIÓN</td>
    <td width="21%" align="center">FECHA EMISIÓN</td>
    <td width="19%" align="center">FECHA VENC.</td>
    <td width="20%" align="center">CONDICIONES</td>
    
  </tr>
  <tr>
    <td align="center"></td>
	<td align="center"></td>
	<td align="center">'.$mostrar['guia'].' </td>
    <td align="center">'.$anio.'</td>
    <td align="center">'.$vto.'</td>
    <td align="center">A '.$mostrar['condiciones'].' DIAS</td>
    
  </tr>
</table>
<hr>	   
<table width="100%" cellpadding="0" cellspacing="0"  >
  <tr>
    <td width=96%>Descripción</td>
	<td width="10%" align="center">COD</td>
	<td width="10%" align="center">U.M.</td>
	<td width="10%" align="center">COD.SUN</td>
    <td width="10%" align="center">Cant.</td>
    <td width="20%" align="center">Pre. Unit.</td>
    <td width="20%" align="right">Sub Total</td>
  </tr>
</table>
<hr>
<table width="100%" cellpadding="0" cellspacing="0"  >';
$rspta = $resumen->detfactura($mostrar['idventa']);
while ($reg = $rspta->fetch_object()){
	
$sql="SELECT *FROM articulo WHERE id='$reg->idproducto' ";
$m= ejecutarConsultaSimpleFila($sql);
	
$sqlm="SELECT *FROM unidad_medida WHERE codigo='$reg->undadmedida' ";
$um= ejecutarConsultaSimpleFila($sqlm);
	
if($um['codigo']=='ZZ'||$um['codigo']=='NIU'){ $unidad='UNIDAD'; }else{ $unidad=$um['tit']; }
	
$html.='
  <tr>
<td width=96% >'.html_entity_decode(nl2br($reg->nombreproducto)).'</td>
<td width=10% align="center" >'.$m['codigo'].'</td>
    <td width=10% align="center" >'.$unidad.$reg->undadmedida.'45</td>
	<td width=10% align="center" >'.$m['codigosunat'].'</td>
    <td width=10% align="center" >'.$reg->txtCANTIDAD_ARTICULO.'</td>
    <td width=20% align="center" >'.$reg->precio.'</td>
    <td width=20% align="right" >'.$reg->subtotal.'</td>
  </tr>';
}
$html.='</table>
<br>
OBSERVACIONES: '.$mostrar['txtOBSERVACION'].'<br>
<hr>								   
SON: '.numtoletras($mostrar['txtTOTAL'], $valmoneda).'
								   <br>								   
	<br>						   
								   
<table width="100%" cellpadding="0" cellspacing="0"  class="cuerpo3">
  <tr>
    <td width="36%" rowspan="8">
		<br>
		<img src="'.$mempresa['ruc'].'.png" width="120" height="120" />
	</td>
    <td width="27%" rowspan="8">';
	
if($mostrar['estadopago']=='2'){
$html.='<b>DOCUMENTO RELACIONADO:<br>'.$mostrar['referencia'].'</b><br>
<b>ANTICIPOS:</b><br>';	 
$sqla="SELECT *FROM venta WHERE referencia='$mostrar[referencia]' AND estadopago='1' ";
$rsptaa=ejecutarConsulta($sqla);
while ($rega = $rsptaa->fetch_object()){
$html.=$rega->txtSERIE.'-'.$rega->txtNUMERO.': '.$mf.$rega->txtTOTAL.'<br>';
}
}
$html.='</td>
    <td width="14%">SUB TOTAL</td>
    <td width="5%">'.$mf.'</td>
    <td width="12%" align="right">'.$subtotal.'</td>
  </tr>
  <tr>
    <td >OP. GRAVADA</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">'.$mostrar['txtSUB_TOTAL'].'</td>
  </tr>
  <tr>
    <td >OP. EXONERADA</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">'.$mostrar['exonerado'].'</td>
  </tr>
  <tr>
    <td >OP. INAFECTA</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">0.00</td>
  </tr>
  <tr>
    <td >OP. GRATUITA</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">'.$mostrar['gratuita'].'</td>
  </tr>
  
  
  <tr>
    <td >OP. DESCUENTO</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">'.$mostrar['descuento'].'</td>
  </tr>
  
  <tr>
    <td >I.G.V. 18%</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">'.$mostrar['txtIGV'].'</td>
  </tr>
  
  <tr>
    <td >IMPORTE TOTAL</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">'.$mostrar['txtTOTAL'].'</td>
  </tr>
</table>
<br>
<hr>								   
 OBSERVACIONES SUNAT:<br>
'.$mostrar['mensaje'].' | CÓDIGO HASH '.$mostrar['hash_cpe'].'<br>
<hr>
Para consultar el documento ingrese a: '.RUTA.'/comprobantes/
<br>

Representación impresa de la '.$tdocumento.'
<br><br>

<div  class="cuenta" >
'.nl2br($mempresa['pie']).'
</div>

</body> </html>
	
	
</body>
</html>




   
   
 ';


   $dompdf = new DOMPDF();
   $dompdf->set_paper('letter','portrait');
   //$dompdf->set_paper('legal','landscape');
//Paper orientation ('portrait' or 'landscape') // portrait (ES VERTICAL)
/*
echo $html;
*/
   $dompdf->load_html($html);
   $dompdf->render();
$pdf = $dompdf->output();

if(isset($_GET['correo'])){
file_put_contents($ruta.$fichero.'.pdf', $pdf);
}

header("Content-type:application/pdf");
echo $pdf;


?>