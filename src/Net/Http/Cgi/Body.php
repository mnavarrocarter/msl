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

namespace MSL\Net\Http\Cgi;

use MSL\IO\PHPResource;
use MSL\IO\ReadCloser;

/**
 * Body represents the PHP CGI input stream.
 */
final class Body extends PHPResource implements ReadCloser
{
    public static function create(): Body
    {
        return new self(fopen('php://input', 'rb'));
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->innerClose();
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $length): string
    {
        return $this->innerRead($length);
    }
}
