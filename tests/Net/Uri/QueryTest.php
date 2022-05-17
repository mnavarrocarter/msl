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

namespace MSL\Net\Uri;

use PHPUnit\Framework\TestCase;

/**
 * @covers \MSL\Net\Uri\Query
 *
 * @internal
 */
class QueryTest extends TestCase
{
    public function testDecode(): void
    {
        $query = Query::decode('foo=bar&bar&foo=foo');
        $this->assertSame('foo=bar&foo=foo&bar', $query->encode());
    }
}
