<?php

declare(strict_types=1);
/**
 * Admin scripts & styles
 */
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['edit-tags.php', 'term.php'], true)) {
        return;
    }

    wp_enqueue_media();

    wp_register_script('ascora-cat-media', false, ['jquery'], null, true);
    wp_enqueue_script('ascora-cat-media');

    wp_add_inline_script('ascora-cat-media', "
        jQuery(document).on('click', '.ascora-upload', function(e){
            e.preventDefault();

            var wrap = jQuery(this).closest('.ascora-media-wrap');
            var input = wrap.find('input');
            var preview = wrap.find('.ascora-preview');

            var frame = wp.media({ title: 'Select Image', multiple: false });

            frame.on('select', function(){
                var file = frame.state().get('selection').first().toJSON();
                input.val(file.id);
                preview.html('<img src=\"'+(file.sizes?.thumbnail?.url || file.url)+'\">');
                wrap.addClass('has-image');
            });

            frame.open();
        });

        jQuery(document).on('click', '.ascora-remove', function(e){
            e.preventDefault();
            var wrap = jQuery(this).closest('.ascora-media-wrap');
            wrap.find('input').val('');
            wrap.find('.ascora-preview').html('');
            wrap.removeClass('has-image');
        });
    ");

    wp_add_inline_style('wp-admin', '
        .wp-clearfix #col-left .form-wrap{
                box-sizing: border-box;
                border: 1px solid #abb2c5;
                background: #FFF;
                padding:15px
        }
        .ascora-media-wrap{
        display:flex;
        align-items: self-start;
        }
        .ascora-media-wrap img, #product_cat_thumbnail img{
            width:auto;
            max-width:50px;
            height:25px;
            margin-right:5px;
            border:1px solid #ddd;
            padding:3px;
            background:#fff;
        }
        .ascora-wc-product-cat-cusotm-taxonomy .ascora-upload,.term-thumbnail-wrap .upload_image_button{
            background-color:#EFEFF0;
            border-color:#EFEFF0;
            color:#444;
        }
        .ascora-wc-product-cat-cusotm-taxonomy .ascora-remove ,.term-thumbnail-wrap .remove_image_button{
            background-color:#FA5757;
            border-color:#FA5757;
            color:#fff;
        }
        .ascora-wc-product-cat-cusotm-taxonomy .ascora-upload::before, .term-thumbnail-wrap .upload_image_button::before{
            content: "\f317"; /* upload icon */
        }
        .ascora-wc-product-cat-cusotm-taxonomy .ascora-remove::before, .term-thumbnail-wrap .remove_image_button::before{
            content: "\f182"; /* trash icon */
        }
        .ascora-wc-product-cat-cusotm-taxonomy .button::before, .term-thumbnail-wrap .upload_image_button::before, .term-thumbnail-wrap .remove_image_button::before{
            font-family: dashicons;
            font-size: 16px;
            margin-right: 6px;
            vertical-align: middle;
        }
        .ascora-media-wrap .ascora-remove{display:none;margin-left:6px}
        .ascora-media-wrap.has-image .ascora-remove{display:inline-block}
    ');
});
