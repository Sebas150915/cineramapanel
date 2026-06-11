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

if(isset($_GET['nivel'])){
$nivel=$_GET['nivel'];
}else{
$nivel='1';	
}
$sqlnn=ejecutarConsulta("SELECT *FROM venta WHERE idventa='$idventa'");
$mostrar= mysqli_fetch_array($sqlnn);

$sqlnn=ejecutarConsulta("SELECT *FROM venta_pasajes WHERE idventa='$idventa'");
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

if($mempresa['tipo']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

if($mostrar['txtID_MONEDA']=='PEN'){ $valmoneda='SOLES'; }
if($mostrar['txtID_MONEDA']=='USD'){ $valmoneda='DOLARES'; }
if($mostrar['txtID_MONEDA']=='EUR'){ $valmoneda='EUROS'; }

$tdocumento='RECIBO';
		
if($mostrar['txtID_TIPO_DOCUMENTO']=='03'){ $tdocumento='BOLETA DE VENTA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }
if($mostrar['txtID_TIPO_DOCUMENTO']=='90'){ $tdocumento='RECIBO'; }

$ruta="../../api_cpe/".$tipop."/".$mempresa['ruc']."/";
$fichero=$mempresa['ruc'].'-'.$mostrar['txtID_TIPO_DOCUMENTO'].'-'.$mostrar['txtSERIE'].'-'.$mostrar['txtNUMERO'];
		
$text=$mempresa['ruc'].' | '.$tdocumento.' | '.$mostrar['txtSERIE'].' | '.$mostrar['txtNUMERO'].' | '.$mostrar['txtIGV'].' | '.$mostrar['txtTOTAL'].' | '.date("Y-m-d", strtotime($mostrar['txtFECHA_DOCUMENTO'])).' | '.$mcliente['tipo_documento'].' | '.$mcliente['txtID_CLIENTE'].' |';
		
$rutaqr=$mempresa['ruc'].".png";
QRcode::png($text, $rutaqr, 'Q', 15, 0);

$cont='


<style type="text/css">
@page {
    margin-top: 0.3em;
    margin-bottom: 0.3em;
    margin-left: 1em;
    margin-right: 0.3em;
    size: auto; /* Cambiar a auto para que se ajuste al contenido */
}

.cuerpo { float: left; width: 100%; font-size: 12px; font-family: Arial; }
.tabla { font-size: 18px; width: 400px; border-collapse: collapse; }
.centrado { text-align: center; }
.negrita { font-weight: bold; }
.logo { max-width: inherit; width: 100%; height: auto; }
H3 { font-size: 24px; margin:0px; padding: 0px; }
body {
    font: 20px Arial, Tahoma, Verdana, Helvetica, sans-serif;
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








<table style="margin:0 auto;width:100%;font-family:arial" align="center" cellpadding="0" cellspacing="0" border="0">
            <tbody><tr>
                <td style="width:100%;height:auto;padding:0 50px;line-height:0;text-align:center">
<img src="'.BASE_URL_PRINCIPAL.'/files/logo/'.$config['id'].'.png" style="margin:0 auto;border:0;max-width:650px" alt="" class="CToWUd" data-bit="iit">
                    <hr style="width:100%;padding:2.5px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>
            
            <tr>
                
                <td style="width:100%;height:auto;padding:10px 50px;text-align:center">
                    
                    
                    
                    
                    
                    
                    
                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:10px 50px;text-align:center">
                    <p style="margin:0px;font-size:15px;text-align:center;color:#ed3968;font-weight:bold"></p>
                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:10px 50px;text-align:center">
                    <p style="margin:0px;font-size:38px;text-align:center;color:#0d1f40;font-weight:bold">Alien: Romulus</p>
                    <p style="font-size:20px;text-align:center;color:#05589e;margin-bottom:0px">Nro. de Compra:
                        <b>WMGMRMM</b></p>
                    <img alt="" style="width:230px">
                    <p style="margin:0px;font-size:20px;text-align:center;color:#05589e;font-weight:bold">DOBLADA</p>
                    <hr style="width:100%;padding:2.5px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>


            <tr>
                <td style="width:100%;height:auto;padding:0px 50px;text-align:center;letter-spacing:-5px">

                    <div style="display:inline-block;width:10%;letter-spacing:normal"><img src="tiempo-rojo.jpg" alt="" width="35" height="39" class="CToWUd" data-bit="iit">
                    </div>
                    <div style="display:inline-block;width:90%;min-width:300px;letter-spacing:normal;font-size:12px;color:#ed3968">
                        Muestra el código QR desde tu celular para canjear tus combos e ingresar a la sala. No necesitas
                        pasar por
                        Boletería ni imprimir este documento
                    </div>
                    <hr style="width:100%;padding:10px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>
            <tr>
                <td style="width:100%;height:auto;padding:5px 50px;text-align:center">
 
                    <p style="color:#05589e;font-size:20px;line-height:1.2;text-align:center;margin:5px 0">
                        Cliente:
                        <b>Paloma Zambrano</b></p>

                    <div style="text-align:center;font-size:11px;color:#05589e;letter-spacing:-5px">
                        <div style="width:22%;display:inline-block;min-width:100px"><img src="maps.jpg" class="CToWUd" data-bit="iit">
                            <p style="letter-spacing:normal;color:#05589e">CP Santa Clara</p>
                        </div>
                        <div style="width:22%;display:inline-block;min-width:100px"><img src="calendario.jpg" class="CToWUd" data-bit="iit">
                            <p style="letter-spacing:normal;color:#05589e">30/08/2024</p>
                        </div>
                        
                        <div style="width:22%;display:inline-block;min-width:100px"><img src="timpo.jpg" class="CToWUd" data-bit="iit">
                            <p style="letter-spacing:normal;color:#05589e">07:30 PM</p>
                        </div>
                        <div style="width:22%;display:inline-block;min-width:100px"><img src="salas.jpg" class="CToWUd" data-bit="iit">
                            <p style="letter-spacing:normal;color:#05589e">SALA 1</p>
                        </div>

                    </div>


                </td>
            </tr>
            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">
                    <hr style="width:100%;padding:10px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">

                    <table style="margin:0px;text-align:left;font-size:16px;color:#05589e;width:50%">
                        <tbody><tr>
                            <td style="width:10%"><img src="asiento.jpg" style="vertical-align:middle" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td style="width:50%">Tus Butacas: <b>M12, M11, M10</b></td>
                        </tr>
                    </tbody></table>
                    <hr style="width:100%;padding:2px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">
                    <table style="margin:0px;text-align:left;font-size:16px;color:#05589e;width:50%">
                        <tbody><tr>
                            <td style="width:10%"><img src="ticket.jpg" style="vertical-align:middle" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td style="width:50%"><b> Entradas</b></td>
                        </tr>
                    </tbody></table>

                    <table style="width:100%;height:auto;margin-top:10px">
                        
                            <tbody><tr style="font-size:16px;color:#05589e">
                                <td style="text-align:left;width:50%">General 2D OL</td>
                                <td style="text-align:center;font-style:italic">Cant. <span>3</span>
                                </td>
                                <td style="text-align:right"><span>S/</span>
                                    <span>54.00</span>
                                </td>
                            </tr>
                             
                        
                    </tbody></table>

                    <hr style="width:100%;padding:10px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                    <table style="width:100%;height:auto">
                        <tbody><tr style="font-size:18px;color:#05589e">
                            <td style="text-align:left"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:right;font-weight:bold">
                                <span>SubTotal: </span><span>S/</span>
                                <span>54.00</span>
                            </td>
                        </tr>
                    </tbody></table>

                    <hr style="width:100%;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">
                    <table style="margin:30px 0px 0px 0px;text-align:left;font-size:16px;color:#05589e;width:50%">
                        <tbody><tr>
                            <td style="width:9%"><img src="dulceria.jpg" style="vertical-align:middle" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td style="width:50%"><b>Dulcería</b></td>
                        </tr>
                    </tbody></table>
                    <table style="width:100%;height:auto;margin-top:10px">
                        

                            <tbody><tr style="font-size:16px;color:#05589e">
                                <td style="text-align:left;width:50%">Combo 2 Salado OL CC</td>
                                <td style="text-align:center;font-style:italic">Cant. <span>1</span></td>
                                <td style="text-align:right"><span>S/</span> <span>39.00</span></td>
                            </tr>

                            <tr style="font-size:13px;color:#9c9c9c">
                                <td style="text-align:left;width:30%">
                                    <span></span>
                                </td>
                                <td colspan="2"></td>
                            </tr>


                        
                    </tbody></table>

                    <hr style="width:100%;padding:10px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                    <table style="width:100%;height:auto">
                        <tbody><tr style="font-size:18px;color:#05589e">
                            <td style="text-align:left"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:right;font-weight:bold">
                                <span>SubTotal: </span><span>S/</span><span>39.00</span>
                            </td>
                        </tr>
                    </tbody></table>

                    <hr style="width:100%;height:1px;border:0;border-bottom:1px solid #f6f6f6">

                </td>
            </tr>

            <tr>
                <td style="width:100%;height:auto;padding:5px 50px">

                    <table style="width:100%;height:auto">
                        <tbody><tr style="font-size:18px;color:#05589e">
                            <td style="text-align:left"></td>
                            <td style="text-align:center"></td>
                            <td style="color:#ed3968;font-size:24px;line-height:0px;text-align:right">
                                <b>TOTAL: </b><b>S/</b><b>93.00</b>
                            </td>
                        </tr>
                    </tbody></table>
                    <hr style="width:100%;padding:5px 0;height:1px;border:0;border-bottom:1px solid #f6f6f6">
                </td>
            </tr>

            <tr>
                <td>
                    <table>

                        <tbody><tr>
                            
                        </tr>
                        <tr>
                            
                        </tr>

                    </tbody></table>
                </td>
            </tr>
            <tr>
                <td style="width:100%;height:auto;padding:0px 50px;text-align:center">
                    <table style="margin:0px;text-align:left;font-size:14px;color:#05589e;width:90%">
                        <tbody><tr>
                            <td colspan="2" style="font-weight:bold;font-size:20px!important;margin-bottom:10px">
                                <p style="margin-bottom:10px;margin-top:5px">Recuerda</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style="width:8%;text-align:right;margin-right:10px"><img height="25" width="30" style="margin-right:10px" src="colas.jpg" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td>
                                <p style="color:#05589e;margin:10px">¡Sin colas! Dirígete directamente a la sala o a la zona de despacho online en la Dulcería para recoger tu combo. No necesitas pasar por boletería.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:8%;text-align:right;margin-right:10px"><img height="25" width="40" style="height:35px;margin-right:10px" src="celular.jpg" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td>
                                <p style="color:#05589e;margin:10px">Muestra tu Orden de Compra directamente desde tu
                                    celular, no es
                                    necesario imprimir.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:8%;text-align:right;margin-right:10px"><img height="25" width="40" style="height:30px;margin-right:10px" src="tiempo.jpg" alt="" class="CToWUd" data-bit="iit">
                            </td>
                            <td>
                                <p style="color:#05589e;margin:10px">El horario de la función indica el inicio de
                                    proyección de
                                    publicidad y avances de los próximos estrenos. Luego de éstos, iniciará la
                                    película.</p>
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
            <tr>

                <td style="width:100%;height:auto;padding:0px 50px;text-align:left;color:#05589e;font-size:14px">

                    <b>Condiciones de compra </b>
                    <ul>
                        <li>La compra y el canje de las entradas y/o combos, solo son válidos para el mismo día de la
                            función.
                        </li>
                        <li>Si utilizaste códigos promocionales o boletos corporativos debes presentar los cupones
                            físicos en el
                            ingreso a la sala.
                        </li>
                        <li>Si utilizaste vales de consumo corporativos debes presentar los cupones físicos en la zona
                            del
                            despacho de dulcería.
                        </li>
                        <li style="color:#e40646">Esta compra no permite cambio de función, anulación y/o devolución de
                            dinero.
                        </li>
                        <li style="color:#e40646">Respetar los protocolos de prevención covid-19 en nuestras
                            instalaciones por
                            disposición del estado. Caso contrario el establecimiento se reserva el derecho de retirar a
                            la persona
                            de sala.
                        </li>
                        <li style="color:#e40646">En función a los protocolos de bioseguridad, no contamos con custodia
                            de
                            productos, por favor tomar sus precauciones.
                        </li>
                    </ul>
                      <b>Estimado Cliente </b><br>
                    Para un mejor servicio realiza los siguientes pasos:<br>
                  <ul>
                        <li>Presenta desde tu smartphone este documento con el código QR en el ingreso a salas. No tiene que pasar por boletería ni imprimirlo</li>
                        <li>Si compraste un producto en Dulcería dirígete a la
                            zona de
                            despacho para recoger tu pedido.
                        </li>
                        <li>Si solo compraste entradas, dirígete al ingreso de tu sala.
                        </li>
                        <li>Cualquier duda respecto al pago, realízala directamente con tu banco emisor.</li>
                        <li>Si deseas la boleta electrónica de tu compra ingresa a <a href="http://boletadigital.cineplanet.com.pe" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://boletadigital.cineplanet.com.pe&amp;source=gmail&amp;ust=1778704181961000&amp;usg=AOvVaw1i1p7bV2l0O7thQl8m-ucO">http://boletadigital.<wbr>cineplanet.com.pe</a> y búscala por el N° de compra o tu DNI.</li>
                    </ul>
                  <span style="font-size:9pt;color:#767171">Cineplex S.A - Av. José Larco N° 663 – Pisos 4 - 5  - Miraflores - Lima - Lima.</span><br>


                    <hr style="border-bottom:5px solid #e50246;width:100%;margin-bottom:10px;margin-top:10px">


                </td>

            </tr>


        </tbody></table>










';

if($nivel=='0'){
header("Content-type:application/pdf");
    
    $filename = "comprobante_".$mostrar['txtSERIE']."-".$mostrar['txtNUMERO'];
    $dompdf = new DOMPDF();
    // Configurar papel con tamaño personalizado más grande
    $dompdf->set_paper(array(0, 0, 220, 800), 'portrait');
    $dompdf->load_html($cont);
    $dompdf->render();
    $dompdf->stream($filename, array("Attachment"=>0));
    $pdf = $dompdf->output();
    
    file_put_contents($ruta.$fichero.'.pdf', $pdf);

}else{
	echo $cont;
}
?>