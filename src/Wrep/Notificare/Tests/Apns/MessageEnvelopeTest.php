<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\MessageEnvelope;
use \Wrep\Notificare\Apns\Message;

class MessageEnvelopeTest extends \PHPUnit_Framework_TestCase
{
	public function testStatus()
	{
		$messageEnvelope = new MessageEnvelope(1, new Message('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef'));
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