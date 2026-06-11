

function listarventas(){

    if ($.fn.DataTable.isDataTable('#ventasTable')) {
        $('#ventasTable').DataTable().destroy();
    }


    var groupColumn = 0;
    
var fecha_inicio=$('#fecha_inicio').val();
var fecha_fin=$('#fecha_fin').val();
var idlocal=$('#idlocal').val();
    
    var table = $('#ventasTable').DataTable({
        "ajax": {
            "url": "../../../assets/ajax/reportes.php?op=listarventa&fecha_inicio="+fecha_inicio+'&fecha_fin='+fecha_fin+'&idlocal='+idlocal,
            "type": "POST",
            "dataSrc": function(json) {
                var datos = json.data || json;
                return datos;
            }
        },
        "columns": [
            {"data": "fecha", "visible": false},
            {"data": "serie"},
            {"data": "tipocomp"},
            {"data": "correlativoinicial"},
            {"data": "correlativofinal"},
            {"data": "cantidad"},
            {"data": "espectador", "className": "text-end"},
            {"data": "valor_venta", "render": function(data) { return 'S/ ' + (data || 0).toFixed(2); }, "className": "text-end"},
            {"data": "iepnd", "render": function(data) { return 'S/ ' + (data || 0).toFixed(2); }, "className": "text-end"},
            {"data": "op_gravadas", "render": function(data) { return 'S/ ' + (data || 0).toFixed(2); }, "className": "text-end"},
            {"data": "igv", "render": function(data) { return 'S/ ' + (data || 0).toFixed(2); }, "className": "text-end"},
            {"data": "total", "render": function(data) { return 'S/ ' + (data || 0).toFixed(2); }, "className": "text-end fw-bold"}
        ],
        "order": [[groupColumn, 'desc']],
        "pageLength": 25,
        "language": {
            "decimal": ",",
            "thousands": ".",
            "processing": "Procesando...",
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "loadingRecords": "Cargando...",
            "zeroRecords": "No se encontraron registros",
            "emptyTable": "No hay datos disponibles",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "drawCallback": function(settings) {
            var api = this.api();
            var rows = api.rows({page: 'current'}).nodes();
            var last = null;
            
            // Calcular totales de TODA la página actual
            var totalEspectador = 0;
            var totalValorVenta = 0;
            var totalGravadas = 0;
            var totalIEPND = 0;
            var totalIGV = 0;
            var totalGeneral = 0;
            
            // Obtener datos de la página actual
            var dataActual = api.rows({page: 'current'}).data();
            
            // Calcular totales generales primero
            dataActual.each(function(row) {
                totalEspectador += parseFloat(row.espectador) || 0;
                totalValorVenta += parseFloat(row.valor_venta) || 0;
                totalGravadas += parseFloat(row.op_gravadas) || 0;
                totalIEPND += parseFloat(row.iepnd) || 0;
                totalIGV += parseFloat(row.igv) || 0;
                totalGeneral += parseFloat(row.total) || 0;
            });
            
            // Actualizar TOTALES GENERALES
            $('#totalvespectador').text(totalEspectador.toFixed(2));
            $('#totalvalorventa').text('S/ ' + totalValorVenta.toFixed(2));
            $('#totalGravadas').text('S/ ' + totalGravadas.toFixed(2));
            $('#totalIEPND').text('S/ ' + totalIEPND.toFixed(2));
            $('#totalIGV').text('S/ ' + totalIGV.toFixed(2));
            $('#totalGeneral').text('S/ ' + totalGeneral.toFixed(2));
            
            // Agrupar por fecha
            var groups = {};
            
            // Agrupar datos por fecha
            dataActual.each(function(row, index) {
                var fecha = row.fecha;
                if (!groups[fecha]) {
                    groups[fecha] = {
                        rows: [],
                        totalEspectador: 0,
                        totalValorVenta: 0,
                        totalGravadas: 0,
                        totalIEPND: 0,
                        totalIGV: 0,
                        totalGeneral: 0
                    };
                }
                groups[fecha].rows.push(index);
                groups[fecha].totalEspectador += parseFloat(row.espectador) || 0;
                groups[fecha].totalValorVenta += parseFloat(row.valor_venta) || 0;
                groups[fecha].totalGravadas += parseFloat(row.op_gravadas) || 0;
                groups[fecha].totalIEPND += parseFloat(row.iepnd) || 0;
                groups[fecha].totalIGV += parseFloat(row.igv) || 0;
                groups[fecha].totalGeneral += parseFloat(row.total) || 0;
            });
            
            // Insertar filas de grupo
            var rowPositions = [];
            for (var fecha in groups) {
                var grupo = groups[fecha];
                var firstRowIndex = grupo.rows[0];
                rowPositions.push({index: firstRowIndex, fecha: fecha, grupo: grupo});
            }
            
            // Ordenar por posición inversa para no afectar los índices
            rowPositions.sort(function(a, b) {
                return b.index - a.index;
            });
            
            // Insertar filas de grupo
            for (var i = 0; i < rowPositions.length; i++) {
                var pos = rowPositions[i];
                var groupRow = '<tr class="group" style="background-color:rgba(0,0,0,0.05);cursor:pointer;">' +
                    '<td colspan="5"><strong>📅 Fecha: ' + pos.fecha + '</strong></td>' +
                    '<td class="text-end"><strong>' + pos.grupo.totalEspectador.toFixed(2) + '</strong></td>' +
                    '<td class="text-end"><strong>S/ ' + pos.grupo.totalValorVenta.toFixed(2) + '</strong></td>' +
                    '<td class="text-end"><strong>S/ ' + pos.grupo.totalIEPND.toFixed(2) + '</strong></td>' +
                    '<td class="text-end"><strong>S/ ' + pos.grupo.totalGravadas.toFixed(2) + '</strong></td>' +
                    '<td class="text-end"><strong>S/ ' + pos.grupo.totalIGV.toFixed(2) + '</strong></td>' +
                    '<td class="text-end"><strong>S/ ' + pos.grupo.totalGeneral.toFixed(2) + '</strong></td>' +
                    '</tr>';
                
                $(rows).eq(pos.index).before(groupRow);
            }
        }
    });
    
    $('#ventasTable tbody').on('click', 'tr.group', function() {
        var currentOrder = table.order()[0];
        if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
            table.order([groupColumn, 'desc']).draw();
        } else {
            table.order([groupColumn, 'asc']).draw();
        }
    });

}



function resumendiario(){

if ($.fn.DataTable.isDataTable('#ventasTable')) {
        $('#ventasTable').DataTable().destroy();
}

var groupColumn = 0;
    
var fecha_inicio=$('#fecha_inicio').val();
var fecha_fin=$('#fecha_fin').val();
var idlocal=$('#idlocal').val();
    
    var table = $('#ventasTable').DataTable({
        "ajax": {
            "url": "../../../assets/ajax/reportes.php?op=resumendiario&fecha_inicio="+fecha_inicio+'&fecha_fin='+fecha_fin+'&idlocal='+idlocal,
            "type": "POST",
            "dataSrc": function(json) {
                var datos = json.data || json;
                return datos;
            }
        },
        
        "columns": [
            {"data": "sala", "visible": false},
            {"data": "fecha"},
            {"data": "hora"},
            {"data": "codigo"},
            {"data": "descripcion"},
            {"data": "capacidad"},
            {"data": "espectador", "className": "text-end"},
            {"data": "porcentaje", "render": function(data) { return (data || 0).toFixed(2)+'%'; }, "className": "text-end"},
            {"data": "total", "render": function(data) { return 'S/ ' + (data || 0).toFixed(2); }, "className": "text-end"},
        ],
        "order": [[groupColumn, 'desc']],
        "pageLength": 25,
        "language": {
            "decimal": ",",
            "thousands": ".",
            "processing": "Procesando...",
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "loadingRecords": "Cargando...",
            "zeroRecords": "No se encontraron registros",
            "emptyTable": "No hay datos disponibles",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "drawCallback": function(settings) {
        	
        	
        	/**/
            var api = this.api();
            var rows = api.rows({page: 'current'}).nodes();
            var last = null;
            
            // Calcular totales de TODA la página actual
            var totalbutacas = 0;
            var totalespectador = 0;
            var totaltotal = 0;

            // Obtener datos de la página actual
            var dataActual = api.rows({page: 'current'}).data();
            
            // Calcular totales generales primero
            dataActual.each(function(row) {
                totalbutacas += parseFloat(row.capacidad) || 0;
                totalespectador += parseFloat(row.espectador) || 0;
                totaltotal += parseFloat(row.total) || 0;
            });
            
            // Actualizar TOTALES GENERALES
            $('#totalvespectador').text(totalbutacas.toFixed(2));
            $('#totalGravadas').text('' + totalespectador.toFixed(2));
            $('#totalGeneral').text('S/ ' + totaltotal.toFixed(2));
            
            // Agrupar por fecha
            var groups = {};
            
            // Agrupar datos por fecha
            dataActual.each(function(row, index) {
                var sala = row.sala;
                if (!groups[sala]) {
                    groups[sala] = {
                        rows: [],
                        totalbutacas: 0,
                        totalespectador: 0,
                        totaltotal: 0,
                    };
                }
                groups[sala].rows.push(index);
                groups[sala].totalbutacas += parseFloat(row.capacidad) || 0;
                groups[sala].totalespectador += parseFloat(row.espectador) || 0;
                groups[sala].totaltotal += parseFloat(row.total) || 0;
            });
            
            // Insertar filas de grupo
            var rowPositions = [];
            for (var sala in groups) {
                var grupo = groups[sala];
                var firstRowIndex = grupo.rows[0];
                rowPositions.push({index: firstRowIndex, sala: sala, grupo: grupo});
            }
            
            // Ordenar por posición inversa para no afectar los índices
            rowPositions.sort(function(a, b) {
                return b.index - a.index;
            });
            
            // Insertar filas de grupo
            for (var i = 0; i < rowPositions.length; i++) {
                var pos = rowPositions[i];
                var groupRow = '<tr class="group" style="background-color:rgba(0,0,0,0.05);cursor:pointer;">' +
                    '<td colspan="4"><strong>Sala: ' + pos.sala + '</strong></td>' +
                    '<td class="text-end"><strong> ' + pos.grupo.totalbutacas.toFixed(2) + '</strong></td>' +
                    '<td class="text-end"><strong> ' + pos.grupo.totalespectador.toFixed(2) + '</strong></td>' +
                    '<td class="text-end"><strong></strong></td>' +
                    '<td class="text-end"><strong>S/ ' + pos.grupo.totaltotal.toFixed(2) + '</strong></td>' +
                    '</tr>';
                
                $(rows).eq(pos.index).before(groupRow);
            }
            
            
            
            
        }
    });
    

}





function ventavariable(){

   if ($.fn.DataTable.isDataTable('#ventasTable')) {
        $('#ventasTable').DataTable().destroy();
    }


    var groupColumn = 0;
    
var anio=$('#anio').val();
var mes=$('#mes').val();
var idlocal=$('#idlocal').val();
    
    var table = $('#ventasTable').DataTable({
        "ajax": {
            "url": "../../../assets/ajax/reportes.php?op=ventavariable&anio="+anio+'&mes='+mes+'&idlocal='+idlocal,
            "type": "POST",
            "dataSrc": function(json) {
                var datos = json.data || json;
                return datos;
            }
        },
        
        "columns": [
            {"data": "fecha"},
            
            {"data": "espectador", "className": "text-end"},
            {"data": "totalbruto", "className": "text-end"},
            {"data": "totalsinigv", "className": "text-end"},
            {"data": "totalsiniepnd", "className": "text-end"},
            {"data": "renta", "className": "text-end"},

            {"data": "capacidad", "className": "text-end"},
            {"data": "porcentaje", "className": "text-end"},
            {"data": "rentaejecutivo", "className": "text-end"},

            {"data": "totalventachoco", "className": "text-end"},
            {"data": "totalsinigvchoco", "className": "text-end"},
            {"data": "rentachoco", "className": "text-end"},
   
            {"data": "totalventajuegos", "className": "text-end"},
            {"data": "totalsinigvjuegos", "className": "text-end"},
            {"data": "rentajuegos", "className": "text-end"},
            
            {"data": "total", "className": "text-end"},

        ],
        "order": [[0, 'asc']],
        "paging": false,
        scrollCollapse: true, scrollY: '600px',
        "language": {
            "decimal": ",",
            "thousands": ".",
            "processing": "Procesando...",
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "loadingRecords": "Cargando...",
            "zeroRecords": "No se encontraron registros",
            "emptyTable": "No hay datos disponibles",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
"drawCallback": function(settings) {
	
	
        // Obtener todos los datos de la tabla (paginados o no)
        var api = this.api();
        var data = api.rows().data();
        
        // Variables para los totales
        var totalEspectador = 0;
        var total_bruto=0;
        var totalGravadas = 0;
        var totalGeneral = 0;
        var total_sin_iepnd=0;
        var total_renta=0;
        
        // Recorrer los datos y sumar
        for (var i = 0; i < data.length; i++) {
            // Columna espectador (índice 1)
            totalEspectador += parseFloat(data[i].espectador) || 0;
            total_bruto += parseFloat(data[i].totalbruto) || 0;
            // Columna totalsinigv (índice 3) - Para totalGravadas
            totalGravadas += parseFloat(data[i].totalsinigv) || 0;
            total_sin_iepnd+= parseFloat(data[i].totalsiniepnd) || 0;
            total_renta+=parseFloat(data[i].renta) || 0;
            
            // Columna total (última columna, índice 15)
            totalGeneral += parseFloat(data[i].total) || 0;
        }
        
        // Formatear los totales
        totalEspectador = totalEspectador.toFixed(2);
        total_bruto = total_bruto.toFixed(2);
        totalGravadas = totalGravadas.toFixed(2);
        total_sin_iepnd=total_sin_iepnd.toFixed(2);
        total_renta=total_renta.toFixed(2);
        
        totalGeneral = totalGeneral.toFixed(2);
        
        // Actualizar los elementos en el pie de tabla
        $('#totalvespectador').text(totalEspectador);
        $('#total_bruto').text('S/ ' + total_bruto);
        $('#totalGravadas').text('S/ ' + totalGravadas);
        $('#total_sin_iepnd').text('S/ ' + total_sin_iepnd);
        
        $('#totalGeneral').text('S/ ' + totalGeneral);
	
	
}
    });
    

}



function ventavariableacumulada(){
	
	
   if ($.fn.DataTable.isDataTable('#ventasTable')) {
        $('#ventasTable').DataTable().destroy();
    }

	
var groupColumn = 0;
    
var fecha_inicio=$('#fecha_inicio').val();
var fecha_fin=$('#fecha_fin').val();
var idlocal=$('#idlocal').val();
    
    var table = $('#ventasTable').DataTable({
        "ajax": {
            "url": "../../../assets/ajax/reportes.php?op=ventavariableacumulada&fecha_inicio="+fecha_inicio+'&fecha_fin='+fecha_fin+'&idlocal='+idlocal,
            "type": "POST",
            "dataSrc": function(json) {
                var datos = json.data || json;
                return datos;
            }
        },

        
        "columns": [
            {"data": "concepto"},
            
            {"data": "cine", "className": "text-end"},
            {"data": "corporativo", "className": "text-end"},
            {"data": "chocolateria", "className": "text-end"},
            {"data": "juegos", "className": "text-end"},
            {"data": "total", "className": "text-end"},

        ],
        "order": [[0, 'asc']],
        "paging": false,
        scrollCollapse: true, scrollY: '600px',
        "language": {
            "decimal": ",",
            "thousands": ".",
            "processing": "Procesando...",
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "loadingRecords": "Cargando...",
            "zeroRecords": "No se encontraron registros",
            "emptyTable": "No hay datos disponibles",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
    });

}






function paloteo(){

    if ($.fn.DataTable.isDataTable('#ventasTable')) {
        $('#ventasTable').DataTable().destroy();
    }


    var groupColumn = 0;
    
var fecha_inicio=$('#fecha_inicio').val();
var fecha_fin=$('#fecha_fin').val();
var idlocal=$('#idlocal').val();
    
    var table = $('#ventasTable').DataTable({
        "ajax": {
            "url": "../../../assets/ajax/reportes.php?op=paloteo&fecha_inicio="+fecha_inicio+'&fecha_fin='+fecha_fin+'&idlocal='+idlocal,
            "type": "POST",
            "dataSrc": function(json) {
                var datos = json.data || json;
                return datos;
            }
        },
        
        "columns": [
            {"data": "categoria", "visible": false},
            {"data": "id"},
            {"data": "nombre"},
            {"data": "unidad"},
            {"data": "cantidad", "className": "text-end"},
            {"data": "total", "className": "text-end"}
        ],
        "order": [[groupColumn, 'desc']],
        "pageLength": 25,
        "language": {
            "decimal": ",",
            "thousands": ".",
            "processing": "Procesando...",
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "loadingRecords": "Cargando...",
            "zeroRecords": "No se encontraron registros",
            "emptyTable": "No hay datos disponibles",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "drawCallback": function(settings) {
        	
        	
        	/**/
            var api = this.api();
            var rows = api.rows({page: 'current'}).nodes();
            var last = null;
            
            // Calcular totales de TODA la página actual
            var cantidadtotal = 0;
            var totaltotal = 0;

            // Obtener datos de la página actual
            var dataActual = api.rows({page: 'current'}).data();
            
            // Calcular totales generales primero
            dataActual.each(function(row) {
                cantidadtotal += parseFloat(row.cantidad) || 0;
                totaltotal += parseFloat(row.total) || 0;
            });
            
            // Actualizar TOTALES GENERALES
            $('#totalGravadas').text('' + cantidadtotal.toFixed(2));
            $('#totalGeneral').text('S/ ' + totaltotal.toFixed(2));
            
            // Agrupar por fecha
            var groups = {};
            
            // Agrupar datos por fecha
            dataActual.each(function(row, index) {
                var categoria = row.categoria;
                if (!groups[categoria]) {
                    groups[categoria] = {
                        rows: [],
                        cantidadtotal: 0,
                        totaltotal: 0,
                    };
                }
                groups[categoria].rows.push(index);
                groups[categoria].cantidadtotal += parseFloat(row.cantidad) || 0;
                groups[categoria].totaltotal += parseFloat(row.total) || 0;
            });
            
            // Insertar filas de grupo
            var rowPositions = [];
            for (var categoria in groups) {
                var grupo = groups[categoria];
                var firstRowIndex = grupo.rows[0];
                rowPositions.push({index: firstRowIndex, categoria: categoria, grupo: grupo});
            }
            
            // Ordenar por posición inversa para no afectar los índices
            rowPositions.sort(function(a, b) {
                return b.index - a.index;
            });
            
            // Insertar filas de grupo
            for (var i = 0; i < rowPositions.length; i++) {
                var pos = rowPositions[i];
                var groupRow = '<tr class="group" style="background-color:rgba(0,0,0,0.05);cursor:pointer;">' +
                    '<td colspan="3"><strong>Familia: ' + pos.categoria + '</strong></td>' +
                    '<td class="text-end"><strong> ' + pos.grupo.cantidadtotal.toFixed(2) + '</strong></td>' +
                    '<td class="text-end"><strong>S/ ' + pos.grupo.totaltotal.toFixed(2) + '</strong></td>' +
                    '</tr>';
                
                $(rows).eq(pos.index).before(groupRow);
            }
            
            
            
            
        }
    });
    

}







function cargarReporteSalas() {
    // Destruir tabla si ya existe
    if ($.fn.DataTable.isDataTable('#ventasTable')) {
        $('#ventasTable').DataTable().destroy();
    }

    // Obtener valores de filtros (si los necesitas)
    var anio = $('#anio').val();
    var mes = $('#mes').val();
    var idlocal = $('#idlocal').val();
    var codsala = $('#codsala').val(); // Si quieres filtrar por sala específica

    var table = $('#ventasTable').DataTable({
        "ajax": {
            "url": "../../../assets/ajax/reportes.php?op=iepndporfecha&anio=" + anio + '&mes=' + mes + '&idlocal=' + idlocal + '&codsala=' + codsala,
            "type": "POST",
            "dataSrc": function(json) {
                var datos = json.data || json;
                return datos;
            }
        },

        "columns": [
            { "data": "codsala", "title": "ID Sala" },
            { "data": "nombre_sala", "title": "Sala" },
            { "data": "codtarifa", "title": "Cod Tarifa" },
            { "data": "nombre_tarifa", "title": "Tarifa" },
            { "data": "precio_tarifa", "title": "Precio", "className": "text-end" },
            { "data": "cantidad", "title": "Cantidad", "className": "text-end" },
            { "data": "precio_unitario", "title": "P. Unitario", "className": "text-end" },
            { "data": "importe_total", "title": "Importe", "className": "text-end" },
            { "data": "serie", "title": "Serie" },
            { "data": "correlativo", "title": "Correlativo" },
            { "data": "fecha_emision", "title": "Fecha Emisión" }
        ],

        "order": [[0, 'asc'], [9, 'asc']],
        "paging": true,
        "pageLength": 25,
        "scrollY": "500px",
        "scrollCollapse": true,
        "language": {
            "decimal": ",",
            "thousands": ".",
            "processing": "Procesando...",
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "loadingRecords": "Cargando...",
            "zeroRecords": "No se encontraron registros",
            "emptyTable": "No hay datos disponibles",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },

        "drawCallback": function(settings) {
            var api = this.api();
            var data = api.rows().data();

            // Variables para totales
            var totalPrecio = 0;
            var totalCantidad = 0;
            var totalImporte = 0;

            // Sumar totales
            for (var i = 0; i < data.length; i++) {
                totalPrecio += parseFloat(data[i].precio_tarifa) || 0;
                totalCantidad += parseFloat(data[i].cantidad) || 0;
                totalImporte += parseFloat(data[i].importe_total) || 0;
            }

            // Formatear
            totalPrecio = totalPrecio.toFixed(2);
            totalCantidad = totalCantidad.toFixed(0);
            totalImporte = totalImporte.toFixed(2);

            // Mostrar totales en el pie de página (debes tener estos elementos HTML)
            $('#totalPrecio').text('S/ ' + totalPrecio);
            $('#totalCantidad').text(totalCantidad);
            $('#totalImporteGeneral').text('S/ ' + totalImporte);
        }
    });
}























init();
