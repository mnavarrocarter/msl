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

use Exception;

class TransportError extends Exception
{
    private Request $request;

    public function __construct(string $message, int $code, Request $request)
    {
        parent::__construct($message, $code);
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
