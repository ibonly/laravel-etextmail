<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class MessageNotSentException extends Exception
{
	public function __construct()
    {
        parent::__construct("Message not sent, please try again");
    }
}