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

class ServerRequest extends Request
{
    /**
     * Returns the parsed body.
     */
    public function getParsedBody(): array|object|null
    {
        return $this->context()->value(ContextKey::PARSED_BODY);
    }

    /**
     * Returns the parsed cookies.
     */
    public function getParsedCookies(): array
    {
        return $this->context()->value(ContextKey::PARSED_COOKIES) ?? [];
    }
}
