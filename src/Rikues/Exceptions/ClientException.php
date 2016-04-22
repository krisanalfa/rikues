<?php

namespace Rikues\Exceptions;

use Exception;

class ClientException extends Exception
{
    public $response;

    public function __construct($message = '', $code = 0, $response = '', Exception $previous = null)
    {
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }
}
