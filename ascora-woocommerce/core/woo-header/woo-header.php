<?php

/**
 *
 * The Header Of Ascora Theme
 * @package Ascora
 * @since 1.0.0
 */
// Prevent direct access.
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (! function_exists('ascora_woocommerce_header')) {
    function ascora_woocommerce_header()
    {
        global $ascora;
        ?>
<div class="header-layout-woocommerce">
    <div class="acora-only-pc-woo-header">

        <div class="ascora-woo-header">
            <div class="logo-img text-center">
                <?php if (!empty($ascora['logo-img']['url'])): ?>
                <a href="<?php echo home_url(); ?>"
                    class="logo-img"><img
                        src="<?php echo esc_url($ascora['logo-img']['url']) ?>"
                        alt="<?php bloginfo('name') ?>"></a>
                <?php else: ?>
                <a href="<?php echo home_url(); ?>">
                    <h2 class="Web-logo">
                        <?php echo esc_html($ascora['logo-text']); ?>
                    </h2>
                </a>
                <?php endif; ?>
            </div>
            <div class="as-wo-search">
                <?php if (class_exists('Ascora_WooCommerce')) {
                    echo do_shortcode('[ascora_wc_search]');
                } ?>
            </div>
            <div class="ascora-woo-menu">
                <div class="ascora-header-account">
                    <a href="<?php echo is_user_logged_in() ? get_permalink(get_option('woocommerce_myaccount_page_id')) : wc_get_page_permalink('myaccount'); ?>"
                        class="ascora-account-icon">
                        <svg class="wc-block-customer-account__account-icon" viewBox="1 1 29 29"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="16" cy="10.5" r="3.5" stroke="currentColor" stroke-width="2" fill="none">
                            </circle>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M11.5 18.5H20.5C21.8807 18.5 23 19.6193 23 21V25.5H25V21C25 18.5147 22.9853 16.5 20.5 16.5H11.5C9.01472 16.5 7 18.5147 7 21V25.5H9V21C9 19.6193 10.1193 18.5 11.5 18.5Z"
                                fill="currentColor"></path>
                        </svg>
                    </a>
                </div>
                <div class="ascora-wishlist">
                    <a href="<?php $wishlist_page = get_page_by_path('wishlist');
        echo $wishlist_page ? esc_url(get_permalink($wishlist_page->ID)) : '#';?>"
                        class="wishlist-header-icon">
                        <i class="fa-regular fa-heart"></i>
                        <span class="wishlist-count">
                            <?php
        $user_id = get_current_user_id();
        $key     = $user_id ? "user_$user_id" : 'guest_' . ascora_get_guest_id();
        $list    = get_transient($key) ?: [];
        echo count($list);
        ?>
                        </span>
                    </a>
                </div>

                <div class="as-mini-cart"> <?php if (class_exists('Ascora_WooCommerce')) {
                    echo do_shortcode('[ascora-mini-cart]');
                } ?></div>
            </div>

        </div>
        <div class="ascora-woocommerce-menu-center">
            <div class="ascora-mega-category-menu">
                <?php  require_once ASCORA_WC_PATH . 'templates/mega-category-menu.php'?>


            </div>
            <div class="header-menu">
                <nav class="main-menu right">
                    <?php wp_nav_menu([
                'theme_location' => 'main-menu',
                'container'      => false,
                'fallback_cb'    => 'default_menu'
                ]) ?>
                </nav>
            </div>
        </div>
    </div>
    <div class="ascora-woocommerce-menu">
        <div class="ascora-woo-all-menu">
            <div class="ascora-woo-category-menu">

            </div>
        </div>
    </div>
    <div class="ascora-responsive-woo-header">
        <div class="ascora-row">
            <div class="header-menu ascora-woo-header">
                <nav class="main-menu right">
                    <?php wp_nav_menu([
                        'theme_location' => 'main-menu',
                        'container'      => false,
                        'fallback_cb'    => 'default_menu'
                        ]) ?>
                </nav>
            </div>
            <div class="logo-img text-center">
                <?php if (!empty($ascora['logo-img']['url'])): ?>
                <a href="<?php echo home_url(); ?>"
                    class="logo-img"><img
                        src="<?php echo esc_url($ascora['logo-img']['url']) ?>"
                        alt="<?php bloginfo('name') ?>"></a>
                <?php else: ?>
                <a href="<?php echo home_url(); ?>">
                    <h2 class="Web-logo">
                        <?php echo esc_html($ascora['logo-text']); ?>
                    </h2>
                </a>
                <?php endif; ?>
            </div>
            <div class="ascora-woo-menu">
                <div class="ascora-header-account">
                    <a href="<?php echo is_user_logged_in() ? get_permalink(get_option('woocommerce_myaccount_page_id')) : wc_get_page_permalink('myaccount'); ?>"
                        class="ascora-account-icon">
                        <svg class="wc-block-customer-account__account-icon" viewBox="1 1 29 29"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="16" cy="10.5" r="3.5" stroke="currentColor" stroke-width="2" fill="none">
                            </circle>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M11.5 18.5H20.5C21.8807 18.5 23 19.6193 23 21V25.5H25V21C25 18.5147 22.9853 16.5 20.5 16.5H11.5C9.01472 16.5 7 18.5147 7 21V25.5H9V21C9 19.6193 10.1193 18.5 11.5 18.5Z"
                                fill="currentColor"></path>
                        </svg>
                    </a>
                </div>
                <div class="ascora-wishlist">
                    <a href="<?php $wishlist_page = get_page_by_path('wishlist');
        echo $wishlist_page ? esc_url(get_permalink($wishlist_page->ID)) : '#';?>"
                        class="wishlist-header-icon">
                        <i class="fa-regular fa-heart"></i>
                        <span class="wishlist-count">
                            <?php
        $user_id = get_current_user_id();
        $key     = $user_id ? "user_$user_id" : 'guest_' . ascora_get_guest_id();
        $list    = get_transient($key) ?: [];
        echo count($list);
        ?>
                        </span>
                    </a>
                </div>

                <div class="as-mini-cart"> <?php if (class_exists('Ascora_WooCommerce')) {
                    echo do_shortcode('[ascora-mini-cart]');
                } ?></div>
            </div>
        </div>
    </div>
</div>
<?php  }
} ?>