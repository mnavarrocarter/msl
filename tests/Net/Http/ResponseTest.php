<?php

declare(strict_types=1);

/**
 * @project Matt's Standard Library
 * @link https://github.com/mnavarrocarter/msl
 * @package mnavarrocarter/msl
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 Matias Navarro Carter
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MSL\Net\Http;

use Closure;
use MSL\IO\Buffer;
use MSL\IO\NoopCloser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MSL\Net\Http\Headers
 * @covers \MSL\Net\Http\Response
 *
 * @internal
 */
class ResponseTest extends TestCase
{
    /**
     * @param Closure(): Response $getResponse
     *
     * @dataProvider getToStringData
     */
    public function testToString(Closure $getResponse, string $expected): void
    {
        $response = $getResponse();
        $this->assertSame($expected, (string) $response);
    }

    public function getToStringData(): array
    {
        return [
            [
                function () {
                    $resp = Response::create(204);
                    $resp->headers->add('set-cookie', 'something');
                    $resp->headers->add('set-cookie', 'something else');

                    return $resp;
                },
                <<<'TXT'
                HTTP/1.1 204 NO CONTENT
                Set-Cookie: something
                Set-Cookie: something else
                
                
                TXT
            ],
            [
                function () {
                    $resp = Response::create(200);
                    $resp->headers->add('content-type', 'application/json');
                    $resp->headers->add('set-cookie', 'something');
                    $resp->headers->add('set-cookie', 'something else');
                    $resp->body = new NoopCloser(Buffer::make('{"hello":"msg"}'));

                    return $resp;
                },
                <<<'TXT'
                HTTP/1.1 200 OK
                Content-Type: application/json
                Set-Cookie: something
                Set-Cookie: something else
                
                {"hello":"msg"}
                TXT
            ],
        ];
    }
}
