<?php
class Ascora_Price_Filter_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'ascora_price_filter',
            __('Ascora – Price Filter (AJAX)', 'ascora'),
            ['description' => __('Price range slider with AJAX filter', 'ascora')]
        );
    }

    // WIDGET FRONTEND
    public function widget($args, $instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : 'Filter by Price';

        global $wpdb;

        $max_price = (int) $wpdb->get_var("
            SELECT MAX(meta_value+0) 
            FROM $wpdb->postmeta 
            WHERE meta_key = '_price'
        ");

        if ($max_price < 1) {
            $max_price = 5000;
        }

        echo $args['before_widget'];
        ?>

<div class="ascora-price-widget">
    <h3 class="widget-title"><?php echo esc_html($title); ?></h3>



    <div id="ascora-price-slider"></div>

    <input type="hidden" id="ascora-price-min" value="0">
    <input type="hidden" id="ascora-price-max"
        value="<?php echo $max_price; ?>">
    <div class="ascora-price-slider-widgets-footer">
        <div class="price-range-values">
            <span>Price :</span>
            <span id="ascora-price-min-val">$ 0</span> –
            <span id="ascora-price-max-val">$
                <?php echo $max_price; ?></span>
        </div>
        <button id="ascora-price-apply" class="ascora-button widgets-button">Apply Filter</button>
    </div>
</div>

<?php
        echo $args['after_widget'];
    }

    // BACKEND FORM
    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : 'Filter by Price';
        ?>

<p>
    <label
        for="<?php echo $this->get_field_id('title'); ?>">Widget
        Title:</label>
    <input class="widefat"
        id="<?php echo $this->get_field_id('title'); ?>"
        name="<?php echo $this->get_field_name('title'); ?>"
        type="text" value="<?php echo esc_attr($title); ?>">
</p>

<?php
    }

    // SAVE DATA
    public function update($new_instance, $old_instance)
    {
        $instance          = [];
        $instance['title'] = sanitize_text_field($new_instance['title']);

        return $instance;
    }
}
?>