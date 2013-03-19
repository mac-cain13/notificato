<?php

namespace Wrep\Notificato\Apns;

use Wrep\Notificato\Apns\Certificate;

class CertificateFactory
{
	private $defaultCertificate;

	/**
	 * Create the CertificateFactory
	 *
	 * @param string|null Path to the PEM certificate file of the default certificate
	 * @param string|null Passphrase to use with the PEM file
	 * @param boolean Set to false to skip the validation of the certificate, default true
	 * @param string|null APNS environment this certificate is valid for, by default autodetects during validation
	 */
	public function __construct($pemFile = null, $passphrase = null, $validate = true, $endpointEnv = null)
	{
		// Set the default certificate
		if (null !== $pemFile) {
			$this->setDefaultCertificate( $this->createCertificate($pemFile, $passphrase, $validate, $endpointEnv) );
		}
	}

	/**
	 * Set the default certificate
	 *
	 * @param Certificate|null The certificate to use as default
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
	 * Create a Certificate
	 *
	 * @param string Path to the PEM certificate file
	 * @param string|null Passphrase to use with the PEM file
	 * @param boolean Set to false to skip the validation of the certificate, default true
	 * @param string|null APNS environment this certificate is valid for, by default autodetects during validation
	 * @return Certificate
	 */
	public function createCertificate($pemFile, $passphrase = null, $validate = true, $endpointEnv = null)
	{
		return new Certificate($pemFile, $passphrase, $validate, $endpointEnv);
	}
}
