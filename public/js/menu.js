$(document).ready(function () {

    // Abrir el cajón deslizante al tocar el botón de hamburguesa
    $(document).on('click', '.bars', function(e) {
        e.preventDefault();
        $('body').addClass('sidebar-open');
    });

    // Cerrar el cajón deslizante al tocar la 'X' oscura o el fondo gris
    $(document).on('click', '.overlay, .close-sidebar', function(e) {
        e.preventDefault();
        $('body').removeClass('sidebar-open');
    });
    
});