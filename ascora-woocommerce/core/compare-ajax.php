<?php

declare(strict_types=1);

add_action('wp_ajax_ascora_compare_items', 'ascora_compare_items');
add_action('wp_ajax_nopriv_ascora_compare_items', 'ascora_compare_items');

function ascora_compare_items()
{
    $ids = isset($_POST['ids']) ? $_POST['ids'] : [];

    if (empty($ids)) {
        echo '<p>No products found.</p>';
        wp_die();
    }

    echo '<table class="ascora-compare-table" style="width:100%; border:1px solid #ddd;">';
    echo '<tr>';

    foreach ($ids as $id) {
        $product = wc_get_product($id);
        if (!$product) {
            continue;
        }

        echo '<td style="padding:20px; text-align:center; border:1px solid #ddd;">';
        echo $product->get_image();
        echo '<h4>' . $product->get_name() . '</h4>';
        echo '<p class="price">' . $product->get_price_html() . '</p>';
        echo '</td>';
    }

    echo '</tr>';
    echo '</table>';

    wp_die();
}
