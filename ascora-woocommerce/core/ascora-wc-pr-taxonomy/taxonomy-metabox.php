<?php
/**
 * Ascora WooCommerce Category Custom Fields (Clean Version)
 */

defined('ABSPATH') || exit;

/**
 * Hooks
 */
add_action('product_cat_add_form_fields', 'ascora_cat_add_fields');
add_action('product_cat_edit_form_fields', 'ascora_cat_edit_fields', 10, 2);

/**
 * Add form (Create)
 */
function ascora_cat_add_fields()
{
    ascora_cat_fields_markup();
}

/**
 * Edit form
 * @param mixed $term
 */
function ascora_cat_edit_fields($term)
{
    ascora_cat_fields_markup($term);
}

/**
 * Fields Markup
 * @param null|mixed $term
 */
function ascora_cat_fields_markup($term = null)
{
    $is_edit = $term instanceof WP_Term;
    $term_id = $is_edit ? $term->term_id : 0;

    $icon       = get_term_meta($term_id, 'ascora_cat_icon', true);
    $icon_large = get_term_meta($term_id, 'ascora_cat_icon_large', true);
    $bg         = get_term_meta($term_id, 'ascora_cat_title_bg', true);
    $desc       = get_term_meta($term_id, 'ascora_cat_extra_desc', true);

    // Nonce
    wp_nonce_field('ascora_cat_meta_nonce', 'ascora_cat_meta_nonce');

    // Wrapper tag based on screen
    $wrap_start = $is_edit ? '<tr class="form-field">' : '<div class="form-field">';
    $wrap_end   = $is_edit ? '</tr>' : '</div>';
    ?>

<?php echo $wrap_start; ?>
<?php if ($is_edit): ?>
<th><label>Category Icon</label></th>
<td><?php endif; ?>
    <?php if (!$is_edit): ?><label>Category
        Icon</label><?php endif; ?>
    <?php ascora_media_field('ascora_cat_icon', $icon); ?>
    <?php echo $wrap_end; ?>

    <?php echo $wrap_start; ?>
    <?php if ($is_edit): ?>
<th><label>Large Category Icon</label></th>
<td><?php endif; ?>
    <?php if (!$is_edit): ?><label>Large Category
        Icon</label><?php endif; ?>
    <?php ascora_media_field('ascora_cat_icon_large', $icon_large); ?>
    <?php echo $wrap_end; ?>

    <?php echo $wrap_start; ?>
    <?php if ($is_edit): ?>
<th><label>Category Title Background</label></th>
<td><?php endif; ?>
    <?php if (!$is_edit): ?><label>Category Title
        Background</label><?php endif; ?>
    <?php ascora_media_field('ascora_cat_title_bg', $bg); ?>
    <?php echo $wrap_end; ?>

    <?php echo $wrap_start; ?>
    <?php if ($is_edit): ?>
<th><label>Extra Description</label></th>
<td><?php endif; ?>
    <?php if (!$is_edit): ?><label>Extra
        Description</label><?php endif; ?>

    <?php
        wp_editor(
            $desc,
            'ascora_cat_extra_desc',
            [
                'textarea_name' => 'ascora_cat_extra_desc',
                'textarea_rows' => 6,
                'media_buttons' => true,
                'teeny'         => false,
            ]
        );
    ?>
    <?php echo $wrap_end; ?>

    <?php if ($is_edit): ?>
</td><?php endif; ?>
<?php
}
?>