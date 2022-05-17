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

use MSL\Context;
use MSL\IO\ReadCloser;
use MSL\Net\Uri;

class Request
{
    public Version $version;
    public Method $method;
    public Uri $uri;
    public Headers $headers;
    public ReadCloser $body;
    private Context $ctx;

    protected function __construct(Context $ctx, Version $version, Method $method, Uri $uri, Headers $headers, ReadCloser $body)
    {
        $this->version = $version;
        $this->ctx = $ctx;
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Creates an HTTP Request.
     */
    public static function create(string $method, string|Uri $uri, ReadCloser $body = Body::EMPTY): Request
    {
        if (is_string($uri)) {
            $uri = Uri::parse($uri);
        }

        return new self(Context\nil(), Version::HTTP11, Method::from($method), $uri, new Headers(), $body);
    }

    /**
     * Returns a new request with the passed context.
     */
    public function withContext(Context $ctx): Request
    {
        $clone = clone $this;
        $clone->ctx = $ctx;

        return $clone;
    }

    /**
     * Query parses the query parameters from the request uri.
     *
     * The query object is cached inside the request context for the duration
     * of the request.
     *
     * When $fresh is true, the cache is ignored and the query string is parsed
     * again.
     */
    public function getParsedQuery(bool $fresh = false): Uri\Query
    {
        $query = $this->ctx->value(ContextKey::PARSED_QUERY);
        if (!$query instanceof Uri\Query || $fresh) {
            $query = Uri\Query::decode($this->uri->getQuery());
            $this->ctx = Context\withValue($this->ctx, ContextKey::PARSED_QUERY, $query);
        }

        return $query;
    }

    /**
     * Returns the request context.
     */
    public function context(): Context
    {
        return $this->ctx;
    }
}
