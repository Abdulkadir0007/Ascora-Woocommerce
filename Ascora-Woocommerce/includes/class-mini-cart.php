<?php
/**
 * Plugin Name: Ascora Mini Cart (Slide Out)
 * Description: WooCommerce-এর জন্য একটি শর্টকোড-ভিত্তিক স্লাইড-আউট মিনি-কার্ট যোগ করে।
 * Version: 1.0.1
 * Author: Gemini
 * Requires at least: 5.0
 * Requires PHP: 7.0
 */

if (!defined('ABSPATH')) {
    exit; // Direct access blocked
}

class Ascora_Slide_Mini_Cart
{
    public function __construct()
    {
        // Ensure WooCommerce is active
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            add_shortcode('ascora_mini_cart', [$this, 'render_mini_cart_shortcode']);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_mini_cart_assets']);
            add_filter('woocommerce_add_to_cart_fragments', [$this, 'mini_cart_fragment']);
            add_filter('woocommerce_add_to_cart_fragments', [$this, 'cart_count_fragment']);
        }
    }

    /**
     * Renders the mini cart panel and icon.
     */
    public function render_mini_cart_shortcode()
    {
        if (did_action('ascora_mini_cart_rendered')) {
            return '';
        }

        ob_start(); ?>

<!-- Cart Icon Wrapper -->
<div id="ascora-mini-cart-wrapper" class="ascora-mini-cart-wrapper">
    <a href="<?php echo esc_url(wc_get_cart_url()); ?>"
        class="ascora-cart-icon-toggle"
        title="<?php esc_attr_e('View Cart', 'ascora'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24px" height="24px">
            <path d="M17 18a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM1 1h4l2.68 12.87a2 2 0 0 0 2 1.63h9.72a2 2 0 0 0 2-1.63L23 6H6"
                fill="none" />
            <path
                d="M16 13c-1.11 0-2 .89-2 2a2 2 0 1 0 4 0c0-1.11-.89-2-2-2Zm-7.17-2h10.34l.79-3h-13.42l-.46-2H3V4h2.17l.66 3.19L3 14v2h2v-1h13.03l-1.42-3H9.17ZM6 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM17 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" />
        </svg>
        <span class="ascora-cart-count" id="ascora-cart-count-fragment">
            <?php echo WC()->cart->get_cart_contents_count(); ?>
        </span>
    </a>
</div>

<!-- Slide-Out Mini Cart Panel -->
<div id="ascora-mini-cart-panel" class="ascora-mini-cart-panel ascora-hidden">
    <div class="panel-header">
        <h3><?php _e('Cart', 'ascora'); ?>
        </h3>
        <button class="close-btn"
            aria-label="<?php esc_attr_e('Close Cart', 'ascora'); ?>">&times;</button>
    </div>

    <div class="woocommerce-mini-cart-content-wrap">
        <?php woocommerce_mini_cart(); ?>
    </div>

    <?php
            $cart_is_empty   = WC()->cart->is_empty();
        $footer_style        = $cart_is_empty ? 'display: none;' : '';
        $empty_message_style = $cart_is_empty ? 'display: block;' : 'display: none;';
        ?>
    <p class="woocommerce-mini-cart__empty-message"
        style="<?php echo $empty_message_style; ?>">
        <?php _e('Your cart is currently empty.', 'ascora'); ?>
    </p>

    <div class="panel-footer" style="<?php echo $footer_style; ?>">
        <p class="woocommerce-mini-cart__total total">
            <strong><?php _e('Total:', 'ascora'); ?></strong>
            <?php echo WC()->cart->get_cart_total(); ?>
        </p>
        <div class="buttons">
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>"
                class="button view-cart-btn"><?php _e('View Cart', 'ascora'); ?></a>
            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
                class="button checkout-btn"><?php _e('Checkout', 'ascora'); ?></a>
        </div>
    </div>
</div>

<div id="ascora-mini-cart-overlay" class="ascora-mini-cart-overlay ascora-hidden"></div>

<?php
        do_action('ascora_mini_cart_rendered');

        return ob_get_clean();
    }

    /**
     * AJAX fragment for mini-cart content.
     * @param mixed $fragments
     */
    public function mini_cart_fragment($fragments)
    {
        ob_start();
        woocommerce_mini_cart();
        $fragments['.woocommerce-mini-cart-content-wrap'] = ob_get_clean();

        return $fragments;
    }

    /**
     * AJAX fragment for cart count.
     * @param mixed $fragments
     */
    public function cart_count_fragment($fragments)
    {
        ob_start(); ?>
<span class="ascora-cart-count" id="ascora-cart-count-fragment">
    <?php echo WC()->cart->get_cart_contents_count(); ?>
</span>
<?php
        $fragments['#ascora-cart-count-fragment'] = ob_get_clean();

        return $fragments;
    }

    /**
     * Enqueue CSS and JS for mini cart.
     */
    public function enqueue_mini_cart_assets()
    {
        // Inline CSS
        $css = file_get_contents(plugin_dir_path(__FILE__) . 'assets/ascora-mini-cart.css');
        wp_register_style('ascora-mini-cart-style', false);
        wp_enqueue_style('ascora-mini-cart-style');
        wp_add_inline_style('ascora-mini-cart-style', $css);

        // Inline JS
        $js = file_get_contents(plugin_dir_path(__FILE__) . 'assets/ascora-mini-cart.js');
        wp_register_script('ascora-mini-cart-script', false, ['jquery'], '1.0.1', true);
        wp_enqueue_script('ascora-mini-cart-script');
        wp_add_inline_script('ascora-mini-cart-script', $js);
    }
}

// Instantiate the mini cart
new Ascora_Slide_Mini_Cart();
?>