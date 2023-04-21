<?php

declare(strict_types=1);

namespace App\Middleware;

use App\AdminRbac\Model\AdminVisitLog;
use App\Utils\Help;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class VisitLogMiddleware implements MiddlewareInterface
{
    protected HttpResponse $response;

    protected RequestInterface $request;

    protected Help $help;

    public function __construct(HttpResponse $response, RequestInterface $request, Help $help)
    {
        $this->response = $response;
        $this->request = $request;
        $this->help = $help;
    }

    public function process(serverRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->savaData();
        return $handler->handle($request);
    }

    private function savaData(): void
    {
        if ($this->help->getAdminId()) {
            $url = $this->request->url();
            $saveData = [
                'server' => !str_contains($url, 'https') ? 'http' : 'https',
                'method' => $this->request->getMethod(),
                'url' => $url,
                'params' => json_encode($this->request->all()),
                'admin_id' => $this->help->getAdminId(),
                'create_time' => time(),
                'ip' => $this->help->ipToInt($this->help->getIp()),
            ];
            AdminVisitLog::create($saveData);
        }
    }
}
