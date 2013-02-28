<?php

namespace Wrep\Notificare\Apns;

class Sender
{
	private $defaultCertificate;
	private $connectionPool;

	/**
	 * Construct Sender
	 *
	 * @param $defaultCertificate Certificate|null The certificate to use when no certificate is given in the message itself
	 */
	public function __construct(Certificate $defaultCertificate = null)
	{
		$this->setDefaultCertificate($defaultCertificate);
		$this->connectionPool = array();
	}

	/**
	 * Set a default certificate
	 *
	 * @param $defaultCertificate Certificate|null The certificate to use when no certificate is given in the message itself
	 */
	public function setDefaultCertificate(Certificate $defaultCertificate = null)
	{
		$this->defaultCertificate = $defaultCertificate;
	}

	/**
	 * Get the current default certificate
	 *
	 * @return Certificate|null
	 */
	public function getDefaultCertificate()
	{
		return $this->defaultCertificate;
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
		// Get specific certificate from the message
		$certificate = $message->getCertificate();

		// Check if a specific certificate is found
		if (null == $certificate)
		{
			// No, fallback to the default certificate
			$certificate = $this->getDefaultCertificate();

			// Check if the default certificate was found
			if (null == $certificate)
			{
				// No certificate at all, this is not going to work
				throw new \RuntimeException('No APNS certificate found, unable to queue message: ' . $message->getJson());
			}
		}

		// Get the connection for the certificate
		$connection = $this->getConnectionForCertificate($certificate);

		// Queue the message
		return $connection->queue($message);
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
			// No flush the whole connection pool
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
			$this->connectionPool[$certificate->getFingerprint()] = new Connection($certificate);
		}

		// Return the connection for this certificate
		return $this->connectionPool[$certificate->getFingerprint()];
	}
}