function toast(icon, title) {
	if (!window.Swal) return;
	clienteToastSecuencia++;
	var secuencia = clienteToastSecuencia;
	Swal.close();
	var duracion = icon === 'error' ? 3500 : 2500;
	Swal.fire({
		toast: true,
		position: 'top-end',
		icon: icon,
		title: title,
		showConfirmButton: false,
		timer: duracion,
		timerProgressBar: true,
		didOpen: function(popup) {
			setTimeout(function() {
				if (secuencia !== clienteToastSecuencia) return;
				var contenedor = popup.closest('.swal2-container');
				popup.remove();
				if (contenedor) contenedor.remove();
				document.querySelectorAll('.swal2-container').forEach(function(elemento) {
					elemento.remove();
				});
			}, duracion + 100);
		}
	});
}

function crearMensajesCliente() {
	['cedula','nombre','apellido','sexo','id_ciudad','direccion',
	 'cedulaEditar','nombreEditar','apellidoEditar','sexoEditar','id_ciudadEditar','direccionEditar'].forEach(function(campo) {
		if (!$('#error-' + campo).length) {
			$('<span>', {
				class: 'cliente-campo-error',
				id: 'error-' + campo,
				'aria-live': 'polite'
			}).insertAfter($('#' + campo).closest('.input-group'));
		}
	});
}

function limpiarValidacionesCliente(esEdicion) {
	var sufijo = esEdicion ? 'Editar' : '';
	['cedula','nombre','apellido','sexo','id_ciudad','direccion'].forEach(function(campo) {
		limpiarErrorCliente(campo + sufijo);
	});
}

var clienteGuardando = false;
var clienteEditando = false;
var clienteToastSecuencia = 0;

function mostrarErrorCliente(campo, mensaje) {
	var $campo = $('#' + campo);
	$campo.removeClass('cliente-input-success').addClass('cliente-input-error').attr('aria-invalid', 'true');
	$('#error-' + campo).text(mensaje).stop(true, true).slideDown(180);
}

function limpiarErrorCliente(campo) {
	var $campo = $('#' + campo);
	$campo.removeClass('cliente-input-error cliente-input-success').removeAttr('aria-invalid');
	$('#error-' + campo).stop(true, true).slideUp(150).text('');
}

function marcarClienteValido(campo) {
	$('#' + campo).removeClass('cliente-input-error').addClass('cliente-input-success').removeAttr('aria-invalid');
	$('#error-' + campo).stop(true, true).slideUp(150).text('');
}

function validarCampoCliente(campo, esEdicion, mostrarVacio) {
	var sufijo = esEdicion ? 'Editar' : '';
	var id = campo + sufijo;
	var $campo = $('#' + id);
	var valor = $.trim($campo.val() || '');
	var patronNombre = /^\p{L}[\p{L}\p{M}]*(?: +\p{L}[\p{L}\p{M}]*)*$/u;
	var valido = true;
	var mensaje = '';

	if (campo === 'cedula') {
		if (!valor) mensaje = 'La cédula es obligatoria.';
		else if (!/^[0-9]{5,20}$/.test(valor)) mensaje = 'La cédula debe contener entre 5 y 20 números.';
	}
	if ((campo === 'nombre' || campo === 'apellido') && !valor) {
		mensaje = campo === 'nombre' ? 'El nombre es obligatorio.' : 'El apellido es obligatorio.';
	} else if ((campo === 'nombre' || campo === 'apellido') && !patronNombre.test(valor)) {
		mensaje = campo === 'nombre'
			? 'El nombre solo debe contener letras y espacios.'
			: 'El apellido solo debe contener letras y espacios.';
	}
	if (campo === 'sexo' && !valor) mensaje = 'Selecciona el sexo.';
	if (campo === 'id_ciudad' && !valor) mensaje = 'Selecciona una ciudad.';
	if (campo === 'direccion' && Array.from(valor).length > 100) {
		mensaje = 'La dirección no debe exceder los 100 caracteres.';
	}
	if (campo === 'direccion' && !valor) mensaje = 'La dirección es obligatoria.';

	valido = mensaje === '';
	if (valido) marcarClienteValido(id);
	else if (mostrarVacio || valor !== '') mostrarErrorCliente(id, mensaje);
	else limpiarErrorCliente(id);
	return valido;
}

function validarFormularioCliente(esEdicion, mostrarErrores) {
	var sufijo = esEdicion ? 'Editar' : '';
	var campos = ['cedula', 'nombre', 'apellido', 'sexo', 'id_ciudad', 'direccion'];
	var faltantes = [];
	var primerCampo = null;

	campos.forEach(function(campo) {
		var valido = validarCampoCliente(campo, esEdicion, mostrarErrores);
		if (!valido) {
			faltantes.push(campo);
			primerCampo = primerCampo || campo + sufijo;
		}
	});
	return {valido: faltantes.length === 0, primerCampo: primerCampo};
}

$(document).ready(function(){

	crearMensajesCliente();
	listar();

	cargarCiudades();

	$('#formulario').on('submit', function(e) {
		e.preventDefault();
		crear();
	});
	$('#formularioEditar').on('submit', function(e) {
		e.preventDefault();
		editar();
	});

	['cedula','nombre','apellido','sexo','id_ciudad','direccion'].forEach(function(campo) {
		$('#' + campo).on('input change blur', function() {
			validarCampoCliente(campo, false, true);
		});
		var campoEditar = campo === 'id_ciudad' ? 'id_ciudadEditar' : campo + 'Editar';
		$('#' + campoEditar).on('input change blur', function() {
			validarCampoCliente(campo, true, true);
		});
	});

	$('#modal').on('shown.bs.modal', function() {
		limpiarValidacionesCliente(false);
	});
	$('#modalEditar').on('shown.bs.modal', function() {
		limpiarValidacionesCliente(true);
	});

	function prepararCedula(selector, idSelector) {
		$(selector).on('input', function() {
			this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20);
			var valor = $.trim(this.value);
			if (valor !== '' && !/^[0-9]{5,20}$/.test(valor)) {
				this.setCustomValidity('Ingrese entre 5 y 20 números.');
				$(this).addClass('is-invalid');
				return;
			}
			this.setCustomValidity('');
			$(this).removeClass('is-invalid');
			if (/^[0-9]{5,20}$/.test(valor)) {
				$.ajax({
					url: 'app/controllers/ClienteController.php',
					type: 'POST',
					dataType: 'json',
					data: {action: 'validarCedula', cedula: valor, id: $(idSelector).val() || 0}
				}).done(function(data) {
					if (!data.success) {
						$(selector).removeClass('cliente-input-success cliente-is-valid').addClass('cliente-input-error is-invalid');
						var campo = selector.replace('#', '');
						$('#error-' + campo).text(data.msj || 'Esta cédula ya está registrada.').stop(true,true).slideDown(180);
					} else {
						$(selector).removeClass('cliente-input-error is-invalid').addClass('cliente-input-success');
						var campo = selector.replace('#', '');
						$('#error-' + campo).stop(true,true).slideUp(150).text('');
					}
				});
			}
		});
	}
	prepararCedula('#cedula', '#idEditar');
	prepararCedula('#cedulaEditar', '#idEditar');

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
			console.log( data )
			toast('error', data.msj || 'Error al listar clientes');
		}
	})
}





// -------------------------------------------------------------------------------------------------
//				Crear
//--------------------------------------------------------------------------------------------------

function validarDatosCliente(cedula, nombre, apellido, direccion) {
	cedula = $.trim(cedula);
	nombre = $.trim(nombre);
	apellido = $.trim(apellido);
	var patronNombre = /^\p{L}[\p{L}\p{M}]*(?: +\p{L}[\p{L}\p{M}]*)*$/u;

	if (!/^[0-9]{5,20}$/.test(cedula)) {
		toast('error', 'La cédula debe contener entre 5 y 20 números.');
		return false;
	}

	if (!patronNombre.test(nombre)) {
		toast('error', 'El nombre solo debe contener letras y espacios.');
		return false;
	}

	if (!patronNombre.test(apellido)) {
		toast('error', 'El apellido solo debe contener letras y espacios.');
		return false;
	}

	if (Array.from(direccion).length > 100) {
		toast('error', 'La dirección no debe exceder los 100 caracteres.');
		return false;
	}

	return true;
}

$('#guardar').click(function() {
	crear();
})

function crear(event) {
	if (clienteGuardando) return;
	var cedula = $.trim($('#cedula').val())
	var nombre = $.trim($('#nombre').val())
	var apellido = $.trim($('#apellido').val())
	var sexo = $('#sexo').val()
	var id_ciudad = $('#id_ciudad').val() 
	var direccion = $('#direccion').val()

	var validacion = validarFormularioCliente(false, true);
	if (!validacion.valido) {
		toast('error', 'Completa primero los campos marcados en rojo.');
		$('#' + validacion.primerCampo).trigger('focus');
		return;
	}

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
			$('#cedula').val("");
			$('#nombre').val("");
			$('#apellido').val("");
			$('#sexo').val("");
			$('#id_ciudad').val("").change();
			$('#direccion').val("");
			$('#fecha_creacion').val("");
			limpiarValidacionesCliente(false);

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
	})
})


$('#editar').click(function() {
	editar();
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

	var validacion = validarFormularioCliente(true, true);
	if (!validacion.valido) {
		toast('error', 'Completa primero los campos marcados en rojo.');
		$('#' + validacion.primerCampo).trigger('focus');
		return;
	}

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
			limpiarValidacionesCliente(true);

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

	if (window.Swal) {
		clienteToastSecuencia++;
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
	} else {
		if (confirm('¿Estás seguro de eliminar este usuario?')) eliminar(id);
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

				toast('success', 'Cliente eliminado correctamente')
			} else {
				toast('error', data.msj || 'No se pudo eliminar el cliente');
			}
	})
}