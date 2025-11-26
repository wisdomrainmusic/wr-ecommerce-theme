(function ($) {
    $(function () {
        var $toggle = $('.nav-toggle');
        var $menu = $('.nav');

        $toggle.on('click', function () {
            var isOpen = $menu.toggleClass('is-open').hasClass('is-open');
            $(this).attr('aria-expanded', isOpen);
        });
    });
})(jQuery);
