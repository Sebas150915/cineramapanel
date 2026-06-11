<?php

//require_once("dompdf/dompdf_config.inc.php");

$rutat=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$rutat= str_replace("plugins/dompdf/nota.php", "", $rutat);

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

$ruta="../../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero=$mempresa['ruc'].'-'.$mostrar['txtID_TIPO_DOCUMENTO'].'-'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];

if($mostrar['txtID_TIPO_DOCUMENTO']=='07'){ $tdocumento='NOTA DE CREDITO ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='08'){ $tdocumento='NOTA DE DEBITO ELECTRÓNICA'; }

$ndoc='OTROS';

if($mostrar['docmodifica_tipo']=='01'){ $tdocumentom='01 - FACTURA ELECTRÓNICA';  $ndoc='RUC';  }
if($mostrar['docmodifica_tipo']=='03'){ $tdocumentom='03 - BOLETA ELECTRÓNICA';  $ndoc='DNI'; }

if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }

// Los delimitadores pueden ser barra, punto o guión
$fecha = date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO']));
$fechaf = explode('-', $fecha);
$anio= $fechaf[2].'/'.$fechaf[1].'/'.$fechaf[0];

$fechaf2 = explode('-', $mostrar['fecha_vto']);
$vto=$fechaf2[2].'/'.$fechaf2[1].'/'.$fechaf2[0];

$subtotal=number_format($mostrar['txtTOTAL']-$mostrar['txtIGV'], 2, '.', '');

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
    text-align: left; font-size: 22px;
}
.cabecera h1 {
    font-size:22px; padding-bottom: 0px; margin-bottom: 0px;
}

.cabecera2 table { border-collapse: collapse; border: solid 1px #000000;}
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; border: solid 1px #000000; font-size:16px; } 
.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 1px #000000; }
.cuerpo thead { border: solid 1px #000000; } 
.cuerpo2 thead { border: solid 1px #000000; } 
.titdocu{ font-size:13px; text-align: center; }
table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 3pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt; border: solid 1px #000000; }
.footer { position: fixed; bottom: 150px; font-size:10px;  width: 100%; border: solid 0px #000000; }
.fg { font-size: 11px;} 
.fg2 { text-align: center; }
.fg3 { border: solid 0px; } 
.total td { border: solid 0px; padding: 0px; } 
.total2 { text-align: right; } 


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
      <td width="31%" align="center" valign="middle"><table width="100%" class="cabecera2" cellspacing="0" >
        <tbody>
          <tr>
            <td ><br>
              RUC:  '.$mempresa['ruc'].'<br>
              '.$tdocumento.'<br>
              '.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'<br>
              <br>
              <br>
              <br></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr>
      <td colspan="2"><br>
        ';
        
        $html.=$mempresa['razon_social'].'<br>
        ';
        $html.=$mempresa['telefono'].'<br>
        ';
        $html.=$mempresa['correo'].'<br>
        ';
        $html.=$mempresa['direccion'].'<br>
        ';
    
        $html.='<br>
        CLIENTE: '.$mcliente['nombre'].'<br>
        '.$ndoc.': '.$mcliente['txtID_CLIENTE'].'<br>
        DIRECCIÓN: '.$mcliente['direccion'].'<br>
        MONEDA: '.$valmoneda.' 
</td>
    </tr>
  </tbody>
</table>
<br>
<table width="100%"  class="cuerpo4" cellpadding="0" cellspacing="0">
  <tr>
    <td width="20%" align="center">ORDEN COMPRA</td>
    <td width="21%" align="center">FECHA EMISIÓN</td>
    <td width="19%" align="center">FECHA VENCIMIENTO</td>
    <td width="20%" align="center">CONDICIONES</td>
    <td width="20%" align="center">GUIA REMISIÓN</td>
  </tr>
  <tr>
    <td align="center">'.$mostrar['orden'].' </td>
    <td align="center">'.$anio.'</td>
    <td align="center">'.$vto.'</td>
    <td align="center">A 0 DIAS</td>
    <td align="center">'.$mostrar['guia'].' </td>
  </tr>
</table>
<table width="100%" class="cuerpo" cellspacing="0">
<thead>
    <tr>
<td class="titdocu" >DOCUMENTO QUE MODIFICA</td>
    </tr>
  </thead>
</table>
<table width="100%" class="cuerpo" cellspacing="0">
<thead>
    <tr>
      <td>TIPO DOCUMENTO: '.$tdocumentom.'</td>
      <td>NUMERO: '.$mostrar['docmodifica'].'</td>
      <td>MOTIVO: '.$mostrar['modifica_motivo'].' - '.$mostrar['modifica_motivod'].'</td>
    </tr>
  </thead>
</table>


<table width="100%" class="cuerpo2" border="0" cellspacing="0">
<thead> 
    <tr>
      <td width="10%">CODIGO</td>
      <td width="60%">DESCRIPCION</td>
      <td width="10%">PRECIO</td>
      <td width="10%">CANTIDAD</td>
      <td width="10%">IMPORTE</td>
    </tr>
</thead>
<tbody>
';
$rspta = $resumen->detfactura($mostrar['idventa']);
while ($reg = $rspta->fetch_object()){	
$html.='
<tr>
      <td>'.$reg->codigoproducto.'</td>
      <td>'.$reg->nombreproducto.'</td>
      <td>'.$reg->precio.'</td>
      <td>'.$reg->txtCANTIDAD_ARTICULO.'</td>
      <td>'.$reg->importe.'</td>
    </tr>';
}
$html.='
  </tbody>
</table>





</div>
OBSERVACIONES: '.$mostrar['txtOBSERVACION'].'<br>
<hr>
SON: '.numtoletras($mostrar['txtTOTAL'], $valmoneda).' <br>
<br>
<table width="100%" cellpadding="0" cellspacing="0"  class="cuerpo3">
  <tr>
    <td width="59%" rowspan="7"><br>
      <img src="'.$mempresa['ruc'].'.png" width="90" height="90" /> <br>
      Representación impresa de la factura electrónica</td>
    <td width="16%">SUB TOTAL</td>
    <td width="5%">'.$mf.'</td>
    <td width="20%" align="right">'.$subtotal.'</td>
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
Para consultar el documento ingrese a: '.RUTA.'/comprobantes/ <br>
<br>
Representación impresa de la '.$tdocumento.' <br>
<br>
<div  class="cuenta" > '.nl2br($mempresa['pie']).' </div>
</body> </html>






   
   
 ';
   $dompdf = new DOMPDF();
$dompdf->set_paper('letter','portrait');
   //$dompdf->set_paper('legal','landscape');
   $dompdf->load_html($html);
   $dompdf->render();
$pdf = $dompdf->output();

if(isset($_GET['correo'])){
file_put_contents($ruta.$fichero.'.pdf', $pdf);
}

header("Content-type:application/pdf");
echo $pdf;

?>