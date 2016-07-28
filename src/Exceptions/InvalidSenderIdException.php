<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class InvalidSenderIdException extends Exception
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
			case -35:
				return "Missing or Invalid sender Id";

			case -40:
				return "Sender Id exceed the maximum length of 11 characters";

			default:
				return "Invalid sender id";
		}
	}
}