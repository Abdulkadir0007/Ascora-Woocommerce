<?php

declare(strict_types=1);


/**
 * Ascora Wc Shop Page Options.
 *
 * @package Ascora
 */

defined('ABSPATH') || exit;

if (! class_exists('Redux')) {
    return;
}



Redux::set_fields(
    $opt_name,
    'ascora-main-shop',
    [
            [
                'id'        => 'shop-page-post-per',
                'type'      => 'slider',
                'title'     => esc_html__('Product Show Per Page', 'ascora-wc'),
                'min'       => 1,
                'step'      => 1,
                'max'       => 32,
                'default'   => 16,

            ],
            [
            'id'       => 'shop-page-post-per-column',
            'type'     => 'select',
            'title'    => esc_html__('Product Show Per Colum', 'ascora-wc'),
            'subtitle' => esc_html__('Choose the content to display on the left side of your header top.', 'ascora-wc'),
            'options'  => [
                'columns-2' => 'Per columns 2 Product',
                'columns-3' => 'Per columns 3 Product',
                'columns-4' => 'Per columns 4 Product',

            ],
            'default'  => 'columns-3',
            'select2'  => ['allowClear' => false],
            ],
            [
                'id'           => 'shop-page-hero-img',
                'type'         => 'media',
                'url'          => false,
                'remove'       => false,
                'title'        => esc_html__('Shop Page Hero Bannar Image', 'ascora-wc'),
                'desc'         => esc_html__('Upload Shop Page Hero Bannar Image.', 'ascora-wc'),
                'subtitle'     => esc_html__('Upload any media For Shop Page Hero Bannar Image.', 'ascora-wc'),
                'default'      => [
                    'url' => ASCORA_WC_URL . 'core/options/img/hero-banner.jpg',
                ],
            ],
            [
                'id'       => 'shop-page-hero-description',
                'title'    => esc_html__('Shop Page Hero Bannar Description', 'ascora-wc'),
                'subtitle' => esc_html__('Shop Page Hero Bannar Description', 'ascora-wc'),
                'type'     => 'textarea',
                'default'  => esc_html__('This is where you can browse products in this store', 'ascora-wc'),
            ],
    ]
);
