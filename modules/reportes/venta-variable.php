<?php
require_once '../../config/config.php';

$page_title = "Reporte de ventas";
include '../../includes/header.php';
include '../../includes/sidebar.php';

$aniosistema=date("Y");

try {
    $stmtDist = $db->query("SELECT id, nombre FROM tbl_locales ORDER BY nombre");
    $distribuidoras = $stmtDist->fetchAll();
} catch (PDOException $e) {
    $distribuidoras = [];
}


?>

<main class="admin-content" style="padding: 10px;">
    <!-- Layout Container -->
    <div class="layout-wrapper" style="display: flex; height: calc(100vh - 80px); gap: 10px;">

        <!-- Sidebar Filters -->
        <div class="card sidebar-filters" style="width: 280px; padding: 15px; height: 100%; overflow-y: auto; flex-shrink: 0;">
            <h5 style="border-bottom: 2px solid #ddd; padding-bottom: 5px; margin-bottom: 15px;">REPORTE VENTA VARIABLE</h5>

            <form method="GET" action="">
                <div class="form-group">
                    <label style="font-weight: bold; font-size: 0.9em;">Año</label>
<select class="form-control form-select" name="anio" id="anio">
<?php
for($i=2025;$i<=$aniosistema;$i++) {
if($aniosistema==$i){ 
echo "<option value='".$i."' selected >".$i."</option>"; 
}else{
echo "<option value='".$i."'>".$i."</option>"; 
}
}

?>
</select>
                </div>

                <div class="form-group">
                    <label style="font-weight: bold; font-size: 0.9em;">Mes</label>
 <select name="mes" class="form-control form-select" id="mes"  >
        <?php
        for ($i=1; $i<=12; $i++) {
			
			
$n = str_pad($i, 2, "0", STR_PAD_LEFT);		
			
            if ($n == date('m')){

                echo '<option value="'.$n.'" selected>'.$n.'</option>';
            }else{
                echo '<option value="'.$n.'">'.$n.'</option>';
			}
        }
        ?>
</select>
                </div>

                <div class="form-group">
                    <label style="font-weight: bold; font-size: 0.9em;">Moneda</label>
                    <select class="form-control form-select form-control-sm" disabled>
                        <option>SOLES</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="font-weight: bold; font-size: 0.9em;">Sucursal</label>
                    <div style="display: flex; gap: 5px;">
                        <select name="idlocal" id="idlocal" class="form-control form-control-sm form-select">
                            <option value="0">-- Todas --</option>
                            <?php foreach ($distribuidoras as $dist): ?>
                                <option value="<?php echo $dist['id']; ?>" <?php echo ($id_distribuidora == $dist['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dist['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-sm btn-block" style="margin-top: 10px;" onclick="ventavariable()" >
                    <i class="fas fa-sync"></i> Procesar
                </button>
                <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm btn-block" style="margin-top: 5px;">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </form>
        </div>

        <!-- Main Report Area -->
        <div class="card report-container" style="flex-grow: 1; padding: 0; overflow: hidden; display: flex; flex-direction: column;">
        	
        	
        	
        	
   <div style=" padding: 20px;">     	
        
  <table id="ventasTable" class="table table-striped table-bordered">
<thead class="table-dark">
    <tr>
        <th rowspan="2" >Fecha</th>
        <th colspan="5">CINE</th>
        <th colspan="3">CORPORATIVO</th>
<th colspan="3">CHICOLATERIA</th>
<th colspan="3">JUEGOS</th>
<th rowspan="2" >Total Renta</th>
    </tr>
    <tr>
        <!-- cINE -->
        <th>Esp</th>
        <th>Bruto</th>
        <th>Sin IGV</th>
        <th>Sin IEPND</th>
        <th>Renta</th>
        <!-- corporativo -->
        <th>Bruto</th>
        <th>Sin IGV</th>
        <th>Renta</th>
        <!-- chocolateria -->
        <th>Bruto</th>
        <th>Sin IGV</th>
        <th>Renta</th>
         <!-- juegos -->
        <th>Bruto</th>
        <th>Sin IGV</th>
        <th>Renta</th>


    </tr>
</thead>
    <tbody></tbody>
<tfoot>
    <tr>
        <td><strong></strong></td>
        <td class="text-end"><strong id="totalvespectador">0.00</strong></td>
        <td class="text-end"><strong id="total_bruto">S/ 0.00</strong></td>
        <td class="text-end"><strong id="totalGravadas">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_sin_iepnd">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_renta">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_capacidad">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_porcentaje">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_renta_ejecutivo">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_choco_bruto">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_choco_sinigv">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_choco_renta">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_juegos_bruto">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_juegos_sinigv">S/ 0.00</strong></td>
        <td class="text-end"><strong id="total_juegos_renta">S/ 0.00</strong></td>
        <td class="text-end"><strong id="totalGeneral">S/ 0.00</strong></td>
    </tr>
</tfoot>
</table>
        	
</div>	
        	

        </div>
    </div>
</main>


<?php
include '../../includes/footer.php';
?>

<script defer src="<?php echo BASE_URL; ?>assets/js/reportes.js?v=6"></script>

<script >
function init(){
ventavariable();
}

</script>