<?php

// Class Ascorra Wishlist
declare(strict_types=1);
defined('ABSPATH') || exit;

// AJAX Handlers
add_action('wp_ajax_ascora_wishlist_toggle', 'ascora_wishlist_toggle');
add_action('wp_ajax_nopriv_ascora_wishlist_toggle', 'ascora_wishlist_toggle');

function ascora_wishlist_toggle()
{
    $product_id = intval($_POST['product_id']);
    $user_id    = get_current_user_id();
    $key        = $user_id ? "user_$user_id" : 'guest_' . ascora_get_guest_id();

    $list = get_transient($key) ?: [];

    if (in_array($product_id, $list)) {
        $list   = array_diff($list, [$product_id]);
        $status = 'removed';
    } else {
        $list[] = $product_id;
        $status = 'added';
    }

    set_transient($key, array_values($list), DAY_IN_SECONDS * 30);

    wp_send_json_success([
        'status' => $status,
        'count'  => count($list),
    ]);
}

// Guest ID
function ascora_get_guest_id()
{
    if (!isset($_COOKIE['ascora_guest'])) {
        $id = wp_generate_password(12, false);
        setcookie('ascora_guest', $id, time() + DAY_IN_SECONDS * 30, COOKIEPATH, COOKIE_DOMAIN);
    } else {
        $id = $_COOKIE['ascora_guest'];
    }

    return $id;
}


// Wishlist Page Shortcode
add_shortcode('ascora_wishlist', 'ascora_wishlist_page');
function ascora_wishlist_page()
{
    ob_start();
    require_once ASCORA_WC_PATH . 'core/wishlist-page.php';

    return ob_get_clean();
}
