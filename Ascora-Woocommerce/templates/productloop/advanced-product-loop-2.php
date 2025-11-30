<?php
/**
 * Advanced Product Loop Item (Ascora Pro Style)
 *
 * @package Ascora
 * @since 2.0.0
 */

defined('ABSPATH') || exit;

global $product;

if (empty($product) || ! $product->is_visible()) {
    return;
}

?>
<li <?php wc_product_class('ascora-product-box advanced-product-loop', $product); ?>>

    <div class="product-thumbnail">

        <a href="<?php the_permalink(); ?>">

            <!-- Primary Image -->
            <div class="product-img-main">
                <?php echo wp_kses_post($product->get_image('woocommerce_thumbnail')); ?>
            </div>

            <!-- Secondary Hover Image -->
            <?php
            $attachment_ids = $product->get_gallery_image_ids();
if (isset($attachment_ids[0])) :
    ?>
            <div class="product-img-hover">
                <?php echo wp_get_attachment_image($attachment_ids[0], 'woocommerce_thumbnail'); ?>
            </div>
            <?php endif; ?>

        </a>

        <!-- Badges -->
        <div class="product-badges">

            <?php if ($product->is_on_sale()) : ?>
            <span class="badge sale-badge">
                <?php echo esc_html__('Sale', 'ascora'); ?>
            </span>
            <?php endif; ?>

            <?php if ($product->is_featured()) : ?>
            <span class="badge featured-badge">
                <?php echo esc_html__('Featured', 'ascora'); ?>
            </span>
            <?php endif; ?>

            <!-- Sale percentage -->
            <?php
            if ($product->is_on_sale() && $product->get_regular_price()) :
                $percent = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                ?>
            <span class="badge discount-badge">
                -<?php echo esc_html($percent); ?>%
            </span>
            <?php endif; ?>

        </div>

        <!-- Action Buttons (Wishlist / Quick View / Compare etc.) -->
        <div class="product-actions">
            <button class="action-btn quick-view"
                data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                <i class="fas fa-eye"></i>
            </button>

            <?php if (function_exists('YITH_WCWL')) : ?>
            <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
            <?php endif; ?>
        </div>

    </div>

    <!-- Content -->
    <div class="product-content">

        <!-- Title -->
        <h3 class="product-title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h3>

        <!-- Rating -->
        <div class="product-rating">
            <?php echo wc_get_rating_html($product->get_average_rating()); ?>
        </div>

        <!-- Price -->
        <div class="product-price">
            <?php echo wp_kses_post($product->get_price_html()); ?>
        </div>

        <!-- Add to cart -->
        <div class="add-cart-btn">
            <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>

    </div>

</li>