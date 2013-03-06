<?php

namespace Wrep\Notificare\Apns;

class MessageFactory
{
	private $defaultCertificate;

	/**
	 * Create the MessageFactory
	 *
	 * @param $defaultCertificate Certificate|null The certificate to use when no other certificate is given on message creation
	 */
	public function __construct(Certificate $certificate = null)
	{
		$this->setDefaultCertificate($certificate);
	}

	/**
	 * Set a default certificate for new messages
	 *
	 * @param $defaultCertificate Certificate|null The certificate to use when no other certificate is given on message creation
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
	 * Create a Message
	 *
	 * @param $deviceToken string Receiver of this message
	 * @param $certificate Certificate|null The certificate that must be used for the APNS connection this message is send over, null to use the default certificate
	 */
	public function createMessage($deviceToken, Certificate $certificate = null)
	{
		// Check if a certificate is given, if not use the default certificate
		if (null == $certificate) {
			$certificate = $this->getDefaultCertificate();
		}

		// Check if there is a certificate to use after falling back on the default certificate
		if (null == $certificate) {
			throw new \RuntimeException('No certificate given for the creation of the message and no default certificate available.');
		}

		// Create and return the new Message
		return new Message($deviceToken, $certificate);
	}
}