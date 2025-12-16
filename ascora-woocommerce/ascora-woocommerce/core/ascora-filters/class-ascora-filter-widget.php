<?php

class Ascora_Filter_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'ascora_filter_widget',
            __('Ascora Product Filter', 'ascora'),
            ['description' => __('AJAX Product Filter Widget', 'ascora')]
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        ?>

<div id="ascora-filter-widget">

    <!-- PRICE FILTER -->
    <div class="filter-section">
        <h4>Price</h4>
        <input type="range" id="min_price" min="0" max="1550" step="10">
        <input type="range" id="max_price" min="0" max="1550" step="10">
        <div class="price-values">
            <span id="price-min-val">0</span> - <span id="price-max-val">1550</span>
        </div>
    </div>

    <!-- CATEGORY FILTER -->
    <div class="filter-section">
        <h4>Categories</h4>
        <?php
                $terms = get_terms(['taxonomy' => 'product_cat','hide_empty' => true]);
        foreach ($terms as $term):
            ?>
        <label>
            <input type="checkbox" class="filter-cat"
                value="<?php echo $term->term_id; ?>">
            <?php echo $term->name; ?>
        </label><br>
        <?php endforeach; ?>
    </div>

    <!-- COLOR ATTRIBUTE -->
    <div class="filter-section">
        <h4>Color</h4>
        <?php
            $colors = wc_get_product_terms(get_the_ID(), 'pa_color');
        foreach ($colors as $color):
            ?>
        <label>
            <input type="checkbox" class="filter-color"
                value="<?php echo $color->slug; ?>">
            <?php echo $color->name; ?>
        </label><br>
        <?php endforeach; ?>
    </div>

    <!-- SIZE ATTRIBUTE -->
    <div class="filter-section">
        <h4>Size</h4>
        <?php
            $sizes = wc_get_attribute_taxonomies();
        foreach ($sizes as $size):
            ?>
        <label>
            <input type="checkbox" class="filter-size"
                value="<?php echo $size->attribute_name; ?>">
            <?php echo $size->attribute_label; ?>
        </label><br>
        <?php endforeach; ?>
    </div>

    <!-- RATING -->
    <div class="filter-section">
        <h4>Rating</h4>
        <?php for ($i = 5; $i >= 1; $i--): ?>
        <label>
            <input type="radio" name="filter-rating" class="filter-rating"
                value="<?php echo $i; ?>">
            <?php echo $i; ?> ★ & Up
        </label><br>
        <?php endfor; ?>
    </div>

</div>

<?php
            echo $args['after_widget'];
    }
} // end class
?>