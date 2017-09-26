<?php

namespace Ibonly\EtextMail;

use Ibonly\EtextMail\Exception\InvalidUrlException;
use Illuminate\Support\Facades\Config;
use Ibonly\EtextMail\Exception\MessageException;
use Ibonly\EtextMail\Exception\InvalidUserException;
use Ibonly\EtextMail\Exception\SystemErrorException;
use Ibonly\EtextMail\Exception\MessageLimitException;
use Ibonly\EtextMail\Exception\RequestErrorException;
use Ibonly\EtextMail\Exception\MessageNotSentException;
use Ibonly\EtextMail\Exception\InvalidSenderIdException;
use Ibonly\EtextMail\Exception\InsufficientCreditException;
use Ibonly\EtextMail\Exception\InvalidDestinationException;

class SMSService
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

        try {
            if (!$url['scheme']) {
                die('Only Http request are supported !');
            }
        } catch (\Exception $e) {
            throw new InvalidUrlException();
        }

        return $url;
    }

    /**
     * Process http request
     *
     * @param  $url
     * @param  $_data
     * @return object
     */
    public function sendRequest($url, $_data)
    {
        $data = $this->queryString($_data);
        $host = $this->parseUrl($url)['host'];                                   // extract host and path:
        $path = $this->parseUrl($url)['path'];
        $socket = fsockopen($host, 80);                                     // open a socket connection on port 80

        fputs($socket, "POST $path HTTP/1.1\r\n");                             // send the request headers:
        fputs($socket, "Host: $host\r\n");
        fputs($socket, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($socket, "Content-length: ". strlen($data) ."\r\n");
        fputs($socket, "Connection: close\r\n\r\n");
        fputs($socket, $data);

        return $socket;
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

    public function validateSenderId($senderId)
    {
        return strlen($senderId) <= 11 != 0 && strlen($senderId) >= 2 ? true : false;
    }

    /**
     * Get the response data from the result
     *
     * @param  $url
     * @param  $data
     * @return object
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
     * @param  $messageResponse
     * @param  $errorCode
     * @return string
     */
    public function successErrorMessage($messageResponse, $errorCode)
    {
        if ($messageResponse == "OK") {
            $messageResponse = strtok(" ");
            return $messageResponse;
        }

        return $this->etextmailExceptions($errorCode);
    }

    /**
     * Custom etextmail exception
     *
     * @param  $errorCode
     * @return \Ibonly\EtextMail\Exception
     */
    public function etextmailExceptions($errorCode = null)
    {
        switch ($errorCode) {
            case -5:
                throw new InsufficientCreditException();
                break;

            case -10:
                throw new InvalidUserException();
                break;

            case -15:
                throw new InvalidDestinationException(-15);
                break;

            case -20:
                throw new SystemErrorException();
                break;

            case -25:
                throw new RequestErrorException();
                break;

            case -30:
                throw new MessageNotSentException();
                break;

            case -45:
                throw new InvalidDestinationException(-45);
                break;

            case -50:
                throw new MessageException();
                break;

            case -55:
                throw new MessageLimitException();
                break;

            default:
                throw new \Exception('Error: please contact app admin via laravel-etextmail github issues');
                break;
        }
    }
}