<?php
/**
 * Ascora wc product taxonomy data save
 */
add_action('created_product_cat', 'ascora_save_cat_meta');
add_action('edited_product_cat', 'ascora_save_cat_meta');

function ascora_save_cat_meta($term_id)
{
    // Security check
    if (
        !isset($_POST['ascora_cat_meta_nonce']) ||
        !wp_verify_nonce($_POST['ascora_cat_meta_nonce'], 'ascora_cat_meta_nonce')
    ) {
        return;
    }

    if (!current_user_can('manage_product_terms')) {
        return;
    }

    $fields = [
        'ascora_cat_icon',
        'ascora_cat_icon_large',
        'ascora_cat_title_bg',
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_term_meta($term_id, $field, absint($_POST[$field]));
        }
    }

    if (isset($_POST['ascora_cat_extra_desc'])) {
        update_term_meta(
            $term_id,
            'ascora_cat_extra_desc',
            wp_kses_post($_POST['ascora_cat_extra_desc'])
        );
    }
}


/**
 * Media uploader field
 * @param mixed $name
 * @param mixed $value
 */
function ascora_media_field($name, $value = '')
{
    $img       = $value ? wp_get_attachment_image_url($value, 'thumbnail') : '';
    $has_image = $img ? 'has-image' : '';
    ?>
<div
    class="ascora-media-wrap <?php echo esc_attr($has_image); ?> ascora-wc-product-cat-cusotm-taxonomy">
    <input type="hidden" name="<?php echo esc_attr($name); ?>"
        value="<?php echo esc_attr($value); ?>">
    <div class="ascora-preview">
        <?php if ($img): ?>
        <img src="<?php echo esc_url($img); ?>" alt="">
        <?php endif; ?>
    </div>
    <button type="button" class="button ascora-upload">
        <?php esc_html_e('Upload/Add image', 'ascora-c'); ?>
    </button>

    <button type="button" class="button ascora-remove">
        <?php esc_html_e('Remove image', 'ascora-wc'); ?>
    </button>


</div>
<?php
}
?>