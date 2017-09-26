<?php
/**
 * Created by PhpStorm.
 * User: ibonly
 * Date: 9/26/17
 * Time: 9:53 AM
 */

namespace Ibonly\EtextMail\Test;

use Ibonly\EtextMail\SMSService;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    protected $smsService;

    public function setUp()
    {
        parent::setUp();

        $this->smsService = new SMSService();
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\InsufficientCreditException
     * @expectedExceptionMessage Insufficient sms credit
     */
    public function testInsufficientCreditException()
    {
        $this->smsService->etextmailExceptions(-5);
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\InvalidUserException
     * @expectedExceptionMessage Invalid Username or Password provided
     */
    public function testInvalidUserException()
    {
        $this->smsService->etextmailExceptions(-10);
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\MessageException
     * @expectedExceptionMessage Message is required
     */
    public function testMessageException()
    {
        $this->smsService->etextmailExceptions(-50);
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\InvalidDestinationException
     * @expectedExceptionMessage Invalid destination or destination not covered
     */
    public function testInvalidDestinationException()
    {
        $this->smsService->etextmailExceptions(-15);
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\InvalidDestinationException
     * @expectedExceptionMessage Missing or Invalid destination
     */
    public function testInvalidDestinationNotSetException()
    {
        $this->smsService->etextmailExceptions(-45);
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\SystemErrorException
     * @expectedExceptionMessage System error, please try again
     */
    public function testSystemErrorException()
    {
        $this->smsService->etextmailExceptions(-20);
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\RequestErrorException
     * @expectedExceptionMessage Request error, please try again
     */
    public function testRequestErrorException()
    {
        $this->smsService->etextmailExceptions(-25);
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\MessageNotSentException
     * @expectedExceptionMessage Message not sent, please try again
     */
    public function testMessageNotSentException()
    {
        $this->smsService->etextmailExceptions(-30);
    }

    /**
     * @expectedException \Ibonly\EtextMail\Exception\MessageLimitException
     * @expectedExceptionMessage Message exceed maximum length of 459 characters
     */
    public function testMessageLimitException()
    {
        $this->smsService->etextmailExceptions(-55);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Error: please contact app admin via laravel-etextmail github issues
     */
    public function testDefaultException()
    {
        $this->smsService->etextmailExceptions();
    }
}
