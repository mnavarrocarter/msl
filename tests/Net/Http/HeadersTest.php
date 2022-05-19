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

use MSL\IO\Temp;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \MSL\Net\Http\Headers
 */
class HeadersTest extends TestCase
{
    public function testAddAndGet(): void
    {
        $header = new Headers();
        $header->add('Set-Cookie', 'hello');
        $header->add('set-cookie', 'hello2');

        $this->assertSame('hello', $header->get('SET-COOKIE'));
        $this->assertSame('hello2', $header->values('SET-COOKIE')[1]);
    }

    public function testCopy(): void
    {
        $header = new Headers();
        $copy = $header->copy();

        $header->add('Content-Type', 'application/json');
        $copy->add('Content-Type', 'text/html');

        $this->assertSame('application/json', $header->get('Content-Type'));
        $this->assertSame('text/html', $copy->get('Content-Type'));
    }

    public function testDelete(): void
    {
        $header = Headers::fromMap([
            'content-type' => 'application/json',
        ]);

        $this->assertSame('application/json', $header->get('Content-Type'));
        $header->del('content-type');
        $this->assertSame('', $header->get('Content-Type'));
    }

    public function testWrite(): void
    {
        $buffer = Temp::make();

        $headers = Headers::fromMap([
            'content-type' => 'application/json',
        ]);

        $headers->writeTo($buffer);
        $this->assertSame(<<<'TEXT'
        Content-Type: application/json
        
        
        TEXT, (string) $buffer);
    }

    public function testIterator(): void
    {
        $headers = Headers::fromMap([
            'content-type' => 'application/json',
        ]);

        foreach ($headers as $key => $value) {
            $this->assertSame('Content-Type', $key);
            $this->assertSame('application/json', $value);
        }
    }
}
