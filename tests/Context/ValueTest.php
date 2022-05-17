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

namespace MSL\Context;

use MSL\Context;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MSL\Context\Value
 *
 * @internal
 */
class ValueTest extends TestCase
{
    public function testWithValue(): void
    {
        $ctx = $this->createMock(Context::class);
        $ctx->expects($this->once())
            ->method('value')
            ->with('bar')
            ->willReturn(null)
        ;

        $ctx = withValue($ctx, 'foo', 'bar');
        $this->assertSame('bar', $ctx->value('foo'));
        $this->assertNull($ctx->value('bar'));
    }
}
