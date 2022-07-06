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

namespace MSL\Net;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UriTest extends TestCase
{
    /**
     * @dataProvider getUris
     */
    public function testParsingAndString(string $uri): void
    {
        self::assertSame($uri, Uri::parse($uri)->toString());
    }

    public function getUris(): array
    {
        return [
            ['https://example.com/hello'],
            ['https://127.0.0.1:8000/path?foo=bar#hello'],
            ['mailto:jdoe@example.com'],
            ['/hello?foo=bar'],
        ];
    }
}
