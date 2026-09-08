$(document).ready(function() {
    
    listarCiudadesModulo();
    cargarEstadosModulo();
    $('#btnGuardarEstadoExpress').click(function() {
        var nombre = $('#nombre_estado_nuevo').val().trim();

        if(nombre == "") {
            alert("El nombre del estado es obligatorio");
            return;
        }

        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'guardarEstado', nombre: nombre }
        }).done(function(data) {
            if(data.success) {
                alert("Estado guardado con éxito");
                $('#nombre_estado_nuevo').val(''); 
                $('#modalEstado').modal('hide');  
                cargarEstadosModulo();            
            } else {
                alert("Error al guardar: " + (data.msj || "Error desconocido"));
            }
        });
    });

    
    $('#btnGuardarMunicipioExpress').click(function() {
        var id_estado = $('#id_estado_muni_nuevo').val();
        var nombre = $('#nombre_municipio_nuevo').val().trim();

        if(id_estado == "" || nombre == "") {
            alert("Todos los campos son obligatorios");
            return;
        }

        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'guardarMunicipio', nombre: nombre, id_estado: id_estado }
        }).done(function(data) {
            if(data.success) {
                alert("Municipio guardado con éxito");
                $('#nombre_municipio_nuevo').val('');
                $('#modalMunicipio').modal('hide');  
                
                
                var estadoActualCiudad = $('#id_estado_mod').val();
                if(estadoActualCiudad == id_estado) {
                    cargarMunicipiosModulo(id_estado, '#id_municipio_mod');
                }
            } else {
                alert("Error al guardar: " + (data.msj || "Error desconocido"));
            }
        });
    });

   
    $('#id_estado_mod').change(function() {
        var id_estado = $(this).val();
        if(id_estado !== "") {
            cargarMunicipiosModulo(id_estado, '#id_municipio_mod');
        } else {
            $('#id_municipio_mod').html('<option value="">-- Seleccione un Estado Primero --</option>');
        }
    });

    
    $('#id_estado_edit_mod').change(function() {
        var id_estado = $(this).val();
        if(id_estado !== "") {
            cargarMunicipiosModulo(id_estado, '#id_municipio_edit_mod');
        }
    });

    
    $('#btnGuardarCiudad').click(function() {
        var nombre = $('#nombre_ciudad_mod').val();
        var id_municipio = $('#id_municipio_mod').val();
        var id_estado = $('#id_estado_mod').val();

        if(nombre == "" || id_municipio == "" || id_estado == "") {
            alert("Todos los campos son obligatorios");
            return;
        }

        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'guardarCiudad', nombre: nombre, id_municipio: id_municipio, id_estado: id_estado }
        }).done(function(data) {
            if(data.success) {
                alert("Ciudad registrada con éxito");
                $('#formCiudad')[0].reset();
                $('#btnCerrarModCiudad').click();
                listarCiudadesModulo();
                if (typeof cargarCiudades === "function") { cargarCiudades(); } 
            } else {
                alert(data.msj);
            }
        });
    });


    $('#btnActualizarCiudad').click(function() {
        var id = $('#idCiudadEdit').val();
        var nombre = $('#nombre_ciudad_edit_mod').val();
        var id_municipio = $('#id_municipio_edit_mod').val();
        var id_estado = $('#id_estado_edit_mod').val();

        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'modificarCiudad', id: id, nombre: nombre, id_municipio: id_municipio, id_estado: id_estado }
        }).done(function(data) {
            if(data.success) {
                alert("Ciudad actualizada correctamente");
                $('#btnCerrarEditModCiudad').click();
                listarCiudadesModulo();
                if (typeof cargarCiudades === "function") { cargarCiudades(); }
            } else {
                alert(data.msj);
            }
        });
    });
});


function listarCiudadesModulo() {
    $.ajax({
        url: 'app/controllers/UbicacionController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'listarCiudades' }
    }).done(function(data) {
        if(data.success) {
            var filas = "";
            data.datos.forEach(function(item) {
                filas += `<tr>
                    <td>${item.id_ciudad}</td>
                    <td>${item.nombre_ciudad}</td>
                    <td>${item.municipio}</td>
                    <td>${item.estado}</td>
                    <td>
                        <button type="button" class="btn btn-warning waves-effect" onclick="abrirEditarCiudad(${item.id_ciudad}, '${item.nombre_ciudad}', ${item.id_estado}, ${item.id_municipio})"><i class="material-icons">edit</i></button>
                        <button type="button" class="btn btn-danger waves-effect" onclick="eliminarCiudadModulo(${item.id_ciudad})"><i class="material-icons">delete</i></button>
                    </td>
                </tr>`;
            });
            $('#tablaCiudades tbody').html(filas);
        }
    });
}


function cargarEstadosModulo() {
    $.ajax({
        url: 'app/controllers/UbicacionController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarEstados' }
    }).done(function(data) {
        if(data.success || data) { 
            var opciones = '<option value="">-- Seleccione Estado --</option>';
            
            var lista = data.datos ? data.datos : data;
            
            lista.forEach(function(item) {
                opciones += `<option value="${item.id}">${item.nombre}</option>`;
            });
            
            
            $('#id_estado_mod').html(opciones);
            $('#id_estado_edit_mod').html(opciones);
            $('#id_estado_muni_nuevo').html(opciones); 
        }
    });
}


function cargarMunicipiosModulo(id_estado, selectorDestino, idMunicipioSeleccionar = null) {
    $.ajax({
        url: 'app/controllers/UbicacionController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarMunicipios', id_estado: id_estado }
    }).done(function(data) {
        if(data.success) {
            var opciones = '<option value="">-- Seleccione Municipio --</option>';
            data.datos.forEach(function(item) {
                opciones += `<option value="${item.id}">${item.nombre}</option>`;
            });
            $(selectorDestino).html(opciones);
            
            if(idMunicipioSeleccionar != null) {
                $(selectorDestino).val(idMunicipioSeleccionar);
            }
        }
    });
}


function abrirEditarCiudad(id, nombre, id_estado, id_municipio) {
    $('#idCiudadEdit').val(id);
    $('#nombre_ciudad_edit_mod').val(nombre);
    $('#id_estado_edit_mod').val(id_estado);
    
    
    cargarMunicipiosModulo(id_estado, '#id_municipio_edit_mod', id_municipio);
    
    $('#modalEditarCiudad').modal('show');
}


function eliminarCiudadModulo(id) {
    if(confirm("¿Seguro que deseas eliminar esta ciudad?")) {
        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'borrarCiudad', id: id }
        }).done(function(data) {
            if(data.success) {
                alert("Ciudad eliminada con éxito");
                listarCiudadesModulo();
                if (typeof cargarCiudades === "function") { cargarCiudades(); }
            } else {
                alert(data.msj);
            }
        });
    }
}