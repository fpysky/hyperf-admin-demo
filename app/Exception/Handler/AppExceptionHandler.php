<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Extend\Log\Log;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;

/**
 * 默认的异常处理器.
 * @package App\Exception\Handler
 */
class AppExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    protected StdoutLoggerInterface $stdoutLogger;

    public function __construct(StdoutLoggerInterface $stdoutLogger)
    {
        $this->stdoutLogger = $stdoutLogger;
    }

    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stdoutLogger->error(sprintf(
            '%s[%s] in %s',
            $throwable->getMessage(),
            $throwable->getLine(),
            $throwable->getFile()
        ));
        $this->stdoutLogger->error($throwable->getTraceAsString());

        Log::get()->error($throwable->getMessage(), [
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $throwable->getTraceAsString(),
        ]);

        if (isProd()) {
            $message = ErrorCode::getMessage(ErrorCode::SERVER_ERROR);
        } else {
            $message = $throwable->getMessage();
        }

        return $response
            ->withBody($this->buildStdOutput($message, ErrorCode::SERVER_ERROR))
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(500);
    }

    public function isValid(\Throwable $throwable): bool
    {
        return true;
    }
}
