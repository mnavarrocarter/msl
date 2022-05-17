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

use CurlHandle;
use MSL\Arr;
use MSL\Bytes;
use MSL\Fmt;
use MSL\IO;
use MSL\IO\Buffer;
use MSL\IO\EndOfFile;
use MSL\IO\NoopCloser;
use MSL\IO\ReadCloser;
use MSL\IO\Reader;
use MSL\IO\Seeker;
use MSL\Str;

/**
 * DefaultTransport uses ext-curl to send requests.
 */
final class CurlTransport implements Transport
{
    private static ?CurlTransport $instance = null;

    /**
     * @var CurlHandle[]
     */
    private array $handles;

    /**
     * @param bool   $followRedirects
     * @param int    $maxRedirects
     * @param string $proxy
     * @param int    $timeout
     * @param bool   $sslVerify
     * @param int    $maxHandles
     * @param array  $customOptions
     * @param bool   $exposeCurlInfo
     */
    public function __construct(
        private readonly bool $followRedirects = true,
        private readonly int $maxRedirects = 10,
        private readonly string $proxy = '',
        private readonly int $timeout = 0,
        private readonly bool $sslVerify = true,
        private readonly int $maxHandles = 5,
        private readonly array $customOptions = [],
        private readonly bool $exposeCurlInfo = false,
    ) {
        $this->handles = [];
    }

    public static function default(): CurlTransport
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function send(Request $request): Response
    {
        $handle = $this->createHandle($request);
        $buffer = Buffer::make('');

        $response = $this->prepare($handle, $request, $buffer);

        $curlInfo = null;

        try {
            curl_exec($handle);
            $this->checkError($request, $handle, curl_errno($handle));

            if ($this->exposeCurlInfo) {
                $curlInfo = curl_getinfo($handle);
            }
        } finally {
            $this->releaseHandle($handle);
        }

        if (null !== $curlInfo) {
            $response->headers->add('__curl_info', serialize($curlInfo));
        }

        $buffer->seek(0, Seeker::START);
        $response->body = $buffer;

        return $response;
    }

    /**
     * @throws TransportError
     */
    private function createHandle(Request $request): CurlHandle
    {
        $handle = [] !== $this->handles ? array_pop($this->handles) : curl_init();
        if (false === $handle) {
            throw new TransportError('Could not create curl handle', 0, $request);
        }

        return $handle;
    }

    private function prepare(CurlHandle $handle, Request $request, Buffer $buffer): Response
    {
        if (\defined('CURLOPT_PROTOCOLS')) {
            curl_setopt($handle, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($handle, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        }

        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($handle, CURLOPT_FAILONERROR, false);

        $this->setOptionsFromTransport($handle);
        $this->setOptionsFromRequest($handle, $request);

        $response = Response::create();

        curl_setopt($handle, CURLOPT_HEADERFUNCTION, static function (CurlHandle $ch, string $data) use ($response) {
            $str = Str\trim($data);
            if ('' !== $str) {
                if (0 === Str\index(Str\toLower($str), 'http/')) {
                    $parts = Str\split($str, ' ', 3);
                    $response->version = Version::from(Str\toUpper($parts[0]));
                    $response->status = Status::from((int) ($parts[1] ?? '200'));

                    return Bytes\len($data);
                }

                $parts = Arr\map(Str\split($str, ':'), Str\trim(...));
                $response->headers->add($parts[0] ?? '', $parts[1] ?? '');
            }

            return Bytes\len($data);
        });

        curl_setopt($handle, CURLOPT_WRITEFUNCTION, static function (CurlHandle $ch, string $data) use ($buffer) {
            return $buffer->write($data);
        });

        // Apply custom options
        if ([] !== $this->customOptions) {
            curl_setopt_array($handle, $this->customOptions);
        }

        return $response;
    }

    private function setOptionsFromRequest(CurlHandle $handle, Request $request): void
    {
        $options = [
            CURLOPT_CUSTOMREQUEST => $request->method->value,
            CURLOPT_URL => (string) $request->uri,
            CURLOPT_HTTPHEADER => $this->convertHeaders($request->headers),
        ];

        if (0 !== $version = $this->getProtocolVersion($request)) {
            $options[CURLOPT_HTTP_VERSION] = $version;
        }

        if ('' !== $request->uri->getUserInfo()) {
            $options[CURLOPT_USERPWD] = $request->uri->getUserInfo();
        }

        switch ($request->method) {
            case Method::HEAD:
                $options[CURLOPT_NOBODY] = true;

                break;

            case Method::GET:
                $options[CURLOPT_HTTPGET] = true;

                break;

            case Method::POST:
            case Method::PUT:
            case Method::DELETE:
            case Method::PATCH:
            case Method::OPTIONS:
                $body = $request->body;
                $bodySize = $this->getBodySize($body);

                // If the body size could not be determined or is too big, we stream it.
                if (0 === $bodySize || $bodySize > 1024 * 1024) {
                    $options[CURLOPT_UPLOAD] = true;
                    if (0 !== $bodySize) {
                        $options[CURLOPT_INFILESIZE] = $bodySize;
                    }
                    $options[CURLOPT_READFUNCTION] = static function ($ch, $fd, $length) use ($body) {
                        try {
                            return $body->read($length);
                        } catch (EndOfFile) {
                            return '';
                        }
                    };

                    break;
                }

                // Small body can be loaded into memory
                $options[CURLOPT_POSTFIELDS] = IO\readAll($body);

            break;
        }

        curl_setopt_array($handle, $options);
    }

    /**
     * @return string[]
     */
    private function convertHeaders(Headers $headers): array
    {
        $curlHeaders = [];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = Fmt\sprintf('%s: %s', $key, $value);
        }

        return $curlHeaders;
    }

    private function getProtocolVersion(Request $request): int
    {
        return match ($request->version) {
            Version::HTTP10 => CURL_HTTP_VERSION_1_0,
            Version::HTTP11 => CURL_HTTP_VERSION_1_1,
            Version::HTTP20 => CURL_HTTP_VERSION_2_0
        };
    }

    /**
     * @param ReadCloser $body
     */
    private function getBodySize(Reader $body): int
    {
        if ($body instanceof NoopCloser) {
            $body = $body->getInner();
        }

        // We can only determine the size of a seekable body
        if ($body instanceof Seeker) {
            $bytes = $body->seek(0, Seeker::END);
            $body->seek(0, Seeker::START);

            return $bytes;
        }

        return 0;
    }

    private function setOptionsFromTransport(CurlHandle $handle): void
    {
        if ('' !== $this->proxy) {
            curl_setopt($handle, CURLOPT_PROXY, $this->proxy);
        }

        $canFollow = $this->followRedirects;
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, $canFollow);
        curl_setopt($handle, CURLOPT_MAXREDIRS, $canFollow ? $this->maxRedirects : 0);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, $this->sslVerify ? 1 : 0);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, $this->sslVerify ? 2 : 0);
        if ($this->timeout > 0) {
            curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        }
    }

    /**
     * @throws TransportError
     */
    private function checkError(Request $request, CurlHandle $handle, int $error): void
    {
        switch ($error) {
            case CURLE_OK:
                // All OK, create a response object
                break;

            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new TransportError(curl_error($handle), $error, $request);

            case CURLE_ABORTED_BY_CALLBACK:
                throw new TransportError(curl_error($handle), $error, $request);

            default:
                throw new TransportError(curl_error($handle), $error, $request);
        }
    }

    /**
     * Release a cUrl resource. This function is from Guzzle.
     */
    private function releaseHandle(CurlHandle $handle): void
    {
        if (\count($this->handles) >= $this->maxHandles) {
            curl_close($handle);
        } else {
            // Remove all callback functions as they can hold onto references
            // and are not cleaned up by curl_reset. Using curl_setopt_array
            // does not work for some reason, so removing each one
            // individually.
            curl_setopt($handle, CURLOPT_HEADERFUNCTION, null);
            curl_setopt($handle, CURLOPT_READFUNCTION, null);
            curl_setopt($handle, CURLOPT_WRITEFUNCTION, null);
            curl_setopt($handle, CURLOPT_PROGRESSFUNCTION, null);
            curl_reset($handle);

            if (!\in_array($handle, $this->handles, true)) {
                $this->handles[] = $handle;
            }
        }
    }
}
