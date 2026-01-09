<?php
class Ascora_Top_Rated_Products_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'ascora_top_rated_products',
            __('Ascora: Top Rated Products', 'ascora'),
            ['description' => __('Custom top rated products widget', 'ascora')]
        );
    }

    public function widget($args, $instance)
    {
        $count = ! empty($instance['count']) ? absint($instance['count']) : 5;

        $query = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => $count,
            'meta_key'       => '_wc_average_rating',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'meta_query'     => [
                [
                    'key'     => '_wc_average_rating',
                    'compare' => '>',
                    'value'   => 0,
                ],
            ],
        ]);

        echo $args['before_widget'];

        if ($query->have_posts()) {
            echo '<ul class="ascora-widget-products">';

            while ($query->have_posts()) {
                $query->the_post();
                global $product;
                ?>
<li>
    <span class="widget-product-wrap">

        <a href="<?php the_permalink(); ?>"
            class="widget-product-img">
            <?php echo $product->get_image('thumbnail'); ?>
        </a>

        <span class="widget-product-info">
            <a href="<?php the_permalink(); ?>" class="ascora-title">
                <?php the_title(); ?>
            </a>

            <?php echo wc_get_rating_html($product->get_average_rating()); ?>
            <?php echo $product->get_price_html(); ?>
        </span>

    </span>
</li>
<?php
            }

            echo '</ul>';
        }

        wp_reset_postdata();

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $count = $instance['count'] ?? 5;
        ?>
<p>
    <label><?php esc_html_e('Number of products:', 'ascora'); ?></label>
    <input class="widefat" type="number"
        name="<?php echo $this->get_field_name('count'); ?>"
        value="<?php echo esc_attr($count); ?>">
</p>
<?php
    }
}
?>