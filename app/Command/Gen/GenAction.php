<?php

declare(strict_types=1);

namespace App\Command\Gen;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class GenAction extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('gen:action');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
        $this->addArgument('name');
    }

    public function handle()
    {
        $name = $this->input->getArgument('name');
        $this->buildAction($name);
        $this->line('Hello Hyperf!', 'info');
    }

    public function buildAction(string $name)
    {
        $stub = file_get_contents(__DIR__ . '/stubs/Action.stub');
        $stub = str_replace('%CLASS%',$name,$stub);
        $stub = str_replace('%NAMESPACE%',$name,$stub);

        $path = BASE_PATH."/app/Actions/{$name}.php";
        $this->mkdir($path);
        file_put_contents($path,$stub);
    }

    protected function mkdir(string $path): void
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }
}
