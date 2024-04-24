<?php

namespace App\Controller;

use Hyperf\Codec\Json;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api')]
class SwaggerController extends AbstractController
{
    #[GetMapping(path: 'swagger')]
    public function handle(): ResponseInterface
    {
        $content = file_get_contents(BASE_PATH . '/swaggerDoc/http.json');

        return $this->response->json(Json::decode($content));
    }
}