function toast(icon, title) {
	$.toast({
		heading: icon === 'success' ? 'Éxito' : 'Error',
		text: title,
		showHideTransition: 'slide',
		icon: icon,
		position: 'top-right',
		bgColor: icon === 'success' ? '#6ad86a' : '#e74d4d',
		textColor: '#ffffff'
	});
}

var clienteGuardando = false;
var clienteEditando = false;

$(document).ready(function(){
	listar();
	cargarCiudades();

	if (!$.validator.methods.clienteNombre) {
		$.validator.addMethod('clienteNombre', function(value, element) {
			return this.optional(element) || /^\p{L}[\p{L}\p{M}]*(?: +\p{L}[\p{L}\p{M}]*)*$/u.test($.trim(value));
		}, 'Use solo letras y espacios.');
	}

	function validacionCliente(selector, esEdicion) {
		$(selector).validate({
			rules: {
				cedula: {
					required: true,
					digits: true,
					minlength: 5,
					maxlength: 20,
					remote: {
						url: 'app/controllers/ClienteController.php',
						type: 'POST',
						data: {
							action: function() { return 'validarCedula'; },
							id: function() { return $(selector).find('#idEditar').val() || 0; }
						},
						dataFilter: function(response) {
							return JSON.parse(response).success ? 'true' : 'false';
						}
					}
				},
				nombre: {
					required: true,
					clienteNombre: true,
					maxlength: 50,
					normalizer: function(value) { return $.trim(value); }
				},
				apellido: {
					required: true,
					clienteNombre: true,
					maxlength: 50,
					normalizer: function(value) { return $.trim(value); }
				},
				sexo: { required: true },
				id_ciudad: { required: true },
				direccion: {
					required: true,
					maxlength: 100,
					normalizer: function(value) { return $.trim(value); }
				}
			},
			messages: {
				cedula: {
					required: 'La cédula es obligatoria.',
					digits: 'La cédula solo debe contener números.',
					minlength: 'La cédula debe contener entre 5 y 20 números.',
					maxlength: 'La cédula debe contener entre 5 y 20 números.',
					remote: 'La cédula ya está registrada.'
				},
				nombre: {
					required: 'El nombre es obligatorio.',
					maxlength: 'El nombre no debe exceder 50 caracteres.',
					clienteNombre: 'El nombre solo debe contener letras y espacios.'
				},
				apellido: {
					required: 'El apellido es obligatorio.',
					maxlength: 'El apellido no debe exceder 50 caracteres.',
					clienteNombre: 'El apellido solo debe contener letras y espacios.'
				},
				sexo: { required: 'Selecciona el sexo.' },
				id_ciudad: { required: 'Selecciona una ciudad.' },
				direccion: {
					required: 'La dirección es obligatoria.',
					maxlength: 'La dirección no debe exceder los 100 caracteres.'
				}
			},
			errorElement: 'small',
			errorClass: 'txt-error',
			highlight: function(element) {
				$(element).removeClass('cliente-input-success').addClass('cliente-input-error');
				$(element).closest('.form-line').css('border-bottom', '2px solid #F44336');
			},
			unhighlight: function(element) {
				$(element).removeClass('cliente-input-error').addClass('cliente-input-success');
				$(element).closest('.form-line').css('border-bottom', '2px solid #4CAF50');
			},
			errorPlacement: function(error, element) {
				error.css({
					color: '#F44336',
					display: 'block',
					marginTop: '5px'
				});
				var $linea = element.closest('.form-line');
				error.insertAfter($linea.length ? $linea : element);
			},
			invalidHandler: function() {
				toast('error', 'Completa los campos solicitados.');
			},
			submitHandler: function() {
				if (esEdicion) editar();
				else crear();
			}
		});
	}

	validacionCliente('#formulario', false);
	validacionCliente('#formularioEditar', true);

	$('#guardar').on('click', function() {
		$('#formulario').trigger('submit');
	});
	$('#editar').on('click', function() {
		$('#formularioEditar').trigger('submit');
	});

	$('#modal, #modalEditar').on('hidden.bs.modal', function() {
		var $formulario = $(this).find('form');
		var validador = $formulario.data('validator');
		if (validador) validador.resetForm();
		$formulario[0].reset();
		$formulario.find('.cliente-input-error, .cliente-input-success').removeClass('cliente-input-error cliente-input-success');
		$formulario.find('.form-line').css('border-bottom', '');
	});

	$('#cedula, #cedulaEditar').on('input', function() {
		this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20);
	});

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

		if ( data.success ) {

			let html = '';

				$.each(data.data.datos, function(index, val) {
    html += `
      <tr>
        <td> ${val.id}</td>
        <td> ${val.cedula}</td>
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
			toast('error', data.msj || 'Error al listar clientes');
		}
	})
	.fail(function() {
		toast('error', 'Error al cargar los clientes.');
	});
}





// -------------------------------------------------------------------------------------------------
//				Crear
//--------------------------------------------------------------------------------------------------

function crear(event) {
	if (clienteGuardando) return;
	var cedula = $.trim($('#cedula').val())
	var nombre = $.trim($('#nombre').val())
	var apellido = $.trim($('#apellido').val())
	var sexo = $('#sexo').val()
	var id_ciudad = $('#id_ciudad').val() 
	var direccion = $('#direccion').val()

	clienteGuardando = true;
	$('#guardar').prop('disabled', true).text('Guardando...');
	$.ajax({
		url: 'app/controllers/ClienteController.php',
		type: 'POST',
		dataType: 'json',
		data: {action: 'crear',
				cedula: cedula,
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
			toast('success', 'Cliente guardado correctamente');
		} else {
			toast('error', data.msj || 'No se pudo guardar el cliente');
		}
	})
	.fail(function(xhr) {
		var respuesta = xhr.responseJSON || {};
		toast('error', respuesta.msj || 'No se pudo guardar el cliente.');
	})
	.always(function() {
		clienteGuardando = false;
		$('#guardar').prop('disabled', false).text('Guardar');
	});
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
			
			if(Array.isArray(data)) {
				data.forEach(function(item) {
					opciones += `<option value="${item.id_ciudad}" data-municipio="${item.municipio}" data-estado="${item.estado}">${item.nombre_ciudad}</option>`;
				});
				$('#id_ciudad').html(opciones);
				$('#id_ciudadEditar').html(opciones);
			} else {
				toast('error', 'No se pudieron cargar las ciudades.');
			}
		},
		error: function() {
			toast('error', 'No se pudieron cargar las ciudades.');
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

		if (data.success == true) {

			$('#idEditar').val( data.data.datos[0].id ) 
			$('#cedulaEditar').val( data.data.datos[0].cedula || '' )
			$('#nombreEditar').val( data.data.datos[0].nombre )
			$('#apellidoEditar').val( data.data.datos[0].apellido )
			$('#sexoEditar').val( data.data.datos[0].sexo )
			$('#id_ciudadEditar').val( data.data.datos[0].id_ciudad ).change()
			
			$('#direccionEditar').val( data.data.datos[0].direccion )

			$('#modalEditar').modal('show'); 

		} else {

			toast('error', data.msj || 'Error al consultar el cliente');
		}
	}).fail(function() {
		toast('error', 'Error al consultar el cliente.');
	});
})

function editar() {
	if (clienteEditando) return;
	var id = $('#idEditar').val() 
	var cedula = $.trim($('#cedulaEditar').val())
	var nombre = $.trim($('#nombreEditar').val())
	var apellido = $.trim($('#apellidoEditar').val())
	var sexo = $('#sexoEditar').val()
	var id_ciudad = $('#id_ciudadEditar').val()
	var direccion = $('#direccionEditar').val()

	clienteEditando = true;
	$('#editar').prop('disabled', true).text('Guardando...');
	$.ajax({
		url: 'app/controllers/ClienteController.php',
		type: 'POST',
		dataType: 'json',
		data: {action: 'editar',
				id:id,
				cedula: cedula,
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
			toast('success', 'Cliente actualizado correctamente')

		} else {

			toast('error', data.msj || 'No se pudo actualizar el cliente')
		}
	})
	.fail(function(xhr) {
		var respuesta = xhr.responseJSON || {};
		toast('error', respuesta.msj || 'No se pudo actualizar el cliente.');
	})
	.always(function() {
		clienteEditando = false;
		$('#editar').prop('disabled', false).text('Editar');
	});
}
// -------------------------------------------------------------------------------------------------
//				Eliminar
//--------------------------------------------------------------------------------------------------

$("body").on("click","button.boton-borrar",function(){

    var id = $(this).attr("id")

	Swal.fire({
		title: '¿Eliminar cliente?',
		text: 'Esta acción no se puede deshacer.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Eliminar',
		cancelButtonText: 'Cancelar',
		confirmButtonColor: '#d33'
	}).then(function(result) {
		if (result.isConfirmed) eliminar(id);
	});
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

				toast('success', 'Cliente eliminado correctamente')
			} else {
				toast('error', data.msj || 'No se pudo eliminar el cliente');
			}
	})
	.fail(function(xhr) {
		var respuesta = xhr.responseJSON || {};
		toast('error', respuesta.msj || 'Error al eliminar el cliente.');
	});
}