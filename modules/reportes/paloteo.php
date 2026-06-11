<?php
require_once '../../config/config.php';

$page_title = "Reporte de ventas";
include '../../includes/header.php';
include '../../includes/sidebar.php';

// Filtros
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m').'-01';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$idlocal= isset($_GET['idlocal']) ? (int)$_GET['idlocal'] : 0;

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
            <h5 style="border-bottom: 2px solid #ddd; padding-bottom: 5px; margin-bottom: 15px;">REPORTE PALOTEO</h5>

            <form method="GET" action="">
                <div class="form-group">
                    <label style="font-weight: bold; font-size: 0.9em;">Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control form-control-sm" value="<?php echo $fecha_inicio; ?>">
                </div>

                <div class="form-group">
                    <label style="font-weight: bold; font-size: 0.9em;">Hasta</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control form-control-sm" value="<?php echo $fecha_fin; ?>">
                </div>

                <div class="form-group">
                    <label style="font-weight: bold; font-size: 0.9em;">Moneda</label>
                    <select class="form-control form-control-sm" disabled>
                        <option>SOLES</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="font-weight: bold; font-size: 0.9em;">Sucursal</label>
                    <div style="display: flex; gap: 5px;">
                        <select name="idlocal" id="idlocal" class="form-control form-control-sm">
                            <option value="0">-- Todas --</option>
                            <?php foreach ($distribuidoras as $dist): ?>
                                <option value="<?php echo $dist['id']; ?>" <?php echo ($id_distribuidora == $dist['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dist['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-sm btn-block" style="margin-top: 10px;" onclick="paloteo()" >
                    <i class="fas fa-sync"></i> Procesar
                </button>
                <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm btn-block" style="margin-top: 5px;">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </form>
        </div>

        <!-- Main Report Area -->
        <div class="card report-container" style="flex-grow: 1; padding: 0; overflow: hidden; display: flex; flex-direction: column;">
        	
        	
        	
        	
   <div class="table-scroll-area" style="flex-grow: 1; overflow: auto; padding: 20px;">     	
        
  <table id="ventasTable" class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
        	<th>Artículo</th>
            <th>Artículo</th>
            <th>Artículo</th>
            <th>Uni</th>
            <th>Cantidad</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody></tbody>
<tfoot>
    <tr>
    	
        <td colspan="4"><strong>TOTAL GENERAL:</strong></td>
        <td class="text-end"><strong id="totalGravadas">S/ 0.00</strong></td>
        <td class="text-end"><strong id="totalGeneral">0.00</strong></td>
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

<script defer src="<?php echo BASE_URL; ?>assets/js/reportes.js?v=5"></script>

<script >
function init(){
paloteo();
}

</script>