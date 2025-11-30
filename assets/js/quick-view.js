jQuery(function ($) {

    if (typeof wr_ajax === 'undefined') return;

    /* --------------------------------------------------------------
       OPEN QUICK VIEW
    -------------------------------------------------------------- */
    $(document).on('click', '.wr-quick-view-btn', function (e) {
        e.preventDefault();

        const id = $(this).data('id');
        const $modal = $('#wr-quick-view-modal');
        const $body = $modal.find('.wr-qv-body');

        if (!id) {
            $body.html('<div class="wr-qv-error">Missing product ID</div>');
            $modal.fadeIn(150);
            return;
        }

        // Body scroll lock
        $('body').addClass('wr-qv-open');

        // Loading state
        $body.attr('aria-busy', 'true')
             .html('<div class="wr-qv-loading">Loading...</div>');

        $modal.fadeIn(150);

        /* AJAX Request */
        $.post(
            wr_ajax.ajax_url,
            {
                action: 'wr_quick_view',
                product_id: id,
            },
            function (response) {

                if (!response || !response.success) {
                    $body.html('<div class="wr-qv-error">Error loading product</div>');
                    return;
                }

                $body.html(response.data);

                /* --------------------------------------------------------------
                   RE-BIND WooCommerce ADD TO CART
                -------------------------------------------------------------- */
                $(document.body).trigger('wc-enhanced-select-init');
                $(document.body).trigger('quick-view-loaded');
                $(document.body).trigger('wc_fragments_loaded');
                $(document.body).trigger('init_variation_form');
                $(document.body).trigger('wc-cart-button-updated');

                // Variation forms inside modal
                $body.find('.variations_form').each(function () {
                    $(this).wc_variation_form();
                });

                // Ensure Add-to-Cart works with AJAX
                $(document.body).trigger('wc_init_add_to_cart');

                /* --------------------------------------------------------------
                   RE-BIND WISHLIST (WR)
                -------------------------------------------------------------- */
                if (typeof wrWishlistRebind === 'function') {
                    wrWishlistRebind();
                }

            }
        ).fail(function () {
            $body.html('<div class="wr-qv-error">Server Error</div>');
        }).always(function () {
            $body.attr('aria-busy', 'false');
        });
    });


    /* --------------------------------------------------------------
       CLOSE QUICK VIEW
    -------------------------------------------------------------- */
    function closeQuickView() {
        const $modal = $('#wr-quick-view-modal');

        $modal.fadeOut(150, function () {
            // Clear previous content
            $modal.find('.wr-qv-body').empty().attr('aria-busy', 'false');
        });

        // Restore body scrolling
        $('body').removeClass('wr-qv-open');
    }

    $(document).on('click', '.wr-qv-close, .wr-qv-overlay', function () {
        closeQuickView();
    });

    // ESC Key
    $(document).on('keyup', function (e) {
        if (e.key === "Escape") {
            closeQuickView();
        }
    });

});
