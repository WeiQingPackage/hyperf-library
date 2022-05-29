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
use Hyperf\Utils\ApplicationContext;
use Hyperf\Validation\Contract\PresenceVerifierInterface;
use Hyperf\Validation\ValidatorFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\CacheInterface;
use WeiQing\Library\Exception\ReturnJsonException;

class Controller
{
    /**
     * @Inject
     */
    #[Inject]
    protected ResponseInterface $response;

    /**
     * @Inject
     */
    #[Inject]
    protected RequestInterface $request;

    /**
     * @Inject
     */
    #[Inject]
    protected CacheInterface $cache;

    /**
     * @Inject
     */
    #[Inject]
    protected SessionInterface $session;

    /**
     * @Inject
     */
    #[Inject]
    protected ValidatorFactory $validatorFactory;

    /**
     * @Inject
     */
    #[Inject]
    protected ApplicationContext $appCtx;

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
        return $this->response->json($data);
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
        return $this->response->json($data);
    }

    /**
     * 校验请求参数.
     */
    protected function _vali(array $rules, array $messages): array
    {
        $data = $this->request->all();
        try {
            $this->validatorFactory->setPresenceVerifier($this->appCtx::getContainer()->get(PresenceVerifierInterface::class));
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            var_dump("protected_function_validate: 服务器错误: {$e->getMessage()}");
            throw new ReturnJsonException("内部服务器错误: {$e->getMessage()}", 500);
        }
        $validator = $this->validatorFactory->make($data, $rules, $messages);
        if ($validator->fails()) {
            throw new ReturnJsonException($validator->errors()->first());
        }
        return $data;
    }
}
