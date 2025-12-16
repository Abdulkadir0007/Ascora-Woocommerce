<?php

declare(strict_types=1);
if (! defined('ABSPATH')) {
    exit;
}

/**
 * AJAX: Product Live Search
 */

add_action('wp_ajax_ascora_ajax_search', 'ascora_ajax_search');
add_action('wp_ajax_nopriv_ascora_ajax_search', 'ascora_ajax_search');

function ascora_ajax_search()
{
    $keyword = sanitize_text_field($_GET['keyword'] ?? '');
    $cat     = sanitize_text_field($_GET['cat'] ?? '');

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 10,
        's'              => $keyword,
    ];

    if ($cat) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $cat
            ]
        ];
    }

    $query = new WP_Query($args);

    $results = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $product = wc_get_product(get_the_ID());

            $results[] = [
                'title' => get_the_title(),
                'url'   => get_permalink(),
                'img'   => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                'price' => $product ? $product->get_price_html() : '',
            ];
        }
    }

    wp_send_json($results);
}
function as_wc_scripts()
{
    // আগের কোড থাকবে...

    wp_enqueue_script(
        'ascora-ajax-wc',
        ASCORA_WC_URL . 'assets/ascora-ajax-search.js',
        ['jquery'],
        ASCORA_WC_VERSION,
        true
    );

    wp_localize_script('ascora-ajax-wc', 'ascoraSearch', [
        'ajax_url' => admin_url('admin-ajax-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'as_wc_scripts');
