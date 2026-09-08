const Validaciones = {
    cedula: function(valor) {
        const num = parseInt(valor, 10);
        return /^[0-9]+$/.test(valor) && num >= 5000000;
    },
    nombre: function(valor) {
        return /^[a-zA-Z\s]+$/.test(valor) && valor.trim().length > 0;
    },
    apellido: function(valor) {
        return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(valor) && valor.trim().length > 0;
    },
    telefono: function(valor) {
        const regex = /^(?:\+58|0)(?:412|414|424|416|426|422|251)\d{7}$/;
        return regex.test(valor);
    },
    
    username: function(valor) {
        // Alfanumérico, entre 4 y 15 caracteres
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