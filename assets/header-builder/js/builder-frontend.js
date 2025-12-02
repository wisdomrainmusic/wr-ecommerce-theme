(function ($) {
    $(document).on('click', '.wr-hb .nav-toggle', function () {
        const target = $(this).attr('aria-controls');
        const $menu = $('#' + target);
        const expanded = $(this).attr('aria-expanded') === 'true';
        $(this).attr('aria-expanded', expanded ? 'false' : 'true');
        $menu.toggleClass('is-open');
    });
})(jQuery);
