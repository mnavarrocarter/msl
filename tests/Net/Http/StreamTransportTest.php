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

use PHPUnit\Framework\TestCase;

/**
 * @covers \MSL\Net\Http\Headers
 * @covers \MSL\Net\Http\Request
 * @covers \MSL\Net\Http\Response
 * @covers \MSL\Net\Http\StreamBody
 * @covers \MSL\Net\Http\StreamTransport
 *
 * @internal
 */
class StreamTransportTest extends TestCase
{
    public function testItStreamsALargeFile(): void
    {
        $this->markAsRisky();

        $transport = StreamTransport::default();

        $start = time();
        $request = Request::create(Method::GET, 'https://releases.ubuntu.com/22.04/ubuntu-22.04-desktop-amd64.iso');
        $response = $transport->send($request);
        $end = time() - $start;

        // We are asserting that we are holding the data on a stream
        // and not downloading the whole thing at once.
        $this->assertLessThan(3, $end);

        // We close the stream, so we don't leave the server hanging
        $response->body->close();
    }
}
