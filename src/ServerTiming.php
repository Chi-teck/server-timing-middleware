<?php
declare(strict_types = 1);

namespace ChiTeck\ServerTimingMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ServerTiming implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $server = $request->getServerParams();
        $startTime = $server['REQUEST_TIME_FLOAT'];
        $response = $handler->handle($request);

        $timing = sprintf('total;desc="Request execution time";dur=%.2f', 1000 * (microtime(true) - $startTime));
        return $response->withAddedHeader('Server-Timing', $timing);
    }
}
