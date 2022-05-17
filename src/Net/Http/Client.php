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

class Client
{
    private static ?Client $default = null;

    private Transport $transport;

    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    public static function default(): Client
    {
        if (null === self::$default) {
            self::$default = new self(CurlTransport::default());
        }

        return self::$default;
    }

    /**
     * @throws TransportError
     */
    public function send(Request $request): Response
    {
        return $this->transport->send($request);
    }
}
