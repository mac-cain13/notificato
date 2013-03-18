<?php

namespace Wrep\Notificato\Test\Apns\Mock;

use Wrep\Notificato\Apns\Gateway;
use Wrep\Notificato\Apns\MessageEnvelope;

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

	public function getMessageEnvelopeStore()
	{
		return $this->messageEnvelopeStore;
	}

	public function retrieveMessageEnvelope($identifier)
	{
		return parent::retrieveMessageEnvelope($identifier);
	}
}