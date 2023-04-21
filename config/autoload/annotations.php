<?php

declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;

return [
    'scan' => [
        'paths' => [
            BASE_PATH . '/app',
        ],
        'ignore_annotations' => [
            'mixin',
            ArrayShape::class,
        ],
    ],
];
