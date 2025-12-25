<?php

declare(strict_types=1);
/**
 * Plugin Name: Ascora WooCommerce
 * Plugin URI:  https://abkadir.com
 * Description: Cart, Checkout, My-Account, Single Product custom layouts & features for Ascora theme.
 * Version:     1.3.1
 * Author:      Abdul Kadir
 * Author URI:  https://abkadir.com
 * Text Domain: ascora-wc
 * Domain Path: /languages
 * WC tested up to: 8.9
 */

defined('ABSPATH') || exit;

// গ্লোবাল কনস্ট্যান্ট (অন্যান্য ফাইল বা মেথডের আগে দরকার হতে পারে)
if (!defined('ASCORA_WC_URL')) {
    define('ASCORA_WC_URL', plugin_dir_url(__FILE__));
}
if (!defined('ASCORA_WC_PATH')) {
    define('ASCORA_WC_PATH', plugin_dir_path(__FILE__));
}
if (!defined('ASCORA_WC_FILE')) {
    define('ASCORA_WC_FILE', __FILE__);
}

final class Ascora_WooCommerce
{
    /** @var Ascora_WooCommerce|null */
    private static $instance = null;

    /**
     * Singleton Instance
     */
    public static function instance(): Ascora_WooCommerce
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor (private → prevent direct "new")
     */
    private function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->hooks();
    }

    /**
     * Define Plugin Constants
     */
    private function define_constants(): void
    {
        if (!defined('ASCORA_WC_VERSION')) {
            define('ASCORA_WC_VERSION', '1.0.0');
        }
    }

    /**
     * Include Required Files (Price Filter Widget Class included here)
     */
    private function includes(): void
    {
        // অন্যান্য ফাইল
        require_once ASCORA_WC_PATH . 'includes/class-mini-cart.php';
        require_once ASCORA_WC_PATH . 'includes/ajax-search-form.php';
        require_once ASCORA_WC_PATH . 'includes/class-quickc-view.php';
        require_once ASCORA_WC_PATH . 'includes/class-ascora-wc-sorting.php';
        require_once ASCORA_WC_PATH . 'core/meta-attribute-terms.php';
        require_once ASCORA_WC_PATH . 'core/ascora-ajax-search.php';
        require_once ASCORA_WC_PATH . 'core/ascora-wishlist.php';
        require_once ASCORA_WC_PATH . 'includes/class-ascora-wishlist.php';
        require_once ASCORA_WC_PATH . 'core/ascora-filters/class-ascora-filter-ajax.php';
        require_once ASCORA_WC_PATH . 'core/ascora-filters/class-ascora-price-filter.php';
        require_once ASCORA_WC_PATH . 'core/ascora-wc-pr-taxonomy/taxonomy-metabox.php';
        require_once ASCORA_WC_PATH . 'core/ascora-wc-pr-taxonomy/taxonomy-metabox-style.php';
        require_once ASCORA_WC_PATH . 'core/ascora-wc-pr-taxonomy/taxonomy-data-save.php';
        require_once ASCORA_WC_PATH . 'core/mega-category-menu.php';
    }

    /**
     * Hooks (সবগুলো মেথড ক্লাসের ভেতরে ডিফাইন করা আছে)
     */
    private function hooks(): void
    {
        add_action('init', [ $this, 'load_textdomain' ]);
        add_action('admin_notices', [ $this, 'wc_active_check' ]);

        // অ্যাসেট এবং AJAX ফিল্টার স্ক্রিপ্ট এনকিউ
        add_action('wp_enqueue_scripts', [ $this, 'assets' ]);
        add_action('wp_enqueue_scripts', [ $this, 'price_filter_scripts' ]);

        // WooCommerce কম্প্যাটিবিলিটি ও থিম অপশনস
        add_action('before_woocommerce_init', [ $this, 'declare_wc_compatibility' ]);
        add_action('after_setup_theme', [ $this, 'load_theme_woo_options' ]);

        // AJAX ফিল্টার এবং উইজেট রেজিস্ট্রেশন
        add_action('widgets_init', [ $this, 'register_custom_widgets' ]);

        // Shortcodes
        add_shortcode('ascora_wc_search', 'ascora_wc_product_search_form');
    }

    /**
     * Load Textdomain (unchanged)
     */
    public function load_textdomain(): void
    {
        load_plugin_textdomain('ascora-wc', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Admin Notice if WooCommerce is Missing (unchanged)
     */
    public function wc_active_check(): void
    {
        if (is_admin() && current_user_can('activate_plugins') && ! class_exists('WooCommerce')) {
            echo '<div class="error"><p><strong>'
                . esc_html__('Ascora WooCommerce', 'ascora-wc')
                . '</strong> '
                . esc_html__('requires WooCommerce to be active.', 'ascora-wc')
                . '</p></div>';
        }
    }

    /**
     * Frontend Assets (General CSS/JS)
     */
    public function assets(): void
    {
        if (!class_exists('WooCommerce')) {
            return;
        }
        // CSS Load
        if (file_exists(ASCORA_WC_PATH . 'assets/ascora-wc.css')) {
            wp_enqueue_style(
                'ascora-wc-css',
                ASCORA_WC_URL . 'assets/ascora-wc.css',
                [],
                ASCORA_WC_VERSION
            );
        }
        if (file_exists(ASCORA_WC_PATH . 'assets/ascora-shop.css')) {
            wp_enqueue_style(
                'ascora-shop',
                ASCORA_WC_URL . 'assets/ascora-shop.css',
                [],
                ASCORA_WC_VERSION
            );
        }

        // Swiper CSS
        wp_enqueue_style('ascora-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');


        // Swiper JS
        wp_enqueue_script('ascora-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);

        // JS Load + Localize
        if (file_exists(ASCORA_WC_PATH . 'assets/ascora-wc.js')) {
            wp_enqueue_script(
                'ascora-wc',
                ASCORA_WC_URL . 'assets/ascora-wc.js',
                ['jquery'],
                ASCORA_WC_VERSION,
                true
            );
            wp_localize_script('ascora-wc', 'ascora_wc', [
            'ajax_url' => admin_url('admin-ajax.php')]);

            wp_localize_script('ascora-wc', 'ascora_ajax', [
                'ajax_url' => admin_url('admin-ajax.php')
            ]);
            wp_localize_script('ascora-wc', 'ascora_wishlist', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('ascora_wishlist_nonce'),
            ]);
            wp_localize_script('ascora-wc', 'ascora_ajax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'per_page' => get_option('posts_per_page'),
            ]);
            wp_localize_script('ascora-wc', 'ascora_shop', [
                'shop_url' => get_permalink(wc_get_page_id('shop')),
]);
        }
    }


    /**
     * WooCommerce Compatibility (Fixes previous error: declare_wc_compatibility)
     */
    public function declare_wc_compatibility(): void
    {
        if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                'custom_order_tables',
                ASCORA_WC_FILE,
                true
            );

            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                'cart_checkout_blocks',
                ASCORA_WC_FILE,
                true
            );
        }
    }

    /**
     * Load Header Templates (Theme Integration) (Fixes previous error: load_theme_woo_options)
     */
    public function load_theme_woo_options(): void
    {
        if (class_exists('Ascora_Theme_Setup')) {
            $opt_name = 'ascora';
            require_once ASCORA_WC_PATH . 'core/woo-header/woo-header.php';
            require_once ASCORA_WC_PATH . 'core/options/woo-header.php';
            require_once ASCORA_WC_PATH . 'core/options/shop-page/shop-page.php';
            require_once ASCORA_WC_PATH . 'core/options/shop-page/single-shop-page.php';
        }
    }

    /* ----------------------------------------------------- */
    /* --- AJAX PRICE FILTER LOGIC --- */
    /* ----------------------------------------------------- */

    /**
     * Enqueue JavaScript file and localize price format for the filter.
     */
    public function price_filter_scripts(): void
    {
        // Enqueue custom JS
    }
    /**
     * Register the custom price filter widget.
     */
    public function register_custom_widgets(): void
    {
        // register_widget('Ascora_Price_Filter_Widget');
    }

    /**
     * Crucial: Apply the min_price/max_price parameters to the WooCommerce product query.
     */
}

// Initialize plugin
Ascora_WooCommerce::instance();
add_action('widgets_init', function () {
    register_widget('Ascora_Price_Filter_Widget');
});

// add_action('wp_enqueue_scripts', function () {
//     if (is_product() || is_shop() || is_product_category() || is_product_tag()) {
//         wp_enqueue_script('ascora-color-variation', ASCORA_WC_URL . 'assets/ascora-color-variation.js', [], ASCORA_WC_VERSION, true);
//     }
// });
