<div class="ascora-mega-wrapper">
    <div class="ascora-mega-trigger">
        <i class="fa-solid fa-bars"></i>
        <span>Show Categories</span>
    </div>

    <div class="ascora-mega-menu">
        <?php
        $main_cats = get_terms([
            'taxonomy'   => 'product_cat',
            'parent'     => 0,
            'hide_empty' => true,
        ]);

        foreach ($main_cats as $cat) :
            ?>
        <div class="mega-item">

            <a href="<?php echo get_term_link($cat); ?>">
                <div class="mega-item-icon">
                    <?php $icon_id = get_term_meta($cat->term_id, 'ascora_cat_icon', true);

            if ($icon_id) {
                echo wp_get_attachment_image($icon_id, 'thumbnail');
            }
            ?>
                </div>
                <?php echo esc_html($cat->name); ?>
            </a>
            <?php
$sub_cats = get_terms([
               'taxonomy'   => 'product_cat',
               'parent'     => $cat->term_id,
               'hide_empty' => true,
]);

            if ($sub_cats) :
                ?>
            <div class="mega-sub">
                <?php foreach ($sub_cats as $sub) : ?>
                <div class="sub-item">

                    <?php
                    $thumb_id = get_term_meta($sub->term_id, 'thumbnail_id', true);

                    if ($thumb_id) :
                        ?>
                    <div class="sub-item-img">
                        <?php echo wp_get_attachment_image($thumb_id, 'thumbnail'); ?>
                    </div>
                    <?php
                    endif;
                    ?>

                    <a href="<?php echo get_term_link($sub); ?>">
                        <span><?php echo esc_html($sub->name); ?></span>
                    </a>
                    <?php
    // 🔹 Get child categories of this sub-category
    $child_terms = get_terms([
        'taxonomy'   => 'product_cat',
        'parent'     => $sub->term_id,
        'hide_empty' => false,
    ]);

                    if (! empty($child_terms) && ! is_wp_error($child_terms)) :
                        ?>
                    <div class="sub-sub-items">
                        <?php foreach ($child_terms as $child) : ?>
                        <a href="<?php echo esc_url(get_term_link($child)); ?>"
                            class="sub-sub-item">
                            <?php echo esc_html($child->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>


            </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>
    </div>
</div>