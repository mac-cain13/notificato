<?php

namespace Wrep\Notificare\Tests;

use \Wrep\Notificare\Notificare;

class NotificareTests extends \PHPUnit_Framework_TestCase
{
	private $notificare;

	public function setUp()
	{
		$this->notificare = new Notificare();
	}

	public function testCreateMessage()
	{
		$certificate = $this->getMockBuilder('\Wrep\Notificare\Apns\Certificate')
								->disableOriginalConstructor()
								->getMock();

		$messageFactory = $this->getMockBuilder('\Wrep\Notificare\Apns\MessageFactory')
								->disableOriginalConstructor()
								->getMock();

		$messageFactory->expects($this->once())
						->method('createMessage')
						->with($this->equalTo('asdf'), $this->equalTo($certificate));

		$this->notificare->setMessageFactory($messageFactory);
		$this->notificare->createMessage('asdf', $certificate);
	}

	public function testQueue()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();

		$sender = $this->getMockBuilder('\Wrep\Notificare\Apns\Sender')
						->disableOriginalConstructor()
						->getMock();

		$sender->expects($this->once())
				->method('queue')
				->with($this->equalTo($message), $this->equalTo(9));

		$this->notificare->setSender($sender);
		$this->notificare->queue($message, 9);
	}

	public function testFlush()
	{
		$certificate = $this->getMockBuilder('\Wrep\Notificare\Apns\Certificate')
								->disableOriginalConstructor()
								->getMock();

		$sender = $this->getMockBuilder('\Wrep\Notificare\Apns\Sender')
						->disableOriginalConstructor()
						->getMock();

		$sender->expects($this->once())
				->method('flush')
				->with($this->equalTo($certificate));

		$this->notificare->setSender($sender);
		$this->notificare->flush($certificate);
	}

	public function testSend()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();

		$sender = $this->getMockBuilder('\Wrep\Notificare\Apns\Sender')
						->disableOriginalConstructor()
						->getMock();

		$sender->expects($this->once())
				->method('send')
				->with($this->equalTo($message));

		$this->notificare->setSender($sender);
		$this->notificare->send($message);
	}
}