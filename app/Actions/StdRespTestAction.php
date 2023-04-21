<?php

declare(strict_types=1);

namespace App\Actions;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class StdRespTestAction extends AbstractAction
{
    #[RequestMapping(path: '/stdRespTest', methods: ['GET', 'POST'])]
    public function handle(): ResponseInterface
    {
        return $this->success($this->request->input('data'));
    }
}
