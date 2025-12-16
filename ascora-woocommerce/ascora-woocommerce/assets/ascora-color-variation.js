


    // ---------- COLOR DOT CLICK (Loop + Single Product) ----------

jQuery(function ($) {

    function updateMainImage(imgUrl) {
        const $img = $('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image img').first();

        if ($img.length) {
            $img.attr('src', imgUrl);
            $img.attr('srcset', imgUrl);
        }
    }



    // CLICK — select + image update
    $(document).on('click', '.ascora-color-variations .color-dot', function (e) {
        e.preventDefault();

        var $dot = $(this);
        var colorSlug = $dot.data('color');
        var image = $dot.data('image');

        // UI highlight
        $dot.addClass('active').siblings().removeClass('active');

        // Find the correct dropdown
        var $form = $dot.closest('form.variations');
        var $select = $form.find('select[name="attribute_pa_color"]');

        if ($select.length) {
            $select.val(colorSlug).trigger('change')
                .trigger('woocommerce_variation_select_change')
                .trigger('check_variations');
        }

        if (image) {
            updateMainImage(image);
        }
    });

    // HOVER preview
    $(document).on('mouseenter', '.ascora-color-variations .color-dot', function () {
        var img = $(this).data('image');
        if (img) {
            updateMainImage(img);
        }
    });

    // Restore active image
    $(document).on('mouseleave', '.ascora-color-variations .color-dot', function () {
        var activeImg = $(this)
            .closest('.ascora-color-variations')
            .find('.color-dot.active')
            .data('image');

        if (activeImg) {
            updateMainImage(activeImg);
        }
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
