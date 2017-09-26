<?php

namespace Ibonly\EtextMail;

class EtextMail extends SMSService
{
    /**
     * Get sms balance from api
     * 
     * @return object
     */
    public function getSMSBalance()
    {
        return $this->getResponse($this->creditBalanceBaseUrl(), $this->setBalanceData());
    }

    /**
     * Send sms via the api
     * 
     * @param  $destination
     * @param  $message
     * @param  $longSms
     * @return object
     */
    public function sendMessage($destination, $message, $longSms = null) 
    {
        return $this->getResponse($this->sendSMSBaseUrl(), $this->setSendData($destination, $message, $longSms));
    }

    /**
     * Get number of messages sent/to be sent
     * 
     * @param  $message
     *
     * @return object
     */
    public function getMessageCount($message)
    {
        return $this->getResponse($this->messageCountBaseUrl(), $this->setMessageCountData($message));
    }

    /**
     * Get the number of character in a message
     * 
     * @param  $message
     * @return object
     */
    public function getCharacterCount($message)
    {
        return $this->getResponse($this->characterCountBaseUrl(), $this->setMessageCountData($message));
    }
}
