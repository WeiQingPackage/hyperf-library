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
namespace WeiQing\Library;

use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

class Controller
{
    /**
     * @Inject
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @Inject
     * @var CacheInterface
     */
    protected CacheInterface $cache;

    /**
     * @Inject
     * @var SessionInterface
     */
    protected SessionInterface $session;

    protected function tableJson($list, $column = [], $developerMessage = ''): \Psr\Http\Message\ResponseInterface
    {
        if (! empty($column)) {
            $list['column'] = $column;
        }
        $data = [
            'rid' => $this->request->header('REQUEST_ID'),
            'successful' => true,
            'message' => 'SUCCESS',
            'developerMessage' => $developerMessage,
            'code' => 200,
            'data' => $list,
        ];
        return $this->response->json($data);
    }

    protected function success(array $data = [], string $message = 'SUCCESS', string $developerMessage = ''): \Psr\Http\Message\ResponseInterface
    {
        $data = [
            'rid' => $this->request->header('REQUEST_ID'),
            'successful' => true,
            'message' => $message,
            'developerMessage' => $developerMessage,
            'code' => 200,
            'data' => $data,
        ];
        return $this->response->withAddedHeader('x-log-request-id', $data['rid'])->json($data);
    }

    /**
     * @return mixed
     */
    protected function error(string $message, int $code = 500, bool $successful = true, string $developerMessage = '')
    {
        $data = [
            'rid' => $this->request->header('REQUEST_ID'),
            'successful' => $successful,
            'message' => $message,
            'developerMessage' => $developerMessage,
            'code' => $code,
            'data' => null,
        ];
        return $this->response->withAddedHeader('x-log-request-id', $data['rid'])->json($data);
    }
}
