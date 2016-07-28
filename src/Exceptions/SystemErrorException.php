<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class SystemErrorException extends Exception
{
	public function __construct()
    {
        parent::__construct("System error, please try again");
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