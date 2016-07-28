<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class EtextMailException extends Exception
{
    public function __construct($errorCode)
    {
        parent::__construct($this->errorMessage($errorCode));
    }
    
    /**
     * Get error message
     *
     * @return string
     */
    public function errorMessage($errorCode)
    {
        switch ($errorCode) {
            case -5:
                return "Insufficient sms credit";

            case -10:
                return "Invalid Username or Password provided";

            case -15:
                return "Invalid destination or destination not covered";

            case -20:
                return "System error, please try again";

            case -25:
                return "Request error, please try again";

            case -30:
                return "Message not sent";

            case -45:
                return "Missing or Invalid destination";

            case -50:
                return "Message is required";

            case -55:
                return "Message exceed maximum length of 459 characters";
            
            default:
                return "Error: please contact app admin via laravel-etextmail github issues";
        }
    }
}