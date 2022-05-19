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

use MSL\Fmt;
use MSL\IO;
use MSL\IO\ReadCloser;
use MSL\IO\Temp;
use MSL\Str;

class Response implements \Stringable, IO\WriterTo
{
    public Version $version;
    public Status $status;
    public Headers $headers;
    public ReadCloser $body;

    protected function __construct(Version $version, Status $status, Headers $headers, ReadCloser $body)
    {
        $this->version = $version;
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function __toString(): string
    {
        $buff = Temp::make();
        $this->writeTo($buff);

        return (string) $buff;
    }

    public static function create(Status $status = Status::OK, ReadCloser $body = null): Response
    {
        return new self(Version::HTTP11, $status, new Headers(), $body ?? NoBody::instance());
    }

    public function writeTo(IO\Writer $writer): int
    {
        $written = 0;
        $written += $writer->write(Fmt\sprintf(
            '%s %s %s%s',
            $this->version->value,
            $this->status->value,
            Str\toUpper($this->status->phrase()),
            "\n"
        ));

        $written += $this->headers->writeTo($writer);

        $written += IO\copy($this->body, $writer);

        return $written;
    }
}
