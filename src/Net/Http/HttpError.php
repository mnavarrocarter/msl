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
use MSL\Fmt;

class HttpError extends Exception
{
    private Request $request;
    private Response $response;

    public function __construct(string $message, int $code, Request $request, Response $response)
    {
        parent::__construct($message, $code);
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @throws HttpError if the response is an error response
     */
    public static function check(Request $request, Response $response): void
    {
        if ($response->status->isSuccess()) {
            return;
        }

        $message = 'unexpected status code error';

        if ($response->status->isServerError()) {
            $message = Fmt\sprintf(
                'Server (%s) error on %s %s',
                $response->status->value,
                $request->method->value,
                $request->uri->toString()
            );
        }

        if ($response->status->isClientError()) {
            $message = Fmt\sprintf(
                'Client (%s) error on %s %s',
                $response->status->value,
                $request->method->value,
                $request->uri->toString()
            );
        }

        if ($response->status->isRedirect()) {
            $message = Fmt\sprintf(
                'Unexpected redirect (%s) status on %s %s',
                $response->status->value,
                $request->method->value,
                $request->uri->toString()
            );
        }

        throw new HttpError($message, $response->status->value, $request, $response);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
