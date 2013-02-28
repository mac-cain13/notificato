<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\Certificate;
use \Wrep\Notificare\Apns\Connection;
use \Wrep\Notificare\Apns\Message;
use \Wrep\Notificare\Apns\MessageEnvelope;

class ConnectionTests extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider pushArguments
	 */
	public function testPush(Certificate $certificate, $deviceToken)
	{
		// Create a correct and incorrect message
		$message = new Message($deviceToken);
		$incorrectMessage = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');

		// Connect and queue the messages
		$connection = new Connection($certificate);
		$successEnvelope	= $connection->queue($message);
		$failEnvelope 		= $connection->queue($incorrectMessage);
		$retryEnvelope 		= $connection->queue($message);

		// Send the messages
		$connection->flush();

		// Get the retry envelope
		$retrySuccessEnvelope = $retryEnvelope->getRetryEnvelope();
		$this->assertInstanceOf('\Wrep\Notificare\Apns\MessageEnvelope', $retrySuccessEnvelope, 'Retried message has no retry enveloped.');

		// Check for the expected statusses
		$this->assertEquals(MessageEnvelope::STATUS_NOERRORS, $successEnvelope->getStatus());
		$this->assertEquals(8, $failEnvelope->getStatus());
		$this->assertEquals(MessageEnvelope::STATUS_EARLIERERROR, $retryEnvelope->getStatus());
		$this->assertEquals(MessageEnvelope::STATUS_NOERRORS, $retrySuccessEnvelope->getStatus());
	}

	public function pushArguments()
	{
		return array(
			// Add a valid certificate and pushtoken here to run this test
			//array(new Certificate(__DIR__ . '/../resources/paspas.pem'), '95e3097b302dd0634c4300d0386b582efc51d740bb8869412a73b52c0fda6d7c')
			);
	}
}