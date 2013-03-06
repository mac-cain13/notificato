<?php

namespace Wrep\Notificare\Test\Apns\Mock;

use Wrep\Notificare\Apns\Gateway;
use Wrep\Notificare\Apns\MessageEnvelope;

class MockGateway extends Gateway
{
	public function flush()
	{
		// Handle all messages in the queue
		while (!$this->sendQueue->isEmpty())
		{
			// Get the next message to send
			$messageEnvelope = $this->sendQueue->dequeue();
			$messageEnvelope->setStatus(MessageEnvelope::STATUS_NOERRORS);
		}
	}
}