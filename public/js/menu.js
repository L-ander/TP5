$(document).ready(function () {

    var $body = $('body');
    var $menuToggle = $('.bars');
    var wasMobile = window.matchMedia('(max-width: 992px)').matches;

    function updateMenuToggle() {
        var isMobile = window.matchMedia('(max-width: 992px)').matches;
        var isExpanded = isMobile
            ? $body.hasClass('sidebar-open')
            : !$body.hasClass('sidebar-collapsed');

        $menuToggle.attr('aria-expanded', isExpanded ? 'true' : 'false');
    }

    $(document).on('click', '.bars', function(e) {
        e.preventDefault();

        if (window.matchMedia('(max-width: 992px)').matches) {
            $body.toggleClass('sidebar-open');
        } else {
            $body.toggleClass('sidebar-collapsed');
        }

        updateMenuToggle();
    });

    $(document).on('click', '.overlay, .close-sidebar', function(e) {
        e.preventDefault();
        $body.removeClass('sidebar-open');
        updateMenuToggle();
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $body.hasClass('sidebar-open')) {
            $body.removeClass('sidebar-open');
            updateMenuToggle();
        }
    });

    $(window).on('resize', function() {
        var isMobile = window.matchMedia('(max-width: 992px)').matches;

        if (isMobile !== wasMobile) {
            $body.removeClass('sidebar-open sidebar-collapsed');
            wasMobile = isMobile;
            updateMenuToggle();
        }
    });

    updateMenuToggle();
});