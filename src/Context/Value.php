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
 * Value is a context that stores a key - value.
 *
 * The key can be of any type. The best practice is to use enums as keys so
 * comparisons are not performed on strings that could be overridden.
 */
final class Value implements Context
{
    private Context $next;
    private mixed $key;
    private mixed $value;

    public function __construct(Context $next, mixed $key, mixed $value)
    {
        $this->next = $next;
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function value(mixed $key): mixed
    {
        if ($this->key === $key) {
            return $this->value;
        }

        return $this->next->value($key);
    }
}
