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

namespace MSL\IO;

/**
 * PHPResource wraps logic to handle a PHP resource.
 */
abstract class PHPResource
{
    /**
     * @var null|closed-resource|resource
     */
    protected $resource;

    /**
     * @param resource $resource
     */
    protected function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @throws Error
     * @throws EndOfFile
     */
    protected function innerRead(int $length): string
    {
        if (null === $this->resource) {
            throw new Error('Could not read bytes: Underlying resource is closed.');
        }
        if (feof($this->resource)) {
            throw new EndOfFile('Could not read bytes: End of file reached');
        }
        $bytes = fread($this->resource, $length);
        if (!is_string($bytes)) {
            throw new Error('Could not read bytes: Unknown error.');
        }

        return $bytes;
    }

    /**
     * @throws Error
     * @throws EndOfFile
     */
    protected function innerReadAt(int $offset, int $length): string
    {
        $this->innerSeek($offset, Seeker::START);

        return $this->innerRead($length);
    }

    /**
     * @throws Error
     */
    protected function innerSeek(int $offset, int $whence): int
    {
        if (null === $this->resource) {
            throw new Error('Could not seek to offset: Underlying resource is closed.');
        }
        $int = fseek($this->resource, $offset, $whence);
        if (-1 === $int) {
            throw new Error('Could not seek to offset: Unknown error.');
        }

        return $int;
    }

    /**
     * @throws Error
     */
    protected function innerWrite(string $bytes): int
    {
        if (null === $this->resource) {
            throw new Error('Could not write bytes: Underlying resource is closed.');
        }
        $int = fwrite($this->resource, $bytes);
        if (!is_int($int)) {
            throw new Error('Could not write bytes: Unknown error.');
        }

        return $int;
    }

    /**
     * @throws Error
     */
    protected function innerWriteAt(int $offset, string $bytes): int
    {
        $this->innerSeek($offset, Seeker::START);

        return $this->innerWrite($bytes);
    }

    protected function isClosed(): bool
    {
        return null === $this->resource;
    }

    protected function innerClose(): void
    {
        if (null === $this->resource) {
            throw new Error('resource is already closed');
        }
        fclose($this->resource);
    }
}
