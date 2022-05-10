<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace WeiQing\Library;

use Hyperf\Session\Middleware\SessionMiddleware;
use WeiQing\Library\Middleware\CorsMiddleware;
use WeiQing\Library\Middleware\RequestMiddleware;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'admin' => \Hyperf\HttpServer\Server::class
            ],
            'commands' => [
            ],
            'middlewares' => [
                'http' => [
                    CorsMiddleware::class,
                    RequestMiddleware::class,
                    SessionMiddleware::class,
                ],
                'admin' => [
                    CorsMiddleware::class,
                    RequestMiddleware::class,
                    SessionMiddleware::class
                ]
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}
