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
            self::$default = new self(StreamTransport::default());
        }

        return self::$default;
    }

    /**
     * Sends the request and obtains a response.
     *
     * @throws TransportError when a DNS or socket error occurs
     */
    public function send(Request $request): Response
    {
        return $this->transport->send($request);
    }

    /**
     * Sends an HTTP Request in strict mode.
     *
     * Strict mode throws a HTTPError when a non-successful HTTP status code
     * is returned from the server. The exception contains the response for
     * further inspection.
     *
     * @throws HttpError
     * @throws TransportError
     */
    public function sendStrict(Request $request): Response
    {
        $response = $this->send($request);
        HttpError::check($request, $response);

        return $response;
    }
}
