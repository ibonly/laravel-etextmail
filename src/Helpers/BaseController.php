<?php

namespace Ibonly\EtextMail\Helpers;

use Illuminate\Support\Facades\Config;

class BaseController
{
    /**
     * Get senderid from environment variable
     * 
     * @return string
     */
    public function getSenderId()
    {
        return Config::get('etextmail.senderid');
    }

    /**
     * get username from environment variables
     * 
     * @return string
     */
    public function getUsername()
    {
        return Config::get('etextmail.username');
    }

    /**
     * get password from environment variables
     * 
     * @return string
     */
    public function getPassword()
    {
        return Config::get('etextmail.password');
    }

    /**
     * get url from environment variables
     * 
     * @return string
     */
    public function getDomain()
    {
        return Config::get('etextmail.url');
    }

    /**
     * Set the data required to get credit balance
     * 
     * @return array
     */
    public function setBalanceData()
    {
        return [
                'UN' => $this->getUsername(), 
                'p'  => $this->getPassword()
            ];
    }

    /**
     * Set the data required to send sms
     * 
     * @param  $destination
     * @param  $message
     * @param  $long
     * @return array
     */
    public function setSendData($destination, $message, $long)
    {
        $longSms = $long === null ? 0 : $long;

        return [     
                'UN' => $this->getUsername(), 
                'p'  => $this->getPassword(),
                'SA' => $this->getSenderId(),
                'DA' => $destination,
                'L'  => $longSms, 
                'M'  => $message
            ];
    }

    /**
     * Set the data required to get message details
     * 
     * @param  $message
     * @return array
     */
    public function setMessageCountData($message)
    {
        return [
                'UN' => $this->getUsername(), 
                'p'  => $this->getPassword(),
                'M'  => $message
            ];
    }
}