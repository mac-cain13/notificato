<?php

namespace Wrep\Notificare\Apns;

class Gateway extends SslSocket
{
	// APNS response constants
	const ERROR_RESPONSE_COMMAND = 8;	// Command APNS does send on error
	const ERROR_RESPONSE_SIZE = 6;		// Size of the APNS error response

	// Gateway configuration constants
	const MAX_RECOVERY_SIZE = 50;
	const MESSAGE_ENVELOPE_STORE_PREFIX = 'id#';

	// Current state of the connection
	private $lastMessageId;
	protected $messageEnvelopeStore;
	protected $sendQueue;

	/**
	 * Construct Gateway
	 *
	 * @param $certificate Certificate The certificate to use when connecting to APNS
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
	 * @param $message Message The message object to queue for sending
	 * @param $retryLimit int The times Notificare should retry to deliver the message on failure
	 * @return MessageEnvelope
	 */
	public function queue(Message $message, $retryLimit = MessageEnvelope::DEFAULT_RETRY_LIMIT)
	{
		// Put the message in an envelope
		$this->lastMessageId++;
		$envelope = new MessageEnvelope($this->lastMessageId, $message, $retryLimit);

		// Save the message so we can track it
		$this->storeMessageEnvelope($envelope);

		// If valid, queue or else update status and return the envelope
		if ($message->validateLength()) {
			$this->sendQueue->enqueue($envelope);
		} else {
			$envelope->setStatus(MessageEnvelope::STATUS_PAYLOADTOOLONG);
		}

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
			return;
		}

		// Connect to APNS if needed
		if (!is_resource($this->getConnection())) {
			$this->connect();
		}

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
				// Something did go wrong while sending this message, retry if allowed
				if ($messageEnvelope->getRetryLimit() > 0)
				{
					$retryMessageEnvelope = $this->queue( $messageEnvelope->getMessage(), $messageEnvelope->getRetryLimit()-1 );
					$messageEnvelope->setStatus(MessageEnvelope::STATUS_SENDFAILED, $retryMessageEnvelope);
				}
				else
				{
					$messageEnvelope->setStatus(MessageEnvelope::STATUS_TOOMANYRETRIES);
				}
			}
			else
			{
				// Mark the message as send without errors
				$messageEnvelope->setStatus(MessageEnvelope::STATUS_NOERRORS);
			}

			// Take a nap to give PHP some time to relax after doing stuff with the socket
			usleep(self::SEND_INTERVAL);

			// Check for errors
			$this->checkForErrorResponse();
		}

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

			// There are probably requeued messages, so initiate a new flush
			$this->flush();
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
			$failedMessageEnvelope->setStatus($errorMessage['status']);

			// All messages that are send after the failed message should be send again
			$messageId = (int)$errorMessage['identifier'] + 1;
			while ( null !== $this->retrieveMessageEnvelope($messageId) )
			{
				// Get the message envelope
				$messageEnvelope = $this->retrieveMessageEnvelope($messageId);

				// Check if it's send without errors
				if ($messageEnvelope->getStatus() == MessageEnvelope::STATUS_NOERRORS)
				{
					// Mark the message as failed due earlier error and requeue the message again if allowed
					if ($messageEnvelope->getRetryLimit() > 0)
					{
						$retryMessageEnvelope = $this->queue( $messageEnvelope->getMessage(), $messageEnvelope->getRetryLimit()-1 );
						$messageEnvelope->setStatus(MessageEnvelope::STATUS_EARLIERERROR, $retryMessageEnvelope);
					}
					else
					{
						$messageEnvelope->setStatus(MessageEnvelope::STATUS_TOOMANYRETRIES);
					}
				}

				// Next message ID
				$messageId++;
			}

			// Reconnect and go on
			$this->connect();
		}
	}

	/**
	 * Store a message envelope for later reference (error handling etc)
	 *  Only up to self::MAX_RECOVERY_SIZE envelopes will be stored,
	 *  the oldest envelope is purged if self::MAX_RECOVERY_SIZE is exceeded
	 *
	 * @param $envelope MessageEnvelope The envelope to story
	 */
	protected function storeMessageEnvelope(MessageEnvelope $envelope)
	{
		// Add the given anvelope to the messages array
		$this->messageEnvelopeStore[self::MESSAGE_ENVELOPE_STORE_PREFIX . $envelope->getIdentifier()] = $envelope;

		// Remove all oldest elements, so we don't get bigger then self::MAX_RECOVERY_SIZE
		if (count($this->messageEnvelopeStore) > self::MAX_RECOVERY_SIZE) {
			array_splice($this->messageEnvelopeStore, 0, count($this->messageEnvelopeStore) - self::MAX_RECOVERY_SIZE);
		}
	}

	/**
	 * Retrieve a stored envelope
	 *
	 * @return MessageEnvelope|null
	 */
	protected function retrieveMessageEnvelope($identifier)
	{
		if ( !isset($this->messageEnvelopeStore[self::MESSAGE_ENVELOPE_STORE_PREFIX . $identifier]) ) {
			return null;
		}

		return $this->messageEnvelopeStore[self::MESSAGE_ENVELOPE_STORE_PREFIX . $identifier];
	}
}