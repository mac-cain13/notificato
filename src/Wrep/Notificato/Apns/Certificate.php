<?php

namespace Wrep\Notificato\Apns;

use Wrep\Notificato\Apns\Exception\InvalidCertificateException;

class Certificate implements \Serializable
{
	/**
	 * Identifies the APNS production environment
	 */
	const ENDPOINT_ENV_PRODUCTION = 'production';

	/**
	 * Identifies the APNS sandbox/development environment
	 */
	const ENDPOINT_ENV_SANDBOX = 'sandbox';

	/**
	 * Identifies the APNS sending gateway
	 */
	const ENDPOINT_TYPE_GATEWAY = 'gateway';

	/**
	 * Identifies the APNS feedback service
	 */
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

	private $isValidated;
	private $description;
	private $validFrom;
	private $validTo;

	/**
	 * APNS Certificate constructor
	 *
	 * @param string Path to the PEM certificate file
	 * @param string|null Passphrase to use with the PEM file
	 * @param boolean Set to false to skip the validation of the certificate, default true
	 * @param string|null APNS environment this certificate is valid for, by default autodetects during validation
	 *
	 * @throws InvalidCertificateException
	 * @throws \InvalidArgumentException
	 */
	public function __construct($pemFile, $passphrase = null, $validate = true, $endpointEnv = null)
	{
		// Check if the given PEM file does exists and expand the path
		$absolutePemFilePath = realpath($pemFile);
		if (!is_file($absolutePemFilePath)) {
			throw new InvalidCertificateException('Could not find the given PEM file "' . $pemFile . '".');
		}

		// Save the given parameters
		$this->pemFile = $absolutePemFilePath;
		$this->passphrase = $passphrase;
		$this->endpointEnv = $endpointEnv;
		$this->fingerprint = null;
		$this->isValidated = false;

		// Parse (and validate) the certificate
		if ($validate)
		{
			$this->parseCertificate();
			$this->isValidated = true;
		}

		// A valid endpoint is required by now
		if (null == $this->endpointEnv) {
			throw new InvalidCertificateException('No endpoint given and/or detected from certificate.');
		} else if (self::ENDPOINT_ENV_PRODUCTION !== $this->endpointEnv && self::ENDPOINT_ENV_SANDBOX !== $this->endpointEnv) {
			throw new \InvalidArgumentException('Invalid endpoint given: ' . $endpointEnv);
		}
	}

	/**
	 * Parse and validate the certificate and private key, also extracts usefull data and sets it on this object
	 *  Also throws exceptions if the certificate/private key doesn't seem to be a valid APNS cert
	 */
	private function parseCertificate()
	{
		$now = new \DateTime();

		// Parse the certificate
		$certificateData = openssl_x509_parse( file_get_contents($this->getPemFile()) );
		if (false == $certificateData) {
			throw new InvalidCertificateException('Unable to parse certificate "' . $this->getPemFile() . '", are you sure this is a valid PEM certificate?');
		}

		// Validate the "valid from" timestamp
		if (isset($certificateData['validFrom_time_t']))
		{
			$validFrom = new \DateTime('@' . $certificateData['validFrom_time_t']);
			if ($validFrom > $now) {
				throw new InvalidCertificateException('Certificate "' . $this->getPemFile() . '" not yet valid, valid from ' . $validFrom->format(\DateTime::ISO8601) . '.');
			}

			$this->validFrom = $validFrom;
		}
		else {
			throw new InvalidCertificateException('Certificate "' . $this->getPemFile() . '" has no valid from timestamp.');
		}

		// Validate the "valid to" timestamp
		if (isset($certificateData['validTo_time_t']))
		{
			$validTo = new \DateTime('@' . $certificateData['validTo_time_t']);
			if ($validTo < $now)
			{
				throw new InvalidCertificateException('Certificate "' . $this->getPemFile() . '" expired, was valid until ' . $validTo->format(\DateTime::ISO8601) . '.');
			}

			$this->validTo = $validTo;
		}
		else {
			throw new InvalidCertificateException('Certificate "' . $this->getPemFile() . '" has no valid to timestamp.');
		}

		// Check if the certificate was issued by Apple
		if (!isset($certificateData['issuer']) || !isset($certificateData['issuer']['O']) || 'Apple Inc.' != $certificateData['issuer']['O']) {
			throw new InvalidCertificateException('Certificate "' . $this->getPemFile() . '" does not list Apple Inc. as the issuer.');
		}

		// Check if the there is an environment hidden in the certificate
		if (isset($certificateData['subject']) && isset($certificateData['subject']['CN']))
		{
			$this->description = $certificateData['subject']['CN'];

			if (null === $this->endpointEnv)
			{
				if (strpos($certificateData['subject']['CN'], 'Pass Type ID') === 0 ||
					strpos($certificateData['subject']['CN'], 'Apple Push Services') === 0 ||
					strpos($certificateData['subject']['CN'], 'Apple Production IOS Push Services') === 0 ||
					strpos($certificateData['subject']['CN'], 'Apple Production Mac Push Services') === 0) {
					// Passbook Pass certificate & APNS Production/hybrid certs, should be on production
					$this->endpointEnv = self::ENDPOINT_ENV_PRODUCTION;
				} else if (	strpos($certificateData['subject']['CN'], 'Apple Development IOS Push Services') === 0 ||
							strpos($certificateData['subject']['CN'], 'Apple Development Mac Push Services') === 0) {
					// APNS Development, should always be on sandbox
					$this->endpointEnv = self::ENDPOINT_ENV_SANDBOX;
				} else {
					throw new InvalidCertificateException('Could not detect APNS environment based on the CN string "' . $certificateData['subject']['CN'] . '" in certificate "' . $this->getPemFile() . '".');
				}
			}
		}
		else
		{
			throw new InvalidCertificateException('No APNS environment information found in certificate "' . $this->getPemFile() . '".');
		}

		// Validate the private key by loading it
		$privateKey = openssl_pkey_get_private('file://' . $this->getPemFile(), $this->getPassphrase() );
		if (false === $privateKey) {
			throw new InvalidCertificateException('Could not extract the private key from certificate "' . $this->getPemFile() . '", please check if the given passphrase is correct and if it contains a private key.');
		}

		// If a passphrase is given, the private key may not be loaded without it
		if ($this->getPassphrase() != null)
		{
			// Try to load the private key without the passphrase (should fail)
			$privateKey = openssl_pkey_get_private('file://' . $this->getPemFile() );
			if (false !== $privateKey) {
				throw new InvalidCertificateException('Passphrase given, but the private key in "' . $this->getPemFile() . '" is not encrypted, please make sure you are using the correct certificate/passphrase combination.');
			}
		}
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
	 * Get the APNS environment this certificate is associated with
	 *
	 * @return Certificate::ENDPOINT_ENV_PRODUCTION|Certificate::ENDPOINT_ENV_SANDBOX
	 */
	public function getEnvironment()
	{
		return $this->endpointEnv;
	}

	/**
	 * Check if this certificate is validated
	 *
	 * @return boolean
	 */
	public function isValidated()
	{
		return $this->isValidated;
	}

	/**
	 * An as humanreadable as possible description of the certificate to identify the certificate
	 *
	 * @return string
	 */
	public function getDescription()
	{
		$description = $this->description;

		if (null == $description) {
			$description = $this->getFingerprint();
		}

		return $description;
	}

	/**
	 * Get moment this certificate will become valid
	 *  Note: Will return null if certificate validation was disabled
	 *
	 * @return \DateTime|null
	 */
	public function getValidFrom()
	{
		return $this->validFrom;
	}

	/**
	 * Get moment this certificate will expire
	 *  Note: Will return null if certificate validation was disabled
	 *
	 * @return \DateTime|null
	 */
	public function getValidTo()
	{
		return $this->validTo;
	}

	/**
	 * Get the endpoint this certificate is valid for
	 *
	 * @param string The type of endpoint you want
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getEndpoint($endpointType)
	{
		// Check if the endpoint type is valid
		if (self::ENDPOINT_TYPE_GATEWAY !== $endpointType && self::ENDPOINT_TYPE_FEEDBACK !== $endpointType ) {
			throw new \InvalidArgumentException($endpointType . ' is not a valid endpoint type.');
		}

		return self::$endpoints[$this->endpointEnv][$endpointType];
	}

	/**
	 * Get a unique hash of the certificate
	 *  this can be used to check if two Apns\Certificate objects are the same
	 *
	 * @return string
	 */
	public function getFingerprint()
	{
		// Calculate fingerprint if unknown
		if (null == $this->fingerprint) {
			$this->fingerprint = sha1( $this->endpointEnv . sha1_file($this->getPemFile()) );
		}

		return $this->fingerprint;
	}

	/**
	 * String representation of object
	 *
	 * @return string
	 */
	public function serialize()
	{
		return serialize(array(	$this->pemFile,
								$this->passphrase,
								$this->endpointEnv,

								$this->isValidated,
								$this->description,
								$this->validFrom,
								$this->validTo));
	}

	/**
	 * Constructs the object from serialized data
	 *
	 * @param string Serialized data
	 */
	public function unserialize($serialized)
	{
		list(	$this->pemFile,
				$this->passphrase,
				$this->endpointEnv,

				$this->isValidated,
				$this->description,
				$this->validFrom,
				$this->validTo) = unserialize($serialized);

		// Fingerprint should be recalculated
		$this->fingerprint = null;
	}
}