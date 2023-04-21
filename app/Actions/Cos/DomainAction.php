<?php

declare(strict_types=1);

namespace App\Actions\Cos;

use App\Actions\AbstractAction;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class DomainAction extends AbstractAction
{
    #[GetMapping(path: '/system/backend/backendCos/domain')]
    public function handle(): ResponseInterface
    {
        return $this->item('http://awbapp-devtest-1301011274.cos.ap-guangzhou.myqcloud.com');
    }
}
