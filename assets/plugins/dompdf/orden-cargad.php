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

require "../../config/Conexion.php";
require "../../modelos/numeros-letras.php";
include "../phpqrcode/qrlib.php";
require "../../modelos/resumen.php";


$resumen=new Resumen();

$id=$_GET['id'];

$sql="SELECT *FROM venta_orden WHERE id='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sqlchofer="SELECT *FROM guia_chofer WHERE id='$mostrar[idresponsable]' ";
//echo $sqlchofer;
$chofer= ejecutarConsultaSimpleFila($sqlchofer);

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

$ruta="../../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero='D-'.$mempresa['ruc'].'-'.$mostrar['serie'];

$valmoneda='SOLES';

if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }

$tdocumento='ORDEN DE CARGA';

//QRcode::png("".$text);
//DATOS OBLIGATORIOS DE LA SUNAT EN EL QR
//RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE //EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |

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
    text-align: left; font-size: 18px;
}
.cabecera h1 {
    font-size:22px; padding-bottom: 0px; margin-bottom: 0px;
}

.cabecera2 table { border-collapse: collapse; border: solid 1px #000000;}
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: collapse; border: solid 1px #000000; font-size:22px; } 
.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 1px #000000; }
.cuerpo thead { border: solid 1px #000000; } 
.cuerpo2 thead { border: solid 1px #000000; } 

table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 3pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt; border: solid 1px #000000; }
.footer { font-size:11px;  width: 100%; border: solid 0px #000000; }
.fg { font-size: 13px;} 
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
	
<td width="20%"><img src="'.RUTA.'/files/logo/'.$mempresa['id'].'.png" height="70" /></td>
	
<td width="42%" class="cabeza"><h1>'.$mempresa['nombre_comercial'].'</h1>
  <strong>SUCURSAL:</strong> '.$local['direccion'].'<br>
  <strong>TELF. PRINCIPAL:</strong> '.$mempresa['telefono'].'<br>
</td>
		
      <td width="38%">
        
        
        <table width="100%" class="cabecera2" cellspacing="0" >
          <tbody>
            <tr>
              <td >RUC N° '.$mempresa['ruc'].'</td>
            </tr>
            <tr>
              <td class="nfactura">'.$tdocumento.'</td>
            </tr>
            <tr>
              <td >'.$mostrar['serie'].'-'.$mostrar['numero'].'</td>
            </tr>
          </tbody>
        </table>
        
        
        
        
      </td>
    </tr>
  </tbody>
</table>

<br>
<table width="100%" class="cuerpo" cellspacing="0">
<thead>
    <tr>
      <td width="20%">ORIGEN: '.$local['sucursal'].'</td>
      <td width="20%">FECHA: '.date("Y-m-d", strtotime($mostrar['fecha'])).'</td>
      <td width="60%">PILOTO: '.$chofer['apellido'].' '.$chofer['nombre'].'</td>
    </tr>

    <tr>
      <td colspan="3">OBSERVACIONES: '.$mostrar['observaciones'].'</td>
    </tr>
    
    
  </thead>
</table>


<table width="100%" class="cuerpo2" border="0" cellspacing="0">
<thead> 
    <tr>
      <td width="11%">SERIE</td>
      <td width="25%">CONSIGNADO</td>
      <td width="35%">DESCRIPCION</td>
      <td width="20%" align="center">DESTINO</td>
    </tr>
</thead>
<tbody>
';

$datos = "SELECT * FROM venta_opdetallet WHERE idpedido='$id' ORDER BY id DESC  ";
$sql = ejecutarConsulta($datos);
while ($rowd = mysqli_fetch_array($sql)) {

$sqlprod="SELECT *FROM venta_opdetalle WHERE seriedoc='$rowd[serie]' ";
$prod= ejecutarConsultaSimpleFila($sqlprod);

$pieces = explode("-", $rowd['serie']);

$sqlveneficiario="SELECT vp.nombre FROM venta v INNER JOIN venta_pasajes vp ON v.idventa=vp.idventa WHERE v.txtSERIE='$pieces[0]' AND v.txtNUMERO='$pieces[1]'  ";
$veneficiario= ejecutarConsultaSimpleFila($sqlveneficiario);
	
$sqlorigen="SELECT *FROM sucursal WHERE id='$prod[idlocal]' ";
$origen= ejecutarConsultaSimpleFila($sqlorigen);	
	
$sqldestino="SELECT *FROM sucursal WHERE id='$prod[iddestino]' ";
$destino= ejecutarConsultaSimpleFila($sqldestino);	
	
$html.='
<tr>
      <td>'.$rowd['serie'].'</td>
      <td>'.$veneficiario['nombre'].'</td>
    <td>'.$rowd['nombreproducto'].' </td>
      <td align="center">'.$destino['sucursal'].'</td>
    </tr>';

}

$html.='
  </tbody>
</table>





</div> 





</body> </html>







   
   
 ';
 
 
 
 
   $dompdf = new DOMPDF();
  $dompdf->set_paper('legal','potrait');
   //$dompdf->set_paper('legal','landscape');
   $dompdf->load_html($html);
   $dompdf->render();
   //$dompdf->stream("pdf".Date('Y-m-d').".pdf");
//$dompdf->stream("ejemplo-basico.pdf", array('Attachment' => 0));
$pdf = $dompdf->output();
/*
file_put_contents($ruta.$fichero.'.pdf', $pdf);
*/
header("Content-type:application/pdf");
echo $pdf;

?>