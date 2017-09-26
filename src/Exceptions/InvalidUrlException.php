<?php

namespace Ibonly\EtextMail\Exception;

use Exception;

class InvalidUrlException extends Exception
{
    public function __construct()
    {
        parent::__construct("Invalid Url provided");
    }
}