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
		$this->etextmail = m::mock(EtextMail::class);
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

	public function testShouldGetBalanceData()
	{
        $this->receiveAndReturn('assertEquals', 'object', 'getBalanceData', ['UN' => 'username', 'p' => 'password']);
	}

	public function testShouldGetSetSendData()
	{
        $this->receiveAndReturn('assertEquals', 'object', 'setSendData', ['UN' => 'username', 'p' => 'password', '...' => '......']);
	}

}