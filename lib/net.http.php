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
