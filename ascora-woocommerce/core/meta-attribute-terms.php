<?php
// Add Color Picker Field on Add Term screen
add_action('pa_color_add_form_fields', function () {
    ?>
<div class="form-field">
    <label
        for="term_color_code"><?php esc_html_e('Color Code', 'ascora'); ?></label>
    <input type="color" name="term_color_code" id="term_color_code" value="#ffffff">
    <p class="description">
        <?php esc_html_e('Choose a color for this term.', 'ascora'); ?>
    </p>
</div>
<?php
});

// Add Color Picker Field on Edit Term screen
add_action('pa_color_edit_form_fields', function ($term) {
    $color = get_term_meta($term->term_id, 'term_color_code', true);
    ?>
<tr class="form-field">
    <th><label
            for="term_color_code"><?php esc_html_e('Color Code', 'ascora'); ?></label>
    </th>
    <td>
        <input type="color" name="term_color_code" id="term_color_code"
            value="<?php echo esc_attr($color ?: '#ffffff'); ?>">
        <p class="description">
            <?php esc_html_e('Choose a color for this term.', 'ascora'); ?>
        </p>
    </td>
</tr>
<?php
});

// Save term meta (Add and Edit)
add_action('created_pa_color', 'save_color_term_meta');
add_action('edited_pa_color', 'save_color_term_meta');
function save_color_term_meta($term_id)
{
    if (isset($_POST['term_color_code'])) {
        update_term_meta($term_id, 'term_color_code', sanitize_hex_color($_POST['term_color_code']));
    }
}
?>