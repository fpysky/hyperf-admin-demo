<?php

declare(strict_types=1);

namespace App\Extend\StandardOutput;

use App\Constants\StatusCode;
use Hyperf\Contract\Arrayable;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Utils\Collection;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

trait StandardResponse
{
    use StandardOutput;

    /**
     * 返回数据.
     * @param $input
     * @param string $msg
     * @param int $code
     * @return PsrResponseInterface
     * @author fengpengyuan 2023/3/27
     * @modifier fengpengyuan 2023/3/27
     */
    public function success($input, string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return match (true) {
            $input instanceof LengthAwarePaginatorInterface => $this->pagination($input),
            $input instanceof Collection => $this->collection($input, $msg, $code),
            is_string($input), is_bool($input) => $this->item($input),
            $input instanceof Arrayable,
            $input instanceof \JsonSerializable,
            is_array($input),
            $input instanceof \stdClass => $this->item($input, $msg, $code),
            $input === null => $this->message(),
            default => throw new \RuntimeException("can't handle input data."),
        };
    }

    /**
     * 标准分页返回.
     * @param LengthAwarePaginatorInterface $paginator
     * @param string $msg
     * @param int $code
     * @return PsrResponseInterface
     * @author fengpengyuan 2023/3/27
     * @modifier fengpengyuan 2023/3/27
     */
    public function pagination(LengthAwarePaginatorInterface $paginator, string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return $this->buildResp($msg, $code, [
            'list' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }

    public function item($input, string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return $this->buildResp($msg, $code, $input);
    }

    /**
     * 错误返回.
     * @param string $msg
     * @param int $code
     * @param null|mixed $data
     * @return PsrResponseInterface
     * @author fengpengyuan 2023/3/27
     * @modifier fengpengyuan 2023/3/27
     */
    public function error(string $msg = '服务器错误', int $code = 200000, mixed $data = null): PsrResponseInterface
    {
        return $this->buildResp($msg, $code, $data, StatusCode::ServerError);
    }

    /**
     * 信息返回.
     * @param string $msg
     * @param int $code
     * @return PsrResponseInterface
     * @author fengpengyuan 2023/3/27
     * @modifier fengpengyuan 2023/3/27
     */
    public function message(string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return $this->buildResp($msg, $code);
    }

    /**
     * 返回集合列表.
     * @param Collection $collection
     * @param string $msg
     * @param int $code
     * @return PsrResponseInterface
     * @author fengpengyuan 2023/3/27
     * @modifier fengpengyuan 2023/3/27
     */
    public function collection(Collection $collection, string $msg = '', int $code = 200000): PsrResponseInterface
    {
        return $this->buildResp($msg, $code, $collection);
    }

    /**
     * 构造返回对象
     * @param string $msg
     * @param int $code
     * @param null|mixed $data
     * @param StatusCode $statusCode
     * @return PsrResponseInterface
     * @author fengpengyuan 2023/3/27
     * @modifier fengpengyuan 2023/3/27
     */
    private function buildResp(string $msg, int $code, mixed $data = null, StatusCode $statusCode = StatusCode::Ok): PsrResponseInterface
    {
        return $this->response
            ->json($this->buildStruct($msg, $code, $data))
            ->withStatus($statusCode->value);
    }
}
