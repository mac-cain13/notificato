<?php

namespace Wrep\Notificare\Apns;

class Certificate
{
	// Endpoint constants
	const ENDPOINT_PRODUCTION = 'ssl://gateway.push.apple.com:2195';
	const ENDPOINT_SANDBOX = 'ssl://gateway.sandbox.push.apple.com:2195';

	private $pemFile;
	private $passphrase;
	private $endpoint;
	private $fingerprint;

	/**
	 * APNS Certificate constructor
	 *
	 * @param $pemFile string Path to the PEM certificate file
	 * @param $passphrase string Passphrase to use with the PEM file
	 * @param $endpoint string APNS endpoint this certificate is valid for
	 */
	public function __construct($pemFile, $passphrase = null, $endpoint = self::ENDPOINT_PRODUCTION)
	{
		// Expand the path to the PEM file
		$pemFile = realpath($pemFile);

		// Check if the given PEM file does exists
		if (!is_file($pemFile)) {
			throw new \InvalidArgumentException('Could not find the given PEM file "' . $pemFile . '".');
		}

		// An endpoint is required
		if (null == $endpoint) {
			throw new \InvalidArgumentException('No endpoint given.');
		}

		// Save the given parameters
		$this->pemFile = $pemFile;
		$this->passphrase = $passphrase;
		$this->endpoint = $endpoint;
		$this->fingerprint = null;
	}

	/**
	 * Get the path to the PEM file
	 *
	 * @return string
	 */
	public function getPemFile()
	{
		return $this->pemFile;
	}

	/**
	 * Checks if there is a passphrase to use with the certificate
	 *
	 * @return boolean
	 */
	public function hasPassphrase()
	{
		return (strlen($this->passphrase) > 0);
	}

	/**
	 * Passphrase to use with the PEM file
	 *
	 * @return string
	 */
	public function getPassphrase()
	{
		return $this->passphrase;
	}

	/**
	 * Get the endpoint this certificate is valid for
	 *
	 * @return string
	 */
	public function getEndpoint()
	{
		return $this->endpoint;
	}

	/**
	 * Get a unique hash of the certificate
	 *  this can be used to check if two ApnsCertificate objects are the same
	 *
	 * @return string
	 */
	public function getFingerprint()
	{
		// Calculate fingerprint if unknown
		if (null == $this->fingerprint)
		{
			$this->fingerprint = sha1_file($this->getPemFile());
		}

		return $this->fingerprint;
	}
}