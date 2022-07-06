<?php

namespace MSL\Net\Http;

function canonicalHeader(string $key): string
{
    return \ucwords(\strtolower($key), '-');
}

function handlerFunc(callable $func): Handler
{
    return new class($func) implements Handler {

        public function __construct(private readonly \Closure $func) { }

        public function handleHTTP(ResponseWriter $writer, ServerRequest $request): void
        {
            ($this->func)($writer, $request);
        }
    };
}

/**
 * @param string $uri
 * @return Response
 * @throws TransportError
 */
function get(string $uri): Response
{
    $request = Request::create(Method::GET, $uri);
    return Client::default()->send($request);
}