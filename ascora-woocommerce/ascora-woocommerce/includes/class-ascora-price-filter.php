<?php

class Ascora_Price_Filter_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'ascora_price_filter_widget',
            'Ascora: AJAX Price Filter',
            [
                'description'  => 'AJAX based price filter with range slider',
                'show_in_rest' => false,
            ]
        );
    }

    public function widget($args, $instance)
    {
        if (!is_shop() && !is_product_category()) {
            return;
        }

        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');

        $title = $instance['title'] ?? 'Filter by price';

        // Get max price
        global $wpdb;
        $max_price = (int) $wpdb->get_var("
            SELECT MAX(CAST(pm.meta_value AS UNSIGNED))
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_price' AND p.post_status='publish'
        ");

        $current_min = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
        $current_max = isset($_GET['max_price']) ? intval($_GET['max_price']) : $max_price;

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        ?>

<div class="ascora-price-filter"
    data-max="<?php echo esc_attr($max_price); ?>"
    data-min="<?php echo esc_attr($current_min); ?>"
    data-max-current="<?php echo esc_attr($current_max); ?>">

    <div id="ascora-price-slider"></div>

    <div class="ascora-price-values">
        <span>৳ <span
                id="ascora-min-val"><?php echo $current_min; ?></span></span>
        <span> — </span>
        <span>৳ <span
                id="ascora-max-val"><?php echo $current_max; ?></span></span>
    </div>

    <button id="ascora-price-apply" class="ascora-btn">Apply</button>
</div>

<?php
        echo $args['after_widget'];
    }

    public function update($new, $old)
    {
        return ['title' => sanitize_text_field($new['title'])];
    }

    public function form($instance)
    {
        $title = $instance['title'] ?? 'Filter by price';
        ?>
<p>
    <label>Title:</label>
    <input type="text" class="widefat"
        name="<?php echo $this->get_field_name('title'); ?>"
        value="<?php echo esc_attr($title); ?>">
</p>
<?php
    }
}

add_action('widgets_init', function () {
    register_widget('Ascora_Price_Filter_Widget');
});
?>