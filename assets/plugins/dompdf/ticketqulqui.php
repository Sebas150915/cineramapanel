<?php
//require_once("dompdf/dompdf_config.inc.php");
$rutat=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

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

$idventa=$_GET['id'];
$android=$_GET['android'];

$sql="SELECT *FROM venta WHERE idventa='$idventa' ";
$mostrar= ejecutarConsultaSimpleFila($sql);

$sqlco="SELECT *FROM comandas WHERE id='$idventa' ";
$com= ejecutarConsultaSimpleFila($sqlco);




$sql2="SELECT * FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
$mcliente= ejecutarConsultaSimpleFila($sql2);

$sqluser="SELECT *FROM usuario WHERE idusuario='$mostrar[idusuario]' ";
$user= ejecutarConsultaSimpleFila($sqluser);


$sql3="SELECT *FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa= ejecutarConsultaSimpleFila($sql3);

$sqll="SELECT *FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local= ejecutarConsultaSimpleFila($sqll);

$sqlme="SELECT *FROM mesas WHERE id='$mostrar[idmesa]' ";
$mesa= ejecutarConsultaSimpleFila($sqlme);

if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; $mf='S/'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES AMERICANOS'; $mf='USD$'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; $mf='€'; }

$tdocumento='TICKET DE VENTA';

if($mostrar['txtID_TIPO_DOCUMENTO']=='03'){ $tdocumento='BOLETA DE VENTA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='90'){ $tdocumento='RECIBO DE VENTA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='92'){ $tdocumento='TICKET DE VENTA'; }

$subtotal=$mostrar['txtTOTAL']-$mostrar['txtIGV'];
$subtotal= number_format($subtotal, 2);

$formapago='CONTADO';

$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['txtSERIE'].' | '.$mostrar['txtNUMERO'].' | '.$mostrar['txtIGV'].' | '.$mostrar['txtTOTAL'].' | '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';



/*DESCUENTO*/
$descuento='0.00';
$total=$mostrar['txtTOTAL'];

if($mostrar['descuento']!='0.00'){
$descuento=$mostrar['descuento'];
$total=$mostrar['txtTOTAL']-$mostrar['descuento'];
}
/*DESCUENTO*/

$rutaqr=$mempresa['ruc'].".png";
QRcode::png($text, $rutaqr, 'Q', 15, 0);

$cont='<style type="text/css">
.tabla { font-size: 20px; width: 390px; }
.centrado { text-align: center; }
.negrita { font-weight: bold; }

body{
font:16px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}

@page {
            margin-top: 0.9em;
            margin-left: 0.9em;
        }

  </style>';
		
	
		
$cont.='
<table class="tabla" border="0" cellspacing="0">
  <tbody>';


$cont.='<tr>
      <td style="text-align: center" >';

        $cont.='<tr>
<td style="text-align: center" >';
$cont.='<strong>'.$mempresa['razon_social'].'</strong> <br>';
$cont.='<b style="font-size: 20px">'.$mempresa['nombre_comercial'].'</b><br>';
$cont.='RUC: '.$mempresa['ruc'].'<br>';
$cont.=''.$mempresa['direccion'].'<br>';
$cont.='Telefono: '.$mempresa['telefono'].'<br>';



$cont.='<b>'.$tdocumento.'</b><br>';
$cont.='<b>'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'].'</b><br>';
$cont.='FECHA: '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' / ';
$cont.=date("H:i:s", strtotime($mostrar['txtFECHA_DOCUMENTO'])).'<br>';

		  
$cont.='</td>
    </tr>
    <tr>
      <td >';

$cont.='DATOS DEL CLIENTE: <br>';		
$cont.=$mcliente['nombre'].'<br>';
$cont.=$mcliente['tipo_documento'].': '.$mcliente['txtID_CLIENTE'].'<br>';
$cont.= 'DIR:  '.$mcliente['direccion'].'<hr>';


$cont.= 'FORMA DE PAGO:  '.$formapago.'<hr>';
		  
$cont.='</td>
    </tr>
  </tbody>
</table>

';
$cont.='
<table class="tabla" border="0" cellspacing="0">

    <tr>
      <td width="40%" >Prod.</td>
	  
      <td width="5%" style="text-align:center" >Cant.</td>
      <td width="10%" style="text-align:center" >Precio</td>
      <td width="10%" style="text-align: center" >Total</td>
    </tr>

';

$sql="SELECT *FROM detalle_venta WHERE idventa='$idventa' ";
$rspta =ejecutarConsulta($sql);

while ($reg = $rspta->fetch_object()){

$nombre=$reg->unidadmedida;

$cont.='<tr>
      <td width="40%" >'.$reg->nombreproducto.'</td>

      <td width="5%" style="text-align:center" >'.round($reg->txtCANTIDAD_ARTICULO, 2).'</td>
      <td width="10%" style="text-align:center">'.round($reg->precio, 2).'</td>
      <td width="10%" style="text-align:center">'.$reg->importe.'</td>
    </tr>';
}
$cont.='
</table>

<table class="tabla" border="0" cellspacing="0">
<tr><td style="text-align: center" ><hr></td></tr>
</table>

<table border="0" class="tabla" cellspacing="0">
    
<tr>
      <td colspan="3" style="text-align: right !important;">Op Gravada:  '.$mf.'.</td>
      <td  style="text-align: right" >'.$mostrar['txtSUB_TOTAL'].'</td>
    </tr>
	
<tr>
      <td colspan="3" style="text-align: right !important;">Gratuita:  '.$mf.'.</td>
      <td  style="text-align: right" >'.$mostrar['gratuita'].'</td>
</tr>
	
<tr>
      <td colspan="3" style="text-align: right !important;">Exonerado:  '.$mf.'.</td>
      <td style="text-align: right" >'.$mostrar['exonerado'].'</td>
    </tr>

<tr>
      <td colspan="3" style="text-align: right !important;">I.G.V. '.$mostrar['igv'].'%:  '.$mf.'.</td>
      <td  style="text-align: right" >'.$mostrar['txtIGV'].'</td>
    </tr>
	
<tr>
      <td colspan="3" style="text-align: right !important;">ICBPER:  '.$mf.'.</td>
      <td  style="text-align: right" >'.$mostrar['ICB'].'</td>
    </tr>
	
<tr>
        <td >DESCUENTO ('.$mostrar['tipodescuento'].')  '.$mf.'.</td>
      <td  style="text-align: right" >'.$mostrar['descuento'].'</td>
    </tr>
    <tr>
      <td >EFECTIVO: '.$mf.'.</td>
      <td  style="text-align: right" >'.$mostrar['contado'].'</td>
    </tr>
    <tr>
      <td >('.$mostrar['formapago'].')  '.$mf.'.</td>
      <td  style="text-align: right" >'.$mostrar['otros'].'</td>
    </tr>
    
<tr>
<td colspan="3" style="text-align: right !important;">IMPORTE TOTAL:  '.$mf.'.</td>
<td style="text-align: right" >'.$total.'</td>
</tr>

</table>';


$cont.='
<table class="tabla" border="0" cellspacing="0">

<tr>
      <td width="15%" style="text-align: center" >';
$cont.='<br>'.$mostrar['txtOBSERVACION'].'<br>';
$cont.='<br>SON: '.numtoletras($mostrar['txtTOTAL'], $valmoneda);


$cont.='</td>
  </tr>
    <tr>
      <td width="15%" style="text-align: center" >';

//$cont.='<br><img src="'.$mempresa['ruc'].'.png" width="120" height="120" /><br>';
$cont.='<img src="'.RUTA.'/plugins/dompdf/10000000005.png" style="width: 200px; max-height: 190px; " />';
$cont.='</td>
    </tr>
    <tr>
      <td width="15%" style="text-align: center" >';

$cont.='<br> Representación impresa de</br>';
$cont.='<br> '.$tdocumento.'</br>';
$cont.='<br>Consulte su comprobante (pdf,cdr) aquí: <br>';
$cont.='<br> '.RUTA.'/comprobantes/ <br>';

$cont.='GRACIAS POR SU PREFERENCIA';

$cont.='</td>
    </tr>

</table>';


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



/*
echo $cont;
*/

if($android=='0'){

    $dompdf->load_html($cont);
    $dompdf->render();

    $dompdf = new Dompdf();
    $dompdf->set_paper(array(0,0,210,$GLOBALS['bodyHeight']+50));
    $dompdf->loadHtml($cont);
    $dompdf->render();
    $dompdf->stream($filename,array("Attachment"=>0));
}else{
    echo $cont;
}


?>