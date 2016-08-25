<?php

namespace Ibonly\EtextMail;

use Ibonly\EtextMail\Helpers\BaseController;

class EtextMail extends BaseController
{
    /**
     * Get sms balance from api
     * @access public
     * @return string
     */
    public function getCreditBalance()
    {
        return $this->getResponse($this->creditBalanceBaseUrl(), $this->setBalanceData());
    }

    /**
     * Send sms via the api
     * @param  integer $destination
     * @param  string $message
     * @param  integer $longSms
     * @access public
     * @return int/float
     */
    public function sendMessage($destination, $message, $longSms = null) 
    {
        return $this->getResponse($this->sendSMSBaseUrl(), $this->setSendData($destination, $message, $longSms));
    }

    /**
     * Get number of messages sent/to be sent
     * @param  string $message
     * @access public
     * @return string
     */
    public function getMessageCount($message)
    {
        return $this->getResponse($this->messageCountBaseUrl(), $this->setMessageCountData($message));
    }

    /**
     * Get the number of character in a message
     * @param  string $message
     * @access public
     * @return string
     */
    public function getCharacterCount($message)
    {
        return $this->getResponse($this->characterCountBaseUrl(), $this->setMessageCountData($message));
    }
}
