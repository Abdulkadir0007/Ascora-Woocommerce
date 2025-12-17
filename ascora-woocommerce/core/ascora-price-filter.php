<?php

declare(strict_types=1);

add_action('wp_ajax_ascora_filter_products', 'ascora_filter_products');
add_action('wp_ajax_nopriv_ascora_filter_products', 'ascora_filter_products');

function ascora_filter_products()
{
    $min = intval($_POST['min']);
    $max = intval($_POST['max']);


    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => get_option('posts_per_page'),
        'paged'          => $paged,
        'meta_query'     => [
            [
                'key'     => '_price',
                'value'   => [$min, $max],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC'
            ]
        ]
    ];

    $loop = new WP_Query($args);

    ob_start();
    if ($loop->have_posts()) {
        while ($loop->have_posts()) {
            $loop->the_post();
            wc_get_template_part('content', 'product');
        }
    } else {
        echo '<p>No products found.</p>';
    }
    wp_reset_postdata();

    wp_send_json_success(ob_get_clean());

    $is_reset = isset($_POST['reset']);

    if ($is_reset) {
        $min = 0;
        $max = PHP_INT_MAX;
    }
}
