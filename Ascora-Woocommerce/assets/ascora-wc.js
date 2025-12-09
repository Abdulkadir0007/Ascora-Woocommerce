/**
 * ascora-main.js
 * Full clean, single compiled JS for Ascora theme interactions
 * Requires: jQuery, Swiper (optional), (zoom plugin optional)
 */
jQuery(function ($) {

    // ADD / REMOVE (shop + single + wishlist page)
    $(document).on("click", ".ascora-wl-btn, .wishlist-icon", function (e) {
        e.preventDefault();

        let $btn = $(this);
        let pid = $btn.data("id");

        $.post(ascora_ajax.ajax_url, {
            action: "ascora_wishlist_toggle",
            product_id: pid
        }, function (res) {
            if (!res || !res.success) return;

            // toggle heart
            $btn.toggleClass("active");
            $btn.find("i").toggleClass("fa-heart fa-heart-o");

            // update counter
            $(".wishlist-count, .ascora-wishlist-icon .count")
                .text(res.data.count);

            // Remove immediately on wishlist page
            if ($("body").hasClass("wishlist-page")) {
                $(".ascora-wishlist-item[data-id='" + pid + "']")
                    .fadeOut(250, function () {
                        $(this).remove();

                        if (!$(".ascora-wishlist-item").length) {
                            location.reload();
                        }
                    });
            }
        });
    });



});

jQuery(function($){

    $(document).on("click", ".qty-plus", function () {
        let input = $(this).closest(".quantity").find(".qty");
        let val = parseInt(input.val()) || 1;
        let max = parseInt(input.attr("max")) || 9999;

        if (val < max) {
            input.val(val + 1).trigger("change");
        }
    });

    $(document).on("click", ".qty-minus", function () {
        let input = $(this).closest(".quantity").find(".qty");
        let val = parseInt(input.val()) || 1;
        let min = parseInt(input.attr("min")) || 1;

        if (val > min) {
            input.val(val - 1).trigger("change");
        }
    });

});

jQuery(document).ready(function ($) {

    // Quick View Color Change Image
    $(document).on("click", ".ascora-color-variations .color-dot", function () {

        let newImage = $(this).data("image");

        if (!newImage) return;

        // Update first image of swiper
        let mainSlide = $(".qv-swiper .swiper-slide").first();

        mainSlide.find("img").attr("src", newImage);
        mainSlide.find("img").attr("srcset", newImage);

        // Make active dot
        $(this).addClass("active").siblings().removeClass("active");
    });

});
jQuery(function($) {

    $(document).on("click", ".qv-add-to-cart", function (e) {
        e.preventDefault();

        let pid = $(this).data("product-id");
        let qty = $(this).closest(".cart").find("input.qty").val() || 1;

        $.ajax({
            type: "POST",
            url: wc_add_to_cart_params.wc_ajax_url.replace("%%endpoint%%", "add_to_cart"),
            data: {
                product_id: pid,
                quantity: qty,
            },
            success: function (res) {
                if (!res || !res.fragments) return;

                // Update header cart fragments
                $.each(res.fragments, function (key, value) {
                    $(key).replaceWith(value);
                });

                // Optional success message
                $(".qv-add-to-cart").text("Added ✓").addClass("added");

            }
        });

    });

});


(function ($) {
    'use strict';

    // ---------- Config / cached globals ----------
    var ajax = {
        wc: window.ascora_wc || {},
        ajax_url: (window.ascora_wc && ascora_wc.ajax_url) ? ascora_wc.ajax_url : (window.ajaxurl || '/wp-admin/admin-ajax.php')
    };

    var state = {
        gallerySwiper: null
    };

    // ---------- Helpers ----------
    function safeLog() {
        if (window.console && console.log) console.log.apply(console, arguments);
    }

    function ensureQtyButtons(context) {
        context = context || document;
        $(context).find('form.cart, form.variations_form').each(function () {
            var $form = $(this);
            var $qty = $form.find('input.qty').first();
            if (!$qty.length) return;

            if ($qty.closest('.asp-qty-wrapper').length) return; // already wrapped

            var $wrapper = $('<div class="asp-qty-wrapper"></div>');
            var $minus = $('<button type="button" class="qty-btn qty-minus" aria-label="Decrease quantity">-</button>');
            var $plus = $('<button type="button" class="qty-btn qty-plus" aria-label="Increase quantity">+</button>');

            $qty.wrap($wrapper);
            $qty.before($minus);
            $qty.after($plus);
        });
    }

    function initGallerySwiper() {
        if (typeof Swiper === 'undefined') return;

        // destroy old
        if (state.gallerySwiper && state.gallerySwiper.destroy) {
            try { state.gallerySwiper.destroy(true, true); } catch (e) { }
            state.gallerySwiper = null;
        }

        // init new only if slider exists
        var $main = $('.asp-gallery-swiper');
        var $thumbs = $('.asp-thumbs-swiper');

        if (!$main.length) return;

        var thumbsSwiper = null;
        if ($thumbs.length && typeof Swiper !== 'undefined') {
            thumbsSwiper = new Swiper('.asp-thumbs-swiper', {
                spaceBetween: 10,
                slidesPerView: Math.min(5, $thumbs.find('.swiper-slide').length),
                watchSlidesProgress: true,
                breakpoints: { 480: { slidesPerView: 3 }, 768: { slidesPerView: 5 } }
            });
        }

        state.gallerySwiper = new Swiper('.asp-gallery-swiper', {
            loop: true,
            speed: 600,
            spaceBetween: 10,
            navigation: { nextEl: '.asp-next', prevEl: '.asp-prev' },
            pagination: { el: '.asp-pagination', clickable: true },
            thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined
        });

        // expose globally for custom handlers
        window.aspGallerySwiper = state.gallerySwiper;
    }

    function updateMainImageByUrl(imgUrl) {
        if (!imgUrl) return;
        if (state.gallerySwiper) {
            // find slide with same src
            var found = -1;
            $('.asp-gallery-swiper .swiper-slide img').each(function (i) {
                if ($(this).attr('src') === imgUrl) { found = i; return false; }
            });
            if (found >= 0) {
                state.gallerySwiper.slideToLoop(found, 400);
                return;
            }
            // fallback: replace active image
            var $active = $('.asp-gallery-swiper .swiper-slide-active img');
            if ($active.length) $active.attr('src', imgUrl);
        } else {
            // fallback single img
            $('.woocommerce-product-gallery__image img, .asp-main-img').first().attr('src', imgUrl);
        }
    }

    function bindGlobalEvents() {

        // ---------- Mini cart open/close & ajax refresh ----------
        $(document).on('click', '#ascora-cart-trigger', function (e) {
            e.stopPropagation();
            $('#ascora-slide-cart, #ascora-slide-overlay').addClass('active');
            $('#ascora-mini-cart-wrapper').html('<div class="ascora-loading">Loading...</div>');
            refreshMiniCart();
        });

        $(document).on('click', '.ascora-slide-close, #ascora-slide-overlay', function () {
            $('#ascora-slide-cart, #ascora-slide-overlay').removeClass('active');
        });

        $(document).on('click', 'body', function (e) {
            if (!$(e.target).closest('#ascora-slide-cart, #ascora-cart-trigger').length) {
                $('#ascora-slide-cart, #ascora-slide-overlay').removeClass('active');
            }
        });

        $(document).on('click', '.mini-remove', function (e) {
            e.preventDefault();
            var key = $(this).data('key');
            $(this).text('Removing...');
            $.post(ajax.wc.ajax_url, { action: 'ascora_remove_cart_item', cart_item_key: key }, function (res) {
                $('#ascora-mini-cart-wrapper').html(res);
                updateCartCount();
            });
        });

        // mini qty plus/minus (server-side handled)
        $(document).on('click', '.mini-qty.plus, .mini-qty.minus', function () {
            var wrapper = $(this).closest('.mini-qty-wrapper');
            var key = wrapper.data('key');
            var change = $(this).hasClass('plus') ? 'plus' : 'minus';
            $.post(ajax.wc.ajax_url, { action: 'ascora_update_qty', cart_item_key: key, change: change }, function (res) {
                $('#ascora-mini-cart-wrapper').html(res);
                updateCartCount();
            });
        });

        // sync mini cart on WC events
        $(document.body).on('added_to_cart wc_fragments_refreshed wc_fragments_loaded', function () {
            refreshMiniCart();
            updateCartCount();
        });

        // ensure add_to_cart triggers mini cart refresh
        $(document).on('click', '.add_to_cart_button', function () {
            setTimeout(function () {
                refreshMiniCart();
                updateCartCount();
            }, 500);
        });


        // ---------- View switch (grid/list) ----------
        $(document).on('click', '.ascora-view-switch a', function (e) {
            e.preventDefault();
            var view = $(this).data('view');
            $('#ascora-products').removeClass('grid-view list-view').addClass(view);
            $('.ascora-view-switch a').removeClass('active');
            $(this).addClass('active');
        });


        // ---------- Quick view (ajax) ----------
        $(document).on('click', '.quick-view', function (e) {
            e.preventDefault();
            var pid = $(this).data('product-id');
            $('#ascora-quick-view-modal .ascora-qv-body').html('<p>Loading...</p>');
            $('#ascora-quick-view-modal').fadeIn();
            $.post(ajax.wc.ajax_url, { action: 'ascora_quick_view', product_id: pid }, function (response) {
                $('#ascora-quick-view-modal .ascora-qv-body').html(response);
                // re-init components for ajax-loaded content
                $(document).trigger('ascora_qv_loaded');      // legacy event listeners
                initGallerySwiper();                          // init swiper for qv if present
                ensureQtyButtons($('#ascora-quick-view-modal'));
            });
        });

        $(document).on('click', '.ascora-qv-close, .ascora-qv-modal', function (e) {
            if ($(e.target).is('.ascora-qv-modal, .ascora-qv-close')) {
                $('#ascora-quick-view-modal').fadeOut();
            }
        });


        // ---------- Wishlist actions ----------


        // ---------- Sorting (ajax) ----------
        $(document).on('change', '#ascora-sort', function () {
            var sort = $(this).val();
            $.post(ajax.wc.ajax_url, { action: 'ascora_sort_products', sort: sort }, function (response) {
                $('#ascora-products').html(response).removeClass('loading');
            }).fail(function () {
                $('#ascora-products').removeClass('loading');
            });
            $('#ascora-products').addClass('loading');
        });


        // ---------- Product card: quick actions (events delegated) ----------
        $(document).on('click', '.ascora-card .action-btn.quick-view', function (e) {
            e.preventDefault();
            var pid = $(this).data('product-id');
            $(document).trigger('ascora:quickview', [pid]);
        });

        $(document).on('click', '.ascora-card .action-btn.compare-btn', function (e) {
            e.preventDefault();
            var pid = $(this).data('product-id');
            $(document).trigger('ascora:compare', [pid]);
        });

        $(document).on('click', '.ascora-card .wishlist-icon', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var id = $btn.data('id');
            $btn.toggleClass('active');
            $(document).trigger('ascora:wishlist:toggle', [id, $btn.hasClass('active')]);
        });

        // ---------- Color-dot (loop + single) ----------
        $(document).on('click', '.ascora-color-variations .color-dot', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $dot = $(this);
            var color = $dot.data('color') || '';
            var image = $dot.data('image') || '';

            // visual
            $dot.addClass('active').siblings().removeClass('active');

            // try set select (single product)
            var $form = $dot.closest('form.variations, .product, .asp-right, body');
            var $colorSelect = $form.find('select[name="attribute_pa_color"]').first();
            if ($colorSelect && $colorSelect.length) {
                $colorSelect.val(color).trigger('change').trigger('woocommerce_variation_select_change');
            }

            // update gallery/main image
            if (image) updateMainImageByUrl(image);
        });

        // hover change for single product small UX (non-destructive)
        $(document).on('mouseenter', '.ascora-color-variations .color-dot', function () {
            var image = $(this).data('image') || '';
            if (image) $('.woocommerce-product-gallery__image img, .asp-main-img').first().attr('src', image);
        });
    }

    // ---------- AJAX helpers ----------
    function refreshMiniCart() {
        $('#ascora-mini-cart-wrapper').addClass('loading');
        $.post(ajax.wc.ajax_url, { action: 'ascora_load_mini_cart' }, function (res) {
            $('#ascora-mini-cart-wrapper').removeClass('loading').html(res);
        });
    }

    function updateCartCount() {
        $.post(ajax.wc.ajax_url, { action: 'ascora_get_cart_count' }, function (res) {
            if (res && typeof res.count !== 'undefined') {
                $('.ascora-cart-count').text(res.count);
            }
        });
    }


    // ---------- Single product page init (swiper, thumbs, tabs, qty buttons, zoom) ----------
    function initSingleProduct(context) {
        context = context || document;

        // swiper init
        initGallerySwiper();

        // thumbs -> slide
        $(context).find('.asp-thumb-item').off('click.asp').on('click.asp', function () {
            var idx = $(this).index();
            if (state.gallerySwiper) state.gallerySwiper.slideToLoop(idx, 600);
        });

        // tabs
        $(context).find('.asp-tab-buttons .tab-btn').off('click.asp').on('click.asp', function () {
            var tab = $(this).data('tab');
            $(context).find('.asp-tab-buttons .tab-btn').removeClass('active');
            $(this).addClass('active');
            $(context).find('.asp-tab-panels .tab-panel').removeClass('active');
            $(context).find('#' + tab).addClass('active');
        });

        // ensure qty buttons exist in this context
        ensureQtyButtons(context);

        // qty plus/minus handled globally (delegation already present)

        // zoom effect (directional) for each .asp-zoom-wrapper
        $(context).find('.asp-zoom-wrapper').each(function () {
            var $wrap = $(this);
            var $img = $wrap.find('.asp-main-img');

            // remove previous handlers to avoid duplicates
            $wrap.off('mousemove.asp zoom.leave').on('mousemove.asp', function (e) {
                var rect = $wrap[0].getBoundingClientRect();
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                var xPct = (x / rect.width) * 100;
                var yPct = (y / rect.height) * 100;
                $img.css({ 'transform-origin': xPct + '% ' + yPct + '%', 'transform': 'scale(2)' });
            }).on('mouseleave.asp', function () {
                $img.css({ 'transform-origin': 'center center', 'transform': 'scale(1)' });
            });
        });
    }

    // ---------- Document ready ----------
    $(function () {
        bindGlobalEvents();
        initSingleProduct(document);

        // Ensure qty buttons after initial DOM
        ensureQtyButtons();

        // Re-init on ajax complete (useful for quick view, fragments)
        $(document).ajaxComplete(function (event, xhr, settings) {
            // run init routines for newly inserted content
            initSingleProduct(document);
            ensureQtyButtons(document);
        });

        // If quick-view custom event usage
        $(document).on('ascora_qv_loaded', function () {
            initSingleProduct($('#ascora-quick-view-modal')[0]);
        });
    });

})(jQuery);

// Trigger Swiper init after Quick View content loaded
jQuery(document).on('ascora_qv_loaded', function(){
    var qvSwiper = new Swiper('.ascora-qv-wrapper .qv-swiper', {
        slidesPerView: 1,
        loop: true,
        navigation: {
            nextEl: '.qv-swiper-button-next',
            prevEl: '.qv-swiper-button-prev',
        },
        pagination: {
            el: '.qv-swiper-pagination',
            clickable: true,
        },
    });
});
