<?php

declare(strict_types=1);

use Hyperf\Crontab\Crontab;

return [
    'enable' => true,
    'crontab' => [
//        (new Crontab())
//            ->setType('command')
//            ->setName('自动更新API权限')
//            ->setRule('*/1 * * * *')
//            ->setCallback([
//                'command' => 'permission:scan',
//                '--disable-event-dispatcher' => true,
//            ]),
    ],
];
