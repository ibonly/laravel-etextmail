<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class InvalidSenderIdException extends Exception
{
    /**
     * Get error message
     *
     * @return string
     */
	public function errorMessage($errorCode)
	{
		switch ($errorCode) {
			case 35:
				return "Exception: Missing or Invalid sender Id";
				break;

			case 40:
				return "Exception: Sender Id exceed the maximum length of 11 characters"
				break;

			default:
				return "Exception: Invalid sender Id";
				break;
		}
	}
}