<?php

namespace Wrep\Notificato\Apns;

class Gateway extends SslSocket
{
	/**
	 * Command APNS sends back on error (Internal use)
	 */
	const ERROR_RESPONSE_COMMAND = 8;

	/**
	 * Size of the APNS error response (Internal use)
	 */
	const ERROR_RESPONSE_SIZE = 6;

	/**
	 * Prefix used to save message envelopes to the store (Internal use)
	 */
	const MESSAGE_ENVELOPE_STORE_PREFIX = 'id#';

	// Current state of the connection
	private $lastMessageId;
	protected $messageEnvelopeStore;
	protected $sendQueue;

	/**
	 * Construct Gateway
	 *
	 * @param Certificate The certificate to use when connecting to APNS
	 */
	public function __construct(Certificate $certificate)
	{
		parent::__construct($certificate);

		// Setup the current state
		$this->lastMessageId = 0;
		$this->messageEnvelopeStore = array();
		$this->sendQueue = new \SplQueue();
	}

	/**
	 * Queue a message for sending
	 *
	 * @param Message The message object to queue for sending
	 * @return MessageEnvelope
	 */
	public function queue(Message $message)
	{
		// Bump the message ID
		$this->lastMessageId++;

		// Put the message in an envelope
		$envelope = new MessageEnvelope($this->lastMessageId, $message);

		// Save the message so we can update it later on
		$this->storeMessageEnvelope($envelope);

		// Queue and return the envelope
		$this->logger->debug('Queuing Apns\Message #' . $this->lastMessageId . ' to device "' . $message->getDeviceToken() . '" on Apns\Gateway with certificate "' . $this->getCertificate()->getDescription() . '"');
		$this->sendQueue->enqueue($envelope);

		return $envelope;
	}

	/**
	 * Count of all queued messages
	 *
	 * @return int
	 */
	public function getQueueLength()
	{
		return $this->sendQueue->count();
	}

	/**
	 * Send all queued messages
	 */
	public function flush()
	{
		// Don't do anything if the queue is empty
		if ($this->sendQueue->isEmpty()) {
			$this->logger->info('Flushing the already empty queue of Apns\Gateway with certificate "' . $this->getCertificate()->getDescription() . '"');
			return;
		}

		// Connect to APNS if needed
		if (!is_resource($this->getConnection())) {
			$this->connect();
		}

		$this->logger->info('Flushing ' . $this->getQueueLength() . ' messages from the queue of Apns\Gateway with certificate "' . $this->getCertificate()->getDescription() . '"');

		// Handle all messages in the queue
		while (!$this->sendQueue->isEmpty())
		{
			// Make sure signals to this process are respected and handled
			if (function_exists('pcntl_signal_dispatch')) {
				pcntl_signal_dispatch();
			}

			// Get the next message to send
			$messageEnvelope = $this->sendQueue->dequeue();
			$binaryMessage = $messageEnvelope->getBinaryMessage();

			// Send the message and check if all the bytes are written
			$bytesSend = (int)fwrite($this->getConnection(), $binaryMessage);
			if (strlen($binaryMessage) !== $bytesSend)
			{
				// Something did go wrong while sending this message, retry
				$retryMessageEnvelope = $this->queue( $messageEnvelope->getMessage() );
				$messageEnvelope->setStatus(MessageEnvelope::STATUS_SENDFAILED, $retryMessageEnvelope);
				$this->logger->debug('Failed to send Apns\Message #' . $this->lastMessageId . ' "' . $messageEnvelope->getStatusDescription() . '" to device "' . $messageEnvelope->getMessage()->getDeviceToken() . '" on Apns\Gateway with certificate "' . $this->getCertificate()->getDescription() . '"');

				// Sending failed, need to reconnect the socket
				$this->waitAndCheckForErrorResponse();
				$this->disconnect();
				$this->connect();
			}
			else
			{
				// Mark the message as send without errors
				$messageEnvelope->setStatus(MessageEnvelope::STATUS_NOERRORS);

				// Take a nap to give PHP some time to relax after sending data over the socket
				usleep(self::SEND_INTERVAL);

				// Check for errors
				$this->checkForErrorResponse();
			}
		}

		$this->waitAndCheckForErrorResponse();

		// If there are requeued messages, initiate a new flush
		if ($this->getQueueLength() > 0)
		{
			$this->flush();
		}
		else
		{
			// Clear the message envelope store
			$this->clearMessageEnvelopeStore();
		}
	}

	private function waitAndCheckForErrorResponse()
	{
		// All messages send, wait some time for an APNS response
		$read = array($this->getConnection());
		$write = $except = null;
		$changedStreams = stream_select($read, $write, $except, 0, self::READ_TIMEOUT);

		// Did waiting for the response succeed?
		if (false === $changedStreams)
		{
			throw new \RuntimeException('Could not stream_select the APNS connection.');
		}
		// Did we receive a response?
		else if ($changedStreams > 0)
		{
			// Handle the response
			$this->checkForErrorResponse();
		}
	}

	/**
	 * Check the connection for an error response from APNS
	 */
	private function checkForErrorResponse()
	{
		// Check if there is something to read from the socket
		$errorResponse = fread($this->getConnection(), self::ERROR_RESPONSE_SIZE);
		if (false !== $errorResponse && self::ERROR_RESPONSE_SIZE === strlen($errorResponse))
		{
			// Got an error, disconnect
			$this->disconnect();

			// Decode the error response
			$errorMessage = unpack('Ccommand/Cstatus/Nidentifier', $errorResponse);

			// Validate the message
			if (self::ERROR_RESPONSE_COMMAND != $errorMessage['command']) {
				throw new \RuntimeException('APNS responded with corrupt errormessage.');
			}

			// Mark the message that triggered the error as failed
			$failedMessageEnvelope = $this->retrieveMessageEnvelope($errorMessage['identifier']);
			if (null != $failedMessageEnvelope)
			{
				$failedMessageEnvelope->setStatus($errorMessage['status']);
				$this->logger->warning('Failed to send message #' . $failedMessageEnvelope->getIdentifier() . ' "' . $failedMessageEnvelope->getStatusDescription() . '" to device "' . $failedMessageEnvelope->getMessage()->getDeviceToken() . '" from the queue of Apns\Gateway with certificate "' . $this->getCertificate()->getDescription() . '"');
			}
			else
			{
				$this->logger->error('Failed retrieve message envelope for message #' . $errorMessage['identifier'] . ' that failed sending with statuscode #' . $errorMessage['status'] . ' from the queue of Apns\Gateway with certificate "' . $this->getCertificate()->getDescription() . '"');
			}

			// All messages that are send after the failed message should be send again
			$this->logger->info('Requeueing ' . ($this->lastMessageId - $errorMessage['identifier']) . ' messages that where send after the failed message to the queue of Apns\Gateway with certificate "' . $this->getCertificate()->getDescription() . '"');
			$lastMessageToResend = $this->lastMessageId;
			for ($messageId = $errorMessage['identifier'] + 1; $lastMessageToResend >= $messageId; $messageId++)
			{
				// Get the message envelope
				$messageEnvelope = $this->retrieveMessageEnvelope($messageId);

				// Check if it's send without errors
				if (null !== $messageEnvelope && $messageEnvelope->getStatus() == MessageEnvelope::STATUS_NOERRORS)
				{
					// Mark the message as failed due earlier error and requeue the message
					$retryMessageEnvelope = $this->queue( $messageEnvelope->getMessage() );
					$messageEnvelope->setStatus(MessageEnvelope::STATUS_EARLIERERROR, $retryMessageEnvelope);
					$this->logger->debug('Failed to send Apns\Message #' . $this->lastMessageId . ' "' . $messageEnvelope->getStatusDescription() . '" to device "' . $messageEnvelope->getMessage()->getDeviceToken() . '" on Apns\Gateway with certificate "' . $this->getCertificate()->getDescription() . '"');
				}
				else
				{
					$this->logger->warning('Could not requeue message #' . $this->lastMessageId . ' "Envelope already purged from envelope store" to the queue of Apns\Gateway with certificate "' . $this->getCertificate()->getDescription() . '"');
				}
			}

			// Reconnect and go on
			$this->connect();
		}
	}

	/**
	 * Store a message envelope for later reference (error handling etc)
	 *
	 * @param MessageEnvelope The envelope to story
	 */
	protected function storeMessageEnvelope(MessageEnvelope $envelope)
	{
		// Add the given anvelope to the messages array
		$this->messageEnvelopeStore[self::MESSAGE_ENVELOPE_STORE_PREFIX . $envelope->getIdentifier()] = $envelope;
	}

	/**
	 * Retrieve a stored envelope
	 *
	 * @return MessageEnvelope|null
	 */
	protected function retrieveMessageEnvelope($identifier)
	{
		$envelope = null;

		// Fetch the requested anvelope if we have any
		if ( isset($this->messageEnvelopeStore[self::MESSAGE_ENVELOPE_STORE_PREFIX . $identifier]) ) {
			$envelope = $this->messageEnvelopeStore[self::MESSAGE_ENVELOPE_STORE_PREFIX . $identifier];
		}

		return $envelope;
	}

	/**
	 * Wipes all envelopes from the store
	 */
	protected function clearMessageEnvelopeStore()
	{
		$this->messageEnvelopeStore = array();
	}
}