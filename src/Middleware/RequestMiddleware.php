<?php


namespace WeiQing\Library\Middleware;

use WeiQing\Library\Extend\CommonExtend;
use Hyperf\Context\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ServerRequestInterface
     */
    protected $request;


    public function __construct(ContainerInterface $container,ServerRequestInterface $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 为每一个请求增加一个qid
        $request = Context::override(ServerRequestInterface::class,function (ServerRequestInterface $request){
            $request = $request->withAddedHeader('REQUEST_ID',$this->getRequestId());
            return $request;
        });
        // 利用协程上下文存储请求开始的时间，用来计算程序执行时间
        Context::set('REQUEST_START_DTT',microtime(true));
        // http请求标志
        Context::set('http_request_flag',true);

        $response = $handler->handle($request);
        return $response;
    }

    /**
     * 获取请求ID
     * @return string
     */
    protected function getRequestId()
    {
        $tmp = $this->request->getServerParams();
        $name = strtoupper(substr(md5(gethostname()), 12, 8));
        $remote = strtoupper(substr(md5($tmp['remote_addr']),12,8));
        $ip = strtoupper(substr(md5(CommonExtend::make()->getServerLocalIp()), 14, 4));
        return strtoupper(uniqid()). '-' . $remote. '-'.$ip.'-'. $name;
    }
}
