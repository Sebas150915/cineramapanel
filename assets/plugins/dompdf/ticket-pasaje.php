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

function sumarHorasMinutos($fecha, $horas, $minutos = 0) {
    try {
        $fecha_obj = new DateTime($fecha);
        $fecha_obj->modify("+{$horas} hours +{$minutos} minutes");
        return $fecha_obj->format('Y-m-d h:i A');
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

$resumen=new Resumen();

$idventa=$_GET['id'];

if(isset($_GET['nivel'])){
$nivel=$_GET['nivel'];
}else{
$nivel='1';	
}
$sqlnn=ejecutarConsulta("SELECT *FROM venta WHERE idventa='$idventa'");
$mostrar= mysqli_fetch_array($sqlnn);
		
$sqlnn=ejecutarConsulta("SELECT *FROM venta_pasajes WHERE idventa='$idventa' ORDER BY id DESC ");
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


$horas=$or['horas_diferencia'];
$cadena = explode(".", $horas); 

$fecha_original = $horario['fecha'].' '.$horario['hora'].':'.$horario['minuto'].' '.$horario['ampm'];
$nueva_fecha = sumarHorasMinutos($fecha_original, $cadena[0], $cadena[1]);
$nueva_fecha = str_replace($horario['fecha'], '', $nueva_fecha);

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
    margin-top: 0.3em;
    margin-bottom: 0.3em;
    margin-left: 0.3em;
    margin-right: 0.3em;
    size: auto; /* Cambiar a auto para que se ajuste al contenido */
}

.cuerpo { float: left; width: 100%; font-size: 12px; font-family: Arial; }
.tabla { font-size: 12px; width: 255px; border-collapse: collapse; }
.centrado { text-align: center; }
.negrita { font-weight: bold; }
.logo { max-width: inherit; width: 100%; height: auto; }
H3 { font-size: 13px; margin:0px; padding: 0px; }
body {
    font: 12px Arial, Tahoma, Verdana, Helvetica, sans-serif;
    color: #000;
    margin: 0;
    padding: 0;
}
hr {
    margin: 2px 0;
    padding: 0;
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

 - '.$mempresa['direccion'].'

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
     <td style=" font-size: 09pt"><b>FECHA DE VIAJE<b></td>
     <td style=" font-size: 09pt">: <b>'.$horario['fecha'].'</b></td>
   </tr>
   <tr>
     <td style=" font-size: 09pt"><b>HORA DE VIAJE</b></td>
     <td style=" font-size: 09pt"><b>: '.$nueva_fecha.'</b> </td>
     
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
   
<tr>
<td colspan="2">Fecha/Hora Emisión: '.$mostrar['txtFECHA_DOCUMENTO'].'</td>
</tr>
   <tr>
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




  $cont.='
     
<tr>
<td colspan="2" align="left" style="font-size:10px;" >
 <br> 
TÉRMINOS Y CONDICIONES: 
CAMBIO DE TITULARIDAD: El boleto de viaje adquirido es personal. Anticipación mínima del cambio: 02 horas antes del viaje. POSTERGACIÓN O CAMBIO DE ITINERARIO: Cuando el pasajero requiera postergación, cambio de ruta, fecha u hora, deberá apersonarse con una anticipación mínima de 06 horas del viaje. Se rige de acuerdo al artículo 66.7 de la Ley 29572. PASAJERO MAYOR DE 5 AÑOS paga el boleto de viaje completo y viaja en su propio asiento, según el art. 42.1.9° del DS 017-2009 MTC. UNA VEZ EMITIDO EL BOLETO NO HAY REEMBOLSO DE DINERO. EL EQUIPAJE del pasajero no debe superar los 20 kg; en caso de exceso, deberá pagar el monto indicado por TVC. Este boleto solo es válido por 6 meses. Solo se puede postergar una vez. El pasajero deberá de realizar dicho trámite mínimo 6 horas antes del viaje. TVC no se responsabiliza por pérdidas de objetos de valor. Para hacer uso del SOAT deberá llamar a LA DEL SOAT 312200128064 de unión policía PNP–L.A. DEL SOAT 312200128064 · N° de póliza: LA DEL SOAT 312200128064.
  </td>
     </tr>


';
     







  

$cont.='</table>';

if($nivel=='0'){
header("Content-type:application/pdf");
    
    $filename = "comprobante_".$mostrar['txtSERIE']."-".$mostrar['txtNUMERO'];
    
    $dompdf = new DOMPDF();
    
    // Configurar papel con tamaño personalizado más grande
    $dompdf->set_paper(array(0, 0, 220, 1000), 'portrait');
    
    $dompdf->load_html($cont);
    $dompdf->render();
    
    $dompdf->stream($filename, array("Attachment"=>0));

}else{
	echo $cont;
}
?>