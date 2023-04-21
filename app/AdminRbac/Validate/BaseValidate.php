<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

use App\Exception\UnprocessableEntityException;
use App\Utils\Help;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class BaseValidate
{
    #[Inject]
    protected Help $help;

    #[Inject]
    protected ValidatorFactoryInterface $validationFactory;

    protected function checkInfo($data, $rules, $message): string
    {
        $validator = $this->validationFactory->make($data, $rules, $message);
        if ($validator->fails()) {
            throw new UnprocessableEntityException($validator->errors()->first());
        }
        return '';
    }
}
