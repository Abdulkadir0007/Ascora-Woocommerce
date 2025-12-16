<?php
// Wishlist page
defined('ABSPATH') || exit;

$user_id = get_current_user_id();
$key     = $user_id ? "user_$user_id" : 'guest_' . ascora_get_guest_id();
$list    = get_transient($key) ?: [];

?>

<div class="ascora-wishlist-wrapper">

    <h2 class="ascora-wishlist-title">
        <?php esc_html_e('My Wishlist', 'ascora'); ?>
    </h2>

    <?php if (empty($list)) : ?>

    <div class="ascora-wishlist-empty">
        <i class="fa fa-heart-o"></i>
        <p><?php esc_html_e('Your wishlist is empty.', 'ascora'); ?>
        </p>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
            class="ascora-btn">
            <?php esc_html_e('Go to Shop', 'ascora'); ?>
        </a>
    </div>

    <?php else : ?>

    <ul class="ascora-wishlist-grid">

        <?php foreach ($list as $product_id) :
            $product = wc_get_product($product_id);
            if (!$product) {
                continue;
            }
            $img = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
            ?>

        <li class="ascora-wishlist-item"
            data-id="<?php echo esc_attr($product_id); ?>">
            <div class="wl-card">
                <div class="loading-gif hidden">
                    <img src="<?php echo get_theme_file_uri('assets/images/rhombus.gif'); ?>"
                        alt="loading">
                </div>

                <a href="<?php echo get_permalink($product_id); ?>"
                    class="wl-thumb">
                    <img src="<?php echo esc_url($img); ?>"
                        alt="<?php echo esc_attr($product->get_name()); ?>">
                </a>

                <div class="wl-content">
                    <h3 class="wl-title">
                        <a
                            href="<?php echo get_permalink($product_id); ?>">
                            <?php echo esc_html($product->get_name()); ?>
                        </a>
                    </h3>

                    <div class="wl-price">
                        <?php echo wp_kses_post($product->get_price_html()); ?>
                    </div>

                    <div class="wl-actions">

                        <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
                            class="ascora-btn add-to-cart-btn"
                            data-product-id="<?php echo esc_attr($product_id); ?>">
                            <?php echo esc_html($product->add_to_cart_text()); ?>
                        </a>
                        <button class="ascora-btn wl-remove"
                            data-id="<?php echo esc_attr($product_id); ?>">
                            <i class="fa fa-trash"></i>
                        </button>

                    </div>
                </div>

            </div>

        </li>

        <?php endforeach; ?>

    </ul>

    <?php endif; ?>

</div>