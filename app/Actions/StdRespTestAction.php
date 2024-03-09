<?php

declare(strict_types=1);

namespace App\Actions;

use App\Extend\Log\Log;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api')]
class StdRespTestAction extends AbstractAction
{
    #[RequestMapping(path: 'stdRespTest', methods: ['GET', 'POST'])]
    public function handle(): ResponseInterface
    {
        Log::get('order', 'orderCallback')->info('1111');
        return $this->success($this->request->input('data'));
    }
}
