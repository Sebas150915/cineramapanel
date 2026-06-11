<?php
//require_once("dompdf/dompdf_config.inc.php");

session_start();
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
require "../../config/Conexion.php";
require "../../modelos/numeros-letras.php";

$id=$_GET['id'];
$idlocal=$_GET['idlocal'];

$sql="SELECT *FROM manifiesto WHERE id='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sqll="SELECT *FROM guia_vehiculo WHERE id='$mostrar[idbuss]' ";
$vehiculo= ejecutarConsultaSimpleFila($sqll);

$sqlre="SELECT *FROM guia_chofer WHERE id='$vehiculo[idchofer]' ";
$re= ejecutarConsultaSimpleFila($sqlre);

$sqlre2="SELECT *FROM guia_chofer WHERE id='$vehiculo[copiloto]' ";
$re2= ejecutarConsultaSimpleFila($sqlre2);

$sqlre3="SELECT *FROM usuario WHERE idusuario='$vehiculo[asistente]' ";
$re3= ejecutarConsultaSimpleFila($sqlre3);

$sqll="SELECT *FROM sucursal WHERE id='$idlocal' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sqlhr="SELECT *FROM rutas_horario WHERE id='$mostrar[idhorario]' ";
$hor= ejecutarConsultaSimpleFila($sqlhr);

$sqllru="SELECT *FROM rutas WHERE id_ruta='$hor[ruta]' ";
$rut= ejecutarConsultaSimpleFila($sqllru);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

$sqlpas="SELECT SUM(v.txtTOTAL) as total FROM venta_pasajes vp INNER JOIN venta v ON vp.idventa=v.idventa WHERE vp.idhorario='$mostrar[idhorario]' AND vp.estado='0' ";
$totpas= ejecutarConsultaSimpleFila($sqlpas);

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

$ruta=RUTA."/api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero='M-'.$mempresa['ruc'].'-'.$mostrar['serie'].'-'.$mostrar['numero'];

$tdocumento='LIQUIDACIÓN DE CHOFER';

//QRcode::png("".$text);
//DATOS OBLIGATORIOS DE LA SUNAT EN EL QR
//RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE //EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |


 $html ='

   
   
<html> 
   <head> 
   <style> 
body{
font:18px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
.cabecera table {
	width: 100%;
    color:black;
    margin-top: 0em;
    text-align: left; font-size: 18px;
}
.cabecera h1 {
    font-size:22px; padding-bottom: 0px; margin-bottom: 0px; te
}

.cabecera2 table { border-collapse: collapse; }
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; font-size:22px; } 
.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 0.5px #000000; }
.cuerpo thead { border: solid 0.5px #000000; } 
.cuerpo th, .cuerpo td { border-collapse: collapse; border: solid 0.5px #000000; font-size:14px; } 
.cuerpo2 thead { border: solid 1px #000000; } 

table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 1pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt; border: solid 1px #000000; }
.footer { font-size:9px;  width: 100%; border: solid 0px #000000; }
.fg { font-size: 10px;} 
.fg2 { text-align: center; }
.fg3 { border: solid 0px; } 
.total td { border: solid 0px; padding: 0px; } 
.total2 { text-align: right; } 

   </style>
    
   </head> 
    
   <body>        

   


<table width="115%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
	
<td width="20%"><img src="../../files/logo/'.$mempresa['id'].'.png" style="max-width: 600px; max-height: 180px; margin-bottom: -10px; " /></td>
	
<td width="42%" class="cabeza"><h1>'.$mempresa['nombre_comercial'].'</h1>';

$rsptadf=ejecutarConsulta("SELECT r.*, s.* FROM rutas r, sucursal s  where s.id = r.id_sede and  r.numero ='" . $rut['numero'] . "' and  r.estado='1' order by r.orden ");
while ($regdf= $rsptadf->fetch_object()){
$html.=' '.$regdf->sucursal.' -';
}
$html.='</td>
		
      <td width="38%">
        
        
        <table width="100%" class="cabecera2" cellspacing="0" >
          <tbody>
            <tr>
              <td >RUC N° '.$mempresa['ruc'].'</td>
            </tr>
            <tr>
              <td>'.$tdocumento.'</td>
            </tr>

          </tbody>
        </table>
        
        
        
        
      </td>
    </tr>
  </tbody>
</table>

<br>
<table width="101%" class="cuerpo" cellspacing="0">
<thead>
    <tr>
      <td width="26%">PLACA: '.$vehiculo['placa'].'</td>
      <td width="26%">MARCA: '.$vehiculo['marca'].'</td>
      <td width="21%">TARJ. CIRC. N°: '.$vehiculo['circulacion'].'</td>
      <td width="27%">F/H: '.$hor['fecha'].' '.$hor['hora'].':'.$hor['minuto'].$hor['ampm'].'</td>
    </tr>
    <tr>
      <td colspan="2">PUNTO DE PARTIDA: '.$local['sucursal'].'</td>
      <td colspan="2">DESTINO: </td>
    </tr>
    <tr>
      <td colspan="3">NOMBRE DEL PILOTO: '.$re['nombre'].' | TELEF: '.$re['telefono'].'</td>
      <td>BREVETE N°: '.$re['lisencia'].'</td>
    </tr>
    <tr>
      <td colspan="4">GUÍA: '.$re3['nombre'].' | TOTAL VENTAS: S/'.$totpas['total'].'</td>
    </tr>

  </thead>
</table>

<br>
<table width="100%" class="cuerpo" border="0" cellspacing="0">
<thead> 
    <tr>
      <td width="1%">ASI.</td>
      <td width="24%">APELLIDOS Y NOMBRES</td>
      <td width="5%">DNI</td>
      <td width="5%">CEL</td>
      <td width="10%">HOTEL</td>
	  <td width="2%" align="center">NACION</td>
	  <td width="2%" align="center">EDAD</td>
	  <td width="7%" align="center">NRO. BOLETO</td>
	  <td width="6%" align="center">PAGO</td>
	  <td width="6%" align="center">SALDO</td>
    </tr>
</thead>
<tbody>
';




$rsptadff=ejecutarConsulta("SELECT * FROM venta_pasajes WHERE idhorario='$mostrar[idhorario]' AND estado='0' ");
while ($regd= $rsptadff->fetch_object()){
/*
foreach(range("1", $vehiculo['asientos']) as $letra ) {
	*/


$serie='';
$nacion='';
$total='';
$nombre='';
	
$sql2="SELECT *FROM venta WHERE idventa='$regd->idventa' ";
$ve= ejecutarConsultaSimpleFila($sql2);

$sqlper="SELECT *FROM persona WHERE idpersona='$regd->idcliente' ";
$per= ejecutarConsultaSimpleFila($sqlper);

$serie=$ve['txtSERIE'].'-'.$ve['txtNUMERO'];
$nacion='P';
$total=$ve['txtTOTAL'];

$saldo=$ve['txtTOTAL']-$ve['contado'];
	
$nombre=strtoupper($regd->nombre);
$asiento=str_pad($regd->asiento, 2, "0", STR_PAD_LEFT);

$sqlhot="SELECT *FROM persona WHERE idpersona='$regd->idhotel' ";
$hotel= ejecutarConsultaSimpleFila($sqlhot);
	
$html.='
<tr>
    <td>'.$asiento.'</td>
    <td>'.$nombre.'</td>
    <td>'.$regd->dni.'</td>
    <td>'.$per['telefono'].'</td>
     <td>'.$hotel['nombre'].'</td>
	<td align="center">'.$nacion.'</td>
	<td align="center">'.$regd->edad.'</td>
	<td align="center">'.$serie.'</td>
	<td align="center">S/'.$total.'</td>
	<td align="center">S/'.$saldo.'</td>
  </tr>';

}

$html.='
</table>





</div> 

<br>
</body> </html>





   
   
 ';


header("Content-type:application/pdf");

   $dompdf = new DOMPDF();
  $dompdf->set_paper('legal','potrait');
   //$dompdf->set_paper('legal','landscape');
   $dompdf->load_html($html);
   $dompdf->render();
   //$dompdf->stream("pdf".Date('Y-m-d').".pdf");
//$dompdf->stream("ejemplo-basico.pdf", array('Attachment' => 0));
$pdf = $dompdf->output();
//file_put_contents('../'.$ruta, $pdf);
//file_put_contents($ruta.$fichero.'.pdf', $pdf);

echo $pdf;


?>