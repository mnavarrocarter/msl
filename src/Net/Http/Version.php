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

enum Version: string
{
    case HTTP10 = 'HTTP/1.0';

    case HTTP11 = 'HTTP/1.1';

    case HTTP20 = 'HTTP/2';
    public function major(): int
    {
        return match ($this) {
            self::HTTP10, self::HTTP11 => 1,
            self::HTTP20 => 2,
        };
    }

    public function minor(): int
    {
        return match ($this) {
            self::HTTP11 => 1,
            self::HTTP10, self::HTTP20 => 0,
        };
    }

    public function toFloat(): float
    {
        return match ($this) {
            self::HTTP10 => 1.0,
            self::HTTP11 => 1.1,
            self::HTTP20 => 2.0
        };
    }
}
