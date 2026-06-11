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

if(isset($_GET['nivel'])){
$nivel=$_GET['nivel'];
}else{
$nivel='1';	
}
$sqlnn=ejecutarConsulta("SELECT *FROM venta WHERE idventa='$idventa'");
$mostrar= mysqli_fetch_array($sqlnn);
		
$sqlnn=ejecutarConsulta("SELECT *FROM venta_pasajes WHERE idventa='$idventa'");
$pasajero= mysqli_fetch_array($sqlnn);
		
$dor = ejecutarConsulta("SELECT *FROM sucursal WHERE id='$pasajero[idembarque]' ");
$or=mysqli_fetch_array($dor);
	
$ddes= ejecutarConsulta("SELECT *FROM sucursal WHERE id='$pasajero[iddestino]' ");
$des=mysqli_fetch_array($ddes);
		
$dhor=ejecutarConsulta("SELECT *FROM rutas_horario WHERE id='$pasajero[idhorario]' ");
$horario=mysqli_fetch_array($dhor);

$sqluser="SELECT *FROM usuario WHERE idusuario='$mostrar[idusuario]' ";
$user= ejecutarConsultaSimpleFila($sqluser);
		
$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sqlbu="SELECT *FROM guia_vehiculo WHERE id='$horario[buss]' ";
$vehiculo= ejecutarConsultaSimpleFila($sqlbu);
		
if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; }

$tdocumento='RECIBO';
		
if($mostrar['txtID_TIPO_DOCUMENTO']=='03'){ $tdocumento='BOLETA DE VENTA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='90'){ $tdocumento='RECIBO'; }
		
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
';
		
  $cont.='

<table class="tabla" border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
    <td colspan="2" align="center">
    <br>';
	
if($nivel=='0'){
  $cont.='<img src="../../files/logo/'.$mempresa['id'].'.png" width="70%"  />';
}else{
$cont.='<img src="'.RUTA.'/files/logo/'.$mempresa['id'].'.png" width="70%"  />';	
}
$cont.='<b><h3>'.$mempresa['razon_social'].'</h3>
RUC: '.$mempresa['ruc'].'<br>
Fecha/Hora Emisión: '.$mostrar['txtFECHA_DOCUMENTO'].'<br>
<h3>'.$tdocumento.'<br>
'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</h3>
</td>
  </tr>';
  
$cont.='
  <tr>
    <td colspan="2">
<hr>
	
<strong>DATOS DEL VIAJE</strong></td>
  </tr>

   <tr>
    <td width="40%">ORIGEN</td>
    <td width="60%">: '.$or['sucursal'].'</td>
  </tr>
   <tr>
     <td>DESTINO</td>
     <td>: '.$des['sucursal'].'</td>
   </tr>
   <tr>
     <td style=" font-size: 09pt"><b>ASIENTO</b></td>
     <td style=" font-size: 09pt"><b>: '.$pasajero['asiento'].'</b></td>
   </tr>
   <tr>
     <td style=" font-size: 09pt"><b>FECHA DE VIAJE<b></td>
     <td style=" font-size: 09pt">: <b>'.$horario['fecha'].'</b></td>
   </tr>
   <tr>
     <td style=" font-size: 09pt"><b>HORA DE VIAJE</b></td>
     <td style=" font-size: 09pt"><b>: '.$horario['hora'].':'.$horario['minuto'].' '.$horario['ampm'].'</b> </td>
     
   </tr>
   <tr>
     <td colspan="2" align="center"><strong>*PRESENTARSE 30 MINUTOS ANTES*</strong></td>
   </tr>
   <tr>
     <td colspan="2"><hr></td>
   </tr>
   <tr>
     <td colspan="2">
<strong>DATOS DEL PASAJERO:</strong><br>
		 ';
$cont.='DNI: '.$pasajero['dni'].' EDAD: '.$pasajero['edad'].'<br>
';
$cont.='PASAJERO: '.$pasajero['nombre'].'</b><br>
';
if($mcliente['tipo_documento']=='RUC'){
$cont.=$mcliente['tipo_documento'].': '.$mcliente['txtID_CLIENTE'].'<br>
';	
$cont.='RAZÓN SOCIAL: '.$mcliente['nombre'].'<br>
';
$cont.='DIRECCIÒN: '.$mcliente['direccion'].'<br>';
} 
$cont.='</td>
   </tr>
   <tr>
     <td colspan="2"><hr></td>
   </tr>
   <tr>
     <td colspan="2"><strong>DATOS DE PAGO:</strong></td>
   </tr>
   <tr>
     <td>Op. Gratuita</td>
     <td>: '.$mostrar['gratuita'].'</td>
   </tr>
   <tr>
     <td>Op. Exonerado</td>
     <td>: '.$mostrar['exonerado'].'</td>
   </tr>
   <tr>
     <td>Op. Inafectas</td>
     <td>: 0.00</td>
   </tr>
   <tr>
     <td>Total a pagar</td>
     <td>: '.$mostrar['txtTOTAL'].'</td>
   </tr>
   <tr>
     <td colspan="2">SON: '.numtoletras($mostrar['txtTOTAL']).'</td>
   </tr>
   <tr>
   
  <tr>
  <td>VENDEDOR.</td>
  <td>: '.$user['nombre'].'</td>
</tr>
   
     <td colspan="2"><hr></td>
   </tr>


   ';


   
   

if($nivel=='0'){
  $cont.='<tr><td colspan="2" align="center"><img src="'.$rutaqr.'"  width="50" height="50" /></td></tr>';
}else{
$cont.='<tr><td colspan="2" align="center"><img src="'.RUTA.'/plugins/dompdf/'.$rutaqr.'"  width="50" height="50" /></td></tr>';	
}
$cont.='<tr><td colspan="2"><center>Representación impresa de la '.$tdocumento.'</center></td></tr>';
$cont.='<tr><td colspan="2"><center>DESCARGA TU COMPROBANTE: '.RUTA.'/comprobantes </td></tr>';




  $cont.='<tr>
  
       <td colspan="2" align="left">
     <br><br><br>
  <h3>'.$mempresa['razon_social'].'<br>
  CONTROL DE EMBARQUE | PUERTA N°1</h3>
  N°TICKET:'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'<br>
  PASAJERO: '.$pasajero['nombre'].'<br>
  N° DOC: '.$pasajero['dni'].'<br>
  FECHA DE EMISIÓN: '.$pasajero['fecha'].'<br>
  FECHA DE VIAJE: '.$horario['fecha'].'<br>
  HORARIO:'.$horario['hora'].':'.$horario['minuto'].' '.$horario['ampm'].'<br>
  RUTA: '.$or['sucursal'].'-'.$des['sucursal'].'<br>
  ASIENTO: '.$pasajero['asiento'].'<br>
  PRECIO: '.$mostrar['txtTOTAL'].'
  
  
  </td>
     </tr>
     
     
<tr>
<td colspan="2" align="left">
 <br><br><br>    
     TERMINOS Y CONDICIONES (PASAJES) CAMBIO DE TITULARIDAD: el boleto de viaje adquirido es personal. Anticipación mínima de cambio es de 8 horas antes del viaje. Postergación o cambio de itinerario: cuando el pasajero requiera postergación, cambio de ruta, fecha y hora deberá apersonarse con una anticipación mínima de 8 horas del viaje. Se rige de acuerdo al artículo 66.7 de la ley N° 29571 PASAJERO MAYOR DE 5 AÑOS pagara el boleto de viaje completo y viaja en su propio asiento, según el art.42.1.9.3 del DS 017‐2029 MTC. UNA VEZ EMITIDO EL BOLETO NO HAY REENBOLSO DE DINERO. El equipaje del pasajero no debe superar los 20 KL en total, en caso de exceso debe pagar el monto indicado por TVC. Este boleto solo es válido por 6 meses. Solo se puede posponer una sola vez. La postergación se debe realizar con un plazo mínimo de 8 horas antes del viaje y previo pago de penalidad vigente en el tarifario de TVC. En caso de pérdida del equipaje se actuará de acuerdo a las normas del MTC decreto supremo N° 017‐2029. POLIZA DE SEGURO: El pasajero viaja asegurado mediante una póliza del SOAT. N° de póliza‐ la del SOAT 3022100028456 del ómnibus D2P‐959 / N° de póliza la del SOAT 3022100351553‐1 del ómnibus VCO‐958/ N° de póliza – la del SOAT 3022200433948 del ómnibus ZCG‐959/ N° de póliza – la del SOAT 3022400135490 del ómnibus D6S‐953.Tramite personal y debe portar su documento de identidad deberá pagar un monto de S/5.00 por la emisión del nuevo boleto. TERMINOS Y CONDICIONES (ENCOMIENDAS) 1.Sr remitente: por favor revise este boleto antes de retirarse después no aceptamos reclamos. 2.Al momento de su entrega a la Empresa la encomienda debe estar debidamente embalada para su trasporte, la Empresa no será responsable por el deterioro debido al mal embalaje, por la muerte o perdida de animales, ni por dirección equivocada del destinatario. La empresa tampoco responde por la descomposición de los productos perecibles contenidos en la encomienda. 3.El remitente y destinatario están obligados a mostrar el contenido de la encomienda en el momento en que sea requerido por el personal de la Empresa y estos a su vez mostraran el contenido de la encomienda, durante el viaje cuando sea requerido por la Autoridad policial, fiscal y aduanera. La empresa no es responsable del contenido de las encomiendas. 4.Las encomiendas que no hubieran sido requeridas o que no hubieran sido entregadas por error de la dirección, estarán depositadas 60 días en la oficina de la empresa de lugar del destino de la encomienda; vencido el plazo, será entregados a la autoridad competente. 5.La empresa indemnizará la perdida de las encomiendas tan solo DIES VECES el flete pagado por el remitente conforme al Art. 21/10/21 Reglamento de Trasportes, previa presentación de este documento. La empresa no responde por la pérdida de encomienda que contenga dinero, joyas u otros objetos de valor que no hubiese sido previamente declarados y mostrados al personal de la empresa. 6.No será aceptado ningún reclamo luego que el destinatario firme este documento manifestando su conformidad con la recepción con la recepción de la encomienda. 7.El trasporte de encomienda se rige por estas condiciones y por las normas legales peruanas vigente sobre la materia. Las partes del contrato se someten expresamente a la jurisdicción de los tribunales peruano. 8.El cliente acepta condiciones del trasporte de la encomienda.
     
  </td>
     </tr>


';
     







  

$cont.='</table>';

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