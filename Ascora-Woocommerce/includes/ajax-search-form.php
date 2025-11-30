<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Ascora WC Product Search Form
 */
function ascora_wc_product_search_form()
{
    // Start buffering
    ob_start();
    ?>

<div class="ascora-wc-product-search">
    <form role="search" method="get" id="as-wc-searchform"
        action="<?php echo home_url('/'); ?>">
        <div class="search-input-group">
            <div class="product_cat">
                <select name="product_cat" id="product_cat">
                    <option value="">
                        <?php esc_html_e('All Categories', 'ascora'); ?>
                    </option>
                    <?php
            $categories = get_terms([
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
            ]);
    foreach ($categories as $category) {
        echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
    }
    ?>
                </select>
            </div>
            <label for="s"
                class="screen-reader-text"><?php esc_html_e('Search products&hellip;', 'ascora'); ?></label>
            <input type="search" id="s" name="s"
                placeholder="<?php esc_html_e('Search products&hellip;', 'ascora'); ?>" />
            <input type="hidden" name="post_type" value="product" />
            <button type="submit" id="searchsubmit" class="search-submit"><i aria-hidden="true"
                    class="fas fa-search"></i></button>
        </div>
    </form>
    <div class="as-search-results"></div>
</div>

<?php
    return ob_get_clean();
}
?>