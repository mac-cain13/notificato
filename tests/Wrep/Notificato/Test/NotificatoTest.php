<?php

namespace Wrep\Notificato\Tests;

use \Wrep\Notificato\Notificato;

class NotificatoTests extends \PHPUnit_Framework_TestCase
{
	private $notificato;

	public function setUp()
	{
		$this->notificato = new Notificato();
	}

	public function testDefaultCertificate()
	{
		// TODO: We should be able to retrieve the CertificateFactory and test the default certificate on that instance
		//       this is quite a hack to test the default cert
		$this->notificato = new Notificato(__DIR__ . '/resources/certificate_corrupt.pem', 'pem-passphrase', false, 'sandbox');
		$message = $this->notificato->messageBuilder()
									->setDeviceToken('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff')
									->build();

		$this->assertEquals(__DIR__ . '/resources/certificate_corrupt.pem', $message->getCertificate()->getPemFile());
		$this->assertEquals('pem-passphrase', $message->getCertificate()->getPassphrase());
		$this->assertEquals('sandbox', $message->getCertificate()->getEnvironment());
	}

	public function testCreateCertificate()
	{
		$certificateFactory = $this->getMockBuilder('\Wrep\Notificato\Apns\CertificateFactory')
									->disableOriginalConstructor()
									->getMock();

		$certificateFactory->expects($this->once())
							->method('createCertificate')
							->with($this->equalTo('cert.pem'), $this->equalTo('passphrase'), $this->equalTo(true), $this->equalTo(null));

		$this->notificato->setCertificateFactory($certificateFactory);
		$this->notificato->createCertificate('cert.pem', 'passphrase');
	}

	public function testCreateMessage()
	{
		$certificate = $this->getMockBuilder('\Wrep\Notificato\Apns\Certificate')
								->disableOriginalConstructor()
								->getMock();

		$this->notificato->messageBuilder()
						 ->setDeviceToken('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff')
						 ->setCertificate($certificate)
						 ->build();
	}

	public function testQueue()
	{
		$message = $this->getMockBuilder('\Wrep\Notificato\Apns\Message')
						->disableOriginalConstructor()
						->getMock();

		$sender = $this->getMockBuilder('\Wrep\Notificato\Apns\Sender')
						->disableOriginalConstructor()
						->getMock();

		$sender->expects($this->once())
				->method('queue')
				->with($this->equalTo($message));

		$this->notificato->setSender($sender);
		$this->notificato->queue($message);
	}

	public function testFlush()
	{
		$certificate = $this->getMockBuilder('\Wrep\Notificato\Apns\Certificate')
								->disableOriginalConstructor()
								->getMock();

		$sender = $this->getMockBuilder('\Wrep\Notificato\Apns\Sender')
						->disableOriginalConstructor()
						->getMock();

		$sender->expects($this->once())
				->method('flush')
				->with($this->equalTo($certificate));

		$this->notificato->setSender($sender);
		$this->notificato->flush($certificate);
	}

	public function testSend()
	{
		$message = $this->getMockBuilder('\Wrep\Notificato\Apns\Message')
						->disableOriginalConstructor()
						->getMock();

		$sender = $this->getMockBuilder('\Wrep\Notificato\Apns\Sender')
						->disableOriginalConstructor()
						->getMock();

		$sender->expects($this->once())
				->method('send')
				->with($this->equalTo($message));

		$this->notificato->setSender($sender);
		$this->notificato->send($message);
	}

	public function testReceiveFeedback()
	{
		$certificate = $this->getMockBuilder('\Wrep\Notificato\Apns\Certificate')
								->disableOriginalConstructor()
								->getMock();

		$feedback = $this->getMockBuilder('\Wrep\Notificato\Apns\Feedback\Feedback')
								->disableOriginalConstructor()
								->getMock();

		$feedback->expects($this->once())
					->method('receive')
					->with()
					->will($this->returnValue('returnValue'));

		$feedbackFactory = $this->getMockBuilder('\Wrep\Notificato\Apns\Feedback\FeedbackFactory')
								->disableOriginalConstructor()
								->getMock();

		$feedbackFactory->expects($this->once())
						->method('createFeedback')
						->with($this->equalTo($certificate))
						->will($this->returnValue($feedback));

		$this->notificato->setFeedbackFactory($feedbackFactory);
		$this->assertEquals('returnValue', $this->notificato->receiveFeedback($certificate));
	}
}
