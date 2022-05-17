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
use Hyperf\Validation\ValidatorFactory;
use Psr\SimpleCache\CacheInterface;
use WeiQing\Library\Exception\ReturnJsonException;

class Controller
{
    /**
     * @Inject
     */
    protected ResponseInterface $response;

    /**
     * @Inject
     */
    protected RequestInterface $request;

    /**
     * @Inject
     */
    protected CacheInterface $cache;

    /**
     * @Inject
     */
    protected SessionInterface $session;

    /**
     * @Inject
     */
    protected ValidatorFactory $validatorFactory;

    protected function tableJson($list, $column = [], $developerMessage = ''): \Psr\Http\Message\ResponseInterface
    {
        if (! empty($column)) {
            $list['column'] = $column;
        }
        $data = [
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
            'successful' => $successful,
            'message' => $message,
            'developerMessage' => $developerMessage,
            'code' => $code,
            'data' => null,
        ];
        return $this->response->withAddedHeader('x-log-request-id', $data['rid'])->json($data);
    }

    /**
     * 校验请求参数.
     */
    protected function _vali(array $rules, array $messages): array
    {
        var_dump("进来了");
        $data = $this->request->all();
        var_dump($data);
        $validator = $this->validatorFactory->make($data, $rules, $messages);
        var_dump($validator);
        if ($validator->fails()) {
            var_dump("错误了");
            throw new ReturnJsonException($validator->errors()->first());
        }
        var_dump("没触发");
        return $data;
    }
}
