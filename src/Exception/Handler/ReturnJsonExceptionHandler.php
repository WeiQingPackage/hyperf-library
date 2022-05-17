<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace WeiQing\Library\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use WeiQing\Library\Exception\ReturnJsonException;

class ReturnJsonExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        // 判断被捕获到的异常是希望被捕获的异常
        if ($throwable instanceof ReturnJsonException) {
            // 格式化输出
            $data = [
                'successful' => false,
                'message' => $throwable->getMessage(),
                'developerMessage' => '',
                'code' => $throwable->getCode(),
                'data' => null,
            ];

            // 阻止异常冒泡
            $this->stopPropagation();
            return $response->withHeader('Content-Type', 'application/json')->withHeader('Server', 'GoHttpServer')->withBody(new SwooleStream(json_encode($data, JSON_UNESCAPED_UNICODE)));
        }

        // 交给下一个异常处理器
        return $response;
        // 或者不做处理直接屏蔽异常
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理.
     */
    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
