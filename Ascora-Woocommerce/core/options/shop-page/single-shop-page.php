<?php

declare(strict_types=1);
Redux::set_fields(
    $opt_name,
    'ascora-single-shop',
    [
        [
                'id'       => 'as-shop-single',
                'type'     => 'select',
                'title'    => __('Archive Layout', 'ascora'),
                'options'  => [
                    'grid' => 'Grid View',
                    'list' => 'List View',
                ],
                'default' => 'grid',

        ]
    ],
);
