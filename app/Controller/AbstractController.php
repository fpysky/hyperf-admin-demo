<?php

declare(strict_types=1);

namespace App\Controller;

use App\Extend\StandardOutput\StandardResponse;
use App\Request\HttpServer\Request;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

/**
 * @property Request $request
 */
abstract class AbstractController
{
    use StandardResponse;

    #[Inject]
    protected ContainerInterface $container;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected ResponseInterface $response;
}
