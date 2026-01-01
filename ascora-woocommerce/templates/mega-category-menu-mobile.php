<div class="ascora-mega-wrapper-mobaile">
    <div class="ascora-mega-menu">

        <?php
        $main_cats = get_terms([
            'taxonomy'   => 'product_cat',
            'parent'     => 0,
            'hide_empty' => true,
        ]);

        foreach ($main_cats as $cat) :

            // 🔹 FIRST LEVEL SUB CATEGORIES
            $sub_cats = get_terms([
                'taxonomy'   => 'product_cat',
                'parent'     => $cat->term_id,
                'hide_empty' => true,
            ]);
            ?>

        <div class="mega-item">

            <div class="mega-title">
                <a
                    href="<?php echo esc_url(get_term_link($cat)); ?>">
                    <div class="mega-item-icon">
                        <?php
                        $icon_id = get_term_meta($cat->term_id, 'ascora_cat_icon', true);
            if ($icon_id) {
                echo wp_get_attachment_image($icon_id, 'thumbnail');
            }
            ?>
                    </div>
                    <?php echo esc_html($cat->name); ?>
                </a>

                <?php if (!empty($sub_cats)) : ?>
                <span class="submenu-toggle">
                    <i class="fa-solid fa-chevron-down"></i>
                </span>
                <?php endif; ?>
            </div>

            <?php if (!empty($sub_cats)) : ?>
            <div class="mega-sub">

                <?php foreach ($sub_cats as $sub) :

                    // 🔹 SECOND LEVEL (CHILD OF SUB)
                    $child_terms = get_terms([
                        'taxonomy'   => 'product_cat',
                        'parent'     => $sub->term_id,
                        'hide_empty' => true,
                    ]);
                    ?>

                <div class="sub-item">

                    <div class="sub-title">
                        <a
                            href="<?php echo esc_url(get_term_link($sub)); ?>">
                            <?php echo esc_html($sub->name); ?>
                        </a>

                        <?php if (!empty($child_terms)) : ?>
                        <span class="submenu-toggle">
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($child_terms)) : ?>
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