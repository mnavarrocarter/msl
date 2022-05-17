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

use MSL\IO\EndOfFile;
use MSL\IO\ReadCloser;

final class Body implements ReadCloser
{
    private static ?Body $instance = null;

    private function __construct()
    {
        // Noop
    }

    private function __clone()
    {
        // Noop
    }

    public static function empty(): Body
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function close(): void
    {
        // Noop
    }

    /**
     * @throws EndOfFile
     */
    public function read(int $length): string
    {
        throw new EndOfFile();
    }
}
