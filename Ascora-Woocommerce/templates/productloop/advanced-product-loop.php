<?php
/**
 * Safe Advanced Product Loop (Ascora)
 */

defined('ABSPATH') || exit;

global $product;

if (empty($product) || ! $product->is_visible()) {
    return;
}
?>

<li <?php wc_product_class('ascora-product-box advanced-product-card', $product); ?>>

    <div class="product-wrapper">

        <!-- Badges -->
        <div class="product-badges">
            <?php if ($product->is_on_sale()) : ?>
            <span class="badge sale">
                <?php echo esc_html__('Sale', 'ascora'); ?>
            </span>
            <?php endif; ?>

            <?php if (! $product->is_in_stock()) : ?>
            <span class="badge out-of-stock">
                <?php echo esc_html__('Out of Stock', 'ascora'); ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- Image -->
        <a class="product-image-wrapper"
            href="<?php the_permalink(); ?>">
            <?php echo $product->get_image('woocommerce_thumbnail'); ?>

            <?php
            $gallery = $product->get_gallery_image_ids();
if (! empty($gallery)) {
    echo wp_get_attachment_image($gallery[0], 'woocommerce_thumbnail', false, [ 'class' => 'hover-image' ]);
}
?>
        </a>

        <!-- Info -->
        <div class="product-info">

            <h3 class="product-title">
                <a
                    href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>

            <div class="product-rating">
                <?php echo wc_get_rating_html($product->get_average_rating()); ?>
            </div>

            <div class="product-price">
                <?php echo $product->get_price_html(); ?>
            </div>

            <div class="product-actions">
                <?php woocommerce_template_loop_add_to_cart(); ?>
            </div>

        </div>

    </div>

</li>