<?php

namespace Rikues\Exceptions;

use Exception;

/**
 * Server Exception.
 *
 * @author Krisan Alfa Timur <krisan47@gmail.com>
 */
class ServerException extends Exception
{
    /**
     * Server error response.
     *
     * @var string
     */
    public $response;

    /**
     * Object constructor.
     *
     * @param string          $message
     * @param int             $code
     * @param string          $response
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $code = 0, $response = '', Exception $previous = null)
    {
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }
}
