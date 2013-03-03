<?php

namespace Wrep\Notificare\Test\Apns\Mock;

use Wrep\Notificare\Apns\Connection;
use Wrep\Notificare\Apns\MessageEnvelope;

class MockConnection extends Connection
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