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
            <h5 style="border-bottom: 2px solid #ddd; padding-bottom: 5px; margin-bottom: 15px;">REPORTE IEPND POR FECHA</h5>

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

                <button type="button" class="btn btn-primary btn-sm btn-block" style="margin-top: 10px;" onclick="iepndporfecha()" >
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
            <th>Sala</th>
            <th>Código Tarifa</th>
            <th>Tarifa</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Importe</th>
            <th>Serie</th>
            <th>Correlativo</th>
            <th>Producto</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <!-- Los datos se llenan automáticamente con DataTable -->
    </tbody>
    <tfoot>
        <tr class="table-secondary">
            <th colspan="3" class="text-end">TOTALES GENERALES:</th>
            <th class="text-end"><strong id="totalPrecio">S/ 0.00</strong></th>
            <th class="text-end"><strong id="totalCantidad">0</strong></th>
            <th class="text-end"></th>
            <th class="text-end"><strong id="totalImporte">S/ 0.00</strong></th>
            <th colspan="4"></th>
        </tr>
        <tr class="table-info">
            <th colspan="6" class="text-end">TOTAL RENTA (IGV):</th>
            <th class="text-end"><strong id="totalRenta">S/ 0.00</strong></th>
            <th colspan="4"></th>
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

<script defer src="<?php echo BASE_URL; ?>assets/js/reportes.js?v=2"></script>

<script >
function init(){
cargarReporteSalas();
}

</script>