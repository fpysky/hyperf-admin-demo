<?php

declare(strict_types=1);

namespace App\Command\Generator;

use Hyperf\Command\Annotation\Command;
use Hyperf\Devtool\Generator\GeneratorCommand;

#[Command]
class RequestCommand extends GeneratorCommand
{
    public function __construct()
    {
        parent::__construct('_gen:request');
    }

    public function configure()
    {
        $this->setDescription('Create a new form request class');

        parent::configure();
    }

    protected function getStub(): string
    {
        return $this->getConfig()['stub'] ?? __DIR__ . '/stubs/validation-request.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Request';
    }
}
