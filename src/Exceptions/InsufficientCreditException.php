<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class InsufficientCreditException extends Exception
{
	public function __construct()
    {
        parent::__construct("Insufficient sms credit");
    }
}