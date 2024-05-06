<?php

declare(strict_types=1);

namespace App\Command;

use App\Annotation\Permission;
use App\Controller\Permission\AdminController;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Controller;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
#[Command]
class Test extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('test');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $collector = AnnotationCollector::list();
        foreach ($collector as $className => $metadata) {
            if(isset($metadata['_c']) && isset($metadata['_c'][Controller::class])) {
                foreach ($metadata['_m'] as $key => $value){
                    if(isset($value[Permission::class])){
                        var_dump($className);
                    }
                }
            }
        }
    }
}
