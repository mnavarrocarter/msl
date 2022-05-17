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
 * Interface Seeker.
 */
interface Seeker
{
    public const START = 0;
    public const CURRENT = 1;
    public const END = 2;

    /**
     * Seeks a bytes source to a specific position.
     *
     * Calling seek with no arguments will return the current cursor position.
     *
     * @throws Error if the seeking operation fails
     *
     * @return int the new cursor position after the seek operation
     */
    public function seek(int $offset = 0, int $whence = self::CURRENT): int;
}
