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
		$message = $this->getMockBuilder('\Wrep\Notificato\Apns\Message')
						->disableOriginalConstructor()
						->getMock();
		$message->expects($this->any())
				->method('getCertificate')
				->will($this->returnValue( $this->getCertificate('a') ));

		$this->assertEquals(0, $this->sender->getQueueLength());

		$messageEnvelope = $this->sender->send($message);

		$this->assertEquals(0, $this->sender->getQueueLength());
		$this->assertEquals(MessageEnvelope::STATUS_NOERRORS, $messageEnvelope->getStatus());
	}

	public function testQueueAndFlush()
	{
		$messageA = $this->getMockBuilder('\Wrep\Notificato\Apns\Message')
						->disableOriginalConstructor()
						->getMock();
		$messageA->expects($this->any())
				 ->method('getCertificate')
				 ->will($this->returnValue( $this->getCertificate('a') ));
		$messageB = $this->getMockBuilder('\Wrep\Notificato\Apns\Message')
						->disableOriginalConstructor()
						->getMock();
		$messageB->expects($this->any())
				 ->method('getCertificate')
				 ->will($this->returnValue( $this->getCertificate('b') ));
		$messageC = $this->getMockBuilder('\Wrep\Notificato\Apns\Message')
						->disableOriginalConstructor()
						->getMock();
		$messageC->expects($this->any())
				 ->method('getCertificate')
				 ->will($this->returnValue( $this->getCertificate('c') ));

		// Connect and queue the messages
		$sender = new Sender( $this->getCertificate('a') );
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