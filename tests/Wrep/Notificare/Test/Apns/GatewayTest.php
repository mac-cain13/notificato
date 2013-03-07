<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\Certificate;
use \Wrep\Notificare\Apns\Gateway;
use \Wrep\Notificare\Apns\Message;
use \Wrep\Notificare\Apns\MessageEnvelope;

class GatewayTest extends \PHPUnit_Framework_TestCase
{
	private $certificate;
	private $gateway;

	public function setUp()
	{
		$this->certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem');
		$this->gateway = new Gateway($this->certificate);
	}

	public function testGetCertificate()
	{
		$this->assertEquals($this->certificate, $this->gateway->getCertificate());
	}

	public function testInitialQueueLength()
	{
		$this->assertEquals(0, $this->gateway->getQueueLength());
	}

	public function testQueue()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();
		$message->expects($this->once())
				->method('validateLength')
				->will($this->returnValue(true));

		$envelope = $this->gateway->queue($message);

		$this->assertEquals(MessageEnvelope::STATUS_NOTSEND, $envelope->getStatus());
		$this->assertEquals(1, $this->gateway->getQueueLength());
	}

	public function testQueueToLargeMessage()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();
		$message->expects($this->once())
				->method('validateLength')
				->will($this->returnValue(false));

		$envelope = $this->gateway->queue($message);

		$this->assertEquals(MessageEnvelope::STATUS_PAYLOADTOOLONG, $envelope->getStatus());
		$this->assertEquals(0, $this->gateway->getQueueLength());
	}

	public function testConnectionFail()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();
		$message->expects($this->once())
				->method('validateLength')
				->will($this->returnValue(true));

		$this->certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', 'passphrase');
		$this->gateway = new Gateway($this->certificate);

		$this->gateway->queue($message);

		$this->setExpectedException('UnexpectedValueException', 'Error before connecting, please check your certificate and passphrase.');
		$this->gateway->flush();
	}

	/**
	 * @group realpush
	 */
	public function testFlush()
	{
		$this->certificate = new Certificate(__DIR__ . '/../resources/paspas.pem');
		$this->gateway = new Gateway($this->certificate);

		// Create a correct and incorrect message
		$message = new Message('2f9a6ca974ce0b4897fcc171c6a4a9a28f98c36b32962566ab83bbfa0e372c19', $this->certificate);
		$incorrectMessage = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $this->certificate);

		// Connect and queue the messages
		$gateway = new Gateway($this->certificate);
		$successEnvelope	= $gateway->queue($message);
		$failEnvelope 		= $gateway->queue($incorrectMessage);
		$retryEnvelope 		= $gateway->queue($message);

		// Send the messages
		$gateway->flush();

		// Get the retry envelope
		$retrySuccessEnvelope = $retryEnvelope->getRetryEnvelope();
		$this->assertInstanceOf('\Wrep\Notificare\Apns\MessageEnvelope', $retrySuccessEnvelope, 'Retried message has no retry envelope.');

		// Check for the expected statusses
		$this->assertEquals(MessageEnvelope::STATUS_NOERRORS, $successEnvelope->getStatus());
		$this->assertEquals(8, $failEnvelope->getStatus());
		$this->assertEquals(MessageEnvelope::STATUS_EARLIERERROR, $retryEnvelope->getStatus());
		$this->assertEquals(MessageEnvelope::STATUS_NOERRORS, $retrySuccessEnvelope->getStatus());
	}
}