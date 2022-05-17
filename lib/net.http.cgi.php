<?php

namespace MSL\Net\Http\Cgi;

use MSL\IO\Error;
use MSL\Net\Http\Handler;
use MSL\Net\Http\Headers;
use MSL\Net\Uri;

/**
 * The serve method runs a handler in a CGI context.
 *
 * It parses the request from the available super globals and provides a response
 * writer that handles sending the header and the content back to the client.
 *
 * It also processes uploaded files and keys if the request is of the multipart
 * content type.
 *
 * If $unsetGlobals is true, after parsing the Request information, the
 * super globals are removed from scope. This can cause some undesirable
 * side-effects in some third party libraries.
 *
 * @throws Error
 */
function serve(Handler $handler): void
{
    if (PHP_SAPI === 'cli') {
        throw new Error('Cannot serve in a non CGI context');
    }

    $writer = ResponseWriter::create();
    $request = Request::fromGlobals();

    $handler->handleHTTP($writer, $request);
    $writer->flush();
    $request->body->close();
}

function headersFromServer(array $server = []): Headers
{
    $server = $server ?? $_SERVER;

    $headers = new Headers();
    foreach ($server as $key => $value) {
        // Apache prefixes environment variables with REDIRECT_
        // if they are added by rewrite rules
        if (0 === \strpos($key, 'REDIRECT_')) {
            $key = \substr($key, 9);

            // We will not overwrite existing variables with the
            // prefixed versions, though
            if (\array_key_exists($key, $server)) {
                continue;
            }
        }

        if ($value && 0 === \strpos($key, 'HTTP_')) {
            $name = \strtr(\substr($key, 5), '_', '-');
            $headers->add($name, $value);

            continue;
        }

        if ($value && 0 === \strpos($key, 'CONTENT_')) {
            $name = 'content-'.\substr($key, 8);
            $headers->add($name, $value);
        }
    }

    return $headers;
}

/**
 * @param array $server
 * @return Uri
 */
function uriFromServer(array $server = []): Uri
{
    $server = $server ?? $_SERVER;

    $uri = new Uri();
    if (isset($server['HTTP_X_FORWARDED_PROTO'])) {
        $uri = $uri->withScheme($server['HTTP_X_FORWARDED_PROTO']);
    } else {
        if (isset($server['REQUEST_SCHEME'])) {
            $uri = $uri->withScheme($server['REQUEST_SCHEME']);
        } elseif (isset($server['HTTPS'])) {
            $uri = $uri->withScheme('on' === $server['HTTPS'] ? 'https' : 'http');
        }

        if (isset($server['SERVER_PORT'])) {
            $uri = $uri->withPort($server['SERVER_PORT']);
        }
    }

    if (isset($server['HTTP_HOST'])) {
        if (1 === \preg_match('/^(.+)\:(\d+)$/', $server['HTTP_HOST'], $matches)) {
            $uri = $uri->withHost($matches[1])->withPort($matches[2]);
        } else {
            $uri = $uri->withHost($server['HTTP_HOST']);
        }
    } elseif (isset($server['SERVER_NAME'])) {
        $uri = $uri->withHost($server['SERVER_NAME']);
    }

    if (isset($server['REQUEST_URI'])) {
        $uri = $uri->withPath(\current(\explode('?', $server['REQUEST_URI'])));
    }

    if (isset($server['QUERY_STRING'])) {
        $uri = $uri->withQuery($server['QUERY_STRING']);
    }

    return $uri;
}