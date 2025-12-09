<?php
defined('ABSPATH') || exit;

/**
 * Mini Cart Template
 *
 * @var $cart WC_Cart
 * @var $items array
 */
?>

<?php if ($cart->is_empty()) : ?>
<div class="ascora-empty-cart">
    <p>Your cart is empty.</p>
    <a class="strat-shopping" href="">Start Shopping</a>
</div>

<?php else : ?>

<div class="ascora-mini-cart-body">
    <div class="ascora-cart-header">
        <h2>Your Cart
            (<?php echo $cart->get_cart_contents_count(); ?>)
        </h2>
    </div>

    <ul class="ascora-mini-cart-items">

        <?php foreach ($items as $cart_item_key => $cart_item) :
            $_product = $cart_item['data'];
            $qty      = $cart_item['quantity'];
            $price    = $_product->get_price();
            $subtotal = $price * $qty;
            ?>

        <li class="ascora-mini-item">

            <div class="mini-thumb">
                <?php echo $_product->get_image('woocommerce_thumbnail'); ?>
            </div>

            <div class="mini-content">

                <h4 class="mini-title">
                    <?php echo $_product->get_name(); ?>
                </h4>

                <div class="mini-price-row">
                    <span
                        class="mini-line-price"><?php echo wc_price($subtotal); ?></span>
                </div>

                <p class="mini-short-desc">
                    <?php echo wp_trim_words($_product->get_short_description(), 12); ?>
                </p>

                <div class="mini-qty-wrapper"
                    data-key="<?php echo $cart_item_key; ?>">
                    <button class="mini-qty minus">−</button>
                    <span
                        class="mini-qty-number"><?php echo $qty; ?></span>
                    <button class="mini-qty plus">+</button>
                </div>

                <a href="#" class="mini-remove"
                    data-key="<?php echo $cart_item_key; ?>">
                    Remove item
                </a>
            </div>

        </li>

        <?php endforeach; ?>

    </ul>
</div>
<div class="ascora-mini-footer">
    <div class="mini-subtotal">
        <strong>Subtotal:</strong>
        <span><?php echo $cart->get_cart_total(); ?></span>
    </div>

    <a href="<?php echo wc_get_cart_url(); ?>"
        class="mini-btn outline">View my cart</a>
    <a href="<?php echo wc_get_checkout_url(); ?>"
        class="mini-btn primary">Go to checkout</a>
</div>

<?php endif; ?>