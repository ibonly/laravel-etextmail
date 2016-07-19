<?php

namespace Ibonly\EtextMail;

use GuzzleHttp\Client;

class EtextMail
{
	protected $client;

	protected $username;

	protected $password;

	protected $redirectUrl;

	protected $baseUrl;

	public function __construct()
	{
		$this->setUsername();
		$this->setPassword();
		$this->setRedirectUrl();
	}

	public function setUsername()
	{
		$this->username = Config::get('etextmail.username');
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setPassword()
	{
		$this->password = Config::get('etextmail.password');
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setRedirectUrl()
	{
		$this->redirectUrl = Config::get('etextmail.redirectUrl');
	}

	public function setBaseUrl()
	{
		$this->baseUrl = 'http://mail.etextmail.com/smsapi/Send.aspx?';
	}

	public function getBaseUrl()
	{
		return $this->baseUrl;
	}

	public function setClient()
	{
		$this->client = new Client(['base_uri' => $this->getBaseUrl()]);
	}

	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Set the response url for guzzle client
	 * 
	 * @param $url
	 * @return void
	 */
	public function setResponse($url)
	{
		$this->response = $this->getClient()->get($this->baseUrl.$url, []);
	}

	/**
	 * Setup response data
	 * 
	 * @param  $url Query string
	 * @return object
	 */
	public function api($url)
	{
		$this->setResponse($url);

		return $this->data();
	}

    /**
     *  Get the details of the required request
     *  
     * @return object
     */
    private function data()
    {
        return json_decode($this->response->getBody());
    }

    public function getSMSBalance()
    {
    	return $this->api('UN='.$this->username.'&p='.$this->password);
    }

}