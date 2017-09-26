<?php

namespace Ibonly\EtextMail\Test;

use Mockery as m;
use Ibonly\EtextMail\SMSService;

class SMSServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $smsService;
    protected $smsServiceMock;

    public function setUp()
    {
        parent::setUp();

        $this->smsService = new SMSService();
        $this->smsServiceMock = m::mock(SMSService::class);
    }

    public function tearDown()
    {
        m::close();
    }

    public function receiveAndReturn($assert, $expectedType, $receive, $return)
    {
        $value = $this->smsServiceMock->shouldReceive($receive)->andReturn($return);
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

    public function testUrlPassedCorrectly()
    {
        $url = 'http://www.web.com';

        $parsedUrl = $this->smsService->parseUrl($url);

        $this->assertInternalType('array', $parsedUrl);
        $this->assertEquals('http', $parsedUrl['scheme']);
        $this->assertEquals('www.web.com', $parsedUrl['host']);
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\InvalidUrlException
     */
    public function testWrongUrlPassed()
    {
        $url = 'wwwwebcom';

        $this->smsService->parseUrl($url);
    }

    public function testQueryStingGenerator()
    {
        $data = ['user' => 'username', 'data' => 'message to be sent'];

        $query = $this->smsService->queryString($data);

        $this->assertInternalType('string', $query);
        $this->assertContains('&', $query);
    }
}
