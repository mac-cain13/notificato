<?php

namespace Wrep\Notificare\Apns;

class Certificate
{
	// Endpoint constants
	const ENDPOINT_ENV_PRODUCTION = 'production';
	const ENDPOINT_ENV_SANDBOX = 'sandbox';

	const ENDPOINT_TYPE_GATEWAY = 'gateway';
	const ENDPOINT_TYPE_FEEDBACK = 'feedback';

	private static $endpoints = array(
			self::ENDPOINT_ENV_PRODUCTION => array(
				self::ENDPOINT_TYPE_GATEWAY  => 'ssl://gateway.push.apple.com:2195',
				self::ENDPOINT_TYPE_FEEDBACK => 'ssl://feedback.push.apple.com:2196'
				),
			self::ENDPOINT_ENV_SANDBOX => array(
				self::ENDPOINT_TYPE_GATEWAY  => 'ssl://gateway.sandbox.push.apple.com:2195',
				self::ENDPOINT_TYPE_FEEDBACK => 'ssl://feedback.sandbox.push.apple.com:2196'
				)
		);

	private $pemFile;
	private $passphrase;
	private $endpointEnv;
	private $fingerprint;

	/**
	 * APNS Certificate constructor
	 *
	 * @param $pemFile string Path to the PEM certificate file
	 * @param $passphrase string|null Passphrase to use with the PEM file
	 * @param $endpointEnv string APNS environment this certificate is valid for
	 */
	public function __construct($pemFile, $passphrase = null, $endpointEnv = self::ENDPOINT_ENV_PRODUCTION)
	{
		// Check if the given PEM file does exists and expand the path
		$absolutePemFilePath = realpath($pemFile);
		if (false === $absolutePemFilePath) {
			throw new \InvalidArgumentException('Could not find the given PEM file "' . $pemFile . '".');
		}

		// An endpoint is required
		if (null == $endpointEnv) {
			throw new \InvalidArgumentException('No endpoint given.');
		} else if (self::ENDPOINT_ENV_PRODUCTION !== $endpointEnv && self::ENDPOINT_ENV_SANDBOX !== $endpointEnv) {
			throw new \InvalidArgumentException('Invalid endpoint given: ' . $endpointEnv);
		}

		// Save the given parameters
		$this->pemFile = $absolutePemFilePath;
		$this->passphrase = $passphrase;
		$this->endpointEnv = $endpointEnv;
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
	 * @param $endpointType string The type of endpoint you want
	 * @return string
	 */
	public function getEndpoint($endpointType = self::ENDPOINT_TYPE_GATEWAY)
	{
		// Check if the endpoint type is valid
		if (self::ENDPOINT_TYPE_GATEWAY !== $endpointType && self::ENDPOINT_TYPE_FEEDBACK !== $endpointType ) {
			throw new \InvalidArgumentException($endpointType . ' is not a valid endpoint type.');
		}

		return self::$endpoints[$this->endpointEnv][$endpointType];
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
			$this->fingerprint = sha1( $this->endpointEnv . sha1_file($this->getPemFile()) );
		}

		return $this->fingerprint;
	}
}