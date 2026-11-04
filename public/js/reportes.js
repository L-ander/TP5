$(document).ready(function() {
    
    // Cargar listas desplegables
    $.post('app/controllers/ReportesAjaxController.php', { action: 'cargarFiltros' }, function(res) {
        if(res.success) {
            let p = res.data;
            $('#id_cliente').append(p.clientes.map(c => `<option value="${c.id}">${c.nombre} ${c.apellido}</option>`));
            $('#id_vendedor').append(p.vendedores.map(v => `<option value="${v.id}">${v.nombre} ${v.apellido}</option>`));
            $('#id_producto').append(p.productos.map(pr => `<option value="${pr.id}">${pr.nombre}</option>`));
            if($.fn.selectpicker) $('.show-tick').selectpicker('refresh');
        }
    }, 'json');

    // Desactivar filtro de producto si el reporte es agrupado (Vendedor/Fecha)
    $('#tipo').change(function() {
        let esAgrupado = ['vendedor', 'fecha'].includes($(this).val());
        $('#id_producto').prop('disabled', esAgrupado);
        if(esAgrupado) $('#id_producto').val('');
        if($.fn.selectpicker) $('#id_producto').selectpicker('refresh');
    });

    // Acción BUSCAR
    $('#btnBuscar').click(function() {
        let datos = $('#formReportes').serialize() + '&action=generar';
        let tipo = $('#tipo').val();
        
        // Renderizar Cabeceras
        let cabecera = '';
        if (tipo === 'cliente') cabecera = '<tr><th>CLIENTE</th><th>FACTURA</th><th>FECHA</th><th>PRODUCTO</th><th>VENDEDOR</th><th>NETO</th></tr>';
        else if (tipo === 'articulo') cabecera = '<tr><th>ARTÍCULO</th><th>FACTURA</th><th>FECHA</th><th>VENDEDOR</th><th>CLIENTE</th><th>CANTIDAD</th><th>UNIDAD</th><th>PRECIO</th><th>NETO</th></tr>';
        else cabecera = '<tr><th>VENDEDOR</th><th>FACTURA</th><th>FECHA</th><th>CLIENTE</th><th>NETO</th></tr>';
        
        $('#cabeceraReporte').html(cabecera);

        // Fetch Datos
        $.post('app/controllers/ReportesAjaxController.php', datos, function(res) {
            if(res.success) {
                let html = '';
                if(res.data.length === 0) {
                    html = `<tr><td colspan="10" class="text-center">No se encontraron resultados</td></tr>`;
                } else {
                    res.data.forEach(r => {
                        if (tipo === 'cliente') {
                            html += `<tr><td>${r.cliente}</td><td>${r.factura}</td><td>${r.fecha}</td><td>${r.producto}</td><td>${r.vendedor}</td><td>$${r.neto}</td></tr>`;
                        } else if (tipo === 'articulo') {
                            html += `<tr><td>${r.producto}</td><td>${r.factura}</td><td>${r.fecha}</td><td>${r.vendedor}</td><td>${r.cliente}</td><td>${r.cantidad}</td><td>${r.unidad || '-'}</td><td>$${r.precio}</td><td>$${r.neto}</td></tr>`;
                        } else {
                            // Estructura Vendedor / Fecha
                            html += `<tr><td>${r.vendedor}</td><td>${r.factura}</td><td>${r.fecha}</td><td>${r.cliente}</td><td>$${r.neto}</td></tr>`;
                        }
                    });
                }
                $('#cuerpoReporte').html(html);
            }
        }, 'json');
    });
});