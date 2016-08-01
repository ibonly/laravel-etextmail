<?php

namespace Ibonly\EtextMail\Test;

use Mockery as m;
use Ibonly\EtextMail\EtextMail;
use PHPUnit_Framework_TestCase;

class EtextMailTest extends PHPUnit_Framework_TestCase
{
	protected $etextmail;

	public function setUp()
	{
		$this->etextmail = m::mock('Ibonly\EtextMail\EtextMail');
	}

    public function tearDown()
    {
        m::close();
    }

    public function receiveAndReturn($assert, $expectedType, $receive, $return)
    {
        $value = $this->etextmail->shouldReceive($receive)->andReturn($return);
        $this->$assert($expectedType, gettype($value));
    }

	public function testShouldGetSenderId()
	{
        $this->receiveAndReturn('assertEquals', 'object', 'getSenderId', 'senderId');
	}

	public function testShouldGetUsername()
	{
        $this->receiveAndReturn('assertInternalType', 'string', 'getUsername', 'username');
	}

	public function testShouldGetPassword()
	{
        $this->receiveAndReturn('assertInternalType', 'string', 'getPassword', 'password');
	}

	public function testShouldGetDomain()
	{
        $this->receiveAndReturn('assertInternalType', 'string', 'getDomain', 'http://www.etaxtmail.com');
	}

	public function testShouldGetBalanceData()
	{
        $this->receiveAndReturn('assertEquals', 'object', 'getBalanceData', ['UN' => 'username', 'p' => 'password']);
	}

	public function testShouldGetSetSendData()
	{
        $this->receiveAndReturn('assertEquals', 'object', 'setSendData', ['UN' => 'username', 'p' => 'password', '...' => '......']);
	}

	public function testShouldSetMessageCountData()
	{
        $this->receiveAndReturn('assertEquals', 'object', 'setMessageCountData', ['UN' => 'username', 'p' => 'password', '...' => '......']);
	}

	public function testShouldGetCreditBalanceBaseURL()
	{
		$this->receiveAndReturn('assertEquals', 'object', 'sendSMSBaseUrl', 'http://mail.etextmail.com/smsapi/Send.aspx?');
	}

	public function testShouldGetSendSMSBaseURL()
	{
		$this->receiveAndReturn('assertEquals', 'object', 'creditBalanceBaseUrl', 'http://mail.etextmail.com/smsapi/GetCreditBalance.aspx?');
	}

	public function testShouldGetCharacterCountBaseURL()
	{
		$this->receiveAndReturn('assertEquals', 'object', 'characterCountBaseUrl', 'http://mail.etextmail.com/smsapi/GetCharacterCount.aspx?');
	}

	public function testShouldGetMessageCountBaseURL()
	{
		$this->receiveAndReturn('assertEquals', 'object', 'messageCountBaseUrl', 'http://mail.etextmail.com/smsapi/GetMessageCount.aspx?');
	}

	public function testGetCreditBalance()
	{
		$this->receiveAndReturn('assertEquals', 'object', 'getCreditBalance', '127');
	}

}