<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\Sender;
use \Wrep\Notificare\Apns\Certificate;
use \Wrep\Notificare\Apns\Connection;
use \Wrep\Notificare\Apns\Message;
use \Wrep\Notificare\Apns\MessageEnvelope;

class SenderTests extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider defaultCertificateArguments
	 */
	public function testDefaultCertificate($constructionCert, $setCert)
	{
		$sender = new Sender($constructionCert);
		$this->assertEquals($constructionCert, $sender->getDefaultCertificate());

		$sender->setDefaultCertificate($setCert);
		$this->assertEquals($setCert, $sender->getDefaultCertificate());
	}

	public function defaultCertificateArguments()
	{
		return array(
			array(null, null),
			array(new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem'), null),
			array(null, new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem')),
			array(new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem'), new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem'))
			);
	}

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
		$messageA = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');
		$messageB = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificateB);
		$messageC = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificateC);

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