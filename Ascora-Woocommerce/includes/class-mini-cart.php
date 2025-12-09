<?php
defined('ABSPATH') || exit;

class Ascora_Mini_Cart
{
    public function __construct()
    {
        // Shortcode → icon + drawer
        add_shortcode('ascora-mini-cart', [$this, 'render_icon_and_drawer']);

        // Ajax load mini cart
        add_action('wp_ajax_nopriv_ascora_load_mini_cart', [$this, 'load_mini_cart'], 1);
        add_action('wp_ajax_ascora_load_mini_cart', [$this, 'load_mini_cart'], 1);


        // Ajax remove item
        add_action('wp_ajax_ascora_remove_cart_item', [$this, 'remove_cart_item']);
        add_action('wp_ajax_nopriv_ascora_remove_cart_item', [$this, 'remove_cart_item']);

        // Ajax update quantity
        add_action('wp_ajax_ascora_update_qty', [$this, 'update_quantity']);
        add_action('wp_ajax_nopriv_ascora_update_qty', [$this, 'update_quantity']);

        // Ajax update cart count
        add_action('wp_ajax_ascora_get_cart_count', [$this, 'get_cart_count']);
        add_action('wp_ajax_nopriv_ascora_get_cart_count', [$this, 'get_cart_count']);
    }

    /**
     * SHORTCODE OUTPUT (icon + drawer)
     */
    public function render_icon_and_drawer()
    {
        ob_start(); ?>

<div id="ascora-cart-trigger" class="ascora-cart-icon">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" width="20" height="20"
        class="wc-block-mini-cart__icon" aria-hidden="true" focusable="false">
        <circle cx="12.6667" cy="24.6667" r="2" fill="currentColor"></circle>
        <circle cx="23.3333" cy="24.6667" r="2" fill="currentColor"></circle>
        <path fill-rule="evenodd" clip-rule="evenodd"
            d="M9.28491 10.0356C9.47481 9.80216 9.75971 9.66667 10.0606 9.66667H25.3333C25.6232 9.66667 25.8989 9.79247 26.0888 10.0115C26.2787 10.2305 26.3643 10.5211 26.3233 10.8081L24.99 20.1414C24.9196 20.6341 24.4977 21 24 21H12C11.5261 21 11.1173 20.6674 11.0209 20.2034L9.08153 10.8701C9.02031 10.5755 9.09501 10.269 9.28491 10.0356ZM11.2898 11.6667L12.8136 19H23.1327L24.1803 11.6667H11.2898Z"
            fill="currentColor"></path>
        <path fill-rule="evenodd" clip-rule="evenodd"
            d="M5.66669 6.66667C5.66669 6.11438 6.1144 5.66667 6.66669 5.66667H9.33335C9.81664 5.66667 10.2308 6.01229 10.3172 6.48778L11.0445 10.4878C11.1433 11.0312 10.7829 11.5517 10.2395 11.6505C9.69614 11.7493 9.17555 11.3889 9.07676 10.8456L8.49878 7.66667H6.66669C6.1144 7.66667 5.66669 7.21895 5.66669 6.66667Z"
            fill="currentColor"></path>
    </svg> <span
        class="ascora-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
</div>

<div id="ascora-slide-overlay"></div>

<div id="ascora-slide-cart">
    <button class="ascora-slide-close">×</button>
    <div id="ascora-mini-cart-wrapper"></div>
</div>

<?php
        return ob_get_clean();
    }

    /**
     * Load mini cart AJAX
     */
    public function load_mini_cart()
    {
        wc_maybe_define_constant('WOOCOMMERCE_CART', true);
        ob_clean();
        echo $this->render_cart_html();
        wp_die();
    }

    /**
     * Remove from cart AJAX
     */
    public function remove_cart_item()
    {
        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);

        WC()->cart->remove_cart_item($cart_item_key);
        WC()->cart->calculate_totals();

        echo $this->render_cart_html();
        wp_die();
    }

    /**
     * Update quantity (+ / –)
     */
    public function update_quantity()
    {
        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        $change        = sanitize_text_field($_POST['change']);

        $cart = WC()->cart->get_cart();

        if (!isset($cart[$cart_item_key])) {
            wp_die();
        }

        $qty = $cart[$cart_item_key]['quantity'];

        if ($change === 'plus') {
            $qty++;
        } elseif ($change === 'minus') {
            if ($qty > 1) {
                $qty--;
            } else {
                WC()->cart->remove_cart_item($cart_item_key);
            }
        }

        WC()->cart->set_quantity($cart_item_key, $qty, true);
        WC()->cart->calculate_totals();

        echo $this->render_cart_html();
        wp_die();
    }

    /**
     * Get cart count (header)
     */
    public function get_cart_count()
    {
        wp_send_json([
            'count' => WC()->cart->get_cart_contents_count()
        ]);
    }

    /**
     * MINI CART TEMPLATE
     */
    public function render_cart_html()
    {
        ob_start();

        $cart  = WC()->cart;
        $items = $cart->get_cart();

        include ASCORA_WC_PATH . 'core/ascora-mini-cart.php';

        return ob_get_clean();
    }
}

new Ascora_Mini_Cart();
?>