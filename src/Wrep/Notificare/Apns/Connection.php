<?php

namespace Wrep\Notificare\Apns;

class Connection
{
	const ENDPOINT_PRODUCTION = 'ssl://gateway.push.apple.com:2195';
	const ENDPOINT_SANDBOX = 'ssl://gateway.sandbox.push.apple.com:2195';

	const SEND_INTERVAL = 10000; // microseconds, so this equals 0.1 seconds
	const READ_TIMEOUT = 1000000; // microseconds, so this equals 1.0 seconds

	const ERROR_RESPONSE_COMMAND = 8; // Command APNS does send on error
	const ERROR_RESPONSE_SIZE = 6; // Size of the APNS error response

	private $certificate;
	private $connectTimeout;
	private $connection;
	private $endpoint;

	private $lastMessageId;
	private $sendQueue;
	private $messages;

	public function __construct(Certificate $certificate, $endpoint = self::ENDPOINT_PRODUCTION)
	{
		// A certificate is required
		if (null == $certificate) {
			throw new \InvalidArgumentException('No certificate given.');
		}

		// A endpoint is required
		if (null == $endpoint) {
			throw new \InvalidArgumentException('No endpoint given.');
		}

		// Save the given parameters
		$this->certificate = $certificate;
		$this->connectTimeout = ini_get('default_socket_timeout');
		$this->connection = null;
		$this->endpoint = $endpoint;

		$this->lastMessageId = 0;
		$this->sendQueue = new \SplQueue();
		$this->messages = array();
	}

	public function queue(Message $message)
	{
		// Put the message in an envelope
		$this->lastMessageId++;
		$envelope = new MessageEnvelope($this->lastMessageId, $message);

		// Queue the message and save the message so we can track it
		$this->messages[$envelope->getIdentifier()] = $envelope;
		$this->sendQueue->enqueue($envelope);

		return $envelope;
	}

	public function flush()
	{
		// Don't do anything if the queue is empty
		if ($this->sendQueue->isEmpty()) {
			return;
		}

		// Connect to APNS if needed
		if (!is_resource($this->connection)) {
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
			$bytesSend = (int)fwrite($this->connection, $binaryMessage);
			if (strlen($binaryMessage) !== $bytesSend)
			{
				// Something did go wrong while sending this message, requeue
				$messageEnvelope->setStatus(MessageEnvelope::STATUS_SENDFAILED);
				$this->queue( $messageEnvelope->getMessage() );
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
		$read = array($this->connection);
		$write = $except = null;
		$changedStreams = stream_select($read, $write, $except, 0, self::READ_TIMEOUT);

		// Did waiting for the response succeed?
		if (false === $changedStreams)
		{
			throw new \UnexpectedValueException('Could not stream_select the APNS connection.');
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

	private function checkForErrorResponse()
	{
		// Check if there is something to read from the socket
		$errorResponse = fread($this->connection, self::ERROR_RESPONSE_SIZE);
		if (false !== $errorResponse && self::ERROR_RESPONSE_SIZE === strlen($errorResponse)) {
			// Got an error, disconnect
			$this->disconnect();

			// Decode the error response
			$errorMessage = unpack('Ccommand/Cstatus/Nidentifier', $errorResponse);

			// Validate the message
			if (self::ERROR_RESPONSE_COMMAND != $errorMessage['command']) {
				throw new \RuntimeException('APNS responded with corrupt errormessage.');
			}

			// Mark the message that triggered the error as failed
			$failedMessageEnvelope = $this->messages[$errorMessage['identifier']];
			$failedMessageEnvelope->setStatus($errorMessage['status']);

			// All messages that are send after the failed message should be send again
			$messageId = (int)$errorMessage['identifier'] + 1;
			while ( isset($this->messages[$messageId]) )
			{
				// Get the message envelope
				$messageEnvelope = $this->messages[$messageId];

				// Check if it's send without errors
				if ($messageEnvelope->getStatus() == STATUS_NOERRORS)
				{
					// Mark the message as failed due earlier error and queue the message again
					$messageEnvelope->setStatus(MessageEnvelope::STATUS_EARLIERERROR);
					$this->queue( $messageEnvelope->getMessage() );
				}

				// Next message ID
				$messageId++;
			}

			// Reconnect and go on
			$this->connect();
		}
	}

	/**
	 * Open the connection to the APNS endpoint
	 */
	private function connect()
	{
		// Create the SSL context
		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->certificate->getPemFile());

		if ($this->certificate->hasPassphrase()) {
			stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->certificate->getPassphrase());
		}

		// Open the connection
		$errorCode = $errorString = null;
		$this->connection = stream_socket_client($this->endpoint, $errorCode, $errorString, $this->connectTimeout, STREAM_CLIENT_CONNECT, $streamContext);

		// Check if the connection succeeded
		if (false == $this->connection)
		{
			$this->connection = null;
			throw new \UnexpectedValueException('Failed to connect to APNS at ' . $this->endpoint . ' with error #' . $errorCode . ' "' . $errorString . '".');
		}

		// Set stream in non-blocking mode and make writes unbuffered
		stream_set_blocking($this->connection, 0);
		stream_set_write_buffer($this->connection, 0);
	}

	/**
	 * Disconnect from the APNS endpoint
	 */
	private function disconnect()
	{
		// Check if there is a socket to disconnect
		if (is_resource($this->connection))
		{
			// Disconnect and unset the connection variable
			fclose($this->connection);
		}

		$this->connection = null;
	}
}