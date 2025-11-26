jQuery(function ($) {
    if (typeof wr_ajax === 'undefined') {
        return;
    }

    $(document).on('click', '.wr-quick-view-btn', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var $body = $('#wr-quick-view-modal .wr-qv-body');

        if (!id) {
            $body.html('Missing product ID.');
            $('#wr-quick-view-modal').fadeIn(200);
            return;
        }

        var loadingText = 'Loading...';
        var errorText = 'Error loading product.';

        $body.attr('aria-busy', 'true').html(loadingText);
        $('#wr-quick-view-modal').fadeIn(200);

        $.post(
            wr_ajax.ajax_url,
            {
                action: 'wr_quick_view',
                product_id: id,
            },
            function (response) {
                $body.html(response.success ? response.data : errorText);
            }
        ).fail(function () {
            $body.html(errorText);
        }).always(function () {
            $body.attr('aria-busy', 'false');
        });
    });

    $(document).on('click', '.wr-qv-close, .wr-qv-overlay', function () {
        $('#wr-quick-view-modal').fadeOut(200, function () {
            $('#wr-quick-view-modal .wr-qv-body').attr('aria-busy', 'false').empty();
        });
    });
});
