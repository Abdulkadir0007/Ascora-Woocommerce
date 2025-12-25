/**
 * ASCORA MAIN JS (Optimized / Clean / Conflict-Free)
 * Requires: jQuery, Swiper, WooCommerce ajax
 */






jQuery(function ($) {

    /* -----------------------------------------------------
     *  WISHLIST TOGGLE + REMOVE
     * ----------------------------------------------------- */
    $(document).on("click", ".ascora-wl-btn, .wishlist-icon", function (e) {

        if ($(e.target).closest(".wl-remove").length) return;

        e.preventDefault();

        let $btn = $(this);
        let pid = $btn.data("id");

        $.post(ascora_ajax.ajax_url, {
            action: "ascora_wishlist_toggle",
            product_id: pid
        }, function (res) {

            if (!res?.success) return;

            $btn.toggleClass("active");
            $btn.find("i").toggleClass("fa-heart fa-heart");

            $(".wishlist-count, .ascora-wishlist-icon .count").text(res.data.count);

            if ($("body").hasClass("wishlist-page")) {
                $(".ascora-wishlist-item[data-id='" + pid + "']")
                    .fadeOut(250, function () {
                        $(this).remove();

                        if (!$(".ascora-wishlist-item").length) location.reload();
                    });
            }
        });
    });

$(document).on("click", ".wl-remove", function (e) {
    e.preventDefault();

    const $btn   = $(this);
    const $item  = $btn.closest(".ascora-wishlist-item");
    const pid    = $btn.data("id");

    // ইতিমধ্যে removing হলে রিটার্ন
    if ($item.hasClass("removing")) return;

    $item.addClass('removing')
            .find('.loading-gif')
            .removeClass('hidden');           // visual flag
    $btn.prop("disabled", true);         // দ্বিতীয় ক্লিক ব্লক

    $.post(ascora_ajax.ajax_url, {
        action: "ascora_wishlist_toggle",
        product_id: pid
    }, function (res) {
        if (!res?.success) {
            $item.removeClass("removing");
            $btn.prop("disabled", false);
            return;
        }

        $item.fadeOut(250, function () {
            $(this).remove();
            $(".wishlist-count, .ascora-wishlist-icon .count").text(res.data.count);
            if (!$(".ascora-wishlist-item").length) location.reload();
        });
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
                const $form = $(this);
                const $qty = $form.find('input.qty').first();

                if (!$qty.length) return;
                if ($qty.parent().hasClass('asp-qty-wrapper')) return; // already wrapped

                const $wrapper = $('<div class="asp-qty-wrapper"></div>');
                const $minus = $('<button type="button" class="qty-btn qty-minus" aria-label="Decrease quantity">-</button>');
                const $plus  = $('<button type="button" class="qty-btn qty-plus" aria-label="Increase quantity">+</button>');

                $qty.wrap($wrapper);
                $qty.before($minus);
                $qty.after($plus);
            });
        }
        jQuery(function($){
    ensureQtyButtons(); // init on page load

    // If product loaded via AJAX (Quick View / variable product reload)
    $(document).on('woocommerce_update_variation_values', function(e, $form) {
        ensureQtyButtons($form);
    });
});
$(document).on('click', '.qty-btn', function() {
    const $btn = $(this);
    const $input = $btn.siblings('input.qty');
    let val = parseInt($input.val()) || 0;

    if ($btn.hasClass('qty-plus')) {
        val++;
    } else if ($btn.hasClass('qty-minus')) {
        val = Math.max(val - 1, 1); // minimum 1
    }

    $input.val(val).trigger('change');
});



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



        // ---------- COLOR DOT CLICK (Loop + Single Product) ----------

jQuery(function ($) {

    function updateLoopImage($dot, imgUrl) {
        // Find the shared ancestor
        const $media = $dot.closest('.product-media');

        // Primary image
        const $imgMain = $media.find('.product-img-main img').first();

        // Optional: secondary hover image
        const $imgHover = $media.find('.product-img-hover img').first();

        if ($imgMain.length && imgUrl) {
            $imgMain.attr('src', imgUrl).removeAttr('srcset');
        } else {
            console.warn('Primary image not found!', $media);
        }

        if ($imgHover.length && imgUrl) {
            $imgHover.attr('src', imgUrl).removeAttr('srcset');
        }
    }



// CLICK
$(document).on('click', '.ascora-shop-color-variations .color-dot', function (e) {
    e.preventDefault();

    const $dot = $(this);
    const image = $dot.data('image');

    $dot.addClass('active').siblings().removeClass('active');
    updateLoopImage($dot, image);
});

// HOVER preview
$(document).on('mouseenter', '.ascora-shop-color-variations .color-dot', function () {
    updateLoopImage($(this), $(this).data('image'));
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

    });
       
}


function updateQVImage($dot, imgUrl) {
    // Find the closest Quick View wrapper
    const $qvWrapper = $dot.closest('.ascora-qv-wrapper');

    // Main Swiper slide (primary image)
    const $mainSlide = $qvWrapper.find('.qv-swiper .swiper-slide.qv-gallery-item').first();
    const $mainImg = $mainSlide.find('img').first();

    if ($mainImg.length && imgUrl) {
        $mainImg.attr('src', imgUrl).removeAttr('srcset');
    } else {
        console.warn('QV main image not found!', $qvWrapper);
    }

    // Optional: update all Swiper slides to match the selected color
    // Uncomment if you want every slide to change
    /*
    $qvWrapper.find('.qv-swiper .swiper-slide.qv-gallery-item img').each(function () {
        $(this).attr('src', imgUrl).removeAttr('srcset');
    });
    */
}
// CLICK on color dot in Quick View
$(document).on('click', '.ascora-qv-wrapper .ascora-shop-color-variations .color-dot', function (e) {
    e.preventDefault();

    const $dot = $(this);
    const image = $dot.data('image');

    $dot.addClass('active').siblings().removeClass('active');
    updateQVImage($dot, image);
});

// HOVER preview
$(document).on('mouseenter', '.ascora-qv-wrapper .ascora-shop-color-variations .color-dot', function () {
    updateQVImage($(this), $(this).data('image'));
});


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



/* ------------------------------------------------------------------
 * Quick-View Swiper – একবারই, DOM ঢোকার পর
 * ------------------------------------------------------------------ */
jQuery(document).on('ascora_qv_loaded', function () {
    const $slider = jQuery('.qv-swiper');
    if (!$slider.length) return;

    $slider.each(function () {
        new Swiper(this, {
            slidesPerView : 1,
            loop          : true,
            navigation    : {
                nextEl : '.qv-swiper-button-next',
                prevEl : '.qv-swiper-button-prev',
            },
            pagination    : {
                el        : '.qv-swiper-pagination',
                clickable : true,
            },
            autoplay      : false,
        });
    });
});


// Ascora Filter
// Ascora Filter + AJAX Pagination
jQuery(function ($) {

    let isFiltering = false;
    const shopBaseUrl = ascora_shop.shop_url.endsWith('/')
    ? ascora_shop.shop_url
    : ascora_shop.shop_url + '/';


    /* -------------------------
     * UPDATE URL (FILTER / NORMAL)
     * ------------------------- */
    function updateURL(page = 1) {

        if (isFiltering) {
            let min = $('#ascora-price-min').val() || 0;
            let max = $('#ascora-price-max').val() || 0;
        // 🔥 always start from CLEAN shop base URL
    let baseUrl = ascora_shop.shop_url;
    let url = new URL(baseUrl);
            url.searchParams.set('min_price', min);
            url.searchParams.set('max_price', max);
            url.searchParams.set('paged', page);

            window.history.pushState({}, '', url);

        } else {
            let cleanUrl = page > 1
                ? shopBaseUrl + 'page/' + page + '/'
                : shopBaseUrl;

            window.history.pushState({}, '', cleanUrl);
        }
    }

    /* -------------------------
     * LOAD PRODUCTS (AJAX)
     * ------------------------- */
    function loadProducts(page = 1) {

        let min = $('#ascora-price-min').val() || 0;
        let max = $('#ascora-price-max').val() || 0;

        $('#ascora-products').addClass('loading');

        $.post(ascora_ajax.ajax_url, {
            action: 'ascora_filter_price',
            min_price: min,
            max_price: max,
            paged: page
        }, function (res) {

            $('#ascora-products').removeClass('loading');

            if (res.success) {
                $('#ascora-products').html(res.data.products);
                $('#ascora-pagination').html(res.data.pagination);

                updateURL(page);
            }
        });
    }

    /* -------------------------
     * PRICE SLIDER INIT (SAFE)
     * ------------------------- */
    function initPriceSlider() {

        let $slider = $('#ascora-price-slider');
        if (!$slider.length) return;

        if ($slider.hasClass('ui-slider')) return;

        let min = parseInt($('#ascora-price-min').val()) || 0;
        let max = parseInt($('#ascora-price-max').val()) || 0;

        $slider.slider({
            range: true,
            min: 0,
            max: max,
            values: [min, max],
            slide: function (event, ui) {

                $('#ascora-price-min').val(ui.values[0]);
                $('#ascora-price-max').val(ui.values[1]);

                $('#ascora-price-min-val').text('$ ' +ui.values[0]);
                $('#ascora-price-max-val').text('$ '+ui.values[1]);
            }
        });
    }

    /* -------------------------
     * APPLY FILTER
     * ------------------------- */
    $(document).on('click', '#ascora-price-apply', function (e) {
        e.preventDefault();
        isFiltering = true;
         $("#ascora-filter-reset").fadeIn(150); // 👈 reset show
          // 👇 smooth scroll to #ascora-products top
    $('html, body').animate({
        scrollTop: $('#ascora-products').offset().top
    }, 600);
        loadProducts(1);
    });

    /* -------------------------
     * AJAX PAGINATION (HARD STOP)
     * ------------------------- */
    $('body').on(
        'click',
        '.woocommerce-pagination a, #ascora-pagination a',
        function (e) {

            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();

            let href = this.getAttribute('href');
            let page = 1;

            if (href && href.includes('paged=')) {
                page = href.split('paged=')[1];
            } else if (href && href.match(/page\/(\d+)/)) {
                page = href.match(/page\/(\d+)/)[1];
            }

            loadProducts(page);

            return false; // 🔥 IMPORTANT
        }
    );

    /* -------------------------
     * READ FILTER FROM URL (ON LOAD)
     * ------------------------- */
    function getUrlParam(name) {
        return new URLSearchParams(window.location.search).get(name);
    }

    let minFromUrl = getUrlParam('min_price');
    let maxFromUrl = getUrlParam('max_price');

    if (minFromUrl !== null && maxFromUrl !== null) {
        $('#ascora-price-min').val(minFromUrl);
        $('#ascora-price-max').val(maxFromUrl);
        isFiltering = true;
    }

    initPriceSlider();





// Filter reset Button
// RESET FILTER
$(document).on("click", "#ascora-filter-reset", function (e) {
    e.preventDefault();

    isFiltering = false;

    let minDefault = parseInt($("#ascora-price-min").attr("min")) || 0;
    let maxDefault = parseInt($("#ascora-price-max").attr("max")) || 0;

    // reset input values
    $("#ascora-price-min").val(minDefault);
    $("#ascora-price-max").val(maxDefault);

    $("#ascora-price-min-val").text(minDefault);
    $("#ascora-price-max-val").text(maxDefault);

    // reset slider
    if ($("#ascora-price-slider").hasClass("ui-slider")) {
        $("#ascora-price-slider").slider("values", [minDefault, maxDefault]);
    }

    $("#ascora-products").addClass("loading");

    $.post(ascora_ajax.ajax_url, {
        action: "ascora_filter_price",
        min_price: minDefault,
        max_price: maxDefault,
        paged: 1
    }, function (res) {

        $("#ascora-products").removeClass("loading");

        if (res.success) {
            $("#ascora-products").html(res.data.products);
            $("#ascora-pagination").html(res.data.pagination);
        }

        // 🔥 CLEAN URL (remove /page/x/ + params)
        let cleanUrl = ascora_shop.shop_url;
        window.history.pushState({}, "", cleanUrl);

        // hide reset button
        $("#ascora-filter-reset").fadeOut(150);
    });
});
});


jQuery(function ($) {
    $('#product_cat').on('change', function () {
        let text = $(this).find('option:selected').text();
        $('.ascora-selected').text(text);
    });
});


(function($){
  $(document).ready(function(){
    $('.ascora-woo-header .main-menu').hcOffcanvasNav({
        disableAt: 99999,
        insertBack: true,
        labelClose: 'Close',
        labelBack: 'Back',
         levelOpen:'expand',
        levelTitleAsBack: true
    });
  });
})(jQuery);

jQuery(function($){
    $('.ascora-cat-list > li').hover(
        function(){ $(this).addClass('open'); },
        function(){ $(this).removeClass('open'); }
    );
});
