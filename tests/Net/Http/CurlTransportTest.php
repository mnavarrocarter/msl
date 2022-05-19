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

use function MSL\IO\readAll;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MSL\Net\Http\CurlTransport
 * @covers \MSL\Net\Http\Headers
 * @covers \MSL\Net\Http\Request
 * @covers \MSL\Net\Http\Response
 *
 * @internal
 */
class CurlTransportTest extends TestCase
{
    public function testItGetsExampleSite(): void
    {
        $transport = CurlTransport::default();
        $request = Request::create(Method::GET, 'https://example.com');
        $response = $transport->send($request);

        $this->assertStringContainsString('Example', readAll($response->body));
    }
}
