<?php
//require_once("dompdf/dompdf_config.inc.php");
session_start();
$rutat=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$rutat= str_replace("plugins/dompdf/guia.php", "", $rutat);

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

$id=$_GET['id'];

require_once "../../modelos/credito-debito.php";

$venta=new Venta();
$resumen=new Resumen();


$sql="SELECT *FROM guia_guia WHERE id='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$tipo=$mostrar['nivel'];

$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sqlv="SELECT *FROM venta WHERE idventa='$mostrar[iddoc_relacionado]' ";
$mostrav= ejecutarConsultaSimpleFila($sqlv);

if($tipo=='2'){

$sql="SELECT *FROM venta_orden WHERE id='$mostrar[iddoc_relacionado]' ";
$mostrarv= ejecutarConsultaSimpleFila($sql);
$cliente=$mostrarv['idcliente'];

$subtotal=$mostrarv['subtotal'];
	$total=$mostrarv['total'];
	$igv=$mostrarv['igv'];
	$moneda=$mostrarv['moneda'];
}else{
$sql="SELECT *FROM venta WHERE idventa='$mostrar[iddoc_relacionado]' ";
$mostrarv= ejecutarConsultaSimpleFila($sql);
$cliente=$mostrarv['txtID_CLIENTE'];
	
$subtotal=$mostrarv['txtSUB_TOTAL'];
	$total=$mostrarv['txtTOTAL'];
	$igv=$mostrarv['txtIGV'];
	$moneda=$mostrarv['txtID_MONEDA'];
}

if($mostrar['idcliente']=='0'){
$sql2="SELECT *FROM persona WHERE idpersona='$cliente' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
}else{

$sql2="SELECT *FROM persona WHERE idpersona='$mostrar[idcliente]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);
	
}

$grabada=round(($mostrarv['txtSUB_TOTAL']-$mostrarv['exonerado']), 2);

//VEHICULO
$sqlve="SELECT *FROM guia_vehiculo WHERE id='$mostrar[idvehiculo]' ";
$veh= ejecutarConsultaSimpleFila($sqlve);
//CONDUCTOR
$sqlch="SELECT *FROM guia_chofer WHERE id='$mostrar[idchofer]' ";
$chof= ejecutarConsultaSimpleFila($sqlch);
//EMPRESA DE TRANSPORTE
$sqltr="SELECT *FROM guia_transportista WHERE id='$mostrar[emptrans_id]' ";
$trans= ejecutarConsultaSimpleFila($sqltr);

$sqls="SELECT *FROM sucursal WHERE id='$mostrar[sucursal]' ";
$suc= ejecutarConsultaSimpleFila($sqls);

$sqld="SELECT *FROM sucursal WHERE id='$mostrar[destino]' ";
$dest= ejecutarConsultaSimpleFila($sqld);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

$ruta="../../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero=$mempresa['ruc'].'-'.$mostrar['tipodoc'].'-'.$mostrar['serie'].'-'.$mostrar['numero'];

if($mostrar['tipodoc']=='09'){ $tdocumento='GUÍA REMISIÓN - REMITENTE'; }
if($mostrar['tipodoc']=='23'){ $tdocumento='GUÍA REMISIÓN - TRANSPORTISTA'; }

if($mostrav['txtID_TIPO_DOCUMENTO']=='03'){ $tdocumentor='BOLETA DE VENTA ELECTRÓNICA'; $ndoc='DNI'; }
if($mostrav['txtID_TIPO_DOCUMENTO']=='01'){ $tdocumentor='FACTURA ELECTRÓNICA'; $ndoc='RUC'; }

$valmoneda='SOL';


//QRcode::png("".$text);
//DATOS OBLIGATORIOS DE LA SUNAT EN EL QR
//RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE //EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |

$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['serie'].' | '.$mostrar['numero'].' | '.$mostrav['txtIGV'].' | '.$mostrav['txtTOTAL'].' | '.date("Y-m-d", strtotime($mostrar['fecha'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';
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
    text-align: left; font-size: 12px;
}
.cabecera h1 {
    font-size:17px; padding-bottom: 0px; margin-bottom: 0px;
}

.cabecera2 table { border-collapse: collapse; border: solid 1px #000000;}
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; border: solid 1px #000000; font-size:14px; } 
.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 1px #000000; }
.cuerpo thead { border: solid 1px #000000; } 
.cuerpo2 thead { border: solid 1px #000000; } 
.titdocu{ font-size:14px; text-align: center; background-color: #A6A6A6; color:#FFFFFF; }
table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 3pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt; }
.footer { font-size:14px;  width: 100%; border: solid 0px #000000; }
.fg { font-size: 14px;} 
.fg2 { text-align: center; }
.fg3 { border: solid 0px; } 
.total td { border: solid 0px; padding: 0px; } 
.total2 { text-align: right; } 

   </style>
    
   </head> 
    
   <body>        

   


<table width="100%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
<td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="cabecera">
  <tbody>
    <tr>
      <td width="6%"><img src="'.RUTA.'/files/logo/'.$mempresa['id'].'.png" width="266"  /></td>
      <td class="cabeza"><h1>'.$mempresa['razon_social'].'</h1>
        <strong>SUCURSAL:</strong> '.$local['direccion'].'<br>
        '.$mempresa['telefono'].'<br></td>
    </tr>

  </tbody>
</table>  </td>
<td width="30%">
        
           
        <table width="100%" class="cabecera2" cellspacing="0" >
          <tbody>

            <tr>
              <td ><br>RUC N° '.$mempresa['ruc'].'<br>'.$tdocumento.'<br>'.$mostrar['serie'].'-'.$mostrar['numero'].'<br><br><br></td>
            </tr>
          </tbody>
        </table>

      </td>
    </tr>
  </tbody>
</table>

<table width="30%" cellspacing="0">
 <tr>
      <td width="30%">

<table width="30%" class="cuerpo" cellspacing="0">
  <thead>
    <tr>
      <td width="50%" class="titdocu celda_right" >FECHA DE EMISIÓN</td>
      <td width="50%" class="titdocu" >FECHA DE TRASLADO</td>
    </tr>
    <tr>
      <td class=" celda_right">'.date("Y-m-d", strtotime($mostrar['fecha'])).'</td>
      <td>'.date("Y-m-d", strtotime($mostrar['fecha_transporte'])).'</td>
    </tr>
	</thead>
	</table>
</td>
      <td width="70%"></td>
    </tr>

</thead>
</table>



<table width="100%" class="cuerpo" cellspacing="0">
  <thead>
    <tr>
      <td width="50%" class="titdocu celda_right" >DOMICILIO PARTIDA</td>
      <td width="50%" class="titdocu" >DOMICILIO LLEGADA</td>
    </tr>
    <tr>
      <td class=" celda_right">DIRECCIÓN: '.$suc['direccion'].'<br> UBIGEO: '.$suc['ubigeo'].'</td>
      <td>DIRECCIÓN: '.$dest['direccion'].'<br> UBIGEO: '.$dest['ubigeo'].'</td>
    </tr>
	
</thead>
</table>
<br>
<table width="100%" class="cuerpo" cellspacing="0">
  <thead>
    <tr>
      <td width="50%" class="titdocu celda_right" >DESTINATARIO</td>
      <td width="50%" class="titdocu" >UNIDAD DE TRANSPORTE/CONDUCTOR</td>
    </tr>
    <tr> 
      <td class=" celda_right">Apellidos y nombres/ Razón Social: '.$mcliente['nombre'].'<br>
        RUC/DNI: '.$mcliente['txtID_CLIENTE'].' </td>
      <td>Vehículo / Marca y Placa N°: '.$veh['marca'].' / '.$veh['placa'].'<br>
        Lisencia de conducir: '.$chof['lisencia'].'<br>Certificado de inscripción: '.$chof['certificado'].'</td>
    </tr>
  </thead>
</table>

<br>

<table width="100%" class="cuerpo2" border="0" cellspacing="0">
  <thead> 
    <tr>
	<td width="5%">#</td>
      <td width="10%">CODIGO</td>
      <td width="50%">DESCRIPCION</td>
	   <td width="20%">UNIDAD</td>
      <td width="10%">CANTIDAD</td>
    </tr>
</thead>
<tbody>
';


$mes='';
$fecha_inicio=$fechaf;
$fecha_fin=$fechaf;
$n=0;

if($mostrar['idcliente']=='0'){

if($tipo=='2'){ $rspta = $resumen->detfacturaf($mostrar['iddoc_relacionado']); }else{ $rspta = $resumen->detfactura($mostrarv['idventa']); }

}else{
	
$sql="SELECT * FROM guia_detalle WHERE idguia='$id' ";
$rspta=ejecutarConsulta($sql);	
	
}

while ($reg = $rspta->fetch_object()){	

//EMPRESA DE TRANSPORTE
$sqls1="SELECT *FROM articulo_unidad WHERE id='$reg->idpresentacion' ";
$suc1= ejecutarConsultaSimpleFila($sqls1);
	
$n=$n+1;

$sqlm="SELECT *FROM unidad_medida WHERE codigo='$reg->unidadmedida' ";
$um= ejecutarConsultaSimpleFila($sqlm);
	
if($um['codigo']=='ZZ'||$um['codigo']=='NIU'){ $unidad='UNIDAD'; }else{ $unidad=$um['tit']; }
	
	
$html.='
<tr>
<td>'.$n.'</td>
      <td>'.$reg->codigoproducto.'</td>
      <td>'.$reg->nombreproducto.'</td>
	  <td>'.$unidad.'</td>
      <td>'.$reg->cantidad.'</td>
    </tr>';
}
$html.='
  </tbody>
</table>





</div> 

<hr>


<table width="100%"  class="footer" border="0" cellspacing="0">
  <tbody>

    <tr>
      <td width="64%">MOTIVO DEL TRASLADO: '.$mostrar['motivo'].'<br>OBSERVACIONES: '.$mostrar['observacion'].'<br>PESO:  '.$mostrar['peso'].' / CAJAS:  '.$mostrar['cajas'].'</td>
      
      <td  class="fg fg2" >
        <img src="'.$mempresa['ruc'].'.png" width="70" height="70" />
      </td>
      
</tr>
</tbody>
  

  
</table>


<table width="100%" class="cuerpo" cellspacing="0">
  <thead>	
  <tr>
      <td width="60%" class="titdocu celda_right" >EMPRESA DE TRANSPORTE</td>
      <td width="40%" class="titdocu" >COMPROBANTE DE PAGO</td>
    </tr>
    <tr>
      <td class="celda_right">NOMBRE: '.$trans['nombre'].'<br>
        DIRECCIÓN: '.$trans['direccion'].'<br>
        RUC: '.$trans['ruc'].'
      </td>
      <td>TIPO: '.$tdocumentor.'<br>
      NÚMERO:  '.$mostrav['txtSERIE'].'-'.$mostrav['txtNUMERO'].'</td>
    </tr>
  </thead>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cabecera">
  <tbody>
    <tr>
      <td width="35%" align="center"></td>
      <td width="30%" align="center">
       <br><br><br><br><br>
       
       <table width="300" border="0" cellspacing="0">
        <tbody>
          <tr>
            <td><hr></td>
          </tr>
          <tr>
            <td align="center">CONFORMIDAD DEL CLIENTE</td>
          </tr>
        </tbody>
      </table></td>
      <td width="35%" align="center">&nbsp;</td>
    </tr>
  </tbody>
</table>



</body> </html>

   
   
 ';
   $dompdf = new DOMPDF();
   $dompdf->set_paper('letter','portrait');
   //$dompdf->set_paper('legal','landscape');
//('portrait' or 'landscape')
   $dompdf->load_html($html);
   $dompdf->render();
   //$dompdf->stream("pdf".Date('Y-m-d').".pdf");
//$dompdf->stream("ejemplo-basico.pdf", array('Attachment' => 0));
$pdf = $dompdf->output();
//file_put_contents('../'.$ruta, $pdf);
header("Content-type:application/pdf");
echo $pdf;


?>