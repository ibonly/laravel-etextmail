<?php

namespace Ibonly\EtextMail;

use Illuminate\Support\Facades\Config;

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
     * @return boolean
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
		return $this->getResponse($this->smsSendBaseUrl(), $this->setSendData($destination, $message, $longSms));
	}

    /**
     * Get number of messages sent/to be sent
     * 
     * @param  $message
     * @return int
     */
	public function getMessageCount($message)
	{
        return $this->getResponse($this->messageCountBaseUrl(), $this->setMessageCountData($message));
	}

    /**
     * Get the number of character in a message
     * 
     * @param  $message
     * @return int
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
     * @return object
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
     * Get the response data from the result
     * 
     * @param  $url
     * @param  $data
     * @return object
     */
    public function getResponse($url, $data)
    {
        list($header, $content) = $this->postRequest($url, $data);

        // display the result of the request
        //echo $content . '<br>';

        $tok = strtok($content, " "); //Split the $content result into words

        if ($tok == "OK") { //Success
            $tok = strtok(" ");
            return $tok;
        } else {
            //Diaply the full error message
            return $content;
        }
    }
}
