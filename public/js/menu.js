$(document).ready(function () {

	listarNombreMenu()
	MostrarIcono()
});

function MostrarIcono() {
	var session = sessionStorage.getItem("Session");
	session = JSON.parse(session)

	if (session.sexo == "Femenino") {
		$('#femenino').removeClass('oculto');
	} else {
		$('#masculino').removeClass('oculto');
	}
}

function listarNombreMenu() {

	var session = sessionStorage.getItem("Session");
	session = JSON.parse(session)

	$('#nombrePerso').text(session.primernombre + ' ' + session.primerapellido)
	$('#rolPerso').text(session.nombrerol)

}

function SalirDelSistema() {

	console.log('Salir del Sistema')

	sessionStorage.removeItem('Modulos');
	sessionStorage.removeItem('Session');

	url = 'index'

	setTimeout($(location).attr('href', url), 1000);
}

$('#salir').click(function () {

	var session = sessionStorage.getItem("Session");
	session = JSON.parse(session)

	bootoast.toast({
		message: 'Hasta Luego ' + session.usuario,
		type: 'success'
	});

	setTimeout(function () { SalirDelSistema() }, 1000);

});

function validarPermiso(cod_modulo) {

	// console.log( permiso )

	permiso = false

	var modulos = sessionStorage.getItem("Modulos");
	modulos = JSON.parse(modulos)

	var session = sessionStorage.getItem("Session");
	session = JSON.parse(session)

	// console.log( session.cambio_clave )

	for (var i = 0; i < modulos.length; i++) {

		$('#' + modulos[i]).removeClass('oculto')

		if (parseInt(modulos[i]) == cod_modulo) {

			permiso = true
		}

		if (parseInt(modulos[i]) == 16) {

			$('#MenuSeguridad').removeClass('oculto')
		}

		if (parseInt(modulos[i]) == 15 || parseInt(modulos[i]) > 16 && parseInt(modulos[i]) < 24) {

			$('#MenuConfigDatos').removeClass('oculto')
		}
	}

	if (session.codrol == 7) { // Rol Representante

		$('#MenuRepresentante').removeClass('oculto')
	}

	if (permiso == false) {

		// console.log( permiso )
		ruta = 'denied'
		setTimeout(function () { $(location).attr('href', ruta); });

	}
	// else if ( parseInt( session.cambio_clave ) == 0) {

	// 	url = 'perfil'
	// 	setTimeout(function() { $(location).attr('href',url); } );
	// }
}
