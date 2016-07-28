<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class MessageLimitException extends Exception
{
	public function __construct()
    {
        parent::__construct("Message exceed maximum length of 459 characters");
    }
    
    /**
     * Get error message
     *
     * @return string
     */
    public function errorMessage()
    {
        return "Exception: " . $this->getMessage();
    }
}