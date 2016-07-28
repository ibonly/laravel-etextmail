<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class InvalidDestinationException extends Exception
{
	public function errorMessage($errorCode)
	{
		switch ($errorCode) {
			case 15:
				return "Exception: Invalid destination or destination not covered";
				break;

			case 45:
				return "Exception: Missing or Invalid destination;"
				break;

			default:
				return "Exception: Invalid destination";
				break;
		}
	}
}