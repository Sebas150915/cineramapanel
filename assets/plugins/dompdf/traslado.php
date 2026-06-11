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

require "../../modelos/numeros-letras.php";
include "../phpqrcode/qrlib.php";
require "../../modelos/resumen.php";
//require "../../modelos/Venta.php";


$venta=new Venta();
$resumen=new Resumen();

$id=$_GET['id'];

$sql="SELECT *FROM venta WHERE id='$id' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sqla="SELECT *FROM categoria WHERE idcategoria='$mostrar[area]' ";
$area= ejecutarConsultaSimpleFila($sqla);

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

$ruta="../../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero='D-'.$mempresa['ruc'].'-'.$mostrar['serie'];

$valmoneda='SOLES';

if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }

$tdocumento='ORDEN DE CARGA';

if($mostrar['tipo']=='10'){ $tdocumento='VALE DE SALIDA'; }
if($mostrar['tipo']=='11'){ $tdocumento='TRANSFERENCIA ENTRE ALMACENES'; }
if($mostrar['tipo']=='12'){ $tdocumento='RETIRO'; }
if($mostrar['tipo']=='04'){ $tdocumento='CONSIGNACIÓN ENTREGADA'; }

//QRcode::png("".$text);
//DATOS OBLIGATORIOS DE LA SUNAT EN EL QR
//RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE //EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |

$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['serie'].' | '.$mostrar['igv'].' | '.$mostrar['total'].' | '.date("Y-m-d", strtotime($mostrar['fecha'])).' | '.$tdocumento.' | ';
QRcode::png($text, $mempresa['ruc'].".png", 'Q',15, 0);

 $html ='






<html> 
   <head> 
   <style> 
body{
font:13px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
.cabecera table {
	width: 100%;
    color:black;
    margin-top: 0em;
    text-align: left; font-size: 14px;
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

table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 3pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         

.footer th, .footer td { padding: 1pt; border: solid 1px #000000; }
.footer { font-size:11px;  width: 100%; border: solid 0px #000000; }
.fg { font-size: 11px;} 
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
      <td width="10%">MONEDA:</td>
      <td width="44%">SOLES</td>
      <td width="14%">FECHA:</td>
      <td width="32%">'.date("Y-m-d", strtotime($mostrar['fecha'])).'</td>
    </tr>
    <tr>
      <td>ORIGEN:</td>
      <td >'.$local['sucursal'].'</td>
      <td >ÁREA DESTINO:</td>
      <td >'.$area['nombre'].'/'.$mostrar[area].'</td>
    </tr>
    <tr>
      <td colspan="4">OBSERVACIONES: '.$mostrar['observaciones'].'</td>
    </tr>
  </thead>
</table>


<table width="100%" class="cuerpo2" border="0" cellspacing="0">
<thead> 
    <tr>
      <td width="10%">CÓDIGO</td>
      <td width="39%">DESCRIPCION</td>
	  <td width="15%" align="center">U/MED</td>
      <td width="8%" align="center">PRECIO</td>
      <td width="8%" align="center">CTI</td>
      <td width="8%" align="center">TOTAL</td>

    </tr>
</thead>
<tbody>
';

$datos = "SELECT * FROM venta_opdetallet WHERE idpedido='$id' ORDER BY id DESC  ";
$sql = ejecutarConsulta($datos);
while ($rowd = mysqli_fetch_array($sql)) {

$sqls1="SELECT *FROM articulo_unidad WHERE id='$rowd[idpresentacion]' ";
$suc1= ejecutarConsultaSimpleFila($sqls1);
	
$nombre=$suc1['nombre'];
if(!$suc1){
$nombre='UNIDAD';
}	
	
$html.='
<tr>
      <td>'.$rowd['codigoproducto'].'</td>
    <td>'.$rowd['nombreproducto'].' </td>
	<td>'.$nombre.'</td>
      <td align="center">'.$rowd['precio'].'</td>
      <td align="center">'.$rowd['cti'].'</td>
      <td align="center">'.$rowd['importe'].'</td>
    </tr>';

}
					//}
$html.='
  </tbody>
</table>





</div> 

<br><br>

<table width="100%"  class="footer" border="0" cellspacing="0">
  <tbody>

    <tr>
      <td>
        <img src="'.$mempresa['ruc'].'.png" width="80" height="80" />      </td>
      
      <td class="fg fg2" width="20%" >
        
        
        <table width="100%" border="0" cellspacing="0"  class="total"  >
          <tbody>
            <tr><td class="total2" width="50%"><strong>SUB.TOTAL:</strong></td>
              <td><strong>'.$mostrar['subtotal'].'</strong></td></tr>
            <tr><td class="total2"><strong>IGV(18%):</strong></td>
              <td><strong>'.$mostrar['igv'].'</strong></td></tr>
            <tr><td class="total2"><strong>TOTAL:</strong></td>
            <td><strong>'.$mostrar['total'].'</strong></td></tr>
            
          </tbody>
        </table>
        
      </td>
      
    </tr>
  </tbody>
</table>


<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cabecera">
  <tbody>
    <tr>
      <td width="21%" align="center"></td>
      <td width="28%" align="center">
       <br><br><br><br><br>
       
       <table width="300" border="0" cellspacing="0">
        <tbody>
          <tr>
            <td><hr></td>
          </tr>
          <tr>
            <td align="center">RESPONSABLE DE ENTREGA</td>
          </tr>
        </tbody>
      </table>
		
		</td>
      <td width="4%" align="center">&nbsp;</td>
      <td width="28%" align="center">
		
		
<br><br><br><br><br>
       
       <table width="300" border="0" cellspacing="0">
        <tbody>
          <tr>
            <td><hr></td>
          </tr>
          <tr>
            <td align="center">RESPONSABLE DE RECEPCION<br></td>
          </tr>
        </tbody>
      </table>
		
		
		</td>
      <td width="19%" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="center"></td>
      <td>NOMBRE:</td>
      <td align="center">&nbsp;</td>
      <td>NOMBRE:</td>
      <td align="center">&nbsp;</td>
    </tr>
  </tbody>
</table>


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