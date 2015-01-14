<?php

namespace Wrep\Notificato\Tests\Apns;

use \Wrep\Notificato\Apns\MessageEnvelope;
use \Wrep\Notificato\Apns\MessageFactory;

class MessageEnvelopeTest extends \PHPUnit_Framework_TestCase
{
	private $messageEnvelope;

	public function setUp()
	{
		$message = $this->getMockBuilder('\Wrep\Notificato\Apns\Message')
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
		$message = $this->getMockBuilder('\Wrep\Notificato\Apns\Message')
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

	public function testStatusDescription()
	{
		$this->messageEnvelope->setStatus(MessageEnvelope::STATUS_EARLIERERROR);
		$this->assertEquals('Failed due earlier error, will retry with other envelope', $this->messageEnvelope->getStatusDescription());
	}

	public function testFinalStatus()
	{
		$retryEnvelope = new MessageEnvelope(2, $this->messageEnvelope->getMessage());
		$this->messageEnvelope->setStatus(257, $retryEnvelope);

		$retryEnvelope->setStatus(8);
		$this->assertEquals(8, $this->messageEnvelope->getFinalStatus());
	}

	public function testFinalStatusDescription()
	{
		$retryEnvelope = new MessageEnvelope(2, $this->messageEnvelope->getMessage());
		$this->messageEnvelope->setStatus(257, $retryEnvelope);

		$retryEnvelope->setStatus(8);
		$this->assertEquals('[APNS] Invalid token', $this->messageEnvelope->getFinalStatusDescription());
	}

	public function testSetOurselfsAsRetryEnvelope()
	{
		$this->setExpectedException('InvalidArgumentException', 'Retry envelope cannot be set to this envelope.');
		$this->messageEnvelope->setStatus(0, $this->messageEnvelope);
	}
}