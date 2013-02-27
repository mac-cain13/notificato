<?php

namespace Wrep\Notificare\Apns;

class Connection
{
	const ENDPOINT_PRODUCTION = 'ssl://gateway.push.apple.com:2195';
	const ENDPOINT_SANDBOX = 'ssl://gateway.sandbox.push.apple.com:2195';

	private $certificate;
	private $connectTimeout;
	private $connection;
	private $endpoint;

	private $lastMessageId;
	private $messageQueue;

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
		$this->messageQueue = new \SplQueue();
	}

	public function queue(Message $message)
	{
		// Put the message in an envelope
		$this->lastMessageId++;
		$envelope = new MessageEnvelope($this->lastMessageId, $message);

		// Queue the message
		$this->messageQueue->enqueue($envelope);
	}

	public function flush()
	{
		;
	}

	/**
	 * Open the connection to the APNS endpoint
	 */
	private function connect()
	{
		// Create the SSL context
		$streamContext = stream_context_create();
		stream_context_set_option($sslContext, 'ssl', 'local_cert', $this->certificate->getPemFile());

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
}