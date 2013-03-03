<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\Certificate;
use \Wrep\Notificare\Apns\Connection;
use \Wrep\Notificare\Apns\Message;
use \Wrep\Notificare\Apns\MessageEnvelope;

class ConnectionTests extends \PHPUnit_Framework_TestCase
{
	private $certificate;
	private $connection;

	public function setUp()
	{
		$this->certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem');
		$this->connection = new Connection($this->certificate);
	}

	public function testGetCertificate()
	{
		$this->assertEquals($this->certificate, $this->connection->getCertificate());
	}

	public function testInitialQueueLength()
	{
		$this->assertEquals(0, $this->connection->getQueueLength());
	}

	public function testQueue()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();
		$message->expects($this->once())
				->method('validateLength')
				->will($this->returnValue(true));

		$envelope = $this->connection->queue($message);

		$this->assertEquals(MessageEnvelope::STATUS_NOTSEND, $envelope->getStatus());
		$this->assertEquals(1, $this->connection->getQueueLength());
	}

	public function testQueueToLargeMessage()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();
		$message->expects($this->once())
				->method('validateLength')
				->will($this->returnValue(false));

		$envelope = $this->connection->queue($message);

		$this->assertEquals(MessageEnvelope::STATUS_PAYLOADTOOLONG, $envelope->getStatus());
		$this->assertEquals(0, $this->connection->getQueueLength());
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
		$this->connection = new Connection($this->certificate);

		$this->connection->queue($message);

		$this->setExpectedException('UnexpectedValueException', 'Error before connecting, please check your certificate and passphrase.');
		$this->connection->flush();
	}

	/**
	 * @group pushtest
	 */
	public function testFlush()
	{
		$this->certificate = new Certificate(__DIR__ . '/../resources/paspas.pem');
		$this->connection = new Connection($this->certificate);

		// Create a correct and incorrect message
		$message = new Message('2f9a6ca974ce0b4897fcc171c6a4a9a28f98c36b32962566ab83bbfa0e372c19', $this->certificate);
		$incorrectMessage = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $this->certificate);

		// Connect and queue the messages
		$connection = new Connection($this->certificate);
		$successEnvelope	= $connection->queue($message);
		$failEnvelope 		= $connection->queue($incorrectMessage);
		$retryEnvelope 		= $connection->queue($message);

		// Send the messages
		$connection->flush();

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