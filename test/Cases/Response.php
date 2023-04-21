<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

class Response
{
    private int $code;

    private string $msg;

    private mixed $data;

    private string $rawResp;

    public function __construct(int $code, string $msg,mixed $data,string $rawResp)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
        $this->rawResp = $rawResp;
    }

    /**
     * @return string
     */
    public function getRawResp(): string
    {
        return $this->rawResp;
    }

    /**
     * @param string $rawResp
     */
    public function setRawResp(string $rawResp): void
    {
        $this->rawResp = $rawResp;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg(string $msg): void
    {
        $this->msg = $msg;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }
}
