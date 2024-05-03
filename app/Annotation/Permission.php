<?php

declare(strict_types=1);

namespace App\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Permission extends AbstractAnnotation
{
    public function __construct(
        public string $name,
        public string $module,
    ) {}
}
