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

use Stringable;

/**
 * Class Buffer represents a read-write stream that can be stored in memory.
 *
 * It is useful when you need readers and writers for testing purposes.
 */
final class Buffer extends PHPResource implements ReadSeeker, ReaderAt, WriteSeeker, WriterAt, Stringable, ReadCloser, WriteCloser
{
    public function __destruct()
    {
        $this->innerClose();
    }

    /**
     * @throws Error
     */
    public function __toString(): string
    {
        $this->seek(0, Seeker::START);

        return readAll($this);
    }

    /**
     * Creates an in-memory buffer of bytes.
     *
     * The pointer of the buffer is located at the end of the string.
     *
     * @throws Error
     */
    public static function make(string $string = ''): Buffer
    {
        $buffer = new self(fopen('php://temp', 'a+b'));
        $buffer->write($string);
        $buffer->seek(0, Seeker::START);

        return $buffer;
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $length): string
    {
        return $this->innerRead($length);
    }

    /**
     * {@inheritDoc}
     */
    public function readAt(int $offset, int $length): string
    {
        return $this->innerReadAt($offset, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function seek(int $offset = 0, int $whence = Seeker::CURRENT): int
    {
        return $this->innerSeek($offset, $whence);
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $bytes): int
    {
        return $this->innerWrite($bytes);
    }

    /**
     * {@inheritDoc}
     */
    public function writeAt(int $offset, string $bytes): int
    {
        return $this->innerWriteAt($offset, $bytes);
    }

    public function close(): void
    {
        $this->innerClose();
    }
}
