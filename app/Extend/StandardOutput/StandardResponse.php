<?php

declare(strict_types=1);

namespace App\Extend\StandardOutput;

use App\Constants\StatusCode;
use Hyperf\Collection\Collection;
use Hyperf\Contract\Arrayable;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

trait StandardResponse
{
    use StandardOutput;

    public function success(mixed $input, string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return match (true) {
            $input instanceof LengthAwarePaginatorInterface => $this->pagination($input, $msg, $code),
            $input instanceof Collection => $this->collection($input, $msg, $code),
            is_string($input) || is_bool($input) => $this->item($input, $msg, $code),
            $input instanceof Arrayable ||
            $input instanceof \JsonSerializable ||
            is_array($input) ||
            $input instanceof \stdClass => $this->item($input, $msg, $code),
            null => $this->message(),
            default => throw new \RuntimeException("Cannot handle input data."),
        };
    }

    public function pagination(LengthAwarePaginatorInterface $paginator, string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return $this->buildResp($msg, $code, [
            'list' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }

    public function item(mixed $input, string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return $this->buildResp($msg, $code, $input);
    }

    public function error(string $msg = '服务器错误', int $code = 200000, mixed $data = null): PsrResponseInterface
    {
        return $this->buildResp($msg, $code, $data, StatusCode::ServerError);
    }

    public function message(string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return $this->buildResp($msg, $code);
    }

    public function collection(Collection $collection, string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return $this->buildResp($msg, $code, $collection);
    }

    private function buildResp(string $msg, int $code, mixed $data = null, StatusCode $statusCode = StatusCode::Ok): PsrResponseInterface
    {
        return $this->response
            ->json($this->buildStruct($msg, $code, $data))
            ->withStatus($statusCode->value);
    }
}
