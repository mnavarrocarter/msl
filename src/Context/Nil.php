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

namespace MSL\Context;

use MSL\Context;

/**
 * The Nil context is the default context.
 *
 * It always returns nil when it's value method is called.
 *
 * It's a singleton because we won't ever need another instance of it.
 */
final class Nil implements Context
{
    private const VALUE = null;
    private static ?Nil $instance = null;

    private function __construct()
    {
        // Noop
    }

    private function __clone()
    {
        // Noop
    }

    public static function instance(): Nil
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * {@inheritDoc}
     */
    public function value(mixed $key): mixed
    {
        return self::VALUE;
    }
}
