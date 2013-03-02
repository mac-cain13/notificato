<?php

namespace Wrep\Notificare\Apns;

class Sender
{
	private $connectionFactory;
	private $connectionPool;

	/**
	 * Construct Sender
	 */
	public function __construct()
	{
		$this->setConnectionFactory(new ConnectionFactory());
		$this->connectionPool = array();
	}

	/**
	 * Set the connection factory to use for creating connections to APNS
	 *
	 * @param $connectionFactory ConnectionFactory The connection factory to use
	 */
	public function setConnectionFactory(ConnectionFactory $connectionFactory)
	{
		$this->connectionFactory = $connectionFactory;
	}

	/**
	 * Get the current connection factory
	 *
	 * @return ConnectionFactory
	 */
	public function getConnectionFactory()
	{
		return $this->connectionFactory;
	}

	/**
	 * Queue a message on the correct APNS connection
	 * Note: A certificate must be set in the message or as default to make this method work
	 *
	 * @param $message Message The message to queue
	 * @return MessageEnvelope
	 */
	public function queue(Message $message)
	{
		// Get the connection for the certificate
		$connection = $this->getConnectionForCertificate( $message->getCertificate() );

		// Queue the message
		return $connection->queue($message);
	}

	/**
	 * Count of all queued messages
	 *
	 * @return int
	 */
	public function getQueueLength()
	{
		$queueLength = 0;

		foreach ($this->connectionPool as $connection)
		{
			$queueLength += $connection->getQueueLength();
		}

		return $queueLength;
	}

	/**
	 * Send all queued messages
	 *
	 * @param $certificate Certificate|null When given only the connection for the given certificate is flushed
	 */
	public function flush(Certificate $certificate = null)
	{
		// Check if we must flush a specific connection
		if (null == $certificate)
		{
			// No, flush the whole connection pool
			foreach ($this->connectionPool as $connection)
			{
				$connection->flush();
			}
		}
		else
		{
			// Yes, flush only the requested connection
			$this->getConnectionForCertificate($certificate)->flush();
		}
	}

	/**
	 * Get/create the connection associated with the given certificate
	 *
	 * @param $certificate Certificate The certificate to get the connection for
	 * @return Connection
	 */
	private function getConnectionForCertificate(Certificate $certificate)
	{
		// If no connection is available for this certificate create one
		if ( !isset($this->connectionPool[$certificate->getFingerprint()]) )
		{
			$this->connectionPool[$certificate->getFingerprint()] = $this->getConnectionFactory()->createConnection($certificate);
		}

		// Return the connection for this certificate
		return $this->connectionPool[$certificate->getFingerprint()];
	}
}