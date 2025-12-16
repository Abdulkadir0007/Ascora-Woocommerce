<?php
/**
 * Single Product layout & features
 */

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

/**
 * New order
 */
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 15);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 25);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 35);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 45);

/**
 * Sticky add-to-cart bar
 */
add_action('wp_footer', 'ascora_wc_sticky_cart');
function ascora_wc_sticky_cart()
{
    if (! is_product()) {
        return;
    }
    global $product;
    ?>
<div id="ascora-sticky-bar">
    <img
        src="<?php echo esc_url(wp_get_attachment_image_url($product->get_image_id(), 'thumbnail')); ?>" />
    <span
        class="title"><?php echo esc_html($product->get_name()); ?></span>
    <span
        class="price"><?php echo $product->get_price_html(); ?></span>
    <?php woocommerce_template_loop_add_to_cart(); ?>
</div>
<?php
}
