<?php

namespace Wrep\Notificare\Apns;

class Sender
{
	private $defaultCertificate;
	private $connectionPool;

	public function __construct(Certificate $defaultCertificate = null)
	{
		$this->setDefaultCertificate($defaultCertificate);
		$this->connectionPool = array();
	}

	public function setDefaultCertificate(Certificate $defaultCertificate)
	{
		$this->defaultCertificate = $defaultCertificate;
	}

	public function getDefaultCertificate()
	{
		return $this->defaultCertificate;
	}

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
		// TODO; Queue on the connection
	}

	public function flush()
	{

	}

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