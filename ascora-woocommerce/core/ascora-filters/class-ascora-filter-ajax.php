<?php

declare(strict_types=1);
add_action('wp_ajax_ascora_filter_price', 'ascora_filter_price');
add_action('wp_ajax_nopriv_ascora_filter_price', 'ascora_filter_price');

function ascora_filter_price()
{
    global $ascora;

    $min   = isset($_POST['min_price']) ? intval($_POST['min_price']) : 0;
    $max   = isset($_POST['max_price']) ? intval($_POST['max_price']) : 0;
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $post_per_Page = class_exists('Ascora_Core')
        ? ($ascora['shop-page-post-per'] ?? 16)
        : 16;

    if ($max <= 0) {
        $max = 9999999;
    }

    $meta_query = WC()->query->get_meta_query();

    if ($min > 0 || $max > 0) {
        $meta_query[] = [
            'key'     => '_price',
            'value'   => [$min, $max],
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC'
        ];
    }

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => $post_per_Page,
        'paged'          => $paged,
        'meta_query'     => $meta_query
    ];

    $loop = new WP_Query($args);


    ob_start();
    if ($loop->have_posts()) :
        while ($loop->have_posts()) :
            $loop->the_post();
            wc_get_template_part('content', 'product');
        endwhile;
    else :
        echo '<p>No products found.</p>';
    endif;
    $products_html = ob_get_clean();

    // Pagination
    $pagination_html = paginate_links([
        'total'     => $loop->max_num_pages,
        'current'   => $paged,
        'format'    => '?paged=%#%',
        'type'      => 'plain',
        'prev_text' => '<i class="fa-solid fa-angle-left"></i> previous',
        'next_text' => 'Next <i class="fa-solid fa-angle-right"></i>',
    ]);

    wp_reset_postdata();

    wp_send_json_success([
        'products'   => $products_html,
        'pagination' => $pagination_html
    ]);
}
