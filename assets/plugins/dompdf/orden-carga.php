<?php
ob_start();
$rutat = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$rutat = str_replace("plugins/dompdf/index.php", "", $rutat);

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

$resumen = new Resumen();

$id = $_GET['id'];

$sql = "SELECT * FROM venta_orden WHERE id='$id' ";
$mostrar = ejecutarConsultaSimpleFila($sql);

$sql3 = "SELECT * FROM config WHERE estado='1' ";
$mempresa = ejecutarConsultaSimpleFila($sql3);

$sqll = "SELECT * FROM sucursal WHERE id='$mostrar[idlocal]' ";
$local = ejecutarConsultaSimpleFila($sqll);

$valmoneda = 'SOLES';
$mf = 'S/';
$mon = isset($mostrar['txtID_MONEDA']) ? $mostrar['txtID_MONEDA'] : (isset($mostrar['moneda']) ? $mostrar['moneda'] : 'PEN');
if($mon == 'PEN'){ $valmoneda = 'SOLES'; $mf = 'S/'; }
if($mon == 'USD'){ $valmoneda = 'DOLARES AMERICANOS'; $mf = 'USD$'; }
if($mon == 'EUR'){ $valmoneda = 'EUROS'; $mf = '€'; }

if($mempresa['tipo'] == '03'){ 
    $tipop = 'BETA'; 
} else { 
    $tipop = 'PRODUCCION'; 
}

$ruta = "../../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero = $mempresa['ruc'].'-'.$mostrar['serie'];

$tdocumento = 'ORDEN DE CARGA';

// DATOS OBLIGATORIOS DE LA SUNAT EN EL QR
$text = $mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['serie'].' | '.$mostrar['igv'].' | '.$mostrar['total'].' | '.date("Y-m-d", strtotime($mostrar['fecha'])).' | '.$tdocumento.' | ';
QRcode::png($text, $mempresa['ruc'].".png", 'Q', 15, 0);

// Definir RUTA si no está definida
if(!defined('RUTA')) {
    define('RUTA', $rutat);
}

// Totales del documento con respaldo desde detalle si encabezado está vacío
$subt = isset($mostrar['subtotal']) ? (float)$mostrar['subtotal'] : 0.00;
$igvt = isset($mostrar['igv']) ? (float)$mostrar['igv'] : 0.00;
$tott = isset($mostrar['total']) ? (float)$mostrar['total'] : 0.00;
if ($subt == 0.0 && $igvt == 0.0 && $tott == 0.0) {
    $sumd = ejecutarConsultaSimpleFila("SELECT COALESCE(SUM(subtotal),0) AS subtotal, COALESCE(SUM(igv),0) AS igv, COALESCE(SUM(importe),0) AS total FROM venta_opdetalle WHERE idopedido='".$id."'");
    $subt = isset($sumd['subtotal']) ? (float)$sumd['subtotal'] : 0.00;
    $igvt = isset($sumd['igv']) ? (float)$sumd['igv'] : 0.00;
    $tott = isset($sumd['total']) ? (float)$sumd['total'] : 0.00;
}

$html = '
<html> 
   <head> 
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <style> 
        @page { 
            size: A4; 
            margin: 10mm; 
        }
        body {
            font: 11px Arial, Tahoma, Verdana, Helvetica, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .cabecera table {
            width: 100%;
            color: black;
            margin-top: 0em;
            text-align: left; 
            font-size: 11px;
            border-collapse: collapse;
        }
        .cabecera h1 {
            font-size: 17px; 
            padding-bottom: 0px; 
            margin-bottom: 0px;
        }

        .cabecera2 table { 
            border-collapse: collapse; 
            border: solid 1px #000000;
            width: 100%;
        }
        .cabecera2 th, .cabecera2 td { 
            text-align: center; 
            border-collapse: collapse; 
            border: solid 1px #000000; 
            font-size: 11px; 
            padding: 3px;
        } 
        .cabeza{ 
            text-align: left; 
        }
        .nfactura{ 
            background-color: #D8D8D8; 
        }
        .cuerpo table { 
            border-collapse: collapse; 
            margin-top: 1px; 
            border: solid 1px #000000; 
            width: 100%;
        }
        .cuerpo thead { 
            border: solid 1px #000000; 
        } 
        .cuerpo2 thead { 
            border: solid 1px #000000; 
        } 

        table { 
            width: 100%; 
            color: black;
            border-collapse: collapse;
        }
          
        tbody { 
            background-color: #ffffff; 
        }
        th, td { 
            padding: 3pt; 
        }           
        .celda_right{  
            border-right: 1px solid black;  
        }
        .celda_left{  
            border-left: 1px solid black; 
        }         

        .footer th, .footer td { 
            padding: 1pt; 
            border: solid 1px #000000; 
        }
        .footer { 
            font-size: 11px;  
            width: 100%; 
            border: solid 0px #000000; 
        }
        .fg { 
            font-size: 11px;
        } 
        .fg2 { 
            text-align: center; 
        }
        .fg3 { 
            border: solid 0px; 
        } 
        .total td { 
            border: solid 0px; 
            padding: 0px; 
        } 
        .total2 { 
            text-align: right; 
        }
        img {
            max-width: 100%;
            height: auto;
        }
      </style>
   </head> 
    
   <body>        
      <table width="100%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td width="20%">
  <img src="../../files/logo/'.$mempresa['id'].'.png" style="max-width: 200px; max-height: 50px; margin-bottom: -10px; " />                </td>
                <td width="42%" class="cabeza">
                    <h1>'.$mempresa['nombre_comercial'].'</h1>
                    <strong>SUCURSAL:</strong> '.$local['direccion'].'<br>
                    <strong>TELF. PRINCIPAL:</strong> '.$mempresa['telefono'].'<br>
                </td>
                <td width="38%">
                    <table width="100%" class="cabecera2" cellspacing="0">
                        <tbody>
                            <tr>
                                <td>RUC N° '.$mempresa['ruc'].'</td>
                            </tr>
                            <tr>
                                <td class="nfactura">'.$tdocumento.'</td>
                            </tr>
                            <tr>
                                <td>'.(isset($mostrar['numero']) ? ($mostrar['serie'].'-'.$mostrar['numero']) : $mostrar['serie']).'</td>
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
                <td width="50%">'.$valmoneda.'</td>
                <td width="8%">FECHA:</td>
                <td width="32%">'.date("Y-m-d", strtotime($mostrar['fecha'])).'</td>
            </tr>
            <tr>
                <td colspan="4">OBSERVACIONES: '.$mostrar['observaciones'].'</td>
            </tr>
        </thead>
      </table>

      <table width="100%" class="cuerpo2" border="0" cellspacing="0">
        <thead> 
            <tr>
                <td width="10%">TIPO/DOC</td>
                <td width="11%">SERIE</td>
                <td width="38%">CLIENTE</td>
                <td width="11%">DOC/CLI</td>
                <td width="10%" align="center">PRECIO</td>
                <td width="10%" align="center">IGV</td>
                <td width="10%" align="center">IMPORTE</td>
            </tr>
        </thead>
        <tbody>';

$mes = '';
$fecha_inicio = $mostrar['fecha'];
$fecha_fin = $mostrar['fecha'];
        
$datos = "SELECT * FROM venta_opdetalle WHERE idopedido='$id' ORDER BY id DESC";
$sql = ejecutarConsulta($datos);
while ($row = mysqli_fetch_array($sql)) {
    $html .= '
            <tr>
                <td>'.$row['tipodoc'].'</td>
                <td>'.$row['seriedoc'].'</td>
                <td>'.$row['cliente'].'</td>
                <td>'.$row['docliente'].'</td>
                <td align="center">'.$row['subtotal'].'</td>
                <td align="center">'.$row['igv'].'</td>
                <td align="center">'.$row['importe'].'</td>
            </tr>';
}

$html .= '
        </tbody>
      </table>

      <br><br>
      <strong>SON: '.numtoletras(number_format($tott, 2, '.', ''), $valmoneda).'</strong>
      <table width="100%" class="footer" border="0" cellspacing="0">
        <tbody>
            <tr>
                <td>
                    <img src="'.$mempresa['ruc'].'.png" width="80" height="80" />
                </td>
                <td class="fg fg2" width="20%">
                    <table width="100%" border="0" cellspacing="0" class="total">
                        <tbody>
                            <tr>
                                <td class="total2" width="50%"><strong>SUB.TOTAL:</strong></td>
                                <td><strong>'.number_format($subt, 2).'</strong></td>
                            </tr>
                            <tr>
                                <td class="total2"><strong>IGV(18%):</strong></td>
                                <td><strong>'.number_format($igvt, 2).'</strong></td>
                            </tr>
                            <tr>
                                <td class="total2"><strong>TOTAL:</strong></td>
                                <td><strong>'.number_format($tott, 2).'</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
      </table>
   </body> 
</html>';

// Configurar DomPDF con opciones que eviten el error
$options = new Options();
$options->set('isPhpEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');
$options->set('chroot', realpath('.')); // Añadir chroot para seguridad

$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'portrait');

// Limpiar y cargar el HTML
$clean_html = preg_replace('/\s+/', ' ', $html);
$clean_html = str_replace('> <', '><', $clean_html);

$dompdf->loadHtml($clean_html, 'UTF-8');

try {
    $dompdf->render();
    
    if (ob_get_length()) { 
        ob_end_clean(); 
    }
    
    // Limpiar el archivo QR temporal
    if (file_exists($mempresa['ruc'].'.png')) {
        unlink($mempresa['ruc'].'.png');
    }
    
    header("Content-type: application/pdf");
    header("Content-Disposition: inline; filename=\"orden_carga_".$mostrar['serie'].".pdf\"");
    header("Cache-Control: private, max-age=0, must-revalidate");
    
    echo $dompdf->output();
    
} catch (Exception $e) {
    // Limpiar el archivo QR temporal en caso de error
    if (file_exists($mempresa['ruc'].'.png')) {
        unlink($mempresa['ruc'].'.png');
    }
    echo "Error al generar PDF: " . $e->getMessage();
}
?>
