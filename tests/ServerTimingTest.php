<?php
declare(strict_types = 1);

namespace ChiTeck\ServerTimingMiddleware\Tests;

use ChiTeck\ServerTimingMiddleware\ServerTiming;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;

class ServerTimingTest extends TestCase
{
    public function testServerTiming(): void
    {
        $middleware = new ServerTiming();

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                // Set initial Server-Timing header to make sure it is merged
                // with the one provided by the middleware.
                return (new JsonResponse([], 200, ['Server-Timing' => 'example;desc="Example";dur=456']));
            }
        };

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', 'https://example.com', ['REQUEST_TIME_FLOAT' => 1000]);

        ClockMock::register(__CLASS__);
        ClockMock::withClockMock($request->getServerParams()['REQUEST_TIME_FLOAT'] + 0.123_456_789);

        $response = $middleware->process($request, $handler);

        ClockMock::withClockMock(false);

        $expected_headers = [
            'example;desc="Example";dur=456',
            'total;desc="Request execution time";dur=123.457',
        ];
        self::assertSame($expected_headers, $response->getHeaders()['Server-Timing']);
    }
}
