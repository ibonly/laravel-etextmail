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
        return $this->getDomain()."/smsapi/Send.aspx?";
    }

    /**
     * Build credit balance api url
     * 
     * @return string
     */
    public function creditBalanceBaseUrl()
    {
        return $this->getDomain().'/smsapi/GetCreditBalance.aspx?';
    }

    /**
     * Build character count api url
     * 
     * @return string
     */
    public function characterCountBaseUrl()
    {
        return $this->getDomain()."/smsapi/GetCharacterCount.aspx?";
    }

    /**
     * Build message count api url
     * 
     * @return string
     */
    public function messageCountBaseUrl()
    {
        return $this->getDomain()."/smsapi/GetMessageCount.aspx?";
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
     * @param  $_data
     * @return string      
     */
    public function queryString($_data)
    {
        $data = array();

        while (list($var, $value) = each($_data)) {
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
    public function parseUrl($url)
    {
        $url = parse_url($url);
        if ($url['scheme'] != 'http') {
            die('Only Http request are supported !');
        }

        return $url;
    }

    /**
     * Process http request
     * 
     * @param  $url
     * @param  $_data
     * @return resource
     */
    public function sendRequest($url, $_data)
    {
        $data = $this->queryString($_data);
        $host = $this->parseUrl($url)['host'];                                    // extract host and path:
        $path = $this->parseUrl($url)['path'];
        $fp = fsockopen($host, 80);                                               // open a socket connection on port 80
     
        fputs($fp, "POST $path HTTP/1.1\r\n");                                    // send the request headers:
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ". strlen($data) ."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $data);

        return $fp;
    }

    /**
     * Recieve result from the request
     * 
     * @param  $url
     * @param  $_data
     * @return array
     */
    public function postRequest($url, $_data) 
    {
        $fp = $this->sendRequest($url, $_data);

        $result = ''; 
        while (!feof($fp)) {
            $result .= fgets($fp, 128);
        }

        fclose($fp);
     
        $result = explode("\r\n\r\n", $result, 2);                                // split the result header from the content
        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';
     
        return [$header, $content];
    }

    /**
     * @param string $senderId
     */
    public function validateSenderId($senderId)
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
    public function getResponse($url, $data)
    {
        list($header, $content) = $this->postRequest($url, $data);

        $tok = strtok($content, " "); //Split the $content result into words

        $error_code = explode(' ', $content)[1];

        if (!$this->validateSenderId($this->getSenderId())) {
            throw new InvalidSenderIdException($error_code);
        }

        return $this->successErrorMessage($tok, $error_code);
    }

    /**
     * Output function for call
     * 
     * @param  string $tok
     * @param  $errorCode
     * @return string
     */
    public function successErrorMessage($tok, $errorCode)
    {
        if ($tok == "OK") {
            $tok = strtok(" ");
            return $tok;
        } else {
            throw new EtextMailException($errorCode);
        }
    }
}
