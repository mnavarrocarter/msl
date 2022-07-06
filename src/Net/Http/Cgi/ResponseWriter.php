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

use MSL\IO\Error;
use MSL\IO\PHPResource;
use MSL\Net\Http\Flusher;
use MSL\Net\Http\Headers;
use MSL\Net\Http\ResponseWriter as HttpResponseWriter;
use MSL\Net\Http\Status;

final class ResponseWriter extends PHPResource implements HttpResponseWriter, Flusher
{
    private Headers $headers;
    private bool $sentHeaders;

    /**
     * @param resource $resource
     */
    protected function __construct($resource, Headers $headers)
    {
        parent::__construct($resource);
        $this->headers = $headers;
        $this->sentHeaders = false;
    }

    public static function create(): ResponseWriter
    {
        return new self(fopen('php://output', 'wb'), new Headers());
    }

    public function flush(): void
    {
        ob_flush();
    }

    public function headers(): Headers
    {
        return $this->headers;
    }

    /**
     * @throws Error when headers have been sent already
     */
    public function writeHeaders(Status $status = null): void
    {
        $status = $status ?? Status::fromInt();

        if ($this->sentHeaders) {
            throw new Error('Headers already sent');
        }

        foreach ($this->headers as $name => $value) {
            header($name.': '.$value, false, $status->value);
        }

        $this->sentHeaders = true;
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $bytes): int
    {
        if (false === $this->sentHeaders) {
            $this->writeHeaders();
        }

        return $this->innerWrite($bytes);
    }
}
