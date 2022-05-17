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
 * @covers \MSL\Net\Http\Request
 * @covers \MSL\Net\Uri\Query
 *
 * @internal
 */
class RequestTest extends TestCase
{
    public function testQuery(): void
    {
        $request = Request::create('POST', 'https://example.com');

        $query = $request->getParsedQuery()
            ->add('foo', 'bar')
            ->add('bar', 'foo')
            ->add('foo', 'foo')
        ;

        $request->uri = $query->inject($request->uri);

        $this->assertSame('foo=bar&foo=foo&bar=foo', $request->uri->getQuery());
    }

    public function testCreateFails(): void
    {
        $this->expectException(\ValueError::class);
        Request::create('UNKNOWN', 'https://example.com');
    }
}
