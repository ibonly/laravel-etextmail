<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class MessageNotSentException extends Exception
{
	public function __construct()
    {
        parent::__construct("Message not sent");
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