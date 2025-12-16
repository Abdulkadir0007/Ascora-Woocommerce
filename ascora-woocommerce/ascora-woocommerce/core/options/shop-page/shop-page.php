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
                'title'     => esc_html__('Product Show Per Page', 'ascora'),
                'min'       => 1,
                'step'      => 1,
                'max'       => 32,
                'default'   => 16,

            ],
            [
            'id'       => 'shop-page-post-per-column',
            'type'     => 'select',
            'title'    => esc_html__('Product Show Per Colum', 'ascora-core'),
            'subtitle' => esc_html__('Choose the content to display on the left side of your header top.', 'ascora-core'),
            'options'  => [
                'columns-2' => 'Per columns 2 Product',
                'columns-3' => 'Per columns 3 Product',
                'columns-4' => 'Per columns 4 Product',

            ],
            'default'  => 'columns-3',
            'select2'  => ['allowClear' => false],
        ],
    ],
);
