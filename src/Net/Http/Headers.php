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

use Generator;
use IteratorAggregate;
use MSL\Fmt;
use MSL\IO\Writer;
use MSL\IO\WriterTo;
use MSL\Str;

class Headers implements IteratorAggregate, WriterTo
{
    /**
     * @var array<string,string[]>
     */
    private array $headers;

    public function __construct()
    {
        $this->headers = [];
    }

    /**
     * @param array<string,string> $map
     */
    public static function fromMap(array $map): Headers
    {
        $header = new self();
        foreach ($map as $key => $value) {
            $header->set($key, $value);
        }

        return $header;
    }

    /**
     * Adds a value to $key.
     */
    public function add(string $key, string $value): void
    {
        $this->headers[self::canonize($key)][] = $value;
    }

    /**
     * Sets the value of $key.
     *
     * This overrides previously added values of $key
     */
    public function set(string $key, string $value): void
    {
        $this->headers[self::canonize($key)] = [$value];
    }

    /**
     * Returns the first value of $key.
     *
     * If no value found, it returns an empty string
     */
    public function get(string $key): string
    {
        return $this->headers[self::canonize($key)][0] ?? '';
    }

    /**
     * @return string[]
     */
    public function values(string $key): array
    {
        return $this->headers[self::canonize($key)] ?? [];
    }

    public function del(string $key): void
    {
        unset($this->headers[self::canonize($key)]);
    }

    /**
     * Creates a copy of the headers.
     */
    public function copy(): Headers
    {
        $copy = new self();
        $copy->headers = $this->headers;

        return $copy;
    }

    /**
     * Writes the headers in wire format.
     */
    public function writeTo(Writer $writer): int
    {
        $written = 0;
        foreach ($this as $key => $value) {
            $written += $writer->write(Fmt\sprintf('%s: %s%s', $key, $value, "\n"));
        }
        // After the headers there is always an extra line for the body.
        $written += $writer->write("\n");

        return $written;
    }

    /**
     * Returns a generator that iterates over every header value.
     */
    public function getIterator(): Generator
    {
        foreach ($this->headers as $header => $values) {
            foreach ($values as $value) {
                yield $header => $value;
            }
        }
    }

    private static function canonize(string $key): string
    {
        return Str\toTitle(Str\toLower(Str\replace($key, ' ', '')), '-');
    }
}
