<?php

declare(strict_types=1);


add_action('wp_ajax_ascora_sort_products', 'ascora_sort_products');
add_action('wp_ajax_nopriv_ascora_sort_products', 'ascora_sort_products');

function ascora_sort_products()
{
    global $ascora;
    $sort            = sanitize_text_field($_POST['sort'] ?? '');
    $post_per_Page   = class_exists('Ascora_Core') ? ($ascora['shop-page-post-per'] ?? 16) : 16;

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => $post_per_Page,
    ];

    switch ($sort) {
        case 'latest':
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;

        case 'price_asc':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
            break;

        case 'price_desc':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;

        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;

        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

    $loop = new WP_Query($args);

    ob_start();

    if ($loop->have_posts()) :
        while ($loop->have_posts()) : $loop->the_post();
            wc_get_template_part('content', 'product');
        endwhile;
    else:
        echo '<p>No products found.</p>';
    endif;

    wp_reset_postdata();

    echo ob_get_clean();
    wp_die();
}
