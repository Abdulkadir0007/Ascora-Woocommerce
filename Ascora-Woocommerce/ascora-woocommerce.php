<?php

declare(strict_types=1);
/**
 * Plugin Name: Ascora WooCommerce
 * Description: Cart, Checkout, My-Account, Single Product custom layouts & features for Ascora theme.
 * Version:     1.0.0
 * Text Domain: ascora-wc
 */

defined('ABSPATH') || exit;

final class Ascora_WooCommerce
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->hooks();
    }

    /**
     * Define constants
     */
    private function define_constants()
    {
        define('ASCORA_WC_VERSION', '1.0.0');
        define('ASCORA_WC_FILE', __FILE__);
        define('ASCORA_WC_PATH', plugin_dir_path(__FILE__));
        define('ASCORA_WC_URL', plugin_dir_url(__FILE__));
    }

    /**
     * Include files
     */
    private function includes()
    {
        require_once ASCORA_WC_PATH . 'includes/ajax-search-form.php';
        require_once ASCORA_WC_PATH . 'includes/class-mini-cart.php';
        require_once ASCORA_WC_PATH . 'core/ascora-ajax-search.php';
    }

    /**
     * Hooks
     */
    private function hooks()
    {
        // Textdomain
        add_action('init', [ $this, 'load_textdomain' ]);

        // Asset
        add_action('wp_enqueue_scripts', [ $this, 'assets' ]);

        // WC compatibility
        add_action('before_woocommerce_init', [ $this, 'declare_wc_compatibility' ]);

        // Shortcode
        add_shortcode('ascora_wc_search', 'ascora_wc_product_search_form');
    }

    /**
     * Load textdomain
     */
    public function load_textdomain()
    {
        load_plugin_textdomain('ascora-wc', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Assets
     */
    public function assets()
    {
        if (! class_exists('WooCommerce')) {
            return;
        }

        if (file_exists(ASCORA_WC_PATH . 'assets/ascora-wc.css')) {
            wp_enqueue_style('ascora-wc', ASCORA_WC_URL . 'assets/ascora-wc.css', [], ASCORA_WC_VERSION);
        }

        if (file_exists(ASCORA_WC_PATH . 'assets/ascora-wc.js')) {
            wp_enqueue_script('ascora-wc', ASCORA_WC_URL . 'assets/ascora-wc.js', [ 'jquery', 'wc-cart-fragments' ], ASCORA_WC_VERSION, true);
        }
    }

    /**
     * WC HPOS + Blocks compatibility
     */
    public function declare_wc_compatibility()
    {
        if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                'custom_order_tables',
                __FILE__,
                true
            );

            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                'cart_checkout_blocks',
                __FILE__,
                true
            );
        }
    }
}

// Initialize plugin
new Ascora_WooCommerce();
// <?php if (class_exists('Ascora_WooCommerce')) {
//     echo do_shortcode('[ascora_wc_search]');
// }
