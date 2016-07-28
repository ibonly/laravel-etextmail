<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class MessageException extends Exception
{
	public function __construct()
    {
        parent::__construct("Message is required");
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