<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\MessageEnvelope;
use \Wrep\Notificare\Apns\MessageFactory;

class MessageEnvelopeTest extends \PHPUnit_Framework_TestCase
{
	public function testStatus()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();

		$messageEnvelope = new MessageEnvelope(1, $message);
		$this->assertEquals(-1, $messageEnvelope->getStatus());

		$messageEnvelope->setStatus(0);
		$this->assertEquals(0, $messageEnvelope->getStatus());

		$messageEnvelope->setStatus(257);
		$this->assertEquals(257, $messageEnvelope->getStatus());

		$this->setExpectedException('RuntimeException', 'Cannot change status from final state');
		$messageEnvelope->setStatus(8);
		$this->assertEquals(257, $messageEnvelope->getStatus());
	}
}