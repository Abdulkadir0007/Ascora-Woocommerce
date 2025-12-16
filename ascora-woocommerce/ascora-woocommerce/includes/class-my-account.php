<?php

declare(strict_types=1);
/**
 * My-Account dashboard & endpoints
 */

add_filter('woocommerce_account_menu_items', 'ascora_wc_account_menu');
function ascora_wc_account_menu($items)
{
    // Reorder or rename tabs
    return $items;
}

/**
 * Custom dashboard
 */
add_action('woocommerce_account_dashboard', 'ascora_wc_dashboard', 5);
function ascora_wc_dashboard()
{
    wc_get_template('myaccount/dashboard.php', [], '', ASCORA_WC_PATH . 'templates/');
}
