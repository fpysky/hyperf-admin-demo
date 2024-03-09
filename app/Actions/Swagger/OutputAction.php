<?php

declare(strict_types=1);

namespace App\Actions\Swagger;

use App\Actions\AbstractAction;
use Hyperf\Codec\Json;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api')]
class OutputAction extends AbstractAction
{
    #[GetMapping(path: 'swagger')]
    public function handle(): ResponseInterface
    {
        $content = file_get_contents(BASE_PATH . '/swaggerDoc/http.json');

        return $this->response->json(Json::decode($content));
    }
}
