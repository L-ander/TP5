$(document).ready(function () {


});
//__________________________________ Click Boton Login __________________________________________________________
$("#password").keyup(function (event) {

	if (event.keyCode == 13) {

		ingresar();
	}
});

$("#Ingresar").click(function (event) {

	ingresar();

});

$('#mostrar2').on('change', function (event) { //mostrar contraseña 
	// Si el checkbox esta "checkeado"
	if ($('#mostrar2').is(':checked')) {
		// Convertimos el input de contraseña a texto.
		$('#password').get(0).type = 'text';
		// En caso contrario..
	} else {
		// Lo convertimos a contraseña.
		$('#password').get(0).type = 'password';
	}
});
// Fin Function Ingresar ==========================================================================

function ingresar() {
	var action = 'IniciarSesion';
	var usuario = $('#usuario').val();
	var password = $('#password').val();

	if (usuario.length > 0 && password.length > 0) {
		var datos = new FormData();
		datos.append('action', action);
		datos.append("usuario", usuario);
		datos.append('password', password);

		url = 'app/controllers/login.php';
		
        // Deshabilitar el botón mientras carga
        $('#Ingresar').prop('disabled', true).text('Verificando...');

		$.ajax({
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			url: url,
			data: datos,
			dataType: "json",
			success: function (data) {
				if (data.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Acceso concedido!',
                        text: 'Redirigiendo al panel...',
                        showConfirmButton: false,
                        timer: 1500
                    });
					url = data.url;
					setTimeout(function () { $(location).attr('href', url); }, 1500);
				} else {
					if (data.error === 'usuario_inactivo') {
                        Swal.fire({ icon: 'warning', title: 'Acceso Denegado', text: 'Su cuenta de usuario se encuentra inactiva.'});
					} else {
                        Swal.fire({ icon: 'error', title: 'Datos Incorrectos', text: 'El usuario o contraseña no coinciden.'});
					}
                    $('#Ingresar').prop('disabled', false).text('INGRESAR AL SISTEMA');
				}
			},
			error: function(xhr, status, error) {
				console.error("Error del servidor:", xhr.responseText);
                Swal.fire({ icon: 'error', title: 'Error del Servidor', text: 'Ocurrió un problema de comunicación.'});
                $('#Ingresar').prop('disabled', false).text('INGRESAR AL SISTEMA');
			}
		});
	} else {
        Swal.fire({ icon: 'info', title: 'Campos Vacíos', text: 'Por favor, ingrese sus credenciales.'});
	}
};