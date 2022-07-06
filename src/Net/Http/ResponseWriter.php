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

use MSL\IO\Writer;

interface ResponseWriter extends Writer
{
    /**
     * Returns the headers of the response.
     */
    public function headers(): Headers;

    /**
     * Writes the response status line and headers.
     *
     * Modifying the headers after this method is called will have no effect
     * in the response.
     */
    public function writeHeaders(Status $status = null): void;
}
