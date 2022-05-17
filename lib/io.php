<?php

namespace MSL\IO;

/**
 * Reads from a reader until EndOfFile is reached and puts all the contents into
 * memory.
 *
 * @psalm-param positive-int $chunk
 *
 * @throws Error
 */
function readAll(Reader $reader, int $chunk = 4096): string
{
    $contents = '';
    while (true) {
        try {
            $contents .= $reader->read($chunk);
        } catch (EndOfFile) {
            break;
        }
    }

    return $contents;
}

/**
 * Copies bytes from a reader to a writer.
 *
 * @psalm-param positive-int $chunk
 *
 * @throws Error
 *
 * @return int The amount of bytes copied
 */
function copy(Reader $reader, Writer $writer, int $chunk = 4096): int
{
    if ($reader instanceof WriterTo) {
        return $reader->writeTo($writer);
    }
    if ($writer instanceof ReaderFrom) {
        return $writer->readFrom($reader);
    }

    $copied = 0;
    while (true) {
        try {
            $bytes = $reader->read($chunk);
            $copied += $writer->write($bytes);
        } catch (EndOfFile) {
            break;
        }
    }

    return $copied;
}
