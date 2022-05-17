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

/**
 * StrictClient wraps a normal client and throws exceptions on HTTP protocol
 * errors.
 */
class StrictClient
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws HttpError
     * @throws TransportError
     */
    public function send(Request $request): Response
    {
        $response = $this->client->send($request);
        HttpError::check($request, $response);

        return $response;
    }
}
