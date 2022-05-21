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

use Generator;
use IteratorAggregate;
use MSL\Net\Uri;

class Query implements IteratorAggregate, \Stringable
{
    /**
     * @var array<string,string[]>
     */
    private array $params;

    public function __construct()
    {
        $this->params = [];
    }

    public function __toString(): string
    {
        return $this->encode();
    }

    /**
     * @param array<string,string> $array
     */
    public static function fromArray(array $array): Query
    {
        $query = new self();
        foreach ($array as $key => $value) {
            $query->add($key, $value);
        }

        return $query;
    }

    public static function fromUri(Uri $uri): Query
    {
        return self::decode($uri->getQuery());
    }

    public static function decode(string $raw): Query
    {
        $query = new self();
        if ('' === $raw) {
            return $query;
        }

        $parts = explode('&', $raw);
        foreach ($parts as $part) {
            $parts = explode('=', $part, 2);
            $key = $parts[0] ?? '';
            if ('' === $key) {
                continue;
            }
            $query->add($key, $parts[1] ?? '');
        }

        return $query;
    }

    public function isEmpty(): bool
    {
        return [] === $this->params;
    }

    public function add(string $key, string $value): Query
    {
        $this->params[$key][] = $value;

        return $this;
    }

    /**
     * Sets the value of $key.
     *
     * This overrides previously added values of $key
     */
    public function set(string $key, string $value): Query
    {
        $this->params[$key] = [$value];

        return $this;
    }

    /**
     * Returns the first value of $key.
     *
     * If no value found, it returns an empty string
     */
    public function get(string $key): string
    {
        return $this->params[$key][0] ?? '';
    }

    /**
     * @return string[]
     */
    public function values(string $key): array
    {
        return $this->params[$key] ?? [];
    }

    public function del(string $key): Query
    {
        unset($this->params[$key]);

        return $this;
    }

    /**
     * Injects the string version of this query into the.
     */
    public function inject(Uri $uri): Uri
    {
        return $uri->withQuery($this->encode());
    }

    /**
     * Creates a copy of the query parameters.
     */
    public function copy(): Query
    {
        $copy = new self();
        $copy->params = $this->params;

        return $copy;
    }

    public function getIterator(): Generator
    {
        foreach ($this->params as $key => $values) {
            foreach ($values as $value) {
                yield $key => $value;
            }
        }
    }

    public function encode(): string
    {
        $str = '';
        foreach ($this as $key => $value) {
            if ('' !== $str) {
                $str .= '&';
            }
            $str .= $key;

            if ('' !== $value) {
                $str .= '='.$value;
            }
        }

        return $str;
    }
}
