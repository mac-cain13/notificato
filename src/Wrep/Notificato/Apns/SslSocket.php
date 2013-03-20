<?php

namespace Wrep\Notificato\Apns;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class SslSocket implements LoggerAwareInterface
{
	/**
	 * Minimum interval between sending two messages in microseconds (Internal use)
	 */
	const SEND_INTERVAL = 10000;

	/**
	 * Minimum interval to wait for a response (Internal use)
	 */
	const READ_TIMEOUT = 1000000;

	// Settings of the connection
	private $certificate;
	private $CACertificatePath;
	private $connectTimeout;
	private $connection;
	protected $logger;

	/**
	 * Construct Connection
	 *
	 * @param Certificate The certificate to use when connecting
	 */
	public function __construct(Certificate $certificate)
	{
		// Save the given parameters and state
		$this->certificate = $certificate;
		$this->CACertificatePath = dirname(__FILE__) . '/entrust_2048_ca.pem';
		$this->connectTimeout = ini_get('default_socket_timeout');
		$this->connection = null;
		$this->setLogger(new NullLogger());
	}

	/**
	 * Sets a logger instance on the object
	 *
	 * @param LoggerInterface $logger
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Get the certificate used with this connection
	 *
	 * @return Certificate
	 */
	public function getCertificate()
	{
		return $this->certificate;
	}

	/**
	 * Get the SSL connection resource
	 *
	 * @return resource|null
	 */
	protected function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Open the connection
	 */
	protected function connect($endpointType = Certificate::ENDPOINT_TYPE_GATEWAY)
	{
		$this->logger->debug('Connecting Apns\SslSocket to the APNS ' . $endpointType . ' service with certificate "' . $this->getCertificate()->getDescription() . '"');

		// Create the SSL context
		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->certificate->getPemFile());

		if ($this->certificate->hasPassphrase()) {
			stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->certificate->getPassphrase());
		}

		// Verify peer if an Authority Certificate is available
		if (null !== $this->CACertificatePath)
		{
			stream_context_set_option($streamContext, 'ssl', 'verify_peer', true);
			stream_context_set_option($streamContext, 'ssl', 'cafile', $this->CACertificatePath);
		}

		// Open the connection
		$errorCode = $errorString = null;
		$this->connection = @stream_socket_client($this->certificate->getEndpoint($endpointType), $errorCode, $errorString, $this->connectTimeout, STREAM_CLIENT_CONNECT, $streamContext);

		// Check if the connection succeeded
		if (false == $this->connection)
		{
			$this->connection = null;

			// Set a somewhat more clear error message on error 0
			if (0 == $errorCode) {
				$errorString = 'Error before connecting, please check your certificate and passphrase combo and the given CA certificate if any.';
			}

			throw new \UnexpectedValueException('Failed to connect to ' . $this->certificate->getEndpoint($endpointType) . ' with error #' . $errorCode . ' "' . $errorString . '".');
		}

		// Set stream in non-blocking mode and make writes unbuffered
		stream_set_blocking($this->connection, 0);
		stream_set_write_buffer($this->connection, 0);
	}

	/**
	 * Disconnect from the endpoint
	 */
	protected function disconnect()
	{
		$this->logger->debug('Disconnecting Apns\SslSocket from the APNS service with certificate "' . $this->getCertificate()->getDescription() . '"');

		// Check if there is a socket to disconnect
		if (is_resource($this->connection))
		{
			// Disconnect and unset the connection variable
			fclose($this->connection);
		}

		$this->connection = null;
	}
}