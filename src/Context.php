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

namespace MSL;

/**
 * Context is an abstraction that carries request-scoped values across an
 * application.
 *
 * Context exists to avoid the common practice in PHP of relying on shared state
 * for values that are request-specific, like authentication or authorization
 * information.
 */
interface Context
{
    /**
     * Reads a key value from the context.
     *
     * It returns null when the key does not exist.
     */
    public function value(mixed $key): mixed;
}
