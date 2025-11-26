// WR Mini Cart JS
(function ($) {
    $(document).ready(function () {
        var $toggle = $('[data-wr-mini-cart-toggle]');
        var $container = $('[data-wr-mini-cart-container]');
        var $close = $('[data-wr-mini-cart-close]');

        $toggle.on('click', function () {
            $container.toggleClass('is-open');
        });

        $close.on('click', function () {
            $container.removeClass('is-open');
        });

        console.log('WR Mini Cart JS loaded');
    });
})(jQuery);
