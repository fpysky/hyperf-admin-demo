<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
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
        var_dump(encryptPassword('admin123'));
    }

    private function doSomething()
    {
        $this->line('Doing something...');
        sleep(5);
        $this->line('Done.');
    }

    /**
     * @throws \Exception
     */
    private function isCompleted()
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new \Exception('Fork failed');
        }
        if ($pid) {
            var_dump($pid);
            pcntl_wait($status);
            if ($status !== 0) {
                exit(-1);
            }

            return true;
        }

        return false;
    }
}
