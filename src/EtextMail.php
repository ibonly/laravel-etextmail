<?php

namespace Ibonly\EtextMail;

use Illuminate\Support\Facades\Config;
use Ibonly\EtextMail\Exception\EtextMailException;

class EtextMail
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

    /**
     * Build sms send api url
     * 
     * @return string
     */
    public function sendSMSBaseUrl()
    {
        return $this->getDomain() . "/smsapi/Send.aspx?";
    }

    /**
     * Build credit balance api url
     * 
     * @return string
     */
    public function creditBalanceBaseUrl()
    {
        return $this->getDomain() . '/smsapi/GetCreditBalance.aspx?';
    }

    /**
     * Build character count api url
     * 
     * @return string
     */
    public function characterCountBaseUrl()
    {
        return $this->getDomain() . "/smsapi/GetCharacterCount.aspx?";
    }

    /**
     * Build message count api url
     * 
     * @return string
     */
    public function messageCountBaseUrl()
    {
        return $this->getDomain() . "/smsapi/GetMessageCount.aspx?";
    }

    /**
     * Get sms balance from api
     * 
     * @return string
     */
    public function getCreditBalance()
    {
        return $this->getResponse($this->creditBalanceBaseUrl(), $this->setBalanceData());
    }

    /**
     * Send sms via the api
     * 
     * @param  $destination
     * @param  $message
     * @param  $longSms
     * @return int/float
     */
    public function sendMessage($destination, $message, $longSms = null) 
    {
        return $this->getResponse($this->sendSMSBaseUrl(), $this->setSendData($destination, $message, $longSms));
    }

    /**
     * Get number of messages sent/to be sent
     * 
     * @param  $message
     * @return string
     */
    public function getMessageCount($message)
    {
        return $this->getResponse($this->messageCountBaseUrl(), $this->setMessageCountData($message));
    }

    /**
     * Get the number of character in a message
     * 
     * @param  $message
     * @return string
     */
    public function getCharacterCount($message)
    {
        return $this->getResponse($this->characterCountBaseUrl(), $this->setMessageCountData($message));
    }

    /**
     * Build the query string parameter
     * 
     * @param  $sData
     * @return string      
     */
    private function queryString($sData)
    {
        $data = array();

        while (list($var, $value) = each($sData)) {
            $data[] = "$var=$value";
        }

        return implode('&', $data);
    }

    /**
     * Validate api url
     * 
     * @param  $url
     * @return string
     */
    private function parseUrl($url)
    {
        $url = parse_url($url);
        if ($url['scheme'] != 'http') {
            throw new EtextMailException();
        }

        return $url;
    }

    /**
     * Process http request
     * 
     * @param  $url
     * @param  $sData
     * @return resource
     */
    private function sendRequest($url, $sData)
    {
        $data = $this->queryString($sData);
        $host = $this->parseUrl($url)['host']; // extract host and path:
        $path = $this->parseUrl($url)['path'];
        $socket = fsockopen($host, 80); // open a socket connection on port 80
     
        fputs($socket, "POST $path HTTP/1.1\r\n"); // send the request headers:
        fputs($socket, "Host: $host\r\n");
        fputs($socket, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($socket, "Content-length: " . strlen($data) . "\r\n");
        fputs($socket, "Connection: close\r\n\r\n");
        fputs($socket, $data);

        return $socket;
    }

    /**
     * Recieve result from the request
     * 
     * @param  string $url
     * @param  $sData
     * @return array
     */
    private function postRequest($url, $sData) 
    {
        $socket = $this->sendRequest($url, $sData);

        $result = ''; 
        while (!feof($socket)) {
            $result .= fgets($socket, 128);
        }

        fclose($socket);
     
        $result = explode("\r\n\r\n", $result, 2); // split the result header from the content
        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';
     
        return [$header, $content];
    }

    /**
     * @param string $senderId
     */
    private function validateSenderId($senderId)
    {
        return strlen($senderId) <= 11 != 0 && strlen($senderId) >= 2 ? true : false;
    }

    /**
     * Get the response data from the result
     * 
     * @param  string $url
     * @param  $data
     * @return string
     */
    private function getResponse($url, $data)
    {
        list($header, $content) = $this->postRequest($url, $data);

        $tok = strtok($content, " "); //Split the $content result into words

        $errorCode = explode(' ', $content)[1];

        if (!$this->validateSenderId($this->getSenderId())) {
            throw new EtextMailException($errorCode);
        }

        return $this->successErrorMessage($tok, $errorCode);
    }

    /**
     * Output function for call
     * 
     * @param  string $tok
     * @param  $errorCode
     * @return string
     */
    private function successErrorMessage($tok, $errorCode)
    {
        if ($tok == "OK") {
            $tok = strtok(" ");
            return $tok;
        }
        
        throw new EtextMailException($errorCode);
    }
}
