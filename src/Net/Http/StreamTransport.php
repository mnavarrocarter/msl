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

use MSL\Arr;
use MSL\Fmt;
use MSL\IO;
use MSL\Str;

/**
 * StreamTransport uses PHP native streams to make an HTTP request.
 *
 * This transport is more memory efficient than CurlTransport, since cURL stores
 * the whole stream in memory upon fetching.
 *
 * This transport streams the contents directly from the server without relying
 * on temp storage.
 */
final class StreamTransport implements Transport
{
    private static ?StreamTransport $instance = null;

    public function __construct(
        private readonly bool $followRedirects = true,
        private readonly string $proxy = '',
        private readonly int $maxRedirects = 10,
        private readonly float $timeout = -1,
        private readonly bool $sslVerify = true,
    ) {
    }

    public static function default(): StreamTransport
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @throws TransportError
     */
    public function send(Request $request): Response
    {
        $context = [
            'http' => [
                'method' => $request->method->value,
                'header' => $this->convertHeaders($request->headers),
                'contents' => IO\readAll($request->body),
                'ignore_errors' => true,
                'follow_location' => $this->followRedirects ? 1 : 0,
                'max_redirects' => $this->maxRedirects,
                'protocol_version' => $request->version->toFloat(),
            ],
            'ssl' => [
                'verify_peer' => $this->sslVerify,
            ],
        ];

        if ($this->timeout >= 0) {
            $context['http']['timeout'] = $this->timeout;
        }

        if ('' !== $this->proxy) {
            $context['http']['proxy'] = $this->proxy;
            $context['http']['request_fulluri'] = true;
        }

        $resource = @fopen((string) $request->uri, 'rb', false, stream_context_create($context));
        if (!is_resource($resource)) {
            throw new TransportError(error_get_last()['message'] ?? 'Unknown error', 0, $request);
        }

        stream_set_blocking($resource, false);

        // We extract relevant stream meta data
        $meta = stream_get_meta_data($resource);

        $responses = $this->parseResponses($meta['wrapper_data'] ?? []);

        // Pick the last response and put the body there.
        $response = $responses[count($responses) - 1];

        $response->body = StreamBody::make($resource);

        return $response;
    }

    /**
     * @return string[]
     */
    private function convertHeaders(Headers $headers): array
    {
        $streamHeaders = [];
        foreach ($headers as $key => $value) {
            $streamHeaders[] = Fmt\sprintf('%s: %s', $key, $value);
        }

        return $streamHeaders;
    }

    /**
     * @param mixed $lines
     */
    private function parseResponses(array $lines): array
    {
        /** @var Response[] $responses */
        $responses = [];
        $current = 0;
        foreach ($lines as $line) {
            if (-1 !== Str\index(Str\toLower($line), 'http/')) {
                $response = Response::create();
                $parts = Str\split($line, ' ', 3);
                $response->version = Version::from(Str\toUpper($parts[0]));
                $response->status = Status::make((int) ($parts[1] ?? '200'), $parts[2] ?? '');
                $current = Arr\push($responses, $response) - 1;

                continue;
            }

            $parts = Arr\map(Str\split($line, ':', 2), Str\trim(...));
            $responses[$current]->headers->add($parts[0] ?? '', $parts[1] ?? '');
        }

        return $responses;
    }
}
