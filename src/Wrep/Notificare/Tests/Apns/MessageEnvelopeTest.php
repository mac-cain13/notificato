<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\MessageEnvelope;
use \Wrep\Notificare\Apns\MessageFactory;

class MessageEnvelopeTest extends \PHPUnit_Framework_TestCase
{
	private $messageEnvelope;

	public function setUp()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();

		$this->messageEnvelope = new MessageEnvelope(1, $message);
	}

	public function testIdentifier()
	{
		$this->assertEquals(1, $this->messageEnvelope->getIdentifier());
	}

	/**
	 * @dataProvider illigalConstructionArguments
	 */
	public function testIlligalConstuction($identifier, $message)
	{
		$this->setExpectedException('InvalidArgumentException', 'is invalid, must be an integer above zero.');
		new MessageEnvelope($identifier, $message);
	}

	public function illigalConstructionArguments()
	{
		$message = $this->getMockBuilder('\Wrep\Notificare\Apns\Message')
						->disableOriginalConstructor()
						->getMock();

		return array(
			array(0, $message),
			array(-1, $message)
			);
	}

	public function testInitialStatus()
	{
		$this->assertEquals(-1, $this->messageEnvelope->getStatus());
	}

	public function testChangeStatus()
	{
		$this->messageEnvelope->setStatus(0);
		$this->assertEquals(0, $this->messageEnvelope->getStatus());
	}

	public function testChangeToIllegalStatus()
	{
		$this->setExpectedException('InvalidArgumentException', 'is not a valid status.');
		$this->messageEnvelope->setStatus(987);
		$this->assertEquals(-1, $this->messageEnvelope->getStatus());
	}

	public function testChangeFinalState()
	{
		$this->messageEnvelope->setStatus(257);

		$this->setExpectedException('RuntimeException', 'Cannot change status from final state');
		$this->messageEnvelope->setStatus(8);

		$this->assertEquals(257, $this->messageEnvelope->getStatus());
	}

	public function testSetOurselfsAsRetryEnvelope()
	{
		$this->setExpectedException('InvalidArgumentException', 'Retry envelope cannot be set to this envelope.');
		$this->messageEnvelope->setStatus(0, $this->messageEnvelope);
	}
}