<?php

declare(strict_types=1);

add_filter('woocommerce_product_loop_start', function ($html) {
    $ascora = get_option('ascora', []);

    $post_per_column = class_exists('Ascora_Core')
        ? ($ascora['shop-page-post-per-column'] ?? 'columns-4')
        : 'columns-4';

    $classes = [
        'products',
        'ascora-products',
        'grid-view',
        'ascora-product-box',
        //esc_attr($post_per_column),
         $post_per_column = sanitize_html_class($post_per_column),
    ];

    return '<ul id="ascora-products" class="' . implode(' ', $classes) . '">';
});

add_filter('loop_shop_per_page', function ($per_page) {
    $ascora = get_option('ascora', []);

    return class_exists('Ascora_Core')
        ? ($ascora['shop-page-post-per'] ?? 16)
        : 16;
}, 20);


add_action('init', function () {
    // Default sorting remove
    remove_action(
        'woocommerce_before_shop_loop',
        'woocommerce_catalog_ordering',
        30
    );

    // Result count remove
    remove_action(
        'woocommerce_before_shop_loop',
        'woocommerce_result_count',
        20
    );
    // Remove WooCommerce breadcrumbs
    remove_action(
        'woocommerce_before_main_content',
        'woocommerce_breadcrumb',
        20
    );
});


/* Price filter via URL */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if (!is_shop() && !is_product_taxonomy()) {
        return;
    }

    if (isset($_GET['min_price'], $_GET['max_price'])) {
        $min = (int) $_GET['min_price'];
        $max = (int) $_GET['max_price'];

        if ($max > 0) {
            $meta_query   = (array) $query->get('meta_query');
            $meta_query[] = [
                'key'     => '_price',
                'value'   => [$min, $max],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC'
            ];
            $query->set('meta_query', $meta_query);
        }
    }
});
