<?php

namespace Wrep\Notificato\Tests\Apns;

use \Wrep\Notificato\Apns\Sender;
use \Wrep\Notificato\Apns\Certificate;
use \Wrep\Notificato\Apns\Gateway;
use \Wrep\Notificato\Apns\MessageFactory;
use \Wrep\Notificato\Apns\MessageEnvelope;
use \Wrep\Notificato\Test\Apns\Mock\MockGatewayFactory;
use \Wrep\Notificato\Test\Apns\Mock\MockGateway;

class SenderTests extends \PHPUnit_Framework_TestCase
{
	private $sender;

	public function setUp()
	{
		// Get our sender
		$this->sender = new Sender();
		$this->sender->setGatewayFactory(new MockGatewayFactory());
	}

	private function getCertificate($fingerprint)
	{
		// Create cert
		$certificate = $this->getMockBuilder('\Wrep\Notificato\Apns\Certificate')
							->disableOriginalConstructor()
							->getMock();
		$certificate->expects($this->any())
					->method('getFingerprint')
					->will($this->returnValue($fingerprint));

		return $certificate;
	}

	private function getCertificateFactory($defaultCertificate)
	{
		// Create cert
		$certificateFactory = $this->getMockBuilder('\Wrep\Notificato\Apns\CertificateFactory')
							->disableOriginalConstructor()
							->getMock();
		$certificateFactory->expects($this->any())
						->method('getDefaultCertificate')
						->will($this->returnValue($defaultCertificate));

		return $certificateFactory;
	}

	public function testSend()
	{
		$messageFactory = new MessageFactory( $this->getCertificateFactory($this->getCertificate('a')) );
		$message = $messageFactory->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');

		$this->assertEquals(0, $this->sender->getQueueLength());

		$messageEnvelope = $this->sender->send($message);

		$this->assertEquals(0, $this->sender->getQueueLength());
		$this->assertEquals(MessageEnvelope::STATUS_NOERRORS, $messageEnvelope->getStatus());
	}

	public function testQueueAndFlush()
	{
		$certificateA = $this->getCertificate('a');
		$certificateB = $this->getCertificate('b');
		$certificateC = $this->getCertificate('c');

		// Create messages
		$messageFactory = new MessageFactory( $this->getCertificateFactory($certificateA) );
		$messageA = $messageFactory->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');
		$messageB = $messageFactory->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificateB);
		$messageC = $messageFactory->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificateC);

		// Connect and queue the messages
		$sender = new Sender($certificateA);
		$sender->setGatewayFactory(new MockGatewayFactory());

		for ($i = 1; $i <= 5; $i++)
		{
			$sender->queue($messageA);
			$sender->queue($messageB);
			$sender->queue($messageC);
			$this->assertEquals($i * 3, $sender->getQueueLength());
		}

		// Send the messages
		$sender->flush();
		$this->assertEquals(0, $sender->getQueueLength());
	}
}