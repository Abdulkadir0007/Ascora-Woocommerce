<?php

declare(strict_types=1);
/**
 * AJAX Handler for Ascora Price Filter
 */

if (!defined('ABSPATH')) {
    exit;
}

class Ascora_Price_Filter_Ajax
{
    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('wp_ajax_ascora_filter_products', [$this, 'filter_products']);
        add_action('wp_ajax_nopriv_ascora_filter_products', [$this, 'filter_products']);
    }

    /**
     * Filter products via AJAX
     */
    public function filter_products()
    {
        // Verify nonce
        check_ajax_referer('ascora_filter_nonce', 'nonce');

        // Get filter parameters
        $min_price = isset($_POST['min_price']) ? floatval($_POST['min_price']) : 0;
        $max_price = isset($_POST['max_price']) ? floatval($_POST['max_price']) : 999999;

        // Query args
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => get_option('posts_per_page', 12),
            'post_status'    => 'publish',
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => '_price',
                    'value'   => [$min_price, $max_price],
                    'compare' => 'BETWEEN',
                    'type'    => 'DECIMAL(10,2)'
                ]
            ],
            'tax_query' => [
                [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'exclude-from-catalog',
                    'operator' => 'NOT IN',
                ]
            ]
        ];

        // Apply WooCommerce filters
        $args = apply_filters('woocommerce_product_query_args', $args);

        // Execute query
        $query = new WP_Query($args);

        // Start output buffering
        ob_start();

        if ($query->have_posts()) {
            // WooCommerce wrapper start
            woocommerce_product_loop_start();

            while ($query->have_posts()) {
                $query->the_post();

                // Load product template
                wc_get_template_part('content', 'product');
            }

            // WooCommerce wrapper end
            woocommerce_product_loop_end();
        } else {
            // No products found
            echo '<div class="ascora-no-products">';
            echo '<p>' . esc_html__('No products found in this price range.', 'ascora-price-filter') . '</p>';
            echo '</div>';
        }

        wp_reset_postdata();

        // Get output
        $output = ob_get_clean();

        // Send response
        wp_send_json_success([
            'html'           => $output,
            'found_products' => $query->found_posts,
            'min_price'      => $min_price,
            'max_price'      => $max_price
        ]);
    }
}

// Initialize AJAX handler
new Ascora_Price_Filter_Ajax();
