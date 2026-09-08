$(document).ready(function(){

	listar();

	cargarCiudades();

	$('#id_ciudad').change(function() {
		var opcionSeleccionada = $(this).find('option:selected');
		var municipio = opcionSeleccionada.data('municipio');
		var estado = opcionSeleccionada.data('estado');

		if ($(this).val() !== "") {
			$('#municipio').val(municipio);
			$('#estado').val(estado);
			$('#slots_geograficos').slideDown(300);
		} else {
			$('#slots_geograficos').slideUp(200);
		}
	});

	$('#id_ciudadEditar').change(function() {
		var opcionSeleccionada = $(this).find('option:selected');
		var municipio = opcionSeleccionada.data('municipio');
		var estado = opcionSeleccionada.data('estado');

		if ($(this).val() !== "") {
			$('#municipioEditar').val(municipio);
			$('#estadoEditar').val(estado);
			$('#slots_geograficos_editar').slideDown(300);
		} else {
			$('#slots_geograficos_editar').slideUp(200);
		}
	});
})

// -------------------------------------------------------------------------------------------------
//				listar
//--------------------------------------------------------------------------------------------------

function listar(){

	$.ajax({
		url: 'app/controllers/ClienteController.php',
		type: 'POST',
		data: {action: 'listar'},
		dataType: 'json',
	})
	.done(function(data) {

		console.log( data )

		if ( data.success ) {

			let html = '';

				$.each(data.data.datos, function(index, val) {
    html += `
      <tr>
        <td> ${val.id}</td>
        <td> ${val.nombre}</td>
        <td> ${val.apellido}</td>
        <td> ${val.sexo}</td>
        <td> ${val.nombre_ciudad}</td>
        <td> ${val.municipio}</td> <td> ${val.estado}</td>    <td> ${val.direccion}</td>
        <td> ${val.fecha_creacion}</td>
          <td>
            <div class=\"d-flex gap-2\">
                <button type=\"button\" class=\"btn bg-deep-orange btn-circle waves-effect waves-circle waves-float boton-update\" id=\"${val.id}\">
                    <i class=\"material-icons\">mode_edit</i>
                </button>
                <button type=\"button\" class=\"btn bg-red btn-circle waves-effect waves-circle waves-float boton-borrar\" id=\"${val.id}\">
                <i class=\"material-icons\">delete</i>
                </button>
            </div>
          </td>
      </tr>
    `;
});

	      $('#tablaclientes tbody').html(html);

		} else {
			console.log( data )
			alert( data.msj )
		}
	})
}

// -------------------------------------------------------------------------------------------------
//				Crear
//--------------------------------------------------------------------------------------------------

$('#guardar').click(function() {
	crear();
})

function crear(event) {
	var nombre = $('#nombre').val()
	var apellido = $('#apellido').val()
	var sexo = $('#sexo').val()
	var id_ciudad = $('#id_ciudad').val() 
	var direccion = $('#direccion').val()
	
	$.ajax({
		url: 'app/controllers/ClienteController.php',
		type: 'POST',
		dataType: 'json',
		data: {action: 'crear',
				nombre: nombre,
				apellido: apellido,
				sexo: sexo,
				id_ciudad: id_ciudad,
				direccion: direccion
            },
	})
	.done(function(data) {

		if (data.success == true) {

			listar();
			$('#cerrar').click();
			$('#nombre').val("");
			$('#apellido').val("");
			$('#sexo').val("");
			$('#id_ciudad').val("").change();
			$('#direccion').val("");
			$('#fecha_creacion').val("");

			alert( 'Su registro ha sido guardado con exito' )
		} else {

			alert( data.msj )
		}
	})
}

// -------------------------------------------------------------------------------------------------
//				Funciones de Carga (Líneas, Medidas y Ciudades)
//--------------------------------------------------------------------------------------------------

function cargarCiudades() {
	$.ajax({
		url: 'app/controllers/ClienteController.php',
		type: 'POST',
		data: { action: 'listarCiudades' },
		dataType: 'json',
		success: function(data) {
			var opciones = '<option value="">Seleccione una ciudad</option>';
			
			if(data && data.length > 0) {
				data.forEach(function(item) {
					opciones += `<option value="${item.id_ciudad}" data-municipio="${item.municipio}" data-estado="${item.estado}">${item.nombre_ciudad}</option>`;
				});
				
				$('#id_ciudad').html(opciones);
				$('#id_ciudadEditar').html(opciones);
			}
		},
		error: function() {
			console.error("Error");
		}
	});
}

function cargarLineas(){
	$.ajax({
        url: 'app/controllers/ClienteController.php',
        type: 'POST',
        data: { action: 'cargarLineas' }, 
        dataType: 'json',
    })
    .done(function(data) {
        if (data.success) {
            let options = '<option value="">-- Seleccione Línea --</option>';
            
            $.each(data.datos, function(index, val) {
                options += `<option value="${val.id}">${val.nombre}</option>`;
            });

            
        } else {
            console.error("Error cargando líneas: " + data.msj);
        }
    });
}



// -------------------------------------------------------------------------------------------------
//				Modificar
//--------------------------------------------------------------------------------------------------

$("body").on("click","button.boton-update",function(){

	var id = $(this).attr("id")

	$.ajax({
		url: 'app/controllers/ClienteController.php',
		type: 'POST',
		dataType: 'json',
		data: {action: 'consultar',
				id: id,},
	})
	.done(function(data) {

		console.log( data.data.datos )

		if (data.success == true) {

			$('#idEditar').val( data.data.datos[0].id ) 
			$('#nombreEditar').val( data.data.datos[0].nombre )
			$('#apellidoEditar').val( data.data.datos[0].apellido )
			$('#sexoEditar').val( data.data.datos[0].sexo )
			$('#id_ciudadEditar').val( data.data.datos[0].id_ciudad ).change()
			
			$('#direccionEditar').val( data.data.datos[0].direccion )

			$('#modalEditar').modal('show'); 

		} else {

			alert( data.msj )
		}
	})
})


$('#editar').click(function() {
	editar();
})

function editar() {
	var id = $('#idEditar').val() 
	var nombre = $('#nombreEditar').val()
	var apellido = $('#apellidoEditar').val()
	var sexo = $('#sexoEditar').val()
	var id_ciudad = $('#id_ciudadEditar').val()
	var direccion = $('#direccionEditar').val()
	
	$.ajax({
		url: 'app/controllers/ClienteController.php',
		type: 'POST',
		dataType: 'json',
		data: {action: 'editar',
				id:id,
				nombre: nombre,
				apellido: apellido,
				sexo: sexo,
				id_ciudad: id_ciudad,
				direccion: direccion
			},
	})
	.done(function(data) {

		if (data.success == true) {

			listar();
			$('#cerrarEditar').click();

			alert( 'Su registro ha sido actualizado con exito' )

		} else {

			alert( data.msj )
		}
	})
}
// -------------------------------------------------------------------------------------------------
//				Eliminar
//--------------------------------------------------------------------------------------------------

$("body").on("click","button.boton-borrar",function(){

    var id = $(this).attr("id")

	let resultado = confirm("¿Estás seguro de eliminar este usuario?");

	if (resultado) {

		eliminar(id)
	} else {

		alert("Operación cancelada.");
	}
})

function eliminar(id) {

	$.ajax({
		url: 'app/controllers/ClienteController.php',
		type: 'POST',
		dataType: 'json',
		data: {action: 'eliminar',
				id: id},
	})
	.done(function(data) {

			if ( data.success ) {

				listar();

				alert( 'Usuario eliminado' )
			} else {

				alert( data.msj )
			}
	})
}