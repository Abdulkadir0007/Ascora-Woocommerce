<?php

declare(strict_types=1);


add_action('wp_ajax_ascora_quick_view', 'ascora_quick_view');
add_action('wp_ajax_nopriv_ascora_quick_view', 'ascora_quick_view');

function ascora_quick_view()
{
    if (!isset($_POST['product_id'])) {
        wp_die();
    }

    $product_id = intval($_POST['product_id']);
    $product    = wc_get_product($product_id);

    if (!$product) {
        echo '<p>Product not found.</p>';
        wp_die();
    }

    // Load WooCommerce template
    wc_get_template('content-quick-view.php', [
        'product' => $product,
        'post'    => get_post($product_id)
    ], '', ASCORA_WC_PATH . 'core/');

    wp_die();
}
