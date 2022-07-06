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

class Uri implements \Stringable
{
    private string $scheme;
    private string $user;
    private string $pass;
    private string $host;
    private int $port;
    private string $path;
    private string $query;
    private string $fragment;

    public function __construct(
        string $scheme = '',
        string $user = '',
        string $pass = '',
        string $host = '',
        int $port = 0,
        string $path = '',
        string $query = '',
        string $fragment = ''
    ) {
        $this->scheme = $scheme;
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function parse(string $raw): Uri
    {
        return new self(...parse_url($raw));
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme(string $scheme): Uri
    {
        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    public function withUser(string $user): Uri
    {
        $clone = clone $this;
        $clone->user = $user;

        return $clone;
    }

    public function withPass(string $pass): Uri
    {
        $clone = clone $this;
        $clone->pass = $pass;

        return $clone;
    }

    public function withHost(string $host): Uri
    {
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    public function withPort(int $port): Uri
    {
        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    public function withPath(string $path): Uri
    {
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    public function withQuery(string $query): Uri
    {
        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    public function withFragment(string $fragment): Uri
    {
        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    public function toString(): string
    {
        $uri = '';

        if ('' !== $this->scheme) {
            $uri .= $this->scheme.':';
        }

        $authority = $this->getAuthority();
        if ('' !== $authority) {
            $uri .= '//'.$authority;
        }

        if ('' !== $this->path) {
            $uri .= $this->path;
        }

        if ('' !== $this->query) {
            $uri .= '?'.$this->query;
        }

        if ('' !== $this->fragment) {
            $uri .= '#'.$this->fragment;
        }

        return $uri;
    }

    public function getUserInfo(bool $obfuscated = false): string
    {
        $userInfo = '';
        if ('' !== $this->user) {
            $userInfo .= $this->user;
        }
        if ('' !== $this->pass) {
            $userInfo .= ':'.$obfuscated ? '****' : $this->pass;
        }

        return $userInfo;
    }

    public function getAuthority(): string
    {
        $auth = '';
        $userInfo = $this->getUserInfo();
        if ('' !== $userInfo) {
            $auth .= $userInfo.'@';
        }
        if ('' !== $this->host) {
            $auth .= $this->host;
        }
        if (0 !== $this->port) {
            $auth .= ':'.$this->port;
        }

        return $auth;
    }
}
