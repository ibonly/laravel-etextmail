<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class RequestErrorException extends Exception
{
	public function __construct()
    {
        parent::__construct("Request error, please try again");
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
