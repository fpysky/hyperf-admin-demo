<?php

declare(strict_types=1);

namespace App\Request;

use App\Request\Traits\RequestUtils;
use Hyperf\Validation\Request\FormRequest as Base;

class FormRequest extends Base
{
    use RequestUtils;
}
