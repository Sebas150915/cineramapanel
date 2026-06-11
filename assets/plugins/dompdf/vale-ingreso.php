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

$sql="SELECT *FROM ingreso WHERE idingreso='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

$sqlin="SELECT *FROM ingreso_vale WHERE idingreso='$id' ";
$ingreso= ejecutarConsultaSimpleFila($sqlin);

if(!$ingreso){
$sqls="SELECT *FROM series WHERE documento='07' AND idlocal='$mostrar[idlocal]' AND idempresa='$mostrar[idempresa]'  ORDER BY id DESC ";
$s= ejecutarConsultaSimpleFila($sqls);
$rest = substr($s['serie'], -2);
$seriedoc='VI'.$rest;
	
$sql="SELECT *FROM ingreso_vale WHERE serie='$seriedoc' AND idlocal='$mostrar[idlocal]' AND idempresa='$mostrar[idempresa]' AND beta='$mempresa[tipo]'  ORDER BY numero DESC ";
$mostrardoc= ejecutarConsultaSimpleFila($sql);
	
if($mostrardoc){
$num=$mostrardoc['numero']+1;
$num=str_pad($num, 8, "0", STR_PAD_LEFT);
}else if(isset($s)){
$num=$s['numeroinicio'];
}else{
$num='00000001';	
}

$sql="INSERT INTO ingreso_vale VALUES (NULL, '$mostrar[idempresa]', '$mempresa[tipo]', '$mostrar[idlocal]', '$id', '$seriedoc', '$num', '0')";
$idingresonew=ejecutarConsulta_retornarID($sql);
	
$sqlin="SELECT *FROM ingreso_vale WHERE idingreso='$id' ";
$ingreso= ejecutarConsultaSimpleFila($sqlin);

}


$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[idproveedor]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);

$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

if($mostrar['moneda']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['moneda']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['moneda']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }

$tdocumento='VALE DE INGRESO'; 

// Los delimitadores pueden ser barra, punto o guión
$fecha = date("Y-m-d", strtotime($mostrar['fecha_hora']));
$fechaf = explode('-', $fecha);
$anio= $fechaf[2].'/'.$fechaf[1].'/'.$fechaf[0];

$fechaf2 = explode('-', $mostrar['fecha_hora']);
$vto=$fechaf2[2].'/'.$fechaf2[1].'/'.$fechaf2[0];


//QRcode::png("".$text);
//DATOS OBLIGATORIOS DE LA SUNAT EN EL QR
//RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE //EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |

$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$ingreso['serie'].' | '.$ingreso['numero'].' | '.$mostrar['igv'].' | '.$mostrar['total_compra'].' | '.date("Y-m-d", strtotime($mostrar['fecha_hora'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';
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


.cabecera2 table { border-collapse: collapse; border: solid 1px #000000;}
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; border: solid 1px #000000; } 
.cabecera2 { font-size:30px; } 

.cabecera3 table { border-collapse: collapse; }
.cabecera3 th, .cabecera2 td { text-align: center; border-collapse: collapse; } 
.cabecera3 { font-size:15px; text-align: center; } 
.cabecera3 h3 { font-size:22px; margin:0px; padding:0px; } 

.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 1px #000000; }
.cuerpo thead { border: solid 1px #000000; } 
.cuerpo { font-size:15px; text-align: left; } 

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

.serienum {
border-radius: 20px;
 width: 100%; color:black;
 border: 1px solid #000000;
 text-align: center; border-collapse: collapse; border: solid 1px #000000; font-size:16px; font-weight: bold;
 padding-top:30px;
padding-bottom: 30px;
 
}


   </style>
    
   </head> 
    
   <body>        

   


<table width="100%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
	
<td width="28%"><img src="'.RUTA.'/files/logo/'.$mempresa['id'].'.png" style="max-width: 300px; max-height: 180px;  " /></td>
<td width="41%" class="cabecera3">';

$html.='<h3>'.$mempresa['razon_social'].'</h3>';
$html.='DIRECCIÓN: '.$mempresa['direccion'].'<br>';
$html.='TELÉFONO: '.$mempresa['telefono'].' | ';
$html.=$mempresa['correo'].'</td>';
	
$html.='<td width="25%" align="center" valign="middle">
        
<div class="serienum" >
RUC:  '.$mempresa['ruc'].'<br>'.$tdocumento.'<br>'.$ingreso['serie'].'-'.$ingreso['numero'].'
</div>
		
        
        
        
        
      </td>
    </tr>
    <tr>
      <td colspan="3"  class="cuerpo"  ><br>';


$html.='
  
PROVEEDOR: '.$mcliente['nombre'].'<br>
DOCUMENTO: '.$mcliente['txtID_CLIENTE'].'<br>
FECHA: '.$anio.' |  MONEDA: '.$valmoneda.'<br>
DOC. RELACIONADO: '.$mostrar['serie_comprobante'].'-'.$mostrar['num_comprobante'].'<br>
  
  </td>
    </tr>
  </tbody>
</table>

<hr>	   
<table width="100%" cellpadding="0" cellspacing="0"  >
  <tr>
    <td width=96%>Descripción</td>
	<td width="10%" align="center">U.M.</td>
    <td width="20%" align="center">Pre. Unit.</td>
	<td width="10%" align="center">Cant.</td>
    <td width="20%" align="right">Sub Total</td>
  </tr>
</table>
<hr>
<table width="100%" cellpadding="0" cellspacing="0"  >';
	
$rspta=ejecutarConsulta("SELECT * FROM detalle_ingreso WHERE idingreso='$id'  ");
while ($reg = $rspta->fetch_object()){
	
$sql="SELECT *FROM articulo WHERE id='$reg->txtCOD_ARTICULO' ";
$m= ejecutarConsultaSimpleFila($sql);
	
$html.='
  <tr>
<td width=96% >'.$m['txtDESCRIPCION_ARTICULO'].'</td>
    <td width=10% align="center" >'.$m['medida'].'</td>
    <td width=20% align="center" >'.$reg->precio_compra.'</td>
	<td width=10% align="center" >'.$reg->txtCANTIDAD_ARTICULO.'</td>
    <td width=20% align="right" >'.$reg->subtotal.'</td>
  </tr>';
}
$html.='</table>
<br>
OBSERVACIONES: '.$mostrar['txtOBSERVACION'].'<br>
<hr>								   
SON: '.numtoletras($mostrar['total_compra'], $valmoneda).'					   
								   
<table width="100%" cellpadding="0" cellspacing="0"  class="cuerpo3">
  <tr>
    <td width="59%" rowspan="7">
		<br>
		<img src="'.$mempresa['ruc'].'.png" width="110" height="110" />

  <tr>
    <td >OP. GRAVADA</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">'.$mostrar['subtotal'].'</td>
  </tr>



  <tr>
    <td >I.G.V. 18%</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">'.$mostrar['igv'].'</td>
  </tr>
  <tr>
    <td >IMPORTE TOTAL</td>
    <td >'.$mf.'</td>
    <td width="20%" align="right">'.$mostrar['total_compra'].'</td>
  </tr>
</table>




</body> </html>




   
   
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