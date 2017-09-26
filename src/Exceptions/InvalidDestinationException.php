<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class InvalidDestinationException extends Exception
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
    public function errorMessage($errorCode = null)
    {
        switch ($errorCode) {
            case -15:
                return "Invalid destination or destination not covered";
                break;

            case -45:
                return "Missing or Invalid destination";
                break;

            default:
                break;
        }
    }
}