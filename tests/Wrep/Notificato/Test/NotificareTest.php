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

	public function testCreateMessage()
	{
		$certificate = $this->getMockBuilder('\Wrep\Notificato\Apns\Certificate')
								->disableOriginalConstructor()
								->getMock();

		$messageFactory = $this->getMockBuilder('\Wrep\Notificato\Apns\MessageFactory')
								->disableOriginalConstructor()
								->getMock();

		$messageFactory->expects($this->once())
						->method('createMessage')
						->with($this->equalTo('asdf'), $this->equalTo($certificate));

		$this->notificato->setMessageFactory($messageFactory);
		$this->notificato->createMessage('asdf', $certificate);
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
				->with($this->equalTo($message), $this->equalTo(9));

		$this->notificato->setSender($sender);
		$this->notificato->queue($message, 9);
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
}