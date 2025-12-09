<?php


defined('ABSPATH') || exit;
?>

<div class="ascora-qv-wrapper">

    <div class="qv-left">

        <!-- Main Swiper -->
        <div class="swiper qv-swiper">
            <div class="swiper-wrapper">
                <?php
                // Main image
                echo '<div class="swiper-slide qv-gallery-item">' . $product->get_image('large') . '</div>';

// Gallery images
$attachment_ids = $product->get_gallery_image_ids();
if ($attachment_ids) :
    foreach ($attachment_ids as $img_id) {
        echo '<div class="swiper-slide qv-gallery-item">' . wp_get_attachment_image($img_id, 'large') . '</div>';
    }
endif;
?>
            </div>
            <!-- Pagination -->
            <div class="qv-swiper-pagination"></div>
            <!-- Navigation buttons -->
            <div class="qv-swiper-button-prev swiper-button-prev"></div>
            <div class="qv-swiper-button-next swiper-button-next"></div>
        </div>

    </div>


    <div class="qv-right">

        <!-- === PRODUCT TITLE === -->
        <h3><a
                href="<?php echo get_permalink($product->get_id()); ?>">
                <?php echo $product->get_name(); ?>
            </a></h3>

        <!-- === PRODUCT BRAND (if exists) === -->
        <div class="qv-brand">
            <?php
// Example: brand taxonomy = 'product_brand'
$brand = wp_get_post_terms($product->get_id(), 'product_brand');
if (!empty($brand)) {
    echo '<strong>Brand:</strong> ' . esc_html($brand[0]->name);
}
?>
        </div>
        <?php
if ($product->is_type('variable')) {
    $attributes = $product->get_variation_attributes();

    if (isset($attributes['pa_color'])) {
        echo '<div class="ascora-color-variations">';

        // All variation IDs
        $variations = $product->get_children();

        foreach ($attributes['pa_color'] as $color_slug) {
            $term = get_term_by('slug', $color_slug, 'pa_color');
            if (!$term) {
                continue;
            }

            // Get Color HEX stored in term meta
            $color_code = get_term_meta($term->term_id, 'term_color_code', true);
            if (!$color_code) {
                $color_code = '#cccccc';
            }

            // Default image fallback
            $variation_image = '';

            // Match variation by color attribute
            foreach ($variations as $variation_id) {
                $variation = wc_get_product($variation_id);
                $attrs     = $variation->get_attributes();

                if (isset($attrs['pa_color']) && $attrs['pa_color'] === $color_slug) {
                    $variation_image = wp_get_attachment_image_url(
                        $variation->get_image_id(),
                        'woocommerce_thumbnail'
                    );

                    break;
                }
            }

            echo '<span class="color-dot"
                    data-image="' . esc_url($variation_image) . '"
                    style="background-color:' . esc_attr($color_code) . ';"
                    title="' . esc_attr($term->name) . '">
                  </span>';
        }

        echo '</div>';
    }
}
?>
        <!-- === PRICE === -->
        <div class="price">
            <?php echo $product->get_price_html(); ?>
        </div>

        <!-- === SHORT DESCRIPTION === -->
        <div class="qv-desc">
            <?php echo wpautop($product->get_short_description()); ?>
        </div>

        <!-- === QUANTITY + ADD TO CART === -->
        <div class="qv-cart qv-cart-form">
            <form class="cart"
                action="<?php echo esc_url($product->add_to_cart_url()); ?>"
                method="post" enctype="multipart/form-data">

                <?php
        // Quantity box
        woocommerce_quantity_input([
            'min_value'   => 1,
            'max_value'   => $product->get_max_purchase_quantity(),
            'input_value' => 1,
        ]);

?>
                <!-- Add to cart button -->
                <button type="button" class="single_add_to_cart_button button alt qv-add-to-cart"
                    data-product-id="<?php echo $product->get_id(); ?>">
                    <?php echo esc_html($product->single_add_to_cart_text()); ?>
                </button>


                <input type="hidden" name="add-to-cart"
                    value="<?php echo $product->get_id(); ?>">
            </form>
        </div>


        <!-- === FOOTER AREA === -->
        <div class="quick-view-footer">

            <!-- Product SKU -->
            <div class="qv-sku">
                <strong>SKU:</strong>
                <?php echo $product->get_sku() ? $product->get_sku() : 'N/A'; ?>
            </div>

            <!-- Categories -->
            <div class="product-category">
                <strong>Category:</strong>
                <?php echo wp_kses_post(wc_get_product_category_list($product->get_id(), ', ')); ?>
            </div>

        </div>

    </div>

</div>