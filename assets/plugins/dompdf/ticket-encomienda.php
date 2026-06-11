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
$dni=$_GET['dni'];

$sqlnn=ejecutarConsulta(" SELECT d.*, v.idlocal FROM venta_opdetallet d INNER JOIN venta_orden v ON d.idpedido=v.id WHERE d.id='$idventa' ");
$mostrar= mysqli_fetch_array($sqlnn);

$sqlnn=ejecutarConsulta(" SELECT *FROM venta_opdetalle WHERE seriedoc='$mostrar[serie]' AND idopedido='$mostrar[idpedido]' ");
$pasajero= mysqli_fetch_array($sqlnn);

$sqlbu="SELECT *FROM venta WHERE idventa='$pasajero[idventa]' ";
$venta= ejecutarConsultaSimpleFila($sqlbu);


$idem = isset($pasajero['idembarque']) ? $pasajero['idembarque'] : (isset($pasajero['idlocal']) ? $pasajero['idlocal'] : '');
if($idem!=''){
$dor = ejecutarConsulta("SELECT *FROM sucursal WHERE id='".$idem."' ");
$or=mysqli_fetch_array($dor);
}
	
$iddes = isset($pasajero['iddestino']) ? $pasajero['iddestino'] : '';
if($iddes!=''){
$ddes= ejecutarConsulta("SELECT *FROM sucursal WHERE id='".$iddes."' ");
$des=mysqli_fetch_array($ddes);
}
		
$sqluser="SELECT *FROM usuario WHERE idusuario='$_COOKIE[idusuario]' ";
$user= ejecutarConsultaSimpleFila($sqluser);
		
$sql2="SELECT *FROM persona WHERE idpersona='$venta[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);
		
$sqll="SELECT *FROM sucursal WHERE id='".$mostrar['idlocal']."' ";
$local= ejecutarConsultaSimpleFila($sqll);

$mon = isset($mostrar['txtID_MONEDA']) ? $mostrar['txtID_MONEDA'] : (isset($venta['txtID_MONEDA']) ? $venta['txtID_MONEDA'] : 'PEN');
if($mon=='PEN'){ $valmoneda='SOLES'; }
if($mon=='USD'){ $valmoneda='DOLARES'; }
if($mon=='EUR'){ $valmoneda='EUROS'; }

$tdocumento='RECIBO';
if(isset($mostrar['tipodoc'])){
    if($mostrar['tipodoc']=='BOLETA'){ $tdocumento='BOLETA DE VENTA ELECTRÓNICA'; }
    if($mostrar['tipodoc']=='FACTURA'){ $tdocumento='FACTURA ELECTRÓNICA'; }
    if($mostrar['tipodoc']=='RECIBO'){ $tdocumento='RECIBO'; }
}else{
    $td = isset($venta['txtID_TIPO_DOCUMENTO']) ? $venta['txtID_TIPO_DOCUMENTO'] : '90';
    if($td=='03'){ $tdocumento='BOLETA DE VENTA ELECTRÓNICA'; }
    if($td=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }
    if($td=='90'){ $tdocumento='RECIBO'; }
}
		
$cont='

<style type="text/css">
@page {
            margin-top: 0.9em;
            margin-left: 0.9em;
        }

.cuerpo { float: left; width: 100%; font-size: 12px; font-family: Arial; }
.tabla { font-size: 14px;  width: 255px; }
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
    
    
╔══════════════════════════════════════╗
       COMPROBANTE DE ENTREGA
╚══════════════════════════════════════╝
    
    <br>';
	
	

$cont.='<b><h3>'.$mempresa['razon_social'].'</h3>
RUC: '.$mempresa['ruc'].'<br>
Fecha/Hora: '.date("Y-m-d h:i A").'<br>
<h3>'.$mostrar['tipodoc'].'<br>
'.$mostrar['serie'].'</h3>



</td>
  </tr>';
  
$cont.='


<tr>
<td colspan="2" >

**REMITENTE**<br>
Nombre: '.$mcliente['nombre'].'
<br><br>
**DESTINATARIO**<br>
Nombre: '.$pasajero['cliente'].'<br><br>

**DETALLES DEL PAQUETE**<br>
'.$mostrar['nombreproducto'].'<br>
Valor declarado: '.$mostrar['importe'].'<br><br>

**ACUSE DE RECIBO**<br>
Recibí el paquete en perfecto estado
y conforme con lo descrito.<br><br>

Firma del destinatario:<br><br><br><br><br>
<hr>
Nombre y apellido<br><br>
DNI: ____________________<br><br>

Usuario: '.$user['nombre'].'<br>
Firma del usuario:<br><br><br><br><br>


</td>
 </tr>




  

';
     







  

$cont.='</table>';

$nivel = isset($_GET['nivel']) ? $_GET['nivel'] : '0';
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
