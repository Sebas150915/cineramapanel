<?php
//require_once("dompdf/dompdf_config.inc.php");
$rutat = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$rutat = str_replace("plugins/dompdf/index.php", "", $rutat);

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

$resumen = new Resumen();

$id = $_GET['id'];
$idlocal = $_GET['idlocal'];

$sql = "SELECT * FROM venta_orden WHERE id='$id' ";
$mostrar = ejecutarConsultaSimpleFila($sql);

$sql3 = "SELECT * FROM config WHERE id='$mostrar[idempresa]' ";
$mempresa = ejecutarConsultaSimpleFila($sql3);

$sqll = "SELECT * FROM sucursal WHERE id='$idlocal' ";
$local = ejecutarConsultaSimpleFila($sqll);

$sqlchofer = "SELECT * FROM guia_chofer WHERE id='$mostrar[idresponsable]' ";
$chofer = ejecutarConsultaSimpleFila($sqlchofer);

if($mempresa['tipo'] == '03') { 
    $tipop = 'BETA'; 
} else { 
    $tipop = 'PRODUCCION'; 
}

$ruta = "../../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero = 'D-'.$mempresa['ruc'].'-'.$mostrar['serie'];

$valmoneda = 'SOLES';
$mf = 'S/';
$mon = isset($mostrar['txtID_MONEDA']) ? $mostrar['txtID_MONEDA'] : (isset($mostrar['moneda']) ? $mostrar['moneda'] : 'PEN');
if($mon == 'PEN'){ $valmoneda = 'SOLES'; $mf = 'S/'; }
if($mon == 'USD'){ $valmoneda = 'DOLARES AMERICANOS'; $mf = 'USD$'; }
if($mon == 'EUR'){ $valmoneda = 'EUROS'; $mf = '€'; }

$tdocumento = 'RECEPCION DE ENCOMIENDA';

// Definir RUTA si no está definida
if(!defined('RUTA')) {
    define('RUTA', $rutat);
}

$html = '
<html> 
   <head> 
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <style> 
        @page { 
            size: legal; 
            margin: 10mm; 
        }
        body {
            font: 14px Arial, Tahoma, Verdana, Helvetica, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .cabecera table {
            width: 100%;
            color: black;
            margin-top: 0em;
            text-align: left; 
            font-size: 18px;
            border-collapse: collapse;
        }
        .cabecera h1 {
            font-size: 22px; 
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
            font-size: 22px; 
            padding: 5px;
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
            font-size: 13px;
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
                     <img src="../../files/logo/'.$mempresa['id'].'.png" style="max-width: 200px; max-height: 50px; margin-bottom: -10px; " />
                </td>
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
                <td width="20%"><strong>SUCURSAL:</strong> '.$local['sucursal'].'</td>
                <td width="20%">FECHA: '.date("Y-m-d", strtotime($mostrar['fecha'])).'</td>
                <td width="60%">RESPONSABLE: '.trim((isset($chofer['apellido'])?$chofer['apellido']:'').' '.(isset($chofer['nombre'])?$chofer['nombre']:'')).'</td>
            </tr>
            <tr>
                <td colspan="3">OBSERVACIONES: '.$mostrar['observaciones'].'</td>
            </tr>
        </thead>
      </table>

      <table width="100%" class="cuerpo2" border="0" cellspacing="0">
        <thead> 
            <tr>
                <td width="10%">TIPO/DOC</td>
                <td width="13%">SERIE</td>
                <td width="13%">DNI</td>
                <td width="45%">DESCRIPCION</td>
                <td width="7%" align="center">U/MED</td>
                <td width="7%" align="center">TOTAL</td>
                <td width="12%" align="center">ESTADO</td>
            </tr>
        </thead>
        <tbody>';

$datos = "SELECT * FROM venta_opdetallet WHERE idpedido='$id' ORDER BY id DESC";
$sql = ejecutarConsulta($datos);
while ($rowd = mysqli_fetch_array($sql)) {
    $nombre = isset($rowd['unidadmedida']) ? $rowd['unidadmedida'] : 'UNIDAD';
    
    $estado = 'EN RECEPCION';
    if($rowd['estado'] == 0){ 
        $estado = 'PENDIENTE'; 
    }
    
    $html .= '
            <tr>
                <td>'.$rowd['tipodoc'].'</td>
                <td>'.$rowd['serie'].'</td>
                <td>'.$rowd['dni'].'</td>
                <td>'.$rowd['nombreproducto'].'</td>
                <td>'.$nombre.'</td>
                <td align="center">'.$rowd['importe'].'</td>
                <td align="center">'.$estado.'</td>
            </tr>';
}

$html .= '
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
$options->set('chroot', realpath('.'));

$dompdf = new Dompdf($options);
$dompdf->setPaper('legal', 'portrait');

// Limpiar y cargar el HTML
$clean_html = preg_replace('/\s+/', ' ', $html);
$clean_html = str_replace('> <', '><', $clean_html);

$dompdf->loadHtml($clean_html, 'UTF-8');

try {
    $dompdf->render();
    
    // Limpiar buffer de salida
    if (ob_get_length()) { 
        ob_end_clean(); 
    }
    
    header("Content-type: application/pdf");
    header("Content-Disposition: inline; filename=\"recepcion_encomienda_".$mostrar['serie'].".pdf\"");
    header("Cache-Control: private, max-age=0, must-revalidate");
    
    echo $dompdf->output();
    
} catch (Exception $e) {
    // Limpiar buffer en caso de error
    if (ob_get_length()) { 
        ob_end_clean(); 
    }
    echo "Error al generar PDF: " . $e->getMessage();
}
?>