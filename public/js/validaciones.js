const Validaciones = {
    cedula: function(valor) {
        // Solo numeros, longitud estricta de 7 a 10 dígitos.
        const num = parseInt(valor, 10);
        return /^[0-9]{7,10}$/.test(valor) && num >= 5000000;
    },
    nombre: function(valor) {
        // Letras con acentos, de 2 a 25 caracteres
        return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,25}$/.test(valor) && valor.trim().length > 0;
    },
    apellido: function(valor) {
        // Letras con acentos, de 2 a 25 caracteres
        return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,25}$/.test(valor) && valor.trim().length > 0;
    },
    telefono: function(valor) {
        // El telefono (prefijo + exactamente 7 dígitos)
        const regex = /^(?:\+58|0)(?:412|414|424|416|426|422|251)\d{7}$/;
        return regex.test(valor);
    },
    numeroEnteroPositivo: function(valor) {
        return /^[0-9]+$/.test(String(valor)) &&
            Number.isSafeInteger(Number(valor)) &&
            Number(valor) > 0 &&
            Number(valor) <= 1000000;
    },
    
    username: function(valor) {
        // Alfanumerico, entre 4 y 15 caracteres
        return /^[a-zA-Z0-9_]{4,15}$/.test(valor);
    },
    password: function(valor) {
        // 8 a 16 caracteres, 1 mayuscula, 1 minuscula, 1 numero, 1 caracter especial
        const regex = /^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,16}$/;
        return regex.test(valor);
    }
};

function toggleError(input, isValid, errorId) {
    if (isValid) {
        $(input).removeClass('input-error').addClass('input-success');
        $('#' + errorId).slideUp(200);
    } else {
        $(input).removeClass('input-success').addClass('input-error');
        $('#' + errorId).slideDown(200);
    }

}

const ValidacionTiempoReal = {
    activar: function(selector) {
        const $formulario = $(selector);

        $formulario.find('input, select, textarea').on('keyup change', function() {
            const validador = $formulario.data('validator');
            if (validador) validador.element(this);
        });
    }
};

if (typeof jQuery !== 'undefined' &&
    jQuery.fn.validate &&
    jQuery('#formPedido').length) {
    jQuery('#formPedido').validate({
        rules: {
            id_cliente: {
                required: true
            },
            id_vendedor: {
                required: true
            },
            fecha_pedido: {
                required: true,
                date: true
            },
            id_producto: {
                required: true
            },
            cantidad: {
                required: true,
                digits: true,
                min: 1,
                max: 1000000
            },
            usuario: {
                required: true,
                minlength: 4,
                maxlength: 15
            },
            password: {
                required: true,
                minlength: 8,
                maxlength: 16
            }
        },
        messages: {
            id_cliente: {
                required: "Seleccione un cliente."
            },
            id_vendedor: {
                required: "Seleccione un vendedor."
            },
            fecha_pedido: {
                required: "Ingrese la fecha del pedido.",
                date: "Ingrese una fecha válida."
            },
            id_producto: {
                required: "Seleccione un producto."
            },
            cantidad: {
                required: "Ingrese la cantidad.",
                digits: "Ingrese un número entero.",
                min: "La cantidad debe ser al menos 1.",
                max: "La cantidad no puede exceder 1000000."
            },
            usuario: {
                required: "Ingrese un nombre de usuario.",
                minlength: "El nombre de usuario debe tener al menos 4 caracteres.",
                maxlength: "El nombre de usuario no puede exceder 15 caracteres."
            },
            password: {
                required: "Ingrese una contraseña.",
                minlength: "La contraseña debe tener al menos 8 caracteres.",
                maxlength: "La contraseña no puede exceder 16 caracteres."
            }   
        },
        submitHandler: function() {
            if (!detallePedido.length) {
                jQuery('#formPedido').validate().showErrors({
                    id_producto: 'Agrega al menos un producto antes de guardar el pedido.'
                });
                return false;
            }

            guardarPedido();
            return false;
        }
    });



    
}