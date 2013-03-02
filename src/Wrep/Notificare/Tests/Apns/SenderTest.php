<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\Sender;
use \Wrep\Notificare\Apns\Certificate;
use \Wrep\Notificare\Apns\Connection;
use \Wrep\Notificare\Apns\MessageFactory;
use \Wrep\Notificare\Apns\MessageEnvelope;

class SenderTests extends \PHPUnit_Framework_TestCase
{
	public function testQueueAndFlush()
	{
		$certificateA = $this->getMockBuilder('\Wrep\Notificare\Apns\Certificate')
							 ->disableOriginalConstructor()
							 ->getMock();
		$certificateA->expects($this->any())
					 ->method('getFingerprint')
					 ->will($this->returnValue('a'));

		$certificateB = $this->getMockBuilder('\Wrep\Notificare\Apns\Certificate')
							 ->disableOriginalConstructor()
							 ->getMock();
		$certificateB->expects($this->any())
					 ->method('getFingerprint')
					 ->will($this->returnValue('b'));

		$certificateC = $this->getMockBuilder('\Wrep\Notificare\Apns\Certificate')
							 ->disableOriginalConstructor()
							 ->getMock();
		$certificateC->expects($this->any())
					 ->method('getFingerprint')
					 ->will($this->returnValue('c'));

		// Create a correct and incorrect message
		$messageFactory = new MessageFactory($certificateA);
		$messageA = $messageFactory->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');
		$messageB = $messageFactory->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificateB);
		$messageC = $messageFactory->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificateC);

		// Connect and queue the messages
		$sender = new Sender($certificateA);
		$sender->setConnectionFactory(new MockConnectionFactory());

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