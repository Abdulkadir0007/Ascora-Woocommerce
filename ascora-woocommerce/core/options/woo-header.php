<?php

declare(strict_types=1);

/**
 * Ascora Header layout Options.
 *
 * @package Ascora
 */

defined('ABSPATH') || exit;
// Prevent direct access

$opt_name = 'ascora';


Redux::set_fields(
    $opt_name,
    'header-lay',
    [
        [
            'id'       => 'wo-header-layout',
            'type'     => 'image_select',
            'title'    => esc_html__('Header Layout Style', 'ascora-core'),
            'subtitle' => esc_html__('Choose your preferred header layout style.', 'ascora-core'),
            'options'  => [
                '1' => [
                    'alt' => 'Header Layout 1',
                    'img' => ASCORA_WC_URL . 'core/options/img/wo-header.png',
                ],
            ],
            'default'  => '1',
            'required' => ['web_header_type', '=', 'woocommerce'],
        ],
    ],
);
