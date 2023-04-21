<?php

declare(strict_types=1);

return [
    'enable' => false,
    'port' => 9505,
    'json_dir' => BASE_PATH . '/swaggerDoc',
    'html' => null,
    'url' => '/swagger',
    'auto_generate' => true,
    'scan' => [
        'paths' => ['app/Actions'],
    ],
];
