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

namespace MSL\Net\Http\Cgi;

use MSL\Context;
use MSL\Net\Http\ContextKey;
use MSL\Net\Http\Headers;
use MSL\Net\Http\Method;
use MSL\Net\Http\ServerRequest;
use MSL\Net\Http\Version;

final class Request extends ServerRequest
{
    public static function fromGlobals(): Request
    {
        $server = $_SERVER;
        if (!array_key_exists('REQUEST_METHOD', $server)) {
            $server['REQUEST_METHOD'] = 'GET';
        }

        $ctx = Context\nil();
        $method = Method::from($server['REQUEST_METHOD'] ?? throw new \RuntimeException('Could not determine HTTP method'));
        $headers = \function_exists('getallheaders') ? Headers::fromMap(getallheaders()) : headersFromServer($server);

        if (Method::POST === $method) {
            $contentType = $headers->get('Content-Type');
            if (in_array(explode(';', $contentType), ['application/x-www-form-urlencoded', 'multipart/form-data'])) {
                $ctx = Context\withValue($ctx, ContextKey::PARSED_BODY, $_POST);
            }
        }

        $ctx = Context\withValue($ctx, ContextKey::PARSED_COOKIES, $_COOKIE);

        $uri = uriFromServer($server);
        $version = Version::from($server['SERVER_PROTOCOL'] ?? 'HTTP/1.1');
        $body = Body::create();

        return new self($ctx, $version, $method, $uri, $headers, $body);
    }
}
