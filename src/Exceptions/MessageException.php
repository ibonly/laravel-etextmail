<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class MessageException extends Exception
{
	public function __construct()
    {
        parent::__construct("Message is required");
    }
}