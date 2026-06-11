<?php
//require_once("dompdf/dompdf_config.inc.php");
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

$sqlhr="SELECT *FROM rutas_horario WHERE id='$mostrar[idhorario]' ";
$hor= ejecutarConsultaSimpleFila($sqlhr);

$sqllru="SELECT *FROM rutas WHERE numero='$hor[ruta]' ORDER BY orden DESC ";
$rut= ejecutarConsultaSimpleFila($sqllru);

$sqllru2="SELECT *FROM rutas WHERE numero='$hor[ruta]' AND idempresa='$mostrar[idempresa]' ORDER BY orden ASC ";
$rut2= ejecutarConsultaSimpleFila($sqllru2);

$sqll="SELECT *FROM sucursal WHERE id='$rut2[id_sede]' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sqll2="SELECT *FROM sucursal WHERE id='$rut[id_sede]' ";
$local2= ejecutarConsultaSimpleFila($sqll2);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

$ruta=RUTA."/api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero='M-'.$mempresa['ruc'].'-'.$mostrar['serie'].'-'.$mostrar['numero'];

$tdocumento='MANIFIESTO DE USUARIOS';

$num=0;

foreach(range("1", $vehiculo['asientos']) as $letra ) {

$sqls="SELECT *FROM venta_pasajes WHERE asiento='$letra' AND idhorario='$mostrar[idhorario]' AND estado='0'  AND pagodestino='NO' ";
$su= ejecutarConsultaSimpleFila($sqls);

$serie='';

if($su){
	
$sql2="SELECT *FROM venta WHERE idventa='$su[idventa]' ";
$ve= ejecutarConsultaSimpleFila($sql2);
$serie=$ve['txtSERIE'].'-'.$ve['txtNUMERO'];
    
    
if($serie=='-'){
}else{
$num++;
}


}	
$sqlmenor="SELECT * FROM venta_pasajes_ninos  where idpasaje='$su[id]' ";
$results=ejecutarConsulta($sqlmenor);
while($obj = $results->fetch_object()){
$num++;
}

}








 $html ='

   
   
   
   
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
    font-size:22px; padding-bottom: 0px; margin-bottom: 0px;
}

.cabecera2 table { border-collapse: collapse; border: solid 1px #000000;}
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; border: solid 1px #000000; font-size:22px; } 
.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 0.5px #000000; }
.cuerpo thead { border: solid 0.5px #000000; } 
.cuerpo th, .cuerpo td { border-collapse: collapse; border: solid 0.5px #000000; font-size:12px; } 
.cuerpo2 thead { border: solid 1px #000000; } 

table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 2pt; }           
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

   


<table width="100%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
	
<td width="20%"><img src="../../files/logo/'.$mempresa['id'].'.png" style="max-width: 600px; max-height: 180px; margin-bottom: -10px; " /></td>
	
<td width="42%" class="cabeza"><h1>'.$mempresa['nombre_comercial'].$rut['numero'].'</h1>';

$rsptadf=ejecutarConsulta("SELECT r.*, s.* FROM rutas r, sucursal s  where s.id = r.id_sede and  r.numero ='" . $mostrar['ruta'] . "' and  r.estado='1' AND r.idempresa='$mostrar[idempresa]' order by r.orden ");
while ($regdf= $rsptadf->fetch_object()){
$html.=' '.$regdf->sucursal.' -';
}
$html.='</td>
		
      <td width="38%">
        
        
        <table width="100%" class="cabecera2" cellspacing="0" >
          <tbody>
            <tr>
<td ><br>RUC N° '.$mempresa['ruc'].' <br> '.$tdocumento.' <br> '.$mostrar['serie'].'-'.$mostrar['numero'].'<br><br></td>
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
      <td colspan="2">DESTINO: '.$local2['sucursal'].'</td>
    </tr>
    <tr>
      <td colspan="3">NOMBRE DEL PILOTO: '.$re['nombre'].'</td>
      <td>BREVETE N°: '.$re['lisencia'].'</td>
    </tr>
    <tr>
      <td colspan="3">NOMBRE DEL COPILOTO: '.$re2['nombre'].'</td>
      <td>BREVETE N°: '.$re2['lisencia'].'</td>
    </tr>
    <tr>
      <td colspan="2">NOMBRE AUX. SERV.:</td>
      <td colspan="2">TOTAL ASIENTOS BUS: '.$vehiculo['asientos'].'</td>
    </tr>
    <tr>
      <td>CANT. PASAJ. TER.: '.$num.'</td>
      <td>CANT. PASAJ. RUTA: '.$num.'</td>
      <td colspan="2">CANT. ASI. TRIP.: '.$num.'</td>
    </tr>

  </thead>
</table>

<br>
<table width="100%" class="cuerpo" border="0" cellspacing="0">
<thead> 
    <tr>
      <td width="1%">N°</td>
      <td width="30%">APELLIDOS Y NOMBRES</td>
      <td width="5%">DNI</td>
	  <td width="7%" align="center">NAC</td>
	  <td width="4%" align="center">EDAD</td>
	  <td width="7%" align="center">NRO. BOLETO</td>
	  <td width="5%" align="center">IMPORTE</td>
	  <td width="10%" align="center">ORIGEN</td>
	  <td width="10%" align="center">DESTINO</td>
    </tr>
</thead>
<tbody>
';



/*
$rsptad=ejecutarConsulta("SELECT * FROM venta_opdetallet WHERE idpedido='$id' ");
while ($regd= $rsptad->fetch_object()){
	*/
$num=0;
foreach(range("1", $vehiculo['asientos']) as $letra ) {

if($idlocal==43){
$sqls="SELECT *FROM venta_pasajes WHERE asiento='$letra' AND idhorario='$mostrar[idhorario]' AND estado='0' AND pagodestino='NO' ORDER BY idembarque DESC ";
}else{
$sqls="SELECT *FROM venta_pasajes WHERE asiento='$letra' AND idhorario='$mostrar[idhorario]' AND estado='0' AND pagodestino='NO' ";
}
$su= ejecutarConsultaSimpleFila($sqls);

$serie='';
$nacion='';
$total='';
$nombre='';
$origen='';
$destino='';
$dnifinal='';
$edadfinal='';

if($su){
	
$sql2="SELECT *FROM venta WHERE idventa='$su[idventa]' ";
$ve= ejecutarConsultaSimpleFila($sql2);
$serie=$ve['txtSERIE'].'-'.$ve['txtNUMERO'];

$total=$ve['txtTOTAL'];
	
$sqll="SELECT *FROM sucursal WHERE id='$su[idembarque]' ";
$localf1= ejecutarConsultaSimpleFila($sqll);
	
$sqlcliente="SELECT *FROM persona WHERE txtID_CLIENTE='$su[dni]' ";
$idcliente= ejecutarConsultaSimpleFila($sqlcliente);

$sqll2="SELECT *FROM sucursal WHERE id='$su[iddestino]' ";
$localf2= ejecutarConsultaSimpleFila($sqll2);




//if($serie=='-'){
//}else{
$nombre=strtoupper($su['nombre']);
$dnifinal=$su['dni'];
//$nacion='P';
$nacion=$idcliente['nacionalidad'];
$edadfinal=$su['edad'];
$origen=$localf1['sucursal'];
$destino=$localf2['sucursal'];
//}


}	
$asiento=str_pad($letra, 2, "0", STR_PAD_LEFT);

$sqlmenor="SELECT * FROM venta_pasajes_ninos  where idpasaje='$su[id]' ";

$menoredad='';

if($su['mayor_edad']!=''){ $menoredad=' - '.$su['mayor_edad']; }

$html.='
<tr>
    <td>'.$asiento.'</td>
    <td>'.$nombre.''.$menoredad.'</td>
    <td>'.$dnifinal.'</td>
	<td align="center">'.$nacion.'</td>
	<td align="center">'.$edadfinal.'</td>
	<td align="center">'.$serie.'</td>
	<td align="center">'.$total.'</td>
	<td >'.$origen.'</td>
	<td >'.$destino.'</td>
  </tr>';
 
 
 
$results=ejecutarConsulta($sqlmenor);
while($obj = $results->fetch_object()){



$html.='
<tr>
    <td>'.$asiento.'</td>
    <td>'.$obj->documento_nombre.'</td>
    <td>'.$obj->documento_numero.'</td>
	<td align="center">'.$nacion.'</td>
	<td align="center">'.$obj->edad.'</td>
	<td align="center">'.$serie.'</td>
	<td align="center">0.00</td>
	<td >'.$origen.'</td>
	<td >'.$destino.'</td>
  </tr>';
}



 
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