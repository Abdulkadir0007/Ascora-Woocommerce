<?php

declare(strict_types=1);
function ascora_mega_category_menu()
{
    $cats = get_terms([
        'taxonomy'   => 'product_cat',
        'parent'     => 0,
        'hide_empty' => false
    ]);

    echo '<ul class="ascora-cat-list">';

    foreach ($cats as $cat) {
        $icon    = get_term_meta($cat->term_id, 'ascora_cat_icon', true);
        $mega_on = get_term_meta($cat->term_id, 'ascora_cat_mega_enabled', true);

        echo '<li class="' . ($mega_on ? 'has-mega' : '') . '">';

        echo '<a href="' . get_term_link($cat) . '">';
        if ($icon) {
            echo '<span class="cat-icon">' . $icon . '</span>';
        }
        echo esc_html($cat->name);
        echo '</a>';

        if ($mega_on) {
            ascora_render_mega_panel($cat);
        }

        echo '</li>';
    }

    echo '</ul>';
}

function ascora_render_mega_panel($parent_cat)
{
    $subs = get_terms([
        'taxonomy'   => 'product_cat',
        'parent'     => $parent_cat->term_id,
        'hide_empty' => false
    ]);

    $banner = get_term_meta($parent_cat->term_id, 'ascora_cat_mega_banner', true);
    $banner = $banner ? wp_get_attachment_image_url($banner, 'large') : '';

    echo '<div class="mega-panel">';

    echo '<div class="mega-columns">';
    foreach ($subs as $sub) {
        echo '<div class="mega-col">';
        echo '<h4><a href="' . get_term_link($sub) . '">' . $sub->name . '</a></h4>';

        $childs = get_terms([
            'taxonomy'   => 'product_cat',
            'parent'     => $sub->term_id,
            'hide_empty' => false
        ]);

        if ($childs) {
            echo '<ul>';
            foreach ($childs as $child) {
                echo '<li><a href="' . get_term_link($child) . '">' . $child->name . '</a></li>';
            }
            echo '</ul>';
        }

        echo '</div>';
    }
    echo '</div>';

    if ($banner) {
        echo '<div class="mega-banner"><img src="' . $banner . '"></div>';
    }

    echo '</div>';
}
