<?php

declare(strict_types=1);


add_action('pre_get_posts', 'ascora_woo_main_query');
function ascora_woo_main_query($q)
{
    if (is_admin() || ! $q->is_main_query()) {
        return;
    }

    if (is_shop() || is_product_taxonomy()) {
        $opts     = get_option('ascora', []);
        $per_page = $opts['shop-per-page'] ?? 16;
        $q->set('posts_per_page', absint($per_page));

        // WooCommerce native ordering
        $ordering = WC()->query->get_catalog_ordering_args();
        $q->set('orderby', $ordering['orderby']);
        $q->set('order', $ordering['order']);

        if (!empty($ordering['meta_key'])) {
            $q->set('meta_key', $ordering['meta_key']);
        }

        // Preserve Woo filters
        $q->set('meta_query', WC()->query->get_meta_query());
        $q->set('tax_query', WC()->query->get_tax_query());

        // On-sale
        if (!empty($_GET['on_sale']) && $_GET['on_sale'] === 'yes') {
            $q->set('post__in', wc_get_product_ids_on_sale());
        }
    }
}
