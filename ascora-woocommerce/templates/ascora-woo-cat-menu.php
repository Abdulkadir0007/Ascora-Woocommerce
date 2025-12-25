<?php
$parent_cats = get_terms([
'taxonomy'   => 'product_cat',
'parent'     => 0,
'hide_empty' => true
]);
?>
<nav class="mega-category-nav">
    <ul class="mega-cat-menu">
        <?php foreach ($parent_cats as $cat):
            $thumb_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
            $icon     = wp_get_attachment_image_url($thumb_id, 'thumbnail');
            $subcats  = get_terms([
            'taxonomy'   => 'product_cat',
            'parent'     => $cat->term_id,
            'hide_empty' => true
            ]);
            $banner = function_exists('get_field') ? get_field(
                'cat_banner',
                'product_cat_' . $cat->term_id
            ) : '';
            $brands = function_exists('get_field') ? get_field(
                'cat_brands',
                'product_cat_' . $cat->term_id
            ) : [];
            ?>
        <li class="mega-item">
            <a href="<?php echo get_term_link($cat); ?>"
                class="mega-link">
                <?php if ($icon): ?><img
                    src="<?php echo esc_url($icon); ?>" alt="">
                <?php endif; ?>
                <span><?php echo esc_html($cat->name); ?></span>
            </a>
            <?php if ($subcats): ?>
            <div class="mega-dropdown">
                <div class="mega-grid">
                    <div class="mega-col subcats">
                        <h4><?php echo esc_html($cat->name); ?></h4>
                        <ul>
                            <?php foreach ($subcats as $sub): ?>
                            <li><a
                                    href="<?php echo get_term_link($sub); ?>"><?php echo
            esc_html($sub->name); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php if ($brands): ?>
                    <div class="mega-col brands">
                        <h4>Brands</h4>
                        <ul>
                            <?php foreach ($brands as $brand): ?>
                            <li><a
                                    href="<?php echo esc_url($brand['brand_url']); ?>"><?php echo esc_html($brand['brand_name']); ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if ($banner): ?>
                    <div class="mega-col banner">
                        <img src="<?php echo esc_url($banner); ?>"
                            alt="">
                    </div><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>