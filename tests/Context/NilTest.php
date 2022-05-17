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

use PHPUnit\Framework\TestCase;

/**
 * @covers \MSL\Context\Nil
 *
 * @internal
 */
class NilTest extends TestCase
{
    public function testValue(): void
    {
        $ctx = nil();
        $this->assertNull($ctx->value('key'));
    }
}
